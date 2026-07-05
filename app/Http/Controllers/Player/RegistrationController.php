<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistrationRequest;
use App\Models\EventGame;
use App\Models\Registration;
use App\Models\Squad;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\Contracts\RegistrationRepositoryInterface;
use App\Repositories\Contracts\SquadRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    public function __construct(
        protected RegistrationRepositoryInterface $registrationRepository,
        protected EventRepositoryInterface $eventRepository,
        protected SquadRepositoryInterface $squadRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Registration::class);

        $userId = auth()->id();

        // Get player's owned squads to display as options for registration
        $mySquads = $this->squadRepository->all()->filter(function ($squad) use ($userId) {
            return $squad->team->user_id === $userId;
        })->values();

        // Fetch all active registrations made by player's squads
        $registrations = $this->registrationRepository->all()->filter(function ($reg) use ($userId) {
            return $reg->squad->team->user_id === $userId;
        });

        // Fetch available public events for registration
        $events = $this->eventRepository->getPublicEvents();

        return Inertia::render('player/registrations/Index', [
            'mySquads' => $mySquads,
            'registrations' => $registrations->values(),
            'events' => $events,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        Gate::authorize('create', Registration::class);

        $validated = $request->validated();

        $squad = $this->squadRepository->find($validated['squad_id']);
        $eventGame = EventGame::with('event')->find($validated['event_games_id']);

        if (! $squad || ! $eventGame) {
            abort(404);
        }

        // Leader check
        if ($squad->team->user_id !== auth()->id()) {
            abort(403, 'Only the squad leader can register.');
        }

        // Game compatibility check
        if ($squad->game_id !== $eventGame->game_id) {
            return redirect()->back()->with('error', 'Squad game division does not match tournament game division.');
        }

        // Duplicate registration check
        $exists = $this->registrationRepository->getBySquad($squad->id)
            ->where('event_games_id', $eventGame->id)
            ->whereIn('status', ['pending', 'approved'])
            ->isNotEmpty();

        if ($exists) {
            return redirect()->back()->with('error', 'This squad is already registered for this tournament.');
        }

        // Quota check
        $approvedCount = $this->registrationRepository->getByEventGame($eventGame->id)
            ->where('status', 'approved')
            ->count();

        if ($approvedCount >= $eventGame->max_squads) {
            return redirect()->back()->with('error', 'Registration is full.');
        }

        // Ticket processing
        $ticketPrice = $eventGame->ticket_price;
        $adminFee = $eventGame->admin_ticket_fee;
        $totalFee = $ticketPrice + $adminFee;

        $registration = $this->registrationRepository->create([
            'squad_id' => $squad->id,
            'event_games_id' => $eventGame->id,
            'payment_status' => $totalFee > 0 ? 'pending' : 'completed',
            'payment_method' => $totalFee > 0 ? 'qris' : 'free',
            'ticket_price' => $ticketPrice,
            'admin_fee' => $adminFee,
            'amount_paid' => $totalFee,
            'status' => 'pending',
            'registered_at' => now(),
        ]);

        if ($totalFee > 0) {
            return redirect()->route('player.registrations.index')->with('success', 'Registration submitted. Please pay the ticket fee.');
        }

        return redirect()->route('player.registrations.index')->with('success', 'Squad registered successfully (Free Entry).');
    }

    /**
     * Mock Ticket Fee Payment.
     */
    public function payTicket(int $id): RedirectResponse
    {
        $registration = $this->registrationRepository->find($id);

        if (! $registration) {
            abort(404);
        }

        Gate::authorize('update', $registration);

        if ($registration->payment_status === 'completed') {
            return redirect()->back()->with('error', 'Ticket is already paid.');
        }

        $this->registrationRepository->update($id, [
            'payment_status' => 'completed',
            'payment_receipt' => 'TICKET-PAID-'.time(),
        ]);

        return redirect()->route('player.registrations.index')->with('success', 'Ticket payment successful! Waiting for organizer approval.');
    }

    /**
     * Cancel registration.
     */
    public function cancel(int $id): RedirectResponse
    {
        $registration = $this->registrationRepository->find($id);

        if (! $registration) {
            abort(404);
        }

        Gate::authorize('cancel', $registration);

        if ($registration->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot cancel an already approved registration.');
        }

        $this->registrationRepository->update($id, [
            'status' => 'cancelled',
            'payment_status' => $registration->amount_paid > 0 ? 'refunded' : 'free',
        ]);

        return redirect()->route('player.registrations.index')->with('success', 'Registration cancelled successfully.');
    }

    /**
     * Organizer Action: Approve or Reject Squad Registration.
     */
    public function processRegistration(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approved,rejected',
        ]);

        $registration = $this->registrationRepository->find($id);

        if (! $registration) {
            abort(404);
        }

        Gate::authorize('processRegistration', $registration);

        if ($validated['action'] === 'approved') {
            // Check quota again
            $approvedCount = $this->registrationRepository->getByEventGame($registration->event_games_id)
                ->where('status', 'approved')
                ->count();

            if ($approvedCount >= $registration->eventGame->max_squads) {
                return redirect()->back()->with('error', 'Cannot approve. Quota has been reached.');
            }

            $this->registrationRepository->update($id, [
                'status' => 'approved',
            ]);

            return redirect()->back()->with('success', 'Squad registration approved.');
        }

        if ($validated['action'] === 'rejected') {
            $this->registrationRepository->update($id, [
                'status' => 'rejected',
                'payment_status' => $registration->amount_paid > 0 ? 'refunded' : 'free',
            ]);

            return redirect()->back()->with('success', 'Squad registration rejected. Fees refunded.');
        }

        return redirect()->back();
    }
}
