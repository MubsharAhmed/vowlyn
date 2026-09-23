<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Models\User;

/**
 * The studio's single authorisation rule: a verified email address, nothing more.
 *
 * There is one role in this application — the studio that owns it — so policies
 * exist to keep the panel closed to anybody who is not that, and to give
 * Filament a policy to read. Without one, Filament silently hides a resource's
 * relation managers, which is a trap this project has already fallen into once.
 */
trait AllowsVerifiedAdmins
{
    private function admin(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function viewAny(User $user): bool
    {
        return $this->admin($user);
    }

    public function create(User $user): bool
    {
        return $this->admin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->admin($user);
    }
}
