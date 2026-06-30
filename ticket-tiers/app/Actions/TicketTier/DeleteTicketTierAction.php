<?php

namespace App\Actions\TicketTier;

use App\Models\TicketTier;

class DeleteTicketTierAction
{
    public function execute(TicketTier $ticketTier): void
    {
        $this->guardAgainstAlreadyDeleted($ticketTier);

        $ticketTier->delete();
    }

    private function guardAgainstAlreadyDeleted(TicketTier $ticketTier): void
    {
        if ($ticketTier->trashed()) {
            throw new \RuntimeException('Ticket tier is already deleted.');
        }
    }
}
