<?php

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    protected $templates = [
        [
            'name' => 'Send Boiler Picture',
            'body' => "Hello {name},\n\nJust a courtesy reminder to please send us the boiler pictures to {email} or WhatsApp +44{whatsapp} for your property at:\n\n{address}\n\nCordially,\n{company_name}",
            'properties' => [
                'name',
                'email',
                'whatsapp',
                'address',
                'company_name'
            ],
            'created_by_id' => 1
        ],
        [
            'name' => 'Get in touch',
            'body' => "Hello {name},\n\nThis message is in response to your query for your property at:\n\n{address}\n\nPlease get in touch so we can take your application further.\n\nWe are available:\nMonday to Friday from 9 am to 6pm\nSaturday from 10am till 3pm.\n\nContact us: +44{phone}\n\nCordially,\n{company_name}",
            'properties' => [
                'name',
                'address',
                'phone',
                'company_name'
            ],
            'created_by_id' => 1
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->templates as $key => $template) {
            SmsTemplate::firstOrCreate($template);
        }
    }
}
