<?php

namespace App\Http\Controllers\Api;

use App\Actions\TicketTier\CreateTicketTierAction;
use App\Actions\TicketTier\DeleteTicketTierAction;
use App\Actions\TicketTier\PublishTicketTierAction;
use App\Actions\TicketTier\UpdateTicketTierAction;
use App\Data\CreateTicketTierData;
use App\Data\UpdateTicketTierData;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\TicketTierResource;
use App\Models\TicketTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketTierController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', TicketTier::class);

        $ticketTiers = QueryBuilder::for(TicketTier::class)
            ->allowedFilters([
                'event_id',
                AllowedFilter::callback('channel', function ($query, $value) {
                    $query->availableOnChannel($value);
                }),
            ])
            ->allowedSorts(['name', 'price', 'created_at'])
            ->allowedIncludes(['event'])
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 15));

        return TicketTierResource::collection($ticketTiers);
    }

    public function store(Request $request)
    {
        $this->authorize('create', TicketTier::class);

        $data = CreateTicketTierData::validateAndCreate($request->all());

        try {
            DB::beginTransaction();

            $ticketTier = (new CreateTicketTierAction())->execute($data);

            DB::commit();
        } catch (ValidationException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Failed to create ticket tier.', [
                'exception' => $exception->getMessage(),
                'payload' => $request->all(),
            ]);

            throw ValidationException::withMessages([
                'ticket_tier' => __('ticket_tiers.generic_error'),
            ]);
        }

        return (new ApiResponseResource(
            new TicketTierResource($ticketTier),
            __('ticket_tiers.created')
        ))->response()->setStatusCode(201);
    }

    public function show(TicketTier $ticketTier)
    {
        $this->authorize('view', $ticketTier);

        return new TicketTierResource($ticketTier);
    }

    public function update(Request $request, TicketTier $ticketTier)
    {
        $this->authorize('update', $ticketTier);

        // The Data class needs the existing model to scope the per-event
        // uniqueness rule correctly on partial updates; it is stripped out
        // before being read as a normal attribute by the Data class.
        $payload = array_merge($request->all(), ['__ticketTier' => $ticketTier]);
        $data = UpdateTicketTierData::validateAndCreate($payload);

        try {
            DB::beginTransaction();

            $ticketTier = (new UpdateTicketTierAction())->execute($ticketTier, $data);

            DB::commit();
        } catch (ValidationException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Failed to update ticket tier.', [
                'ticket_tier_id' => $ticketTier->id,
                'exception' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'ticket_tier' => __('ticket_tiers.generic_error'),
            ]);
        }

        return new ApiResponseResource(
            new TicketTierResource($ticketTier),
            __('ticket_tiers.updated')
        );
    }

    public function destroy(TicketTier $ticketTier)
    {
        $this->authorize('delete', $ticketTier);

        try {
            DB::beginTransaction();

            (new DeleteTicketTierAction())->execute($ticketTier);

            DB::commit();
        } catch (ValidationException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Failed to delete ticket tier.', [
                'ticket_tier_id' => $ticketTier->id,
                'exception' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'ticket_tier' => __('ticket_tiers.generic_error'),
            ]);
        }

        return new ApiResponseResource(null, __('ticket_tiers.deleted'));
    }

    public function publish(TicketTier $ticketTier)
    {
        $this->authorize('publish', $ticketTier);

        try {
            DB::beginTransaction();

            $ticketTier = (new PublishTicketTierAction())->execute($ticketTier);

            DB::commit();
        } catch (ValidationException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Failed to publish ticket tier.', [
                'ticket_tier_id' => $ticketTier->id,
                'exception' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'ticket_tier' => __('ticket_tiers.generic_error'),
            ]);
        }

        return new ApiResponseResource(
            new TicketTierResource($ticketTier),
            __('ticket_tiers.published')
        );
    }
}
