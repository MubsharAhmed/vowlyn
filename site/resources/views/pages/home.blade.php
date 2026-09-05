{{-- ============================================================================
HOME — Anchors all marketing sections in order
Meta + structured data per Vowlyn-Homepage-Rewrite-2026.md
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Custom Software Development Studio | Vowlyn')
@section('description', 'Vowlyn is a custom software development studio building web apps, mobile apps, AI features, and scalable SaaS. A small, senior team that ships fast — and you own the code. Start a project.')
@section('canonical', 'https://vowlyn.com/')
@section('og_title', 'Custom Software Development Studio | Vowlyn')
@section('og_description', 'Web apps, mobile apps, AI, and SaaS — built by one senior studio. You own the code, no lock-in.')
@section('twitter_title', 'Custom Software Development Studio | Vowlyn')
@section('twitter_description', 'Web apps, mobile, AI, and SaaS built by one senior studio. You own the code.')

@push('head')
    {{-- Organization + WebSite — the single biggest AEO/GEO lever --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "@id": "https://vowlyn.com/#organization",
          "name": "Vowlyn",
          "url": "https://vowlyn.com/",
          "logo": "https://vowlyn.com/brand/vowlyn-logo.png",
          "description": "Custom software development studio building web apps, mobile apps, AI features, and scalable SaaS platforms.",
          "founder": { "@type": "Person", "name": "Junaid Swati" },
          "numberOfEmployees": "8",
          "areaServed": ["North America", "Europe", "MENA"],
          "knowsAbout": ["Web app development", "Mobile app development", "AI integration", "SaaS development", "Cloud DevOps", "Enterprise security"],
          "sameAs": [
            "https://www.linkedin.com/company/vowlyn",
            "https://x.com/vowlyn",
            "https://dribbble.com/vowlyn"
          ]
        },
        {
          "@type": "WebSite",
          "@id": "https://vowlyn.com/#website",
          "url": "https://vowlyn.com/",
          "name": "Vowlyn",
          "publisher": { "@id": "https://vowlyn.com/#organization" }
        }
      ]
    }
    </script>

    {{-- FAQPage — mirrors the visible People-Also-Ask section (sections/faq) --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        { "@type": "Question", "name": "What does Vowlyn do?", "acceptedAnswer": { "@type": "Answer", "text": "Vowlyn is a custom software development studio. It designs, engineers, and scales web apps, mobile apps, AI features, and SaaS platforms for founders and teams across North America, Europe, and MENA." } },
        { "@type": "Question", "name": "Is Vowlyn an agency or a software development studio?", "acceptedAnswer": { "@type": "Answer", "text": "Vowlyn is a software development studio, not a traditional agency. It is a small, senior-only team that owns strategy, design, engineering, and launch in-house — no outsourced contractors, no middle-management layer." } },
        { "@type": "Question", "name": "What software development services does Vowlyn offer?", "acceptedAnswer": { "@type": "Answer", "text": "Six disciplines: modern web app development, mobile app development, AI integration, scalable SaaS development, enterprise security, and cloud DevOps — available individually or as one end-to-end build." } },
        { "@type": "Question", "name": "How fast can Vowlyn start a project?", "acceptedAnswer": { "@type": "Answer", "text": "Discovery sprints typically begin within a week, and the studio replies to every project request within 24 hours." } },
        { "@type": "Question", "name": "Who owns the code and cloud accounts?", "acceptedAnswer": { "@type": "Answer", "text": "You do. Everything ships to your own repositories and cloud accounts from day one, with no vendor lock-in." } }
      ]
    }
    </script>
@endpush

@section('content')
    @include('sections.hero')
    @include('sections.services')
    {{-- Temporarily hidden until the imagery and showreel are replaced with verified Vowlyn assets. --}}
    {{-- @include('sections.about') --}}
    {{-- @include('sections.reel') --}}
    @include('sections.process')
    @include('sections.portfolio')
    @include('sections.why-us')
    @include('sections.testimonials')
    @include('sections.languages')
    @include('sections.faq')
    @include('sections.contact')

@endsection
