<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
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

            Contact::firstOrCreate(
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
        }
    }
}
