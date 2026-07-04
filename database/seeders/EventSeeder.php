<?php

namespace Database\Seeders;

use App\Enums\EventPaymentStatus;
use App\Enums\EventStatus;
use App\Enums\MatchStatus;
use App\Enums\RegistrationPaymentStatus;
use App\Enums\RegistrationStatus;
use App\Enums\RewardClaimStatus;
use App\Enums\RewardType;
use App\Enums\TournamentType;
use App\Models\Event;
use App\Models\EventGame;
use App\Models\EventPayment;
use App\Models\EventSponsor;
use App\Models\Game;
use App\Models\GameMatch as EloquentMatch;
use App\Models\Organizer;
use App\Models\Player;
use App\Models\Registration;
use App\Models\Reward;
use App\Models\RewardClaim;
use App\Models\Squad;
use App\Models\Standing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizer1 = Organizer::first();
        $organizer2 = Organizer::skip(1)->first();
        $organizer3 = Organizer::skip(2)->first();

        $gameMl = Game::where('name', 'Mobile Legends: Bang Bang')->first();
        $gamePubg = Game::where('name', 'PUBG Mobile')->first();
        $gameFootball = Game::where('name', 'Football')->first();

        $adminUser1 = User::where('username', 'admin1')->first();
        $adminUser2 = User::where('username', 'admin2')->first();

        // ----------------------------------------------------
        // EVENT 1: Completed Tournament (NEX-Sport MLBB Cup)
        // ----------------------------------------------------
        if ($organizer1 && $gameMl && $adminUser1) {
            $event1 = Event::firstOrCreate(
                ['name' => 'NEX-Sport MLBB Cup'],
                [
                    'description' => 'The ultimate Mobile Legends tournament organized by NEX-Sport.',
                    'banner' => 'banners/event_mlbb_cup.png',
                    'organizer_id' => $organizer1->id,
                    'tournament_type' => TournamentType::SingleElimination,
                    'location' => 'Online',
                    'start_date' => Carbon::now()->subMonth(),
                    'end_date' => Carbon::now()->subWeeks(3),
                    'registration_start' => Carbon::now()->subMonths(2),
                    'registration_end' => Carbon::now()->subMonth()->subDays(2),
                    'status' => EventStatus::Completed,
                ]
            );

            // Event Game MLBB mapping
            $eventGame1 = EventGame::firstOrCreate(
                ['event_id' => $event1->id, 'game_id' => $gameMl->id],
                [
                    'ticket_price' => 10000,
                    'max_participants' => 4,
                    'admin_ticket_fee' => 1000,
                ]
            );

            // Sponsors
            EventSponsor::firstOrCreate(
                ['event_id' => $event1->id, 'name' => 'Grab Indonesia'],
                [
                    'banner' => 'sponsors/grab.png',
                    'url' => 'https://grab.com',
                ]
            );
            EventSponsor::firstOrCreate(
                ['event_id' => $event1->id, 'name' => 'Telkomsel'],
                [
                    'banner' => 'sponsors/telkomsel.png',
                    'url' => 'https://telkomsel.com',
                ]
            );

            // Event Payment (Approved by Admin)
            EventPayment::firstOrCreate(
                ['event_id' => $event1->id],
                [
                    'amount' => 7500000, // 7.5 million reward pool
                    'service_fee' => 250000, // 250k service fee
                    'payment_receipt' => 'receipts/event1_pay.png',
                    'status' => EventPaymentStatus::Approved,
                    'verified_by_id' => $adminUser1->id,
                    'verified_at' => Carbon::now()->subMonth()->subDays(5),
                ]
            );

            // Rewards
            $reward1st = Reward::firstOrCreate(
                ['event_games_id' => $eventGame1->id, 'tier' => 1],
                [
                    'reward_type' => RewardType::Prize,
                    'title' => 'Juara 1',
                    'description' => 'Winner prize pool for NEX-Sport MLBB Cup.',
                    'prize_amount' => 5000000,
                ]
            );

            $reward2nd = Reward::firstOrCreate(
                ['event_games_id' => $eventGame1->id, 'tier' => 2],
                [
                    'reward_type' => RewardType::Prize,
                    'title' => 'Juara 2',
                    'description' => 'Runner-up prize pool for NEX-Sport MLBB Cup.',
                    'prize_amount' => 2000000,
                ]
            );

            $rewardMvp = Reward::firstOrCreate(
                ['event_games_id' => $eventGame1->id, 'tier' => 3],
                [
                    'reward_type' => RewardType::Prize,
                    'title' => 'MVP Player',
                    'description' => 'Most Valuable Player award.',
                    'prize_amount' => 500000,
                ]
            );

            // Registrations for Event 1
            $mlSquads = Squad::where('game_id', $gameMl->id)->take(4)->get();
            $squadIds = [];
            foreach ($mlSquads as $squad) {
                $squadIds[] = $squad->id;
                Registration::firstOrCreate(
                    ['squad_id' => $squad->id, 'event_games_id' => $eventGame1->id],
                    [
                        'status' => RegistrationStatus::Approved,
                        'payment_status' => RegistrationPaymentStatus::Paid,
                        'ticket_price' => 10000,
                        'admin_fee' => 1000,
                        'amount_paid' => 11000,
                        'payment_method' => 'GOPAY',
                        'payment_receipt' => 'receipts/reg_squad_'.$squad->id.'.png',
                        'paid_at' => Carbon::now()->subMonth()->subDays(10),
                        'registered_at' => Carbon::now()->subMonth()->subDays(10),
                    ]
                );
            }

            // Matches (Single Elimination Bracket)
            // Round 1: Semifinals
            // Match 1: RRQ Hoshi (squad 1) vs EVOS Glory (squad 2)
            $match1 = EloquentMatch::firstOrCreate(
                ['event_games_id' => $eventGame1->id, 'round' => 1, 'match_order' => 1],
                [
                    'squad_home_id' => $squadIds[0] ?? null,
                    'squad_away_id' => $squadIds[1] ?? null,
                    'score_home' => 2,
                    'score_away' => 1,
                    'winner_id' => $squadIds[0] ?? null,
                    'status' => MatchStatus::Completed,
                    'scheduled_at' => Carbon::now()->subWeeks(4),
                ]
            );

            // Match 2: ONIC Esports (squad 3) vs Alter Ego Ares (squad 4)
            $match2 = EloquentMatch::firstOrCreate(
                ['event_games_id' => $eventGame1->id, 'round' => 1, 'match_order' => 2],
                [
                    'squad_home_id' => $squadIds[2] ?? null,
                    'squad_away_id' => $squadIds[3] ?? null,
                    'score_home' => 2,
                    'score_away' => 0,
                    'winner_id' => $squadIds[2] ?? null,
                    'status' => MatchStatus::Completed,
                    'scheduled_at' => Carbon::now()->subWeeks(4),
                ]
            );

            // Round 2: Final
            // Match 3: RRQ Hoshi vs ONIC Esports
            $match3 = EloquentMatch::firstOrCreate(
                ['event_games_id' => $eventGame1->id, 'round' => 2, 'match_order' => 1],
                [
                    'squad_home_id' => $squadIds[0] ?? null,
                    'squad_away_id' => $squadIds[2] ?? null,
                    'score_home' => 3,
                    'score_away' => 2,
                    'winner_id' => $squadIds[0] ?? null,
                    'status' => MatchStatus::Completed,
                    'scheduled_at' => Carbon::now()->subWeeks(3)->subDays(2),
                ]
            );

            // Reward Claims (Penerimaan Hadiah)
            // Claim 1st Place (RRQ Hoshi) - Paid
            $playerRrq = Player::where('squad_id', $squadIds[0])->first();
            if ($playerRrq) {
                RewardClaim::firstOrCreate(
                    ['reward_id' => $reward1st->id],
                    [
                        'amount' => 5000000,
                        'squad_id' => $squadIds[0],
                        'player_id' => null,
                        'claimed_by_id' => $playerRrq->user_id,
                        'status' => RewardClaimStatus::Paid,
                        'payment_method' => 'BANK_TRANSFER',
                        'bank_name' => 'BCA',
                        'account_number' => '8982738271',
                        'account_name' => 'Manager RRQ Hoshi',
                        'payment_receipt' => 'payouts/claim_rrq.png',
                        'claimed_at' => Carbon::now()->subWeeks(3),
                        'paid_at' => Carbon::now()->subWeeks(3)->addDays(2),
                    ]
                );

                // Claim MVP (Player 1) - Processing
                RewardClaim::firstOrCreate(
                    ['reward_id' => $rewardMvp->id],
                    [
                        'amount' => 500000,
                        'squad_id' => null,
                        'player_id' => $playerRrq->id,
                        'claimed_by_id' => $playerRrq->user_id,
                        'status' => RewardClaimStatus::Processing,
                        'payment_method' => 'GOPAY',
                        'bank_name' => 'GOPAY',
                        'account_number' => '081234567890',
                        'account_name' => $playerRrq->name,
                        'claimed_at' => Carbon::now()->subWeeks(3),
                    ]
                );
            }

            // Claim 2nd Place (ONIC Esports) - Paid
            $playerOnic = Player::where('squad_id', $squadIds[2])->first();
            if ($playerOnic) {
                RewardClaim::firstOrCreate(
                    ['reward_id' => $reward2nd->id],
                    [
                        'amount' => 2000000,
                        'squad_id' => $squadIds[2],
                        'player_id' => null,
                        'claimed_by_id' => $playerOnic->user_id,
                        'status' => RewardClaimStatus::Paid,
                        'payment_method' => 'BANK_TRANSFER',
                        'bank_name' => 'Mandiri',
                        'account_number' => '123000987654',
                        'account_name' => 'ONIC Manager',
                        'payment_receipt' => 'payouts/claim_onic.png',
                        'claimed_at' => Carbon::now()->subWeeks(3),
                        'paid_at' => Carbon::now()->subWeeks(3)->addDays(2),
                    ]
                );
            }
        }

        // ----------------------------------------------------
        // EVENT 2: Ongoing / Live Tournament (NEX-Sport Football League)
        // ----------------------------------------------------
        if ($organizer3 && $gameFootball && $adminUser2) {
            $event2 = Event::firstOrCreate(
                ['name' => 'NEX-Sport Football League'],
                [
                    'description' => 'Exciting round robin league for professional football teams.',
                    'banner' => 'banners/event_football_league.png',
                    'organizer_id' => $organizer3->id,
                    'tournament_type' => TournamentType::RoundRobin,
                    'location' => 'NEX Stadium Jakarta',
                    'start_date' => Carbon::now()->subDays(2),
                    'end_date' => Carbon::now()->addDays(5),
                    'registration_start' => Carbon::now()->subWeeks(3),
                    'registration_end' => Carbon::now()->subDays(4),
                    'status' => EventStatus::Ongoing,
                ]
            );

            // Event Game Football mapping
            $eventGame2 = EventGame::firstOrCreate(
                ['event_id' => $event2->id, 'game_id' => $gameFootball->id],
                [
                    'ticket_price' => 25000,
                    'max_participants' => 4,
                    'admin_ticket_fee' => 2500,
                ]
            );

            // Sponsor
            EventSponsor::firstOrCreate(
                ['event_id' => $event2->id, 'name' => 'Gojek'],
                [
                    'banner' => 'sponsors/gojek.png',
                    'url' => 'https://gojek.com',
                ]
            );

            // Event Payment (Approved by Admin)
            EventPayment::firstOrCreate(
                ['event_id' => $event2->id],
                [
                    'amount' => 15000000, // 15 million reward pool
                    'service_fee' => 250000, // 250k service fee
                    'payment_receipt' => 'receipts/event2_pay.png',
                    'status' => EventPaymentStatus::Approved,
                    'verified_by_id' => $adminUser2->id,
                    'verified_at' => Carbon::now()->subDays(10),
                ]
            );

            // Football Squads
            $footballSquads = Squad::where('game_id', $gameFootball->id)->take(4)->get();
            $fbSquadIds = [];
            foreach ($footballSquads as $squad) {
                $fbSquadIds[] = $squad->id;
                Registration::firstOrCreate(
                    ['squad_id' => $squad->id, 'event_games_id' => $eventGame2->id],
                    [
                        'status' => RegistrationStatus::Approved,
                        'payment_status' => RegistrationPaymentStatus::Paid,
                        'ticket_price' => 25000,
                        'admin_fee' => 2500,
                        'amount_paid' => 27500,
                        'payment_method' => 'BANK_TRANSFER',
                        'payment_receipt' => 'receipts/reg_football_'.$squad->id.'.png',
                        'paid_at' => Carbon::now()->subDays(12),
                        'registered_at' => Carbon::now()->subDays(12),
                    ]
                );

                // Initialize Standings
                Standing::firstOrCreate(
                    ['event_games_id' => $eventGame2->id, 'squad_id' => $squad->id],
                    [
                        'wins' => 0,
                        'losses' => 0,
                        'draws' => 0,
                        'points' => 0,
                    ]
                );
            }

            // Matches (Round Robin)
            // Match 1 (Completed): RRQ FC vs EVOS FC
            EloquentMatch::firstOrCreate(
                ['event_games_id' => $eventGame2->id, 'round' => 1, 'match_order' => 1],
                [
                    'squad_home_id' => $fbSquadIds[0] ?? null,
                    'squad_away_id' => $fbSquadIds[1] ?? null,
                    'score_home' => 1,
                    'score_away' => 1,
                    'winner_id' => null,
                    'status' => MatchStatus::Completed,
                    'scheduled_at' => Carbon::now()->subDays(2),
                ]
            );

            // Update Standings for Match 1
            if (isset($fbSquadIds[0])) {
                Standing::where('event_games_id', $eventGame2->id)->where('squad_id', $fbSquadIds[0])
                    ->update(['draws' => 1, 'points' => 1]);
            }
            if (isset($fbSquadIds[1])) {
                Standing::where('event_games_id', $eventGame2->id)->where('squad_id', $fbSquadIds[1])
                    ->update(['draws' => 1, 'points' => 1]);
            }

            // Match 2 (Completed): ONIC FC vs AE FC
            EloquentMatch::firstOrCreate(
                ['event_games_id' => $eventGame2->id, 'round' => 1, 'match_order' => 2],
                [
                    'squad_home_id' => $fbSquadIds[2] ?? null,
                    'squad_away_id' => $fbSquadIds[3] ?? null,
                    'score_home' => 2,
                    'score_away' => 1,
                    'winner_id' => $fbSquadIds[2] ?? null,
                    'status' => MatchStatus::Completed,
                    'scheduled_at' => Carbon::now()->subDays(2),
                ]
            );

            // Update Standings for Match 2
            if (isset($fbSquadIds[2])) {
                Standing::where('event_games_id', $eventGame2->id)->where('squad_id', $fbSquadIds[2])
                    ->update(['wins' => 1, 'points' => 3]);
            }
            if (isset($fbSquadIds[3])) {
                Standing::where('event_games_id', $eventGame2->id)->where('squad_id', $fbSquadIds[3])
                    ->update(['losses' => 1, 'points' => 0]);
            }

            // Match 3 (Live): RRQ FC vs ONIC FC
            EloquentMatch::firstOrCreate(
                ['event_games_id' => $eventGame2->id, 'round' => 2, 'match_order' => 1],
                [
                    'squad_home_id' => $fbSquadIds[0] ?? null,
                    'squad_away_id' => $fbSquadIds[2] ?? null,
                    'score_home' => 0,
                    'score_away' => 0,
                    'winner_id' => null,
                    'status' => MatchStatus::Live,
                    'scheduled_at' => Carbon::now(),
                ]
            );

            // Match 4 (Scheduled): EVOS FC vs AE FC
            EloquentMatch::firstOrCreate(
                ['event_games_id' => $eventGame2->id, 'round' => 2, 'match_order' => 2],
                [
                    'squad_home_id' => $fbSquadIds[1] ?? null,
                    'squad_away_id' => $fbSquadIds[3] ?? null,
                    'score_home' => 0,
                    'score_away' => 0,
                    'winner_id' => null,
                    'status' => MatchStatus::Scheduled,
                    'scheduled_at' => Carbon::now()->addDay(),
                ]
            );
        }

        // ----------------------------------------------------
        // EVENT 3: Registration Phase (NEX-Sport PUBGM Showdown)
        // ----------------------------------------------------
        if ($organizer2 && $gamePubg) {
            $event3 = Event::firstOrCreate(
                ['name' => 'NEX-Sport PUBGM Showdown'],
                [
                    'description' => 'The premier PUBG Mobile showdown for tactical squads.',
                    'banner' => 'banners/event_pubgm_showdown.png',
                    'organizer_id' => $organizer2->id,
                    'tournament_type' => TournamentType::Swiss,
                    'location' => 'Online',
                    'start_date' => Carbon::now()->addDays(10),
                    'end_date' => Carbon::now()->addDays(12),
                    'registration_start' => Carbon::now()->subDays(5),
                    'registration_end' => Carbon::now()->addDays(5),
                    'status' => EventStatus::Registration,
                ]
            );

            // Event Game PUBGM mapping
            $eventGame3 = EventGame::firstOrCreate(
                ['event_id' => $event3->id, 'game_id' => $gamePubg->id],
                [
                    'ticket_price' => 50000,
                    'max_participants' => 8,
                    'admin_ticket_fee' => 5000,
                ]
            );

            // Event Payment (Approved by Admin)
            EventPayment::firstOrCreate(
                ['event_id' => $event3->id],
                [
                    'amount' => 10000000,
                    'service_fee' => 250000,
                    'payment_receipt' => 'receipts/event3_pay.png',
                    'status' => EventPaymentStatus::Approved,
                    'verified_at' => Carbon::now()->subDays(4),
                ]
            );

            // Registrations
            $pubgSquads = Squad::where('game_id', $gamePubg->id)->take(4)->get();
            $pbSquadIds = [];
            foreach ($pubgSquads as $index => $squad) {
                $pbSquadIds[] = $squad->id;
                // Seed different registration states
                if ($index == 0 || $index == 1) {
                    Registration::firstOrCreate(
                        ['squad_id' => $squad->id, 'event_games_id' => $eventGame3->id],
                        [
                            'status' => RegistrationStatus::Approved,
                            'payment_status' => RegistrationPaymentStatus::Paid,
                            'ticket_price' => 50000,
                            'admin_fee' => 5000,
                            'amount_paid' => 55000,
                            'payment_method' => 'BANK_TRANSFER',
                            'payment_receipt' => 'receipts/reg_pubg_'.$squad->id.'.png',
                            'paid_at' => Carbon::now()->subDays(2),
                            'registered_at' => Carbon::now()->subDays(2),
                        ]
                    );
                } elseif ($index == 2) {
                    // Pending unpaid registration
                    Registration::firstOrCreate(
                        ['squad_id' => $squad->id, 'event_games_id' => $eventGame3->id],
                        [
                            'status' => RegistrationStatus::Pending,
                            'payment_status' => RegistrationPaymentStatus::Unpaid,
                            'ticket_price' => 50000,
                            'admin_fee' => 5000,
                            'amount_paid' => 0,
                            'registered_at' => Carbon::now()->subDay(),
                        ]
                    );
                } else {
                    // Rejected & refunded
                    Registration::firstOrCreate(
                        ['squad_id' => $squad->id, 'event_games_id' => $eventGame3->id],
                        [
                            'status' => RegistrationStatus::Rejected,
                            'payment_status' => RegistrationPaymentStatus::Refunded,
                            'ticket_price' => 50000,
                            'admin_fee' => 5000,
                            'amount_paid' => 55000,
                            'payment_receipt' => 'receipts/reg_pubg_ref.png',
                            'refund_receipt' => 'refunds/reg_pubg_ref_done.png',
                            'paid_at' => Carbon::now()->subDays(3),
                            'refunded_at' => Carbon::now()->subDay(),
                            'registered_at' => Carbon::now()->subDays(3),
                        ]
                    );
                }
            }
        }

        // ----------------------------------------------------
        // EVENT 4: Waiting Verification Phase (Community MLBB Cup)
        // ----------------------------------------------------
        if ($organizer1 && $gameMl) {
            $event4 = Event::firstOrCreate(
                ['name' => 'Community MLBB Open'],
                [
                    'description' => 'A free community cup to discover fresh MLBB talent.',
                    'banner' => 'banners/event_community_mlbb.png',
                    'organizer_id' => $organizer1->id,
                    'tournament_type' => TournamentType::SingleElimination,
                    'location' => 'Online',
                    'start_date' => Carbon::now()->addDays(20),
                    'end_date' => Carbon::now()->addDays(22),
                    'registration_start' => Carbon::now()->addDays(1),
                    'registration_end' => Carbon::now()->addDays(15),
                    'status' => EventStatus::WaitingVerification,
                ]
            );

            // Event Game MLBB mapping
            EventGame::firstOrCreate(
                ['event_id' => $event4->id, 'game_id' => $gameMl->id],
                [
                    'ticket_price' => 0, // Free
                    'max_participants' => 16,
                    'admin_ticket_fee' => 0,
                ]
            );

            // Event Payment (Pending Admin approval)
            EventPayment::firstOrCreate(
                ['event_id' => $event4->id],
                [
                    'amount' => 1000000, // 1 million reward pool
                    'service_fee' => 100000, // 100k service fee
                    'payment_receipt' => 'receipts/event4_pay_pending.png',
                    'status' => EventPaymentStatus::Pending,
                ]
            );
        }

        // ----------------------------------------------------
        // EVENT 5: Draft Phase (New Tournament Draft)
        // ----------------------------------------------------
        if ($organizer2 && $gameMl) {
            Event::firstOrCreate(
                ['name' => 'Organizer 2 MLBB Tournament'],
                [
                    'description' => 'Draft description for an upcoming MLBB cup.',
                    'banner' => null,
                    'organizer_id' => $organizer2->id,
                    'tournament_type' => TournamentType::SingleElimination,
                    'location' => 'Online',
                    'start_date' => Carbon::now()->addDays(30),
                    'end_date' => Carbon::now()->addDays(31),
                    'registration_start' => Carbon::now()->addDays(5),
                    'registration_end' => Carbon::now()->addDays(25),
                    'status' => EventStatus::Draft,
                ]
            );
        }
    }
}
