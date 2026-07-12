<?php

namespace Database\Seeders;

use App\Models\ContactSubmission;
use Illuminate\Database\Seeder;

class ContactSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $submissions = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1 555-0199',
                'subject' => 'Inquiry About Custom Design Sets',
                'message' => 'Hello! I am highly interested in ordering a customized set from Signature By RaiMal\'s. Could you please share the design catalogue and custom sizing options?',
                'status' => 'new',
                'created_at' => now()->subHours(2),
            ],
            [
                'name' => 'Sophia Martinez',
                'email' => 'sophia.m@example.com',
                'phone' => '+44 20 7946 0958',  
                'subject' => 'Exceptional Packaging & Delivery Support',
                'message' => 'Hi, I received my package yesterday and the packaging was absolutely beautiful! Thank you so much for the exceptional service.',
                'status' => 'read',
                'read_at' => now()->subHour(),
                'created_at' => now()->subDays(1),
            ],
            [
                'name' => 'Ahmad Khan',
                'email' => 'ahmad.khan@example.com',
                'phone' => '+92 300 1234567',
                'subject' => 'Boutique Store Timings',
                'message' => 'Is it possible to visit your boutique in person? Please let me know your available store timings and address details.',
                'status' => 'replied',
                'read_at' => now()->subDays(2),
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($submissions as $submission) {
            ContactSubmission::create($submission);
        }
    }
}
