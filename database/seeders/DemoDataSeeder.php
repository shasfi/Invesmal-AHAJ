<?php

namespace Database\Seeders;

use App\Models\Startup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create a student founder
        $founder = User::updateOrCreate(
            ['email' => 'founder@invesmal.com'],
            [
                'name' => 'Alex Founder',
                'password' => Hash::make('password'),
                'role' => 'student_founder',
                'is_verified' => true,
                'university' => 'Stanford University',
                'bio' => 'Computer Science student passionate about AI and sustainability.',
            ]
        );

        // Create an investor
        $investor = User::updateOrCreate(
            ['email' => 'investor@invesmal.com'],
            [
                'name' => 'Sarah Chen',
                'password' => Hash::make('password'),
                'role' => 'investor',
                'is_verified' => true,
                'bio' => 'Early-stage investor focused on edtech and climate startups.',
            ]
        );

        // Create a mentor
        $mentor = User::updateOrCreate(
            ['email' => 'mentor@invesmal.com'],
            [
                'name' => 'Dr. James Wilson',
                'password' => Hash::make('password'),
                'role' => 'mentor',
                'is_verified' => true,
                'bio' => 'Former CTO at TechCorp. Advising student founders on product strategy.',
            ]
        );

        // Create another founder with startups
        $founder2 = User::updateOrCreate(
            ['email' => 'maria@invesmal.com'],
            [
                'name' => 'Maria Garcia',
                'password' => Hash::make('password'),
                'role' => 'student_founder',
                'is_verified' => false,
                'university' => 'MIT',
                'bio' => 'Building the future of healthcare.',
            ]
        );

        // Create startups for Alex
        Startup::updateOrCreate(
            ['name' => 'EcoTrack'],
            [
                'founder_id' => $founder->id,
                'description' => 'AI-powered carbon footprint tracking for university campuses.',
                'industry' => 'Climate Tech',
                'stage' => 'mvp',
                'website' => 'https://ecotrack.example.com',
                'team_size' => 4,
            ]
        );

        Startup::updateOrCreate(
            ['name' => 'StudyBuddy'],
            [
                'founder_id' => $founder->id,
                'description' => 'Peer-to-peer tutoring platform connecting students across campuses.',
                'industry' => 'EdTech',
                'stage' => 'idea',
                'website' => null,
                'team_size' => 2,
            ]
        );

        // Create startups for Maria
        Startup::updateOrCreate(
            ['name' => 'MediConnect'],
            [
                'founder_id' => $founder2->id,
                'description' => 'Telemedicine platform for rural communities.',
                'industry' => 'HealthTech',
                'stage' => 'funded',
                'website' => 'https://mediconnect.example.com',
                'team_size' => 8,
            ]
        );

        Startup::updateOrCreate(
            ['name' => 'FarmSync'],
            [
                'founder_id' => $founder2->id,
                'description' => 'IoT sensors for precision agriculture.',
                'industry' => 'AgTech',
                'stage' => 'mvp',
                'website' => null,
                'team_size' => 3,
            ]
        );

        Startup::updateOrCreate(
            ['name' => 'CodeCraft'],
            [
                'founder_id' => $founder2->id,
                'description' => 'AI pair-programming assistant for CS students.',
                'industry' => 'EdTech',
                'stage' => 'idea',
                'website' => null,
                'team_size' => 1,
            ]
        );
    }
}
