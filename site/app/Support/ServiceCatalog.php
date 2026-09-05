<?php

declare(strict_types=1);

namespace App\Support;

/**
 * ServiceCatalog — single source of truth for the individual service pages.
 *
 * Drives:
 *   - The /services/{slug} detail pages (pages.service-detail)
 *   - The service cards / links on the /services index
 *   - The XML sitemap
 *
 * Keeping the copy here (rather than in Blade) means one place to edit SEO
 * content and one place the sitemap reads slugs from.
 */
final class ServiceCatalog
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'web-app-development' => [
                'slug' => 'web-app-development',
                'no' => '01',
                'name' => 'Web App Development',
                'icon' => 'monitor',
                'eyebrow' => 'Web Engineering',
                'meta_title' => 'Web App Development Services | Vowlyn',
                'meta_description' => 'Custom web application development with Next.js, Laravel and headless CMS. A senior studio that ships fast, performant, SEO-clean web apps — and you own the code.',
                'h1' => 'Web App Development',
                'headline_accent' => 'that ships fast and scales.',
                'lede' => 'We design and build custom web applications — from marketing platforms to complex internal tools — engineered for speed, search visibility, and long-term maintainability. No page-builder shortcuts, no template debt: production code your team can own and extend.',
                'keywords' => ['web app development', 'custom web application', 'Next.js development', 'Laravel development'],
                'outcomes' => [
                    ['v' => '90+', 'l' => 'Lighthouse scores, standard'],
                    ['v' => 'SEO', 'l' => 'Server-rendered by default'],
                    ['v' => '100%', 'l' => 'Code ownership, no lock-in'],
                ],
                'build' => [
                    ['icon' => 'layout-panel-top', 'title' => 'Marketing & content platforms', 'desc' => 'Fast, server-rendered sites with a headless CMS your editors actually enjoy using.'],
                    ['icon' => 'gauge', 'title' => 'Web applications & dashboards', 'desc' => 'Data-dense internal tools, customer portals, and admin panels built for real workflows.'],
                    ['icon' => 'shopping-bag', 'title' => 'Commerce & checkout', 'desc' => 'Conversion-focused storefronts and checkout flows with payments wired in cleanly.'],
                    ['icon' => 'component', 'title' => 'Design systems', 'desc' => 'A reusable component library so every future screen ships faster and stays consistent.'],
                ],
                'process' => [
                    ['t' => 'Blueprint', 'd' => 'We map user journeys, data models and the technical architecture before any code.'],
                    ['t' => 'Design & prototype', 'd' => 'A clickable prototype and design system validate the experience early.'],
                    ['t' => 'Build & integrate', 'd' => 'Server-rendered pages, clean APIs, and automated tests on every merge.'],
                    ['t' => 'Launch & tune', 'd' => 'Performance budgets, Core Web Vitals, and analytics dialed in for growth.'],
                ],
                'stack' => ['Next.js', 'React', 'Laravel', 'TypeScript', 'Tailwind CSS', 'Headless CMS', 'PostgreSQL', 'Edge / CDN'],
                'faqs' => [
                    ['q' => 'How long does a custom web app take to build?', 'a' => 'Most web apps launch in 6–14 weeks depending on scope. We start with a fixed-scope discovery sprint so you get a firm timeline and estimate before any build commitment.'],
                    ['q' => 'Will the site be good for SEO?', 'a' => 'Yes. We build server-rendered by default (Next.js / Laravel), with clean semantic HTML, fast Core Web Vitals, structured data, and per-page metadata — the foundations search engines reward.'],
                    ['q' => 'Do we own the code?', 'a' => 'Completely. Everything ships to your repositories and hosting accounts from day one. There is no proprietary platform and no lock-in.'],
                    ['q' => 'Can you work with our existing brand or designs?', 'a' => 'Absolutely. We can build from your existing brand and Figma files, or design the system from scratch as part of the engagement.'],
                ],
                'related' => ['saas-development', 'ai-development', 'cloud-devops'],
            ],

            'mobile-app-development' => [
                'slug' => 'mobile-app-development',
                'no' => '02',
                'name' => 'Mobile App Development',
                'icon' => 'smartphone',
                'eyebrow' => 'Mobile Engineering',
                'meta_title' => 'Mobile App Development Services (iOS & Android) | Vowlyn',
                'meta_description' => 'Cross-platform and native mobile app development with React Native and Swift/Kotlin. One senior studio ships polished iOS and Android apps built for growth — you own the code.',
                'h1' => 'Mobile App Development',
                'headline_accent' => 'for iOS and Android.',
                'lede' => 'We build cross-platform and native mobile apps that feel fast, intuitive, and platform-true. From one React Native codebase serving both stores to fully native builds where it counts — plus the store submission and release ops that get you live.',
                'keywords' => ['mobile app development', 'iOS app development', 'Android app development', 'React Native development'],
                'outcomes' => [
                    ['v' => '1', 'l' => 'Codebase, both stores'],
                    ['v' => '60fps', 'l' => 'Native-feel interactions'],
                    ['v' => 'App Store', 'l' => 'Submission & release handled'],
                ],
                'build' => [
                    ['icon' => 'smartphone', 'title' => 'Cross-platform apps', 'desc' => 'One React Native codebase that ships to both App Store and Google Play without compromise.'],
                    ['icon' => 'tablet-smartphone', 'title' => 'Native iOS & Android', 'desc' => 'Swift and Kotlin builds where platform-native performance and APIs are non-negotiable.'],
                    ['icon' => 'wifi-off', 'title' => 'Offline-first sync', 'desc' => 'Local-first data and sync engines so the app works on the subway, not just on WiFi.'],
                    ['icon' => 'bell', 'title' => 'Push, auth & payments', 'desc' => 'Notifications, secure auth, in-app purchases and subscriptions wired in properly.'],
                ],
                'process' => [
                    ['t' => 'Blueprint', 'd' => 'Platform strategy (native vs cross-platform), flows, and data model up front.'],
                    ['t' => 'Design & prototype', 'd' => 'Interactive prototypes tested on real devices before build.'],
                    ['t' => 'Build & integrate', 'd' => 'Feature-complete builds with device testing and store-ready assets.'],
                    ['t' => 'Ship & iterate', 'd' => 'Store submission, phased rollout, crash monitoring, and update cadence.'],
                ],
                'stack' => ['React Native', 'Expo', 'Swift', 'Kotlin', 'TypeScript', 'SQLite / WatermelonDB', 'Firebase', 'App Store / Play Console'],
                'faqs' => [
                    ['q' => 'Should we build native or cross-platform?', 'a' => 'For most products, React Native gives you both stores from one codebase at a lower cost. We recommend fully native only when heavy graphics, deep platform APIs, or maximum performance demand it — and we advise honestly on which fits your case.'],
                    ['q' => 'Do you handle App Store and Google Play submission?', 'a' => 'Yes. We manage store setup, review requirements, assets, and phased rollout — and hand over the accounts so you stay in control.'],
                    ['q' => 'Can you add a mobile app to our existing backend?', 'a' => 'Definitely. We integrate with your existing APIs, or build the backend alongside the app if you need one.'],
                    ['q' => 'What about ongoing updates and OS changes?', 'a' => 'Many clients keep us on a lightweight retainer for OS updates, new features, and store maintenance — or we hand off with documentation for your team.'],
                ],
                'related' => ['web-app-development', 'ai-development', 'saas-development'],
            ],

            'ai-development' => [
                'slug' => 'ai-development',
                'no' => '03',
                'name' => 'AI Engineering & Integration',
                'icon' => 'sparkles',
                'eyebrow' => 'Applied AI',
                'meta_title' => 'AI Development & Integration Services | Vowlyn',
                'meta_description' => 'Applied AI engineering: RAG assistants, LLM pipelines, and computer vision built into real products — with evals, guardrails, and observability. Ship AI that actually works.',
                'h1' => 'AI Engineering & Integration',
                'headline_accent' => 'built into real products.',
                'lede' => 'We bring AI into products that ship — retrieval-augmented assistants grounded in your data, reliable LLM pipelines with evals and guardrails, and computer-vision features that hold up in production. Not demos: measured, monitored, and maintainable AI.',
                'keywords' => ['AI development', 'LLM integration', 'RAG development', 'computer vision', 'AI engineering'],
                'outcomes' => [
                    ['v' => 'RAG', 'l' => 'Grounded in your data'],
                    ['v' => 'Evals', 'l' => 'Quality you can measure'],
                    ['v' => 'Guardrails', 'l' => 'Safe, observable output'],
                ],
                'build' => [
                    ['icon' => 'message-square', 'title' => 'RAG assistants & chat', 'desc' => 'Assistants grounded in your own documents and data — accurate, cited, and current.'],
                    ['icon' => 'workflow', 'title' => 'LLM pipelines', 'desc' => 'Structured extraction, classification, and generation with evals, retries and guardrails.'],
                    ['icon' => 'scan-eye', 'title' => 'Computer vision', 'desc' => 'Detection, OCR, and quality-control models moved from notebook to production.'],
                    ['icon' => 'plug', 'title' => 'AI feature integration', 'desc' => 'Dropping AI into an existing app the right way — cost-controlled and observable.'],
                ],
                'process' => [
                    ['t' => 'Frame the problem', 'd' => 'We define the task, the success metric, and whether AI is even the right tool.'],
                    ['t' => 'Prototype & evaluate', 'd' => 'A working prototype scored against an eval set — not vibes.'],
                    ['t' => 'Harden for production', 'd' => 'Guardrails, fallbacks, caching, cost controls, and observability.'],
                    ['t' => 'Monitor & improve', 'd' => 'Track quality and cost in the wild and tune against real usage.'],
                ],
                'stack' => ['Claude / OpenAI', 'Python', 'Vector DBs', 'LangChain / custom', 'PyTorch', 'ONNX', 'Evals & tracing', 'Laravel / Node APIs'],
                'faqs' => [
                    ['q' => 'Is our data safe with AI features?', 'a' => 'Yes. We design for data control — using providers and configurations that keep your data private, with the option of self-hosted or region-locked models where compliance requires it.'],
                    ['q' => 'How do you keep AI output reliable?', 'a' => 'We treat AI like any other system: an evaluation set to measure quality, guardrails and validation on output, fallbacks for failures, and observability so you can see what the model is doing in production.'],
                    ['q' => 'Can you add AI to our existing product?', 'a' => 'That is one of the most common engagements — integrating a grounded assistant, search, or automation into an app you already run, with cost controls from day one.'],
                    ['q' => 'How do you control AI running costs?', 'a' => 'Caching, right-sizing models per task, and monitoring token usage. We build cost visibility in so spend never surprises you.'],
                ],
                'related' => ['web-app-development', 'saas-development', 'mobile-app-development'],
            ],

            'saas-development' => [
                'slug' => 'saas-development',
                'no' => '04',
                'name' => 'SaaS Platform Development',
                'icon' => 'layers',
                'eyebrow' => 'SaaS Platforms',
                'meta_title' => 'SaaS Development Services — Multi-tenant Platforms | Vowlyn',
                'meta_description' => 'End-to-end SaaS platform development: multi-tenancy, subscription billing, roles and permissions, and admin tooling. A senior studio that builds SaaS products that scale.',
                'h1' => 'SaaS Platform Development',
                'headline_accent' => 'from MVP to scale.',
                'lede' => 'We build software-as-a-service platforms end to end — multi-tenant architecture, subscription billing, granular permissions, and the admin tooling that keeps a growing product manageable. Whether it is a first MVP or scaling an existing SaaS, we ship the foundation you can grow on.',
                'keywords' => ['SaaS development', 'multi-tenant SaaS', 'subscription billing', 'SaaS MVP development'],
                'outcomes' => [
                    ['v' => 'Multi', 'l' => 'Tenant from day one'],
                    ['v' => 'Billing', 'l' => 'Subscriptions & metering'],
                    ['v' => 'RBAC', 'l' => 'Roles & permissions'],
                ],
                'build' => [
                    ['icon' => 'layers', 'title' => 'Multi-tenant architecture', 'desc' => 'Isolated, secure tenants on shared infrastructure — with per-brand theming where needed.'],
                    ['icon' => 'credit-card', 'title' => 'Subscription billing', 'desc' => 'Plans, trials, proration, metering and invoices with Stripe wired in reliably.'],
                    ['icon' => 'shield-check', 'title' => 'Auth, roles & teams', 'desc' => 'SSO, team accounts, and role-based permissions that match how customers actually work.'],
                    ['icon' => 'sliders-horizontal', 'title' => 'Admin & operations', 'desc' => 'The internal admin panel and dashboards you need to run the product day to day.'],
                ],
                'process' => [
                    ['t' => 'Blueprint', 'd' => 'Tenancy model, billing design, and roadmap scoped before build.'],
                    ['t' => 'MVP build', 'd' => 'The core loop shipped first — real users, real feedback, fast.'],
                    ['t' => 'Billing & scale', 'd' => 'Subscriptions, permissions, and performance hardened as you grow.'],
                    ['t' => 'Operate & grow', 'd' => 'Observability, admin tooling, and an embedded pod for ongoing velocity.'],
                ],
                'stack' => ['Laravel', 'Next.js', 'PostgreSQL', 'Stripe', 'Redis', 'Filament', 'Queues / Workers', 'AWS / GCP'],
                'faqs' => [
                    ['q' => 'Can you build our SaaS MVP fast without painting us into a corner?', 'a' => 'Yes — that is the balance we specialise in. We ship the core loop quickly while choosing an architecture (multi-tenancy, billing, auth) that scales, so the MVP does not have to be thrown away later.'],
                    ['q' => 'How do you handle multi-tenancy?', 'a' => 'We design isolation into the data model from the start — most often a shared database with tenant scoping, or separate schemas where compliance demands stronger isolation.'],
                    ['q' => 'Do you integrate subscription billing?', 'a' => 'Yes. Stripe subscriptions, trials, proration, usage-based metering, customer portal and invoicing — implemented with idempotent, well-tested webhooks.'],
                    ['q' => 'Can you take over or scale an existing SaaS?', 'a' => 'We regularly inherit existing codebases — audit them, stabilise, and then improve architecture and velocity from there.'],
                ],
                'related' => ['web-app-development', 'ai-development', 'enterprise-security'],
            ],

            'enterprise-security' => [
                'slug' => 'enterprise-security',
                'no' => '05',
                'name' => 'Enterprise Security',
                'icon' => 'shield-check',
                'eyebrow' => 'Security & Compliance',
                'meta_title' => 'Enterprise Application Security Services | Vowlyn',
                'meta_description' => 'Application security engineering: authentication and SSO/SAML, access control, secure coding, and compliance readiness (SOC 2). Harden your product for enterprise buyers.',
                'h1' => 'Enterprise Security',
                'headline_accent' => 'that unlocks bigger deals.',
                'lede' => 'We harden applications for enterprise buyers — robust authentication and SSO, airtight access control, secure-by-default engineering, and the compliance groundwork (SOC 2, GDPR) that procurement teams demand. Security that opens doors instead of slowing you down.',
                'keywords' => ['application security', 'SSO SAML integration', 'SOC 2 readiness', 'secure development'],
                'outcomes' => [
                    ['v' => 'SSO', 'l' => 'SAML / OIDC ready'],
                    ['v' => 'SOC 2', 'l' => 'Controls groundwork'],
                    ['v' => 'RBAC', 'l' => 'Least-privilege access'],
                ],
                'build' => [
                    ['icon' => 'key-round', 'title' => 'Authentication & SSO', 'desc' => 'SAML, OIDC, passkeys and MFA — the enterprise login options buyers expect.'],
                    ['icon' => 'lock', 'title' => 'Access control', 'desc' => 'Role- and attribute-based permissions with least-privilege enforced end to end.'],
                    ['icon' => 'shield-check', 'title' => 'Secure engineering', 'desc' => 'Threat modelling, secure coding, dependency and secret scanning in your pipeline.'],
                    ['icon' => 'clipboard-check', 'title' => 'Compliance readiness', 'desc' => 'The technical controls and evidence to move SOC 2, ISO 27001 and GDPR forward.'],
                ],
                'process' => [
                    ['t' => 'Assess', 'd' => 'A pragmatic review of auth, access control, data handling and dependencies.'],
                    ['t' => 'Prioritise', 'd' => 'A ranked, plain-English remediation plan — highest risk first.'],
                    ['t' => 'Remediate', 'd' => 'We implement fixes and harden the pipeline, not just write a report.'],
                    ['t' => 'Sustain', 'd' => 'Automated scanning and guardrails so security holds as you keep shipping.'],
                ],
                'stack' => ['SAML / OIDC', 'OAuth 2.0', 'Passkeys / MFA', 'RBAC / ABAC', 'SAST / DAST', 'Secret scanning', 'Audit logging', 'SOC 2 controls'],
                'faqs' => [
                    ['q' => 'We need SSO to close an enterprise deal — can you add it fast?', 'a' => 'Yes. SAML/OIDC single sign-on is one of our most common requests. We can add enterprise SSO to an existing product on a tight timeline so it stops blocking the deal.'],
                    ['q' => 'Can you get us SOC 2 ready?', 'a' => 'We handle the engineering side of SOC 2 — access control, logging, encryption, and the technical evidence. We work alongside your compliance/audit partner to close the gaps efficiently.'],
                    ['q' => 'Do you do security reviews of existing apps?', 'a' => 'Yes. We run a focused application security assessment and deliver a prioritised, actionable remediation plan — and can implement the fixes too.'],
                    ['q' => 'Is this a scan or real engineering?', 'a' => 'Both. Automated scanning has its place, but our value is engineers who fix the root causes and build guardrails into your pipeline so issues do not return.'],
                ],
                'related' => ['saas-development', 'cloud-devops', 'web-app-development'],
            ],

            'cloud-devops' => [
                'slug' => 'cloud-devops',
                'no' => '06',
                'name' => 'Cloud & DevOps',
                'icon' => 'cloud',
                'eyebrow' => 'Cloud & Infrastructure',
                'meta_title' => 'Cloud & DevOps Services (AWS, GCP, CI/CD) | Vowlyn',
                'meta_description' => 'Cloud infrastructure and DevOps: AWS and GCP architecture, infrastructure-as-code, CI/CD pipelines, and observability. Ship on every merge — reliably and cost-efficiently.',
                'h1' => 'Cloud & DevOps',
                'headline_accent' => 'so you ship with confidence.',
                'lede' => 'We set up the cloud infrastructure and delivery pipelines that let teams ship on every merge — right-sized AWS and GCP architecture, infrastructure-as-code, automated CI/CD, and the observability to know something broke before your users do. Reliable, repeatable, and cost-aware.',
                'keywords' => ['cloud infrastructure', 'DevOps services', 'CI/CD pipelines', 'AWS GCP architecture', 'infrastructure as code'],
                'outcomes' => [
                    ['v' => 'IaC', 'l' => 'Reproducible infra'],
                    ['v' => 'CI/CD', 'l' => 'Ship on every merge'],
                    ['v' => '24/7', 'l' => 'Observability & alerts'],
                ],
                'build' => [
                    ['icon' => 'cloud', 'title' => 'Cloud architecture', 'desc' => 'Right-sized AWS and GCP designs — serverless or containers — without over-engineering.'],
                    ['icon' => 'file-code', 'title' => 'Infrastructure as code', 'desc' => 'Terraform / Pulumi so environments are reproducible, reviewable and disaster-proof.'],
                    ['icon' => 'git-branch', 'title' => 'CI/CD pipelines', 'desc' => 'Automated build, test and deploy on every merge — safely, with rollbacks.'],
                    ['icon' => 'activity', 'title' => 'Observability', 'desc' => 'Metrics, logs, traces and alerting so problems surface before customers notice.'],
                ],
                'process' => [
                    ['t' => 'Assess', 'd' => 'Review current infra, deploys, cost and reliability pain points.'],
                    ['t' => 'Design', 'd' => 'An architecture and pipeline plan matched to your team and budget.'],
                    ['t' => 'Implement', 'd' => 'Infra-as-code, pipelines, and monitoring stood up and documented.'],
                    ['t' => 'Handover', 'd' => 'Runbooks and training so your team owns it — or we stay on to operate it.'],
                ],
                'stack' => ['AWS', 'GCP', 'Docker', 'Kubernetes', 'Terraform', 'GitHub Actions', 'Grafana / Prometheus', 'Cloudflare'],
                'faqs' => [
                    ['q' => 'Our deploys are slow and scary — can you fix that?', 'a' => 'Yes. We set up automated CI/CD with tests and safe rollbacks so deploys become boring and frequent instead of risky events. That alone transforms most teams’ velocity.'],
                    ['q' => 'Can you reduce our cloud bill?', 'a' => 'Often, yes. We right-size infrastructure, remove waste, and add cost visibility. Over-provisioning is the most common (and fixable) source of a bloated cloud bill.'],
                    ['q' => 'AWS or GCP — which should we use?', 'a' => 'Both are excellent; the right choice depends on your team, existing tools, and workload. We advise based on your situation rather than pushing one platform.'],
                    ['q' => 'Do you hand it over or run it for us?', 'a' => 'Either. We document everything and train your team to own it, or stay on as an embedded pod to operate and evolve the infrastructure.'],
                ],
                'related' => ['saas-development', 'enterprise-security', 'web-app-development'],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function list(): array
    {
        return array_values(self::all());
    }

    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    /**
     * @return array<int, string>
     */
    public static function slugs(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::all() as $slug => $service) {
            $options[$slug] = (string) $service['name'];
        }

        return $options;
    }
}
