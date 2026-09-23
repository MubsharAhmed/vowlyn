<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ContactRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class ContactRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Each sample carries the service its brief is clearly about. The admin
        // form requires a service, and the reply-template picker chooses its
        // starting point from it, so demo leads without one cannot be answered.
        $samples = [
            [
                'name' => 'Daniel Arian',
                'email' => 'daniel@arianrugs.com',
                'company' => 'Arian Rugs',
                'service' => 'web-app-development',
                'brief' => 'Want to refresh the Shopify storefront with a more editorial feel — large hero photography, custom product detail pages, and a curated collection grid. Need it to be fast and SEO-friendly.',
                'status' => ContactRequest::STATUS_REPLIED,
                'created_at' => Carbon::now()->subDays(12),
                'replied_at' => Carbon::now()->subDays(11),
            ],
            [
                'name' => 'Priya Mehta',
                'email' => 'priya@northforge.io',
                'company' => 'NorthForge SaaS',
                'service' => 'saas-development',
                'brief' => 'Building a B2B analytics SaaS — multi-tenant Laravel + Filament admin, role-based access, Stripe billing, and a marketing site like yours. Timeline 10–12 weeks.',
                'status' => ContactRequest::STATUS_REVIEWED,
                'created_at' => Carbon::now()->subDays(4),
                'replied_at' => null,
            ],
            [
                'name' => 'Mark Stevens',
                'email' => 'mark@burloak.com',
                'company' => 'Burloak Group',
                'service' => 'web-app-development',
                'brief' => 'Need a corporate site rebuild with a CMS our marketing team can actually use. Filament-style admin preferred. Multi-language (EN/FR), accessibility AA, lighthouse 95+.',
                'status' => ContactRequest::STATUS_NEW,
                'created_at' => Carbon::now()->subHours(18),
                'replied_at' => null,
            ],
            [
                'name' => 'Lina Karam',
                'email' => 'lina@lumea.app',
                'company' => 'Lumea',
                'service' => 'mobile-app-development',
                'brief' => 'Mobile app MVP — React Native, AI-powered content recommendations, social graph. Looking for a tight engineering partner that can ship in 8 weeks.',
                'status' => ContactRequest::STATUS_NEW,
                'created_at' => Carbon::now()->subHours(6),
                'replied_at' => null,
            ],
            [
                'name' => 'Omar Haddad',
                'email' => 'omar@ontariobuying.ca',
                'company' => 'Ontario Buying Group',
                'service' => 'enterprise-security',
                'brief' => 'Internal procurement portal. Lots of forms, approvals, audit logs. Filament admin would be perfect. Need SSO with Microsoft 365.',
                'status' => ContactRequest::STATUS_ARCHIVED,
                'created_at' => Carbon::now()->subDays(30),
                'replied_at' => Carbon::now()->subDays(28),
            ],
        ];

        foreach ($samples as $sample) {
            ContactRequest::query()->updateOrCreate(
                ['email' => $sample['email']],
                array_merge($sample, [
                    'ip_address' => '203.0.113.'.random_int(2, 250),
                    'user_agent' => Str::random(40),
                    'updated_at' => $sample['created_at'],
                ])
            );
        }
    }
}
