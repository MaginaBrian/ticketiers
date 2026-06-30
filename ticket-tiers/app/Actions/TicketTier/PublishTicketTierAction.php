<?php

namespace App\Actions\TicketTier;

use App\Models\TicketTier;

class PublishTicketTierAction
{
    public function execute(TicketTier $ticketTier): TicketTier
    {
        if ($ticketTier->is_published) {
            return $ticketTier;
        }

        $this->markPublished($ticketTier);

        return $ticketTier->refresh();
    }

    private function markPublished(TicketTier $ticketTier): void
    {
        $ticketTier->update(['is_published' => true]);
    }
}
