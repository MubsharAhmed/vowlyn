<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OutreachMessage;
use App\Models\User;

/**
 * The send log is read-only evidence of what left the building, so there is no
 * create or update here by design — nothing in the panel writes to it directly,
 * and a record of what somebody received should not be editable.
 */
final class OutreachMessagePolicy
{
    private function admin(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function viewAny(User $user): bool
    {
        return $this->admin($user);
    }

    public function view(User $user, OutreachMessage $message): bool
    {
        return $this->admin($user);
    }

    /**
     * Nothing creates a message except the sender itself, and nothing edits or
     * removes one: a record of what somebody was sent stops being evidence the
     * moment it can be changed.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, OutreachMessage $message): bool
    {
        return false;
    }

    public function delete(User $user, OutreachMessage $message): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
