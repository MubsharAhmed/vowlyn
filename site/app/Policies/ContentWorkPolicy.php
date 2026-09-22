<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContentWork;
use App\Models\User;

final class ContentWorkPolicy
{
    private function admin(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function viewAny(User $user): bool
    {
        return $this->admin($user);
    }

    public function view(User $user, ContentWork $work): bool
    {
        return $this->admin($user);
    }

    public function create(User $user): bool
    {
        return $this->admin($user);
    }

    public function update(User $user, ContentWork $work): bool
    {
        return $this->admin($user);
    }

    public function delete(User $user, ContentWork $work): bool
    {
        return $this->admin($user);
    }

    public function restore(User $user, ContentWork $work): bool
    {
        return $this->admin($user);
    }

    public function forceDelete(User $user, ContentWork $work): bool
    {
        return $this->admin($user);
    }
}
