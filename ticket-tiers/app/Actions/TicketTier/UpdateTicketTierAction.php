<?php

namespace App\Actions\TicketTier;

use App\Data\UpdateTicketTierData;
use App\Models\TicketTier;
use Spatie\LaravelData\Optional;

class UpdateTicketTierAction
{
    public function execute(TicketTier $ticketTier, UpdateTicketTierData $data): TicketTier
    {
        $ticketTier->fill($this->toAttributes($data));
        $ticketTier->save();

        return $ticketTier->refresh();
    }

    /**
     * Only attributes that were actually present in the request are
     * included — fields left as Optional must not overwrite existing values.
     */
    private function toAttributes(UpdateTicketTierData $data): array
    {
        $attributes = [];

        foreach (get_object_vars($data) as $key => $value) {
            if (! $value instanceof Optional) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
