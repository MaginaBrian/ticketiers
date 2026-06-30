<?php

namespace App\Policies;

use App\Models\TicketTier;
use App\Models\User;

/**
 * Permission-name assumption: a single role-agnostic permission per action,
 * registered via spatie/laravel-permission, e.g.:
 *   ticket-tiers.view, ticket-tiers.create, ticket-tiers.update, ticket-tiers.delete
 * No per-event ownership check is modelled here — any user holding the
 * permission may act on any tier. If event-ownership scoping is needed later,
 * it belongs in this policy, not the controller.
 */
class TicketTierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ticket-tiers.view');
    }

    public function view(User $user, TicketTier $ticketTier): bool
    {
        return $user->hasPermissionTo('ticket-tiers.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('ticket-tiers.create');
    }

    public function update(User $user, TicketTier $ticketTier): bool
    {
        return $user->hasPermissionTo('ticket-tiers.update');
    }

    public function delete(User $user, TicketTier $ticketTier): bool
    {
        return $user->hasPermissionTo('ticket-tiers.delete');
    }

    public function publish(User $user, TicketTier $ticketTier): bool
    {
        return $user->hasPermissionTo('ticket-tiers.update');
    }
}
