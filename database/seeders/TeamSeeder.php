<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'email' => 'player1@nexsport.com',
                'name' => 'Rex Regum Qeon',
                'short_name' => 'RRQ',
                'logo' => 'logos/team_rrq.png',
                'description' => 'Rex Regum Qeon (RRQ) is a prominent esports organization in Indonesia.',
            ],
            [
                'email' => 'player2@nexsport.com',
                'name' => 'EVOS Esports',
                'short_name' => 'EVOS',
                'logo' => 'logos/team_evos.png',
                'description' => 'EVOS Esports, formerly known as Zero Latitude, is a professional esports organization.',
            ],
            [
                'email' => 'player3@nexsport.com',
                'name' => 'ONIC Esports',
                'short_name' => 'ONIC',
                'logo' => 'logos/team_onic.png',
                'description' => 'ONIC Esports is a Southeast Asian professional esports organization.',
            ],
            [
                'email' => 'player4@nexsport.com',
                'name' => 'Alter Ego',
                'short_name' => 'AE',
                'logo' => 'logos/team_ae.png',
                'description' => 'Alter Ego is an esports organization in Indonesia, known for their competitive teams.',
            ],
        ];

        foreach ($teams as $teamData) {
            $user = User::where('email', $teamData['email'])->first();
            if ($user) {
                Team::firstOrCreate(
                    ['name' => $teamData['name']],
                    [
                        'short_name' => $teamData['short_name'],
                        'logo' => $teamData['logo'],
                        'description' => $teamData['description'],
                        'user_id' => $user->id,
                        'status' => true,
                    ]
                );
            }
        }
    }
}
