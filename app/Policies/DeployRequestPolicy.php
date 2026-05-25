<?php

namespace App\Policies;

use App\Models\DeployRequest;
use App\Models\User;

class DeployRequestPolicy
{
    /** Programmer hanya bisa update request miliknya yang masih pending */
    public function update(User $user, DeployRequest $deployRequest): bool
    {
        return $user->isProgrammer()
            && $deployRequest->requester_id === $user->id
            && $deployRequest->isPending();
    }

    /** Hanya Project Manager yang bisa approve/reject */
    public function decide(User $user, DeployRequest $deployRequest): bool
    {
        return $user->isProjectManager() && $deployRequest->isPending();
    }
}
