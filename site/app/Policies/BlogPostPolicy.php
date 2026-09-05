<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

final class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function view(User $user, BlogPost $post): bool
    {
        return $this->isEditor($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, BlogPost $post): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, BlogPost $post): bool
    {
        return $this->isEditor($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function restore(User $user, BlogPost $post): bool
    {
        return $this->isEditor($user);
    }

    public function restoreAny(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function forceDelete(User $user, BlogPost $post): bool
    {
        return $this->isEditor($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isEditor($user);
    }

    private function isEditor(User $user): bool
    {
        return $user->email_verified_at !== null;
    }
}
