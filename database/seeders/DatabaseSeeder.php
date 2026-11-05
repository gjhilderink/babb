<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Idempotent: veilig om bij elke deploy opnieuw te draaien.
 * Bestaande records (op e-mail / naam) worden niet aangepast.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@babb.nl'],
            [
                'name'     => 'Beheerder',
                'password' => Hash::make('changeme'),
                'role'     => 'admin',
            ]
        );

        $types = [
            [
                'name'           => 'Basis',
                'description'    => 'Standaard lidmaatschap',
                'price_per_year' => 250.00,
                'max_members'    => null,
                'benefits'       => ['Toegang tot netwerkevenementen', 'Nieuwsbrief'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Premium',
                'description'    => 'Uitgebreid lidmaatschap met extra voordelen',
                'price_per_year' => 750.00,
                'max_members'    => null,
                'benefits'       => ['Toegang tot alle evenementen', 'Nieuwsbrief', 'Adverteren in clubmagazine', 'Gratis deelname aan workshops'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Corporate',
                'description'    => 'Bedrijfslidmaatschap voor meerdere medewerkers',
                'price_per_year' => 2000.00,
                'max_members'    => 5,
                'benefits'       => ['Alles van Premium', 'Tot 5 medewerkers', 'Prominente vermelding op website', 'Sponsormogelijkheden'],
                'is_active'      => true,
            ],
        ];

        foreach ($types as $type) {
            MembershipType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
