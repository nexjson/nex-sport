<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\EventStatus;
use App\Enums\MatchStatus;
use App\Http\Controllers\Controller;
use App\Models\EventGame;
use App\Models\GameMatch;
use App\Models\Registration;
use App\Models\Reward;
use App\Models\RewardClaim;
use App\Models\Squad;
use App\Models\Standing;
use App\Repositories\Contracts\MatchRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MatchController extends Controller
{
    public function __construct(
        protected MatchRepositoryInterface $matchRepository
    ) {}

    /**
     * Display all matches and standings for an event game.
     */
    public function index(int $eventGamesId): Response
    {
        $eventGame = EventGame::with(['event', 'game'])->find($eventGamesId);

        if (! $eventGame) {
            abort(404);
        }

        $matches = $this->matchRepository->getByEventGame($eventGamesId);
        $standings = Standing::where('event_games_id', $eventGamesId)->with('squad.team')->orderByDesc('points')->get();

        return Inertia::render('organizer/matches/Bracket', [
            'eventGame' => $eventGame,
            'matches' => $matches,
            'standings' => $standings,
        ]);
    }

    /**
     * Generate Single Elimination Bracket.
     */
    public function generate(int $eventGamesId): RedirectResponse
    {
        $eventGame = EventGame::find($eventGamesId);

        if (! $eventGame) {
            abort(404);
        }

        // Get approved squad registrations
        $approvedRegs = Registration::where('event_games_id', $eventGamesId)
            ->where('status', 'approved')
            ->get();

        $count = $approvedRegs->count();

        if ($count < 2) {
            return redirect()->back()->with('error', 'Need at least 2 approved squads to generate bracket.');
        }

        // Check if bracket already generated
        if (GameMatch::where('event_games_id', $eventGamesId)->exists()) {
            return redirect()->back()->with('error', 'Bracket has already been generated.');
        }

        $type = $eventGame->event->tournament_type->value ?? $eventGame->event->tournament_type;
        $squadsList = $approvedRegs->pluck('squad_id')->shuffle()->toArray();

        if ($type === 'single_elimination') {
            $this->generateSingleElimination($eventGamesId, $squadsList);
        } elseif ($type === 'double_elimination') {
            $this->generateDoubleElimination($eventGamesId, $squadsList);
        } elseif ($type === 'round_robin') {
            $this->generateRoundRobin($eventGamesId, $squadsList);
        } elseif ($type === 'swiss') {
            $this->generateSwiss($eventGamesId, $squadsList);
        } else {
            return redirect()->back()->with('error', 'Unsupported tournament type.');
        }

        // Update event status to ongoing
        $eventGame->event->update(['status' => EventStatus::Ongoing]);

        return redirect()->back()->with('success', 'Bracket generated and tournament started!');
    }

    /**
     * Generate Single Elimination.
     */
    private function generateSingleElimination(int $eventGamesId, array $squadsList): void
    {
        $count = count($squadsList);
        $rounds = ceil(log($count, 2));

        $bracketSize = pow(2, $rounds);
        while (count($squadsList) < $bracketSize) {
            $squadsList[] = null;
        }

        $matchNumber = 0;
        for ($i = 0; $i < $bracketSize; $i += 2) {
            $homeSquadId = $squadsList[$i];
            $awaySquadId = $squadsList[$i + 1];

            $winnerId = null;
            $status = MatchStatus::Scheduled;

            if ($homeSquadId !== null && $awaySquadId === null) {
                $winnerId = $homeSquadId;
                $status = MatchStatus::Completed;
            } elseif ($homeSquadId === null && $awaySquadId !== null) {
                $winnerId = $awaySquadId;
                $status = MatchStatus::Completed;
            }

            $this->matchRepository->create([
                'event_games_id' => $eventGamesId,
                'squad_home_id' => $homeSquadId,
                'squad_away_id' => $awaySquadId,
                'winner_id' => $winnerId,
                'round' => 1,
                'match_order' => $matchNumber,
                'status' => $status,
            ]);

            $matchNumber++;
        }

        $currentRoundMatches = $bracketSize / 2;
        for ($r = 2; $r <= $rounds; $r++) {
            $currentRoundMatches = $currentRoundMatches / 2;
            for ($m = 0; $m < $currentRoundMatches; $m++) {
                $this->matchRepository->create([
                    'event_games_id' => $eventGamesId,
                    'squad_home_id' => null,
                    'squad_away_id' => null,
                    'round' => $r,
                    'match_order' => $m,
                    'status' => MatchStatus::Scheduled,
                ]);
            }
        }

        $this->promoteWinnersToNextRound($eventGamesId, 1);
    }

    /**
     * Generate Double Elimination.
     */
    private function generateDoubleElimination(int $eventGamesId, array $squadsList): void
    {
        $count = count($squadsList);
        $rounds = ceil(log($count, 2));

        $bracketSize = pow(2, $rounds);
        while (count($squadsList) < $bracketSize) {
            $squadsList[] = null;
        }

        $matchNumber = 0;
        for ($i = 0; $i < $bracketSize; $i += 2) {
            $home = $squadsList[$i];
            $away = $squadsList[$i + 1];

            $winnerId = null;
            $status = MatchStatus::Scheduled;
            if ($home !== null && $away === null) {
                $winnerId = $home;
                $status = MatchStatus::Completed;
            } elseif ($home === null && $away !== null) {
                $winnerId = $away;
                $status = MatchStatus::Completed;
            }

            $this->matchRepository->create([
                'event_games_id' => $eventGamesId,
                'squad_home_id' => $home,
                'squad_away_id' => $away,
                'winner_id' => $winnerId,
                'round' => 1,
                'match_order' => $matchNumber,
                'status' => $status,
            ]);
            $matchNumber++;
        }

        $winnersRound2Matches = $bracketSize / 4;
        for ($m = 0; $m < $winnersRound2Matches; $m++) {
            $this->matchRepository->create([
                'event_games_id' => $eventGamesId,
                'squad_home_id' => null,
                'squad_away_id' => null,
                'round' => 2,
                'match_order' => $m,
                'status' => MatchStatus::Scheduled,
            ]);
        }

        $losersRound1Matches = $bracketSize / 4;
        for ($m = 0; $m < $losersRound1Matches; $m++) {
            $this->matchRepository->create([
                'event_games_id' => $eventGamesId,
                'squad_home_id' => null,
                'squad_away_id' => null,
                'round' => 101,
                'match_order' => $m,
                'status' => MatchStatus::Scheduled,
            ]);
        }
    }

    /**
     * Generate Round Robin.
     */
    private function generateRoundRobin(int $eventGamesId, array $squadsList): void
    {
        $count = count($squadsList);
        if ($count % 2 !== 0) {
            $squadsList[] = null;
            $count++;
        }

        $rounds = $count - 1;
        $matchesPerRound = $count / 2;

        foreach ($squadsList as $squadId) {
            if ($squadId !== null) {
                Standing::firstOrCreate([
                    'event_games_id' => $eventGamesId,
                    'squad_id' => $squadId,
                ], [
                    'wins' => 0,
                    'losses' => 0,
                    'draws' => 0,
                    'points' => 0,
                ]);
            }
        }

        for ($round = 1; $round <= $rounds; $round++) {
            for ($i = 0; $i < $matchesPerRound; $i++) {
                $home = $squadsList[$i];
                $away = $squadsList[$count - 1 - $i];

                $winnerId = null;
                $status = MatchStatus::Scheduled;
                if ($home !== null && $away === null) {
                    $winnerId = $home;
                    $status = MatchStatus::Completed;
                } elseif ($home === null && $away !== null) {
                    $winnerId = $away;
                    $status = MatchStatus::Completed;
                }

                if ($home !== null || $away !== null) {
                    $this->matchRepository->create([
                        'event_games_id' => $eventGamesId,
                        'squad_home_id' => $home,
                        'squad_away_id' => $away,
                        'winner_id' => $winnerId,
                        'round' => $round,
                        'match_order' => $i,
                        'status' => $status,
                    ]);
                }
            }
            $first = array_shift($squadsList);
            $last = array_pop($squadsList);
            array_unshift($squadsList, $last);
            array_unshift($squadsList, $first);
        }
    }

    /**
     * Generate Swiss.
     */
    private function generateSwiss(int $eventGamesId, array $squadsList): void
    {
        $count = count($squadsList);
        if ($count % 2 !== 0) {
            $squadsList[] = null;
            $count++;
        }

        $matchNumber = 0;
        for ($i = 0; $i < $count; $i += 2) {
            $home = $squadsList[$i];
            $away = $squadsList[$i + 1];

            $winnerId = null;
            $status = MatchStatus::Scheduled;
            if ($home !== null && $away === null) {
                $winnerId = $home;
                $status = MatchStatus::Completed;
            } elseif ($home === null && $away !== null) {
                $winnerId = $away;
                $status = MatchStatus::Completed;
            }

            $this->matchRepository->create([
                'event_games_id' => $eventGamesId,
                'squad_home_id' => $home,
                'squad_away_id' => $away,
                'winner_id' => $winnerId,
                'round' => 1,
                'match_order' => $matchNumber,
                'status' => $status,
            ]);
            $matchNumber++;
        }
    }

    /**
     * Input match score and update standings.
     */
    public function updateScore(Request $request, int $matchId): RedirectResponse
    {
        $validated = $request->validate([
            'squad_home_score' => 'required|integer|min:0',
            'squad_away_score' => 'required|integer|min:0',
        ]);

        $match = $this->matchRepository->find($matchId);

        if (! $match) {
            abort(404);
        }

        if ($match->eventGame->event->organizer->user_id !== auth()->id() && ! in_array(auth()->user()->role?->name, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized.');
        }

        if ($match->status === MatchStatus::Completed) {
            return redirect()->back()->with('error', 'Match is already completed.');
        }

        $homeScore = $validated['squad_home_score'];
        $awayScore = $validated['squad_away_score'];

        if ($homeScore === $awayScore) {
            return redirect()->back()->with('error', 'Draw score is not allowed in bracket matches.');
        }

        $winnerId = $homeScore > $awayScore ? $match->squad_home_id : $match->squad_away_id;

        $this->matchRepository->update($matchId, [
            'score_home' => $homeScore,
            'score_away' => $awayScore,
            'winner_id' => $winnerId,
            'status' => MatchStatus::Completed,
        ]);

        // Update standings if Round Robin
        $type = $match->eventGame->event->tournament_type->value ?? $match->eventGame->event->tournament_type;
        if ($type === 'round_robin') {
            $this->updateRoundRobinStandings($match);
        } else {
            // Promote winner to next round for Elimination brackets
            $this->promoteWinnersToNextRound($match->event_games_id, $match->round);
        }

        // Check if tournament division is fully finished (all matches completed)
        $totalMatches = GameMatch::where('event_games_id', $match->event_games_id)->count();
        $completedMatches = GameMatch::where('event_games_id', $match->event_games_id)->where('status', MatchStatus::Completed)->count();

        if ($totalMatches === $completedMatches) {
            $this->finalizeTournamentDivision($match->event_games_id);
        }

        return redirect()->back()->with('success', 'Score recorded successfully.');
    }

    /**
     * Private helper to promote winners.
     */
    private function promoteWinnersToNextRound(int $eventGamesId, int $currentRound): void
    {
        $matches = GameMatch::where('event_games_id', $eventGamesId)
            ->where('round', $currentRound)
            ->get();

        foreach ($matches as $match) {
            if ($match->status === MatchStatus::Completed && $match->winner_id !== null) {
                // Find next round match
                $nextRound = $currentRound + 1;
                $nextMatchNum = floor($match->match_order / 2);
                $isHome = ($match->match_order % 2) === 0;

                $nextMatch = GameMatch::where('event_games_id', $eventGamesId)
                    ->where('round', $nextRound)
                    ->where('match_order', $nextMatchNum)
                    ->first();

                if ($nextMatch) {
                    if ($isHome) {
                        $nextMatch->update(['squad_home_id' => $match->winner_id]);
                    } else {
                        $nextMatch->update(['squad_away_id' => $match->winner_id]);
                    }

                    // Auto-resolve if the next match now has a BYE (squad home exists, squad away is null, etc. but wait, let's keep it simple)
                }
            }
        }
    }

    /**
     * Private helper to finalize a tournament division and issue reward claims.
     */
    private function finalizeTournamentDivision(int $eventGamesId): void
    {
        $eventGame = EventGame::with('event')->find($eventGamesId);
        if (! $eventGame) {
            return;
        }

        // Get final match
        $finalMatch = GameMatch::where('event_games_id', $eventGamesId)
            ->orderByDesc('round')
            ->first();

        if (! $finalMatch || ! $finalMatch->winner_id) {
            return;
        }

        $winnerId = $finalMatch->winner_id;
        $runnerUpId = $finalMatch->winner_id === $finalMatch->squad_home_id
            ? $finalMatch->squad_away_id
            : $finalMatch->squad_home_id;

        // Get Champion rewards
        $rewards = Reward::where('event_games_id', $eventGamesId)->get();

        foreach ($rewards as $reward) {
            $targetUserId = null;
            $targetSquadId = null;
            if ($reward->tier === 1) {
                // Champion squad leader receives the payout
                $targetSquadId = $winnerId;
                $targetUserId = Squad::find($winnerId)?->team?->user_id;
            } elseif ($reward->tier === 2) {
                $targetSquadId = $runnerUpId;
                $targetUserId = Squad::find($runnerUpId)?->team?->user_id;
            }

            if ($targetUserId) {
                RewardClaim::create([
                    'reward_id' => $reward->id,
                    'amount' => $reward->prize_amount ?: 0,
                    'squad_id' => $targetSquadId,
                    'claimed_by_id' => $targetUserId,
                    'status' => 'pending',
                ]);
            }
        }

        // Mark event as completed
        $eventGame->event->update(['status' => EventStatus::Completed]);
    }

    /**
     * Update match schedule.
     */
    public function updateSchedule(Request $request, int $matchId): RedirectResponse
    {
        $match = $this->matchRepository->find($matchId);

        if (! $match) {
            abort(404);
        }

        if ($match->eventGame->event->organizer->user_id !== auth()->id() && ! in_array(auth()->user()->role?->name, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date',
        ]);

        $this->matchRepository->update($matchId, [
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        return redirect()->back()->with('success', 'Match schedule updated successfully.');
    }

    /**
     * Override/toggle match status.
     * Guard: changing status from completed back to live/scheduled is super-admin only.
     */
    public function toggleMatchStatus(Request $request, int $matchId): RedirectResponse
    {
        $match = $this->matchRepository->find($matchId);

        if (! $match) {
            abort(404);
        }

        if ($match->eventGame->event->organizer->user_id !== auth()->id() && ! in_array(auth()->user()->role?->name, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:scheduled,live,completed,cancelled',
        ]);

        $newStatus = $validated['status'];

        // Guard: Reverting from completed back to scheduled/live/cancelled is super-admin only
        if ($match->status->value === 'completed' && $newStatus !== 'completed') {
            if (auth()->user()->role?->name !== 'super-admin') {
                return redirect()->back()->with('error', 'Only a Super Admin can revert a completed match.');
            }
        }

        $this->matchRepository->update($matchId, [
            'status' => $newStatus,
        ]);

        return redirect()->back()->with('success', 'Match status updated successfully.');
    }

    /**
     * Private helper to update standings for Round Robin matches.
     */
    private function updateRoundRobinStandings($match): void
    {
        $homeId = $match->squad_home_id;
        $awayId = $match->squad_away_id;
        $winnerId = $match->winner_id;

        if ($homeId) {
            $standingHome = Standing::firstOrCreate([
                'event_games_id' => $match->event_games_id,
                'squad_id' => $homeId,
            ]);

            if ($winnerId === $homeId) {
                $standingHome->increment('wins');
                $standingHome->increment('points', 3);
            } else {
                $standingHome->increment('losses');
            }
        }

        if ($awayId) {
            $standingAway = Standing::firstOrCreate([
                'event_games_id' => $match->event_games_id,
                'squad_id' => $awayId,
            ]);

            if ($winnerId === $awayId) {
                $standingAway->increment('wins');
                $standingAway->increment('points', 3);
            } else {
                $standingAway->increment('losses');
            }
        }
    }
}
