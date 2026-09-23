<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MailTemplate;
use App\Models\User;

final class MailTemplatePolicy
{
    private function admin(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function viewAny(User $user): bool
    {
        return $this->admin($user);
    }

    public function view(User $user, MailTemplate $template): bool
    {
        return $this->admin($user);
    }

    public function create(User $user): bool
    {
        return $this->admin($user);
    }

    public function update(User $user, MailTemplate $template): bool
    {
        return $this->admin($user);
    }

    public function delete(User $user, MailTemplate $template): bool
    {
        return $this->admin($user);
    }
}
