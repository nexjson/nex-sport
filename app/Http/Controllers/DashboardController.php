<?php

namespace App\Http\Controllers;

use App\Models\EventPayment;
use App\Models\Game;
use App\Models\Organizer;
use App\Models\Player;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\Contracts\MatchRepositoryInterface;
use App\Repositories\Contracts\RegistrationRepositoryInterface;
use App\Repositories\Contracts\RewardClaimRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected EventRepositoryInterface $eventRepository,
        protected RegistrationRepositoryInterface $registrationRepository,
        protected MatchRepositoryInterface $matchRepository,
        protected RewardClaimRepositoryInterface $claimRepository
    ) {}

    /**
     * Handle the incoming request.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $role = $user->role?->name;

        $data = [];

        if ($role === 'super-admin') {
            $data = $this->getSuperAdminData();
        } elseif ($role === 'admin') {
            $data = $this->getAdminData();
        } elseif ($role === 'organizer') {
            $data = $this->getOrganizerData($user->id);
        } else {
            $data = $this->getPlayerData($user->id);
        }

        return Inertia::render('Dashboard', $data);
    }

    /**
     * Data for Super Admin.
     */
    private function getSuperAdminData(): array
    {
        $users = $this->userRepository->all();
        $events = $this->eventRepository->all();

        $totalRevenue = EventPayment::where('payment_status', 'approved')->sum('amount');

        // Count user distribution
        $playersCount = $users->filter(fn ($u) => $u->role?->name === 'player')->count();
        $organizersCount = $users->filter(fn ($u) => $u->role?->name === 'organizer')->count();
        $adminsCount = $users->filter(fn ($u) => in_array($u->role?->name, ['admin', 'super-admin']))->count();

        return [
            'stats' => [
                'total_users' => $users->count(),
                'total_events' => $events->count(),
                'platform_revenue' => $totalRevenue,
                'total_games' => Game::count(),
                'players_count' => $playersCount,
                'organizers_count' => $organizersCount,
                'admins_count' => $adminsCount,
            ],
        ];
    }

    /**
     * Data for Admin.
     */
    private function getAdminData(): array
    {
        $events = $this->eventRepository->all();
        $activeEventsCount = $events->filter(fn ($e) => in_array($e->status->value, ['registration', 'ongoing']))->count();

        $pendingPayments = EventPayment::where('payment_status', 'pending')->with('event.organizer')->get();
        $pendingRegsCount = $this->registrationRepository->all()->filter(fn ($r) => $r->status === 'pending' && $r->payment_status === 'completed')->count();

        return [
            'stats' => [
                'active_events' => $activeEventsCount,
                'pending_payments' => $pendingPayments->count(),
                'pending_registrations' => $pendingRegsCount,
            ],
            'pendingPayments' => $pendingPayments->map(fn ($p) => [
                'id' => $p->id,
                'event_name' => $p->event?->name,
                'organizer_name' => $p->event?->organizer?->name,
                'amount' => $p->amount,
            ]),
        ];
    }

    /**
     * Data for Organizer.
     */
    private function getOrganizerData(int $userId): array
    {
        $organizer = Organizer::where('user_id', $userId)->first();

        if (! $organizer) {
            return [
                'stats' => [
                    'my_events_count' => 0,
                    'total_participants' => 0,
                    'ticket_revenue' => 0,
                ],
                'myEvents' => [],
            ];
        }

        $myEvents = $this->eventRepository->getByOrganizer($organizer->id);

        // Sum total participants across my events
        $totalParticipants = 0;
        $ticketRevenue = 0;
        foreach ($myEvents as $event) {
            foreach ($event->eventGames as $eg) {
                $approvedRegs = $this->registrationRepository->getByEventGame($eg->id)->filter(fn ($r) => $r->status === 'approved');
                $totalParticipants += $approvedRegs->count();
                $ticketRevenue += $approvedRegs->sum('amount_paid');
            }
        }

        return [
            'stats' => [
                'my_events_count' => $myEvents->count(),
                'total_participants' => $totalParticipants,
                'ticket_revenue' => $ticketRevenue,
            ],
            'myEvents' => $myEvents->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'status' => $e->status->value,
                'start_date' => $e->start_date?->toDateString(),
                'game_divisions_count' => $e->eventGames->count(),
            ]),
        ];
    }

    /**
     * Data for Player.
     */
    private function getPlayerData(int $userId): array
    {
        $players = Player::where('user_id', $userId)->with('squad.team')->get();
        $mainPlayer = $players->first();

        $squadName = $mainPlayer && $mainPlayer->squad ? $mainPlayer->squad->name : 'No Squad';
        $gameName = $mainPlayer && $mainPlayer->game ? $mainPlayer->game->name : '-';
        $teamName = $mainPlayer && $mainPlayer->squad && $mainPlayer->squad->team ? $mainPlayer->squad->team->name : '-';

        // Calculate winrate from match history
        $winCount = 0;
        $lossCount = 0;
        $recentMatches = [];
        $upcomingMatches = [];

        if ($mainPlayer && $mainPlayer->squad_id) {
            $matches = $this->matchRepository->getMatchesBySquad($mainPlayer->squad_id);

            foreach ($matches as $match) {
                if ($match->status->value === 'completed') {
                    if ($match->winner_id === $mainPlayer->squad_id) {
                        $winCount++;
                    } else {
                        $lossCount++;
                    }

                    $recentMatches[] = [
                        'home_squad' => $match->squadHome?->name ?? 'TBD',
                        'away_squad' => $match->squadAway?->name ?? 'TBD',
                        'home_score' => $match->score_home,
                        'away_score' => $match->score_away,
                        'outcome' => $match->winner_id === $mainPlayer->squad_id ? 'Won' : 'Lost',
                        'tournament' => $match->eventGame?->event?->name,
                    ];
                } elseif ($match->status->value === 'scheduled' || $match->status->value === 'live') {
                    $upcomingMatches[] = [
                        'home_squad' => $match->squadHome?->name ?? 'TBD',
                        'away_squad' => $match->squadAway?->name ?? 'TBD',
                        'status' => $match->status->value,
                        'scheduled_at' => $match->scheduled_at?->toDateTimeString(),
                        'tournament' => $match->eventGame?->event?->name,
                    ];
                }
            }
        }

        $totalMatches = $winCount + $lossCount;
        $winRate = $totalMatches > 0 ? round(($winCount / $totalMatches) * 100) : 0;

        return [
            'squad' => [
                'name' => $squadName,
                'team' => $teamName,
                'game' => $gameName,
                'has_squad' => $mainPlayer && $mainPlayer->squad_id !== null,
            ],
            'stats' => [
                'wins' => $winCount,
                'losses' => $lossCount,
                'winrate' => $winRate,
            ],
            'recentMatches' => array_slice($recentMatches, 0, 5),
            'upcomingMatches' => array_slice($upcomingMatches, 0, 3),
        ];
    }
}
