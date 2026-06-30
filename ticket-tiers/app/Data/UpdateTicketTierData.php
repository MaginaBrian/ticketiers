<?php

namespace App\Data;

use App\Models\TicketTier;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdateTicketTierData extends Data
{
    public function __construct(
        public int|Optional $event_id,
        public string|Optional $name,
        public float|Optional $price,
        public int|Optional $quantity,
        /** @var Optional|array<int, string>|null */
        public array|Optional|null $sales_channels,
        public bool|Optional $is_active,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        /** @var TicketTier|null $ticketTier */
        $ticketTier = $context->payload['__ticketTier'] ?? null;

        // event_id may not be present on a partial update; uniqueness must
        // still be scoped to whichever event the tier ends up belonging to.
        $eventId = $context->payload['event_id'] ?? $ticketTier?->event_id;

        return [
            'event_id' => ['sometimes', 'integer', 'exists:events,id'],
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('ticket_tiers', 'name')
                    ->where('event_id', $eventId)
                    ->whereNull('deleted_at')
                    ->ignore($ticketTier?->id),
            ],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'sales_channels' => ['nullable', 'array'],
            'sales_channels.*' => [Rule::in(TicketTier::CHANNELS)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
