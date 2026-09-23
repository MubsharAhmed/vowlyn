<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OutreachEnrollment;
use App\Models\User;
use App\Policies\Concerns\AllowsVerifiedAdmins;

/**
 * Enrollments are managed from inside a campaign, never on their own, and the
 * listing itself is read-only in the panel. The policy exists so Filament will
 * render the relation manager at all: a related model without a policy is
 * silently dropped from the page.
 */
final class OutreachEnrollmentPolicy
{
    use AllowsVerifiedAdmins;

    public function view(User $user, OutreachEnrollment $enrollment): bool
    {
        return $this->admin($user);
    }

    public function update(User $user, OutreachEnrollment $enrollment): bool
    {
        return $this->admin($user);
    }

    public function delete(User $user, OutreachEnrollment $enrollment): bool
    {
        return $this->admin($user);
    }
}
