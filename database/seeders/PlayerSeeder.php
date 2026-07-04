<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameRole;
use App\Models\Player;
use App\Models\Squad;
use App\Models\SquadRequest;
use App\Models\TransferHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gameMl = Game::where('name', 'Mobile Legends: Bang Bang')->first();
        $mlRoles = GameRole::where('game_id', $gameMl->id)->get();

        // 1. Assign player profiles and put them in MLBB squads
        // RRQ Hoshi MLBB (Players 1-5)
        $squadRrq = Squad::where('name', 'RRQ Hoshi')->first();
        for ($i = 1; $i <= 5; $i++) {
            $user = User::where('username', "player{$i}")->first();
            if ($user && $squadRrq) {
                Player::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'nickname' => 'RRQ '.ucfirst($user->username),
                        'photo' => "photos/player_{$user->username}.png",
                        'game_role_id' => $mlRoles[$i - 1]->id,
                        'squad_id' => $squadRrq->id,
                        'game_id' => $gameMl->id,
                        'jersey_number' => $i * 7,
                    ]
                );
            }
        }

        // EVOS Glory MLBB (Players 6-10)
        $squadEvos = Squad::where('name', 'EVOS Glory')->first();
        for ($i = 6; $i <= 10; $i++) {
            $user = User::where('username', "player{$i}")->first();
            if ($user && $squadEvos) {
                Player::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'nickname' => 'EVOS '.ucfirst($user->username),
                        'photo' => "photos/player_{$user->username}.png",
                        'game_role_id' => $mlRoles[$i - 6]->id,
                        'squad_id' => $squadEvos->id,
                        'game_id' => $gameMl->id,
                        'jersey_number' => $i * 3,
                    ]
                );
            }
        }

        // ONIC Esports MLBB (Players 11-15)
        $squadOnic = Squad::where('name', 'ONIC Esports')->first();
        for ($i = 11; $i <= 15; $i++) {
            $user = User::where('username', "player{$i}")->first();
            if ($user && $squadOnic) {
                Player::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'nickname' => 'ONIC '.ucfirst($user->username),
                        'photo' => "photos/player_{$user->username}.png",
                        'game_role_id' => $mlRoles[$i - 11]->id,
                        'squad_id' => $squadOnic->id,
                        'game_id' => $gameMl->id,
                        'jersey_number' => $i * 2,
                    ]
                );
            }
        }

        // Alter Ego Ares MLBB (Players 16-19)
        $squadAe = Squad::where('name', 'Alter Ego Ares')->first();
        for ($i = 16; $i <= 19; $i++) {
            $user = User::where('username', "player{$i}")->first();
            if ($user && $squadAe) {
                Player::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'nickname' => 'AE '.ucfirst($user->username),
                        'photo' => "photos/player_{$user->username}.png",
                        'game_role_id' => $mlRoles[$i - 16]->id,
                        'squad_id' => $squadAe->id,
                        'game_id' => $gameMl->id,
                        'jersey_number' => $i + 10,
                    ]
                );
            }
        }

        // Player 20 is a free agent
        $user20 = User::where('username', 'player20')->first();
        $freeAgent = null;
        if ($user20) {
            $freeAgent = Player::firstOrCreate(
                ['user_id' => $user20->id],
                [
                    'name' => $user20->name,
                    'nickname' => 'FA '.ucfirst($user20->username),
                    'photo' => "photos/player_{$user20->username}.png",
                    'game_role_id' => $mlRoles[1]->id, // Jungler
                    'squad_id' => null,
                    'game_id' => $gameMl->id,
                    'jersey_number' => 99,
                ]
            );
        }

        // 2. Transfer Histories
        if ($freeAgent && $squadAe) {
            TransferHistory::create([
                'player_id' => $freeAgent->id,
                'from_squad_id' => $squadAe->id,
                'to_squad_id' => null,
                'transfer_type' => 'release',
                'transfer_fee' => null,
                'transfer_date' => now()->subMonth(),
            ]);
        }

        $player1 = Player::where('nickname', 'RRQ Player1')->first();
        if ($player1 && $squadRrq) {
            TransferHistory::create([
                'player_id' => $player1->id,
                'from_squad_id' => null,
                'to_squad_id' => $squadRrq->id,
                'transfer_type' => 'join',
                'transfer_fee' => null,
                'transfer_date' => now()->subMonths(2),
            ]);
        }

        // 3. Squad Requests
        if ($freeAgent && $squadRrq) {
            SquadRequest::create([
                'squad_id' => $squadRrq->id,
                'player_id' => $freeAgent->id,
                'type' => 'apply',
                'status' => 'pending',
                'notes' => 'I would love to try out for the Jungler position!',
            ]);
        }

        $player19 = Player::where('nickname', 'AE Player19')->first();
        if ($player19 && $squadAe) {
            SquadRequest::create([
                'squad_id' => $squadAe->id,
                'player_id' => $player19->id,
                'type' => 'invite',
                'status' => 'approved',
                'notes' => 'Invited to join the main roster.',
            ]);
        }
    }
}
