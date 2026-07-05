<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\EventGame;
use App\Models\EventPayment;
use App\Models\Game;
use App\Models\Organizer;
use App\Repositories\Contracts\EventRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function __construct(
        protected EventRepositoryInterface $eventRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $organizer = Organizer::where('user_id', auth()->id())->first();

        $events = $organizer
            ? $this->eventRepository->getByOrganizer($organizer->id)
            : collect();

        return Inertia::render('organizer/events/Index', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('organizer/events/Create', [
            'games' => Game::where('status', true)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $organizer = Organizer::where('user_id', auth()->id())->first();

        if (! $organizer) {
            return redirect()->back()->with('error', 'You must have an Organizer Profile to create events.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:events',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'banner' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'tournament_type' => 'required|string|in:single_elimination,double_elimination,round_robin,swiss',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['organizer_id'] = $organizer->id;
        $validated['status'] = EventStatus::Draft;
        $validated['registration_start'] = now();
        $startDate = Carbon::parse($validated['start_date']);
        $validated['registration_end'] = $startDate->isToday() ? $startDate : $startDate->copy()->subDay();

        $event = $this->eventRepository->create($validated);

        return redirect()->route('organizer.events.edit', $event->id)->with('success', 'Event draft created. Please complete the setup steps.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): Response
    {
        $event = $this->eventRepository->find($id);

        if (! $event) {
            abort(404);
        }

        if ($event->organizer->user_id !== auth()->id() && ! in_array(auth()->user()->role?->name, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized.');
        }

        return Inertia::render('organizer/events/Edit', [
            'event' => $event,
            'games' => Game::where('status', true)->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $event = $this->eventRepository->find($id);

        if (! $event) {
            abort(404);
        }

        if ($event->organizer->user_id !== auth()->id() && ! in_array(auth()->user()->role?->name, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:events,name,'.$id,
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'banner' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'tournament_type' => 'required|string|in:single_elimination,double_elimination,round_robin,swiss',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->eventRepository->update($id, $validated);

        return redirect()->back()->with('success', 'Event details updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $event = $this->eventRepository->find($id);

        if (! $event) {
            abort(404);
        }

        if ($event->organizer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        // Guard: only draft events can be deleted
        if ($event->status !== EventStatus::Draft) {
            return redirect()->back()->with('error', 'Only draft tournaments can be deleted.');
        }

        $this->eventRepository->delete($id);

        return redirect()->route('organizer.events.index')->with('success', 'Tournament deleted successfully.');
    }

    /**
     * Add event game division.
     */
    public function storeGame(Request $request, int $eventId): RedirectResponse
    {
        $event = $this->eventRepository->find($eventId);
        if ($event->organizer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'max_squads' => 'required|integer|min:2',
            'ticket_price' => 'required|integer|min:0',
            'admin_ticket_fee' => 'required|integer|min:0',
        ]);

        // Duplicate check
        $exists = $event->eventGames()->where('game_id', $validated['game_id'])->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'This game division is already added.');
        }

        $event->eventGames()->create($validated);

        return redirect()->back()->with('success', 'Game division added to tournament.');
    }

    /**
     * Remove event game division.
     */
    public function destroyGame(int $eventId, int $eventGamesId): RedirectResponse
    {
        $event = $this->eventRepository->find($eventId);
        if ($event->organizer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $eventGame = EventGame::find($eventGamesId);
        if ($eventGame->registrations()->exists()) {
            return redirect()->back()->with('error', 'Cannot remove game division with registered squads.');
        }

        $eventGame->delete();

        return redirect()->back()->with('success', 'Game division removed.');
    }

    /**
     * Add sponsor.
     */
    public function storeSponsor(Request $request, int $eventId): RedirectResponse
    {
        $event = $this->eventRepository->find($eventId);
        if ($event->organizer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        $event->eventSponsors()->create($validated);

        return redirect()->back()->with('success', 'Sponsor added.');
    }

    /**
     * Submit publication and pay deposit (Mock Payment).
     */
    public function payDeposit(Request $request, int $eventId): RedirectResponse
    {
        $event = $this->eventRepository->find($eventId);
        if ($event->organizer->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($event->eventGames()->count() === 0) {
            return redirect()->back()->with('error', 'Add at least one game division before publishing.');
        }

        // Calculate deposit: total rewards (let's say we seed default reward configuration or calculate from reward settings)
        // For simplicity: we mock a flat deposit sum of Rp 1,000,000 + 100,000 service fee.
        $totalRewards = $event->rewards()->sum('prize_amount') ?: 1000000;
        $serviceFee = 100000;
        $totalAmount = $totalRewards + $serviceFee;

        // Check if there is already an approved payment
        if ($event->eventPayments()->where('payment_status', 'approved')->exists()) {
            return redirect()->back()->with('error', 'Event deposit has already been paid and verified.');
        }

        // Create deposit payment record
        $payment = EventPayment::create([
            'event_id' => $event->id,
            'amount' => $totalAmount,
            'payment_method' => 'qris',
            'payment_status' => 'approved', // Auto-approved for mock purposes!
            'payment_receipt' => 'MOCK-RECEIPT-'.time(),
            'verified_at' => now(),
        ]);

        // Update event status to waiting verification or registration
        $this->eventRepository->update($event->id, [
            'status' => EventStatus::Registration,
        ]);

        return redirect()->back()->with('success', 'Deposit paid successfully! Tournament status updated to Registration.');
    }

    /**
     * Toggle manual registration status.
     */
    public function toggleRegistration(Request $request, int $id): RedirectResponse
    {
        $event = $this->eventRepository->find($id);

        if (! $event) {
            abort(404);
        }

        if ($event->organizer->user_id !== auth()->id() && ! in_array(auth()->user()->role?->name, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized.');
        }

        if ($event->status === EventStatus::Registration) {
            $this->eventRepository->update($id, ['status' => EventStatus::Ongoing]);
            $msg = 'Registration closed manually. Tournament status updated to Ongoing.';
        } elseif ($event->status === EventStatus::Ongoing) {
            $this->eventRepository->update($id, ['status' => EventStatus::Registration]);
            $msg = 'Registration opened manually. Tournament status updated to Registration.';
        } else {
            return redirect()->back()->with('error', 'Registration can only be toggled when the status is Registration or Ongoing.');
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Override tournament status (Admin/Super-Admin only).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        if (! in_array(auth()->user()->role?->name, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized.');
        }

        $event = $this->eventRepository->find($id);

        if (! $event) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:draft,waiting_payment,waiting_verification,registration,ongoing,completed,cancelled',
        ]);

        $this->eventRepository->update($id, ['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Tournament status overridden successfully to '.$validated['status'].'.');
    }
}
