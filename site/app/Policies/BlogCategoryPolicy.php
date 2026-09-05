<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogCategory;
use App\Models\User;

final class BlogCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function view(User $user, BlogCategory $category): bool
    {
        return $this->isEditor($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, BlogCategory $category): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, BlogCategory $category): bool
    {
        return $this->isEditor($user) && ! $category->posts()->exists();
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
