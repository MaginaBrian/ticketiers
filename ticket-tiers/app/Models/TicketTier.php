<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketTier extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Allowed values for sales_channels. Kept here as a single source of
     * truth so both the Data class validation and any future UI can read it.
     */
    public const CHANNELS = ['web', 'box_office', 'partner', 'mobile_app'];

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quantity',
        'sales_channels',
        'is_published',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'sales_channels' => 'array',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeForEvent(Builder $query, int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * NULL sales_channels means "sold on every channel".
     * Otherwise the channel must be present in the JSON array.
     */
    public function scopeAvailableOnChannel(Builder $query, string $channel): Builder
    {
        return $query->where(function (Builder $query) use ($channel) {
            $query->whereNull('sales_channels')
                ->orWhereJsonContains('sales_channels', $channel);
        });
    }
}
