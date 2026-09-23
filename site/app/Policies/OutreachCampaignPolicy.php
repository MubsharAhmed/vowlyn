<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OutreachCampaign;
use App\Models\User;
use App\Policies\Concerns\AllowsVerifiedAdmins;

final class OutreachCampaignPolicy
{
    use AllowsVerifiedAdmins;

    public function view(User $user, OutreachCampaign $campaign): bool
    {
        return $this->admin($user);
    }

    public function update(User $user, OutreachCampaign $campaign): bool
    {
        return $this->admin($user);
    }

    public function delete(User $user, OutreachCampaign $campaign): bool
    {
        return $this->admin($user);
    }
}
