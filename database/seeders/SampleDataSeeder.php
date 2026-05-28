<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Startup;
use App\Models\Investment;
use App\Models\Meeting;
use App\Models\Document;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Users ----
        $admin = User::firstOrCreate(['email' => 'admin@invesmal.com'], [
            'name' => 'Admin User', 'password' => Hash::make('password'), 'role' => 'admin', 'is_verified' => true,
        ]);

        $founder1 = User::firstOrCreate(['email' => 'ahmed@invesmal.com'], [
            'name' => 'Ahmed Khan', 'password' => Hash::make('password'), 'role' => 'student_founder', 'is_verified' => true,
            'bio' => 'Computer Science student building the next big thing.', 'university' => 'NUST',
        ]);

        $founder2 = User::firstOrCreate(['email' => 'fatima@invesmal.com'], [
            'name' => 'Fatima Ali', 'password' => Hash::make('password'), 'role' => 'student_founder', 'is_verified' => true,
            'bio' => 'Business student passionate about sustainable tech.', 'university' => 'LUMS',
        ]);

        $investor1 = User::firstOrCreate(['email' => 'zain@invesmal.com'], [
            'name' => 'Zain Hassan', 'password' => Hash::make('password'), 'role' => 'investor', 'is_verified' => true,
            'bio' => 'Angel investor focused on early-stage Pakistani startups.',
        ]);

        $investor2 = User::firstOrCreate(['email' => 'sara@invesmal.com'], [
            'name' => 'Sara Malik', 'password' => Hash::make('password'), 'role' => 'investor', 'is_verified' => true,
            'bio' => 'VC associate looking for high-growth opportunities.',
        ]);

        $mentor = User::firstOrCreate(['email' => 'bilal@invesmal.com'], [
            'name' => 'Dr. Bilal', 'password' => Hash::make('password'), 'role' => 'mentor', 'is_verified' => true,
            'bio' => 'Professor of Entrepreneurship with 15 years industry experience.',
        ]);

        // ---- Startups ----
        $startup1 = Startup::create([
            'founder_id' => $founder1->id, 'name' => 'EcoCharge',
            'description' => 'EV battery swapping stations for electric rickshaws in Pakistan.',
            'stage' => 'mvp', 'industry' => 'CleanTech',
            'funding_goal' => 50000, 'amount_raised' => 15000,
            'team_size' => 3, 'is_verified' => true,
        ]);

        $startup2 = Startup::create([
            'founder_id' => $founder2->id, 'name' => 'FarmLink',
            'description' => 'Connecting small farmers directly to buyers via mobile app.',
            'stage' => 'idea', 'industry' => 'AgriTech',
            'funding_goal' => 30000, 'amount_raised' => 0,
            'team_size' => 2, 'is_verified' => true,
        ]);

        $startup3 = Startup::create([
            'founder_id' => $founder1->id, 'name' => 'MediConnect',
            'description' => 'Telemedicine platform for rural healthcare access.',
            'stage' => 'idea', 'industry' => 'HealthTech',
            'funding_goal' => 75000, 'amount_raised' => 0,
            'team_size' => 4, 'is_verified' => false,
        ]);

        $startup4 = Startup::create([
            'founder_id' => $founder2->id, 'name' => 'EduBridge',
            'description' => 'AI-powered personalized learning platform for O/A Level students.',
            'stage' => 'funded', 'industry' => 'EdTech',
            'funding_goal' => 100000, 'amount_raised' => 80000,
            'team_size' => 5, 'is_verified' => true,
        ]);

        // ---- Investments ----
        Investment::create([
            'investor_id' => $investor1->id, 'startup_id' => $startup1->id,
            'amount' => 15000, 'status' => 'approved', 'message' => 'Excited to support green mobility!',
        ]);

        Investment::create([
            'investor_id' => $investor2->id, 'startup_id' => $startup4->id,
            'amount' => 30000, 'status' => 'pending', 'message' => 'Would like to discuss further.',
        ]);

        Investment::create([
            'investor_id' => $investor2->id, 'startup_id' => $startup1->id,
            'amount' => 5000, 'status' => 'pending', 'message' => 'Interested in small stake.',
        ]);

        // ---- Meetings ----
        Meeting::create([
            'scheduler_id' => $founder1->id, 'invitee_id' => $investor1->id,
            'startup_id' => $startup1->id, 'title' => 'EcoCharge Pitch Discussion',
            'notes' => 'Discussing funding terms and roadmap.',
            'scheduled_at' => now()->addDays(3), 'status' => 'accepted', 'location' => 'Zoom',
        ]);

        Meeting::create([
            'scheduler_id' => $investor2->id, 'invitee_id' => $founder2->id,
            'startup_id' => $startup4->id, 'title' => 'EduBridge Investment Meeting',
            'scheduled_at' => now()->addDays(5), 'status' => 'pending', 'location' => 'LUMS Incubation Center',
        ]);

        Meeting::create([
            'scheduler_id' => $founder2->id, 'invitee_id' => $mentor->id,
            'startup_id' => $startup2->id, 'title' => 'Mentoring Session — FarmLink',
            'scheduled_at' => now()->addWeek(), 'status' => 'accepted', 'location' => 'Google Meet',
        ]);

        // ---- Documents ----
        Document::create([
            'user_id' => $founder1->id, 'startup_id' => $startup1->id,
            'type' => 'pitch_deck', 'filename' => 'ecocharge_deck.pdf',
            'original_name' => 'EcoCharge_Pitch_Deck.pdf', 'path' => 'documents/startups/1/sample.pdf',
            'version' => 1, 'size' => 2048000, 'mime_type' => 'application/pdf',
        ]);

        Document::create([
            'user_id' => $founder2->id, 'startup_id' => $startup4->id,
            'type' => 'business_plan', 'filename' => 'edubridge_plan.pdf',
            'original_name' => 'EduBridge_Business_Plan.pdf', 'path' => 'documents/startups/4/sample.pdf',
            'version' => 1, 'size' => 1536000, 'mime_type' => 'application/pdf',
        ]);

        // ---- Conversations (using DB insert for pivot table without timestamps) ----
        $conv1 = Conversation::create(['subject' => 'EcoCharge Investment']);
        \Illuminate\Support\Facades\DB::table('conversation_participants')->insert([
            ['conversation_id' => $conv1->id, 'user_id' => $founder1->id],
            ['conversation_id' => $conv1->id, 'user_id' => $investor1->id],
        ]);
        Message::create([
            'conversation_id' => $conv1->id, 'sender_id' => $investor1->id,
            'body' => 'Hi Ahmed! I reviewed your EcoCharge deck. Very impressed with the battery-swap model.',
        ]);
        Message::create([
            'conversation_id' => $conv1->id, 'sender_id' => $founder1->id,
            'body' => 'Thanks Zain! Would you like to schedule a call to discuss the investment terms?',
        ]);

        $conv2 = Conversation::create(['subject' => 'Mentorship Discussion']);
        \Illuminate\Support\Facades\DB::table('conversation_participants')->insert([
            ['conversation_id' => $conv2->id, 'user_id' => $founder2->id],
            ['conversation_id' => $conv2->id, 'user_id' => $mentor->id],
        ]);
        Message::create([
            'conversation_id' => $conv2->id, 'sender_id' => $founder2->id,
            'body' => 'Dr. Bilal, I would love your guidance on FarmLink\'s go-to-market strategy.',
        ]);
        Message::create([
            'conversation_id' => $conv2->id, 'sender_id' => $mentor->id,
            'body' => 'Of course Fatima! Let\'s set up a mentoring session this week.',
        ]);
    }
}