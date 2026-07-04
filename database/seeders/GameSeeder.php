<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameRole;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Mobile Legends (esport)
        $ml = Game::firstOrCreate(
            ['name' => 'Mobile Legends: Bang Bang'],
            [
                'category' => 'esport',
                'status' => true,
            ]
        );

        $mlRoles = ['Roamer', 'Jungler', 'Midlaner', 'Goldlaner', 'EXPlaner'];
        foreach ($mlRoles as $role) {
            GameRole::firstOrCreate(
                ['game_id' => $ml->id, 'name' => $role],
                ['description' => "Role {$role} in Mobile Legends"]
            );
        }

        // 2. PUBG Mobile (esport)
        $pubg = Game::firstOrCreate(
            ['name' => 'PUBG Mobile'],
            [
                'category' => 'esport',
                'status' => true,
            ]
        );

        $pubgRoles = ['IGL', 'Rusher', 'Sniper', 'Support'];
        foreach ($pubgRoles as $role) {
            GameRole::firstOrCreate(
                ['game_id' => $pubg->id, 'name' => $role],
                ['description' => "Role {$role} in PUBG Mobile"]
            );
        }

        // 3. Football (sport)
        $football = Game::firstOrCreate(
            ['name' => 'Football'],
            [
                'category' => 'sport',
                'status' => true,
            ]
        );

        $footballRoles = ['Goalkeeper', 'Defender', 'Midfielder', 'Forward'];
        foreach ($footballRoles as $role) {
            GameRole::firstOrCreate(
                ['game_id' => $football->id, 'name' => $role],
                ['description' => "Role {$role} in Football"]
            );
        }

        // 4. Volleyball (sport, inactive)
        $volleyball = Game::firstOrCreate(
            ['name' => 'Volleyball'],
            [
                'category' => 'sport',
                'status' => false,
            ]
        );

        $volleyballRoles = ['Setter', 'Outside Hitter', 'Opposite Hitter', 'Middle Blocker', 'Libero'];
        foreach ($volleyballRoles as $role) {
            GameRole::firstOrCreate(
                ['game_id' => $volleyball->id, 'name' => $role],
                ['description' => "Role {$role} in Volleyball"]
            );
        }
    }
}
