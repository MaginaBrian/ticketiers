<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Single wrapper used by every mutating endpoint so the API always replies
 * with the same shape: { "message": "...", "data": {...} }.
 *
 * Usage: (new ApiResponseResource($resource, __('ticket_tiers.created')))
 */
class ApiResponseResource extends JsonResource
{
    public function __construct(
        $resource,
        private readonly string $message,
    ) {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'message' => $this->message,
            'data' => $this->resource,
        ];
    }
}
