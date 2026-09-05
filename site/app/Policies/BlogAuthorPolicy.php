<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogAuthor;
use App\Models\User;

final class BlogAuthorPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function view(User $user, BlogAuthor $author): bool
    {
        return $this->isEditor($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, BlogAuthor $author): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, BlogAuthor $author): bool
    {
        return $this->isEditor($user) && ! $author->posts()->exists();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function isEditor(User $user): bool
    {
        return $user->email_verified_at !== null;
    }
}
