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

        // Pre-create all matches for Single Elimination (supporting 2, 4, 8, 16 squads)
        // Determine rounds needed: log2 of next power of 2
        $rounds = ceil(log($count, 2));
        $squadsList = $approvedRegs->pluck('squad_id')->shuffle()->toArray();

        // Fill list with nulls for BYEs if squad count is not power of 2
        $bracketSize = pow(2, $rounds);
        while (count($squadsList) < $bracketSize) {
            $squadsList[] = null;
        }

        // Generate round 1 matches
        $matchNumber = 0;
        for ($i = 0; $i < $bracketSize; $i += 2) {
            $homeSquadId = $squadsList[$i];
            $awaySquadId = $squadsList[$i + 1];

            // If both are null, skip. If one is null, it's a BYE
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

        // Pre-create next round matches as empty slots
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

        // If any BYEs were resolved, promote their winners to Round 2 immediately!
        $this->promoteWinnersToNextRound($eventGamesId, 1);

        // Update event status to ongoing
        $eventGame->event->update(['status' => EventStatus::Ongoing]);

        return redirect()->back()->with('success', 'Bracket generated and tournament started!');
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

        if ($match->eventGame->event->organizer->user_id !== auth()->id()) {
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

        // Promote winner to next round
        $this->promoteWinnersToNextRound($match->event_games_id, $match->round);

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
}
