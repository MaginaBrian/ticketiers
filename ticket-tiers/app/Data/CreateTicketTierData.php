<?php

namespace App\Data;

use App\Models\TicketTier;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class CreateTicketTierData extends Data
{
    public function __construct(
        public int $event_id,
        public string $name,
        public float $price,
        public int $quantity,
        /** @var Optional|array<int, string>|null */
        public array|Optional|null $sales_channels,
        public bool|Optional $is_active,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        // event_id arrives in $context->payload before the rest of the
        // fields are cast, so we read it raw to scope the uniqueness check.
        $eventId = $context->payload['event_id'] ?? null;

        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ticket_tiers', 'name')
                    ->where('event_id', $eventId)
                    ->whereNull('deleted_at'),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'sales_channels' => ['nullable', 'array'],
            'sales_channels.*' => [Rule::in(TicketTier::CHANNELS)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
