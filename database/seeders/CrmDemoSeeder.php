<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class CrmDemoSeeder extends Seeder
{
    /**
     * Seed a few demo companies and contacts.
     *
     * @return void
     */
    public function run()
    {
        $owner = User::where('email', 'admin@crm.test')->first();
        $ownerId = $owner ? $owner->id : null;

        $companies = [
            ['name' => 'Acme Corporation', 'industry' => 'Manufacturing', 'phone' => '+1 202 555 0111', 'website' => 'acme.example.com'],
            ['name' => 'Globex Ltd', 'industry' => 'Technology', 'phone' => '+1 202 555 0122', 'website' => 'globex.example.com'],
            ['name' => 'Initech', 'industry' => 'Software', 'phone' => '+1 202 555 0133', 'website' => 'initech.example.com'],
        ];

        foreach ($companies as $data) {
            $company = Company::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['owner_id' => $ownerId])
            );

            $contact = Contact::firstOrCreate(
                ['email' => strtolower(str_replace(' ', '', $data['name'])).'.contact@example.com'],
                [
                    'first_name' => explode(' ', $data['name'])[0],
                    'last_name' => 'Manager',
                    'phone' => $data['phone'],
                    'job_title' => 'Account Manager',
                    'company_id' => $company->id,
                    'owner_id' => $ownerId,
                ]
            );

            Deal::firstOrCreate(
                ['title' => $data['name'].' — Annual Contract'],
                [
                    'value' => rand(5, 60) * 1000,
                    'stage' => ['new', 'qualified', 'proposal', 'won'][array_rand([0, 1, 2, 3])],
                    'expected_close_date' => now()->addDays(rand(10, 90))->toDateString(),
                    'contact_id' => $contact->id,
                    'company_id' => $company->id,
                    'owner_id' => $ownerId,
                ]
            );
        }

        $leads = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah.j@example.com', 'source' => 'Website', 'status' => 'new'],
            ['name' => 'Michael Chen', 'email' => 'm.chen@example.com', 'source' => 'Referral', 'status' => 'contacted'],
            ['name' => 'Priya Patel', 'email' => 'priya.p@example.com', 'source' => 'LinkedIn', 'status' => 'qualified'],
        ];

        foreach ($leads as $data) {
            Lead::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['owner_id' => $ownerId])
            );
        }

        $firstContact = Contact::orderBy('id')->first();
        $firstDeal = Deal::orderBy('id')->first();

        $activities = [
            ['type' => 'call', 'subject' => 'Follow-up call with Acme', 'due_date' => now()->addDays(1)->toDateString(), 'contact_id' => $firstContact ? $firstContact->id : null],
            ['type' => 'meeting', 'subject' => 'Proposal review meeting', 'due_date' => now()->addDays(3)->toDateString(), 'deal_id' => $firstDeal ? $firstDeal->id : null],
            ['type' => 'email', 'subject' => 'Send pricing sheet', 'due_date' => now()->addDays(2)->toDateString()],
            ['type' => 'task', 'subject' => 'Update CRM records', 'due_date' => now()->subDays(1)->toDateString(), 'completed' => true],
        ];

        foreach ($activities as $data) {
            Activity::firstOrCreate(
                ['subject' => $data['subject']],
                array_merge($data, ['owner_id' => $ownerId])
            );
        }
    }
}
