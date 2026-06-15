{{-- ============================================================================
HOME — Anchors all marketing sections in order
============================================================================ --}}
@extends('layouts.app')

@section('content')
    @include('sections.hero')
    @include('sections.services')
    @include('sections.about')
    @include('sections.reel')
    @include('sections.process')
    @include('sections.portfolio')
    @include('sections.why-us')
    @include('sections.testimonials')
    @include('sections.languages')
    @include('sections.contact')
@endsection