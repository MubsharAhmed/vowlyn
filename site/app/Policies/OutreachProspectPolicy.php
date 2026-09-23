<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OutreachProspect;
use App\Models\User;
use App\Policies\Concerns\AllowsVerifiedAdmins;

final class OutreachProspectPolicy
{
    use AllowsVerifiedAdmins;

    public function view(User $user, OutreachProspect $prospect): bool
    {
        return $this->admin($user);
    }

    public function update(User $user, OutreachProspect $prospect): bool
    {
        return $this->admin($user);
    }

    public function delete(User $user, OutreachProspect $prospect): bool
    {
        return $this->admin($user);
    }

    public function restore(User $user, OutreachProspect $prospect): bool
    {
        return $this->admin($user);
    }

    public function forceDelete(User $user, OutreachProspect $prospect): bool
    {
        return $this->admin($user);
    }
}
