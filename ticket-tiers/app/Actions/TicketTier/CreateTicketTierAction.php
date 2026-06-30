<?php

namespace App\Actions\TicketTier;

use App\Data\CreateTicketTierData;
use App\Models\TicketTier;

class CreateTicketTierAction
{
    public function execute(CreateTicketTierData $data): TicketTier
    {
        return TicketTier::create($this->toAttributes($data));
    }

    private function toAttributes(CreateTicketTierData $data): array
    {
        return [
            'event_id' => $data->event_id,
            'name' => $data->name,
            'price' => $data->price,
            'quantity' => $data->quantity,
            'sales_channels' => $data->sales_channels instanceof \Spatie\LaravelData\Optional
                ? null
                : $data->sales_channels,
            'is_active' => $data->is_active instanceof \Spatie\LaravelData\Optional
                ? true
                : $data->is_active,
        ];
    }
}
