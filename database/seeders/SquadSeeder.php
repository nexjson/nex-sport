<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Squad;
use App\Models\Team;
use Illuminate\Database\Seeder;

class SquadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamRrq = Team::where('short_name', 'RRQ')->first();
        $teamEvos = Team::where('short_name', 'EVOS')->first();
        $teamOnic = Team::where('short_name', 'ONIC')->first();
        $teamAe = Team::where('short_name', 'AE')->first();

        $gameMl = Game::where('name', 'Mobile Legends: Bang Bang')->first();
        $gamePubg = Game::where('name', 'PUBG Mobile')->first();
        $gameFootball = Game::where('name', 'Football')->first();

        // --- RRQ Squads ---
        if ($teamRrq) {
            Squad::firstOrCreate(
                ['team_id' => $teamRrq->id, 'game_id' => $gameMl->id],
                [
                    'name' => 'RRQ Hoshi',
                    'short_name' => 'RRQ Hoshi',
                    'logo' => 'logos/squad_rrq_hoshi.png',
                    'max_players' => 6,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamRrq->id, 'game_id' => $gamePubg->id],
                [
                    'name' => 'RRQ Ryu',
                    'short_name' => 'RRQ Ryu',
                    'logo' => 'logos/squad_rrq_ryu.png',
                    'max_players' => 5,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamRrq->id, 'game_id' => $gameFootball->id],
                [
                    'name' => 'RRQ FC',
                    'short_name' => 'RRQ FC',
                    'logo' => 'logos/squad_rrq_fc.png',
                    'max_players' => 18,
                    'status' => 'active',
                ]
            );
        }

        // --- EVOS Squads ---
        if ($teamEvos) {
            Squad::firstOrCreate(
                ['team_id' => $teamEvos->id, 'game_id' => $gameMl->id],
                [
                    'name' => 'EVOS Glory',
                    'short_name' => 'EVOS Glory',
                    'logo' => 'logos/squad_evos_glory.png',
                    'max_players' => 6,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamEvos->id, 'game_id' => $gamePubg->id],
                [
                    'name' => 'EVOS Reborn',
                    'short_name' => 'EVOS Reborn',
                    'logo' => 'logos/squad_evos_reborn.png',
                    'max_players' => 5,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamEvos->id, 'game_id' => $gameFootball->id],
                [
                    'name' => 'EVOS FC',
                    'short_name' => 'EVOS FC',
                    'logo' => 'logos/squad_evos_fc.png',
                    'max_players' => 18,
                    'status' => 'active',
                ]
            );
        }

        // --- ONIC Squads ---
        if ($teamOnic) {
            Squad::firstOrCreate(
                ['team_id' => $teamOnic->id, 'game_id' => $gameMl->id],
                [
                    'name' => 'ONIC Esports',
                    'short_name' => 'ONIC',
                    'logo' => 'logos/squad_onic_ml.png',
                    'max_players' => 6,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamOnic->id, 'game_id' => $gamePubg->id],
                [
                    'name' => 'ONIC Olympus',
                    'short_name' => 'ONIC Olympus',
                    'logo' => 'logos/squad_onic_olympus.png',
                    'max_players' => 5,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamOnic->id, 'game_id' => $gameFootball->id],
                [
                    'name' => 'ONIC FC',
                    'short_name' => 'ONIC FC',
                    'logo' => 'logos/squad_onic_fc.png',
                    'max_players' => 18,
                    'status' => 'active',
                ]
            );
        }

        // --- Alter Ego Squads ---
        if ($teamAe) {
            Squad::firstOrCreate(
                ['team_id' => $teamAe->id, 'game_id' => $gameMl->id],
                [
                    'name' => 'Alter Ego Ares',
                    'short_name' => 'AE Ares',
                    'logo' => 'logos/squad_ae_ares.png',
                    'max_players' => 6,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamAe->id, 'game_id' => $gamePubg->id],
                [
                    'name' => 'Alter Ego Ares PUBGM',
                    'short_name' => 'AE Ares PUBGM',
                    'logo' => 'logos/squad_ae_pubg.png',
                    'max_players' => 5,
                    'status' => 'active',
                ]
            );

            Squad::firstOrCreate(
                ['team_id' => $teamAe->id, 'game_id' => $gameFootball->id],
                [
                    'name' => 'Alter Ego FC',
                    'short_name' => 'AE FC',
                    'logo' => 'logos/squad_ae_fc.png',
                    'max_players' => 18,
                    'status' => 'active',
                ]
            );
        }
    }
}
