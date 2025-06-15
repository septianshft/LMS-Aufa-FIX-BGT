<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TalentScoutingSeeder extends Seeder
{
    /**
     * Seed talent scouting system data.
     * Clean seeder - no talents created, use TraineeSeeder for actual talent creation.
     */
    public function run(): void
    {
        $this->command->info('🎯 TalentScoutingSeeder: Clean seeder - no data created');
        $this->command->info('ℹ️  Use TraineeSeeder to create trainees with course completion data');
        $this->command->info('✅ Talent scouting seeder completed');
    }
}
