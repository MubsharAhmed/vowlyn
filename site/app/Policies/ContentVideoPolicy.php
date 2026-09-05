<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContentVideo;
use App\Models\User;

final class ContentVideoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->email_verified_at !== null;
    }

    public function view(User $user, ContentVideo $video): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, ContentVideo $video): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, ContentVideo $video): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, ContentVideo $video): bool
    {
        return $this->viewAny($user);
    }

    public function forceDelete(User $user, ContentVideo $video): bool
    {
        return $this->viewAny($user);
    }

    public function reorder(User $user): bool
    {
        return $this->viewAny($user);
    }
}
