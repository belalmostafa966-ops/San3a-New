<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class WalletPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO: الأسعار دي مقترحة في اجتماع الفريق - محتاجة تأكيد نهائي
        $plans = [
            [
                'name' => 'Basic',
                'price' => 400,
                'monthly_leads_or_requests' => 6,
                'perks_json' => ['support' => 'standard'],
            ],
            [
                'name' => 'Advanced',
                'price' => 700,
                'monthly_leads_or_requests' => 15,
                'perks_json' => ['support' => 'priority'],
            ],
            [
                'name' => 'Premium',
                'price' => 1000,
                'monthly_leads_or_requests' => null, // null = عدد غير محدود
                'perks_json' => ['support' => 'vip', 'unlimited_requests' => true],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}