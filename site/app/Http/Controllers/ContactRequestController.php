<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\ContactRequest;
use Illuminate\Http\RedirectResponse;

final class ContactRequestController extends Controller
{
    public function store(StoreContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $formSource = $data['form_source'] ?? 'contact';
        $anchor = $formSource === 'hero' ? 'home' : 'contact';
        $service = $data['service'] ?? 'not-specified';

        // Drop honeypot, attach metadata
        unset($data['website'], $data['form_source']);
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        ContactRequest::create($data);

        return redirect()
            ->to(url('/').'#'.$anchor)
            ->with('contact.success', "Thanks — we'll be in touch within 24 hours.")
            ->with('analytics.event', [
                'event' => 'generate_lead',
                'form_source' => $formSource,
                'service' => $service,
            ]);
    }
}
