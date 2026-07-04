<?php

namespace Database\Seeders;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizers = [
            [
                'email' => 'organizer1@nexsport.com',
                'name' => 'NEX Arena Organizer',
                'logo' => 'logos/organizer_nex.png',
                'description' => 'Official Tournament Organizer by NEX-Sport Arena.',
            ],
            [
                'email' => 'organizer2@nexsport.com',
                'name' => 'Ligagame Esports',
                'logo' => 'logos/organizer_ligagame.png',
                'description' => 'Pioneering Esports Organizer in Indonesia.',
            ],
            [
                'email' => 'organizer3@nexsport.com',
                'name' => 'PSSI DKI Jakarta',
                'logo' => 'logos/organizer_pssi.png',
                'description' => 'Persatuan Sepakbola Seluruh Indonesia regional DKI Jakarta.',
            ],
        ];

        foreach ($organizers as $orgData) {
            $user = User::where('email', $orgData['email'])->first();
            if ($user) {
                Organizer::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $orgData['name'],
                        'logo' => $orgData['logo'],
                        'description' => $orgData['description'],
                        'status' => true,
                    ]
                );
            }
        }
    }
}
