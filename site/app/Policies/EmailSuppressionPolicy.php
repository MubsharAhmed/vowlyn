<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EmailSuppression;
use App\Models\User;
use App\Policies\Concerns\AllowsVerifiedAdmins;

/**
 * Deleting a suppression re-allows emailing that address, so it is deliberately
 * an admin action with a confirmation rather than a tidy-up operation. Nothing
 * in the panel can bulk-delete this list.
 */
final class EmailSuppressionPolicy
{
    use AllowsVerifiedAdmins;

    public function view(User $user, EmailSuppression $suppression): bool
    {
        return $this->admin($user);
    }

    public function update(User $user, EmailSuppression $suppression): bool
    {
        return $this->admin($user);
    }

    public function delete(User $user, EmailSuppression $suppression): bool
    {
        return $this->admin($user);
    }
}
