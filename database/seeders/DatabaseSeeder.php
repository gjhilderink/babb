<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        MembershipType::insert([
            [
                'name'           => 'Basis',
                'description'    => 'Standaard lidmaatschap',
                'price_per_year' => 250.00,
                'benefits'       => json_encode(['Toegang tot netwerkevenementen', 'Nieuwsbrief']),
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Premium',
                'description'    => 'Uitgebreid lidmaatschap met extra voordelen',
                'price_per_year' => 750.00,
                'benefits'       => json_encode(['Toegang tot alle evenementen', 'Nieuwsbrief', 'Adverteren in clubmagazine', 'Gratis deelname aan workshops']),
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Corporate',
                'description'    => 'Bedrijfslidmaatschap voor meerdere medewerkers',
                'price_per_year' => 2000.00,
                'max_members'    => 5,
                'benefits'       => json_encode(['Alles van Premium', 'Tot 5 medewerkers', 'Prominente vermelding op website', 'Sponsormogelijkheden']),
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}
