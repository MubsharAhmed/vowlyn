<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContactRequestMessage;
use App\Models\User;

/**
 * The outbound history is append-only: replies are written by the send action,
 * never edited by hand. This policy therefore only grants reading, and Filament
 * needs it to exist at all — a relation manager whose related model has no
 * policy is hidden from the request page without any error to explain why.
 */
final class ContactRequestMessagePolicy
{
    private function admin(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function viewAny(User $user): bool
    {
        return $this->admin($user);
    }

    public function view(User $user, ContactRequestMessage $message): bool
    {
        return $this->admin($user);
    }
}
