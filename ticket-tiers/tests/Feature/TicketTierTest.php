<?php

use App\Models\Event;
use App\Models\TicketTier;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    foreach ([
        'ticket-tiers.view',
        'ticket-tiers.create',
        'ticket-tiers.update',
        'ticket-tiers.delete',
    ] as $permission) {
        Permission::findOrCreate($permission);
    }

    $this->user = User::factory()->create();
    $this->user->givePermissionTo([
        'ticket-tiers.view',
        'ticket-tiers.create',
        'ticket-tiers.update',
        'ticket-tiers.delete',
    ]);

    $this->actingAs($this->user, 'sanctum');
});

it('creates a ticket tier', function () {
    $event = Event::factory()->create();

    $payload = [
        'event_id' => $event->id,
        'name' => 'Early Bird',
        'price' => 49.99,
        'quantity' => 100,
        'sales_channels' => ['web'],
    ];

    $response = $this->postJson('/api/ticket-tiers', $payload);

    $response->assertCreated();
    $response->assertJsonPath('message', __('ticket_tiers.created'));
    $response->assertJsonPath('data.name', 'Early Bird');

    $this->assertDatabaseHas('ticket_tiers', [
        'event_id' => $event->id,
        'name' => 'Early Bird',
        'quantity' => 100,
    ]);
});

it('enforces name uniqueness per event but allows the same name across events', function () {
    $eventA = Event::factory()->create();
    $eventB = Event::factory()->create();

    TicketTier::factory()->create(['event_id' => $eventA->id, 'name' => 'VIP']);

    $duplicateInSameEvent = $this->postJson('/api/ticket-tiers', [
        'event_id' => $eventA->id,
        'name' => 'VIP',
        'price' => 100,
        'quantity' => 10,
    ]);
    $duplicateInSameEvent->assertUnprocessable();
    $duplicateInSameEvent->assertJsonValidationErrors('name');

    $sameNameDifferentEvent = $this->postJson('/api/ticket-tiers', [
        'event_id' => $eventB->id,
        'name' => 'VIP',
        'price' => 100,
        'quantity' => 10,
    ]);
    $sameNameDifferentEvent->assertCreated();

    $this->assertDatabaseHas('ticket_tiers', [
        'event_id' => $eventB->id,
        'name' => 'VIP',
    ]);
});

it('returns all-channel and matching-channel tiers via availableOnChannel and excludes other channels', function () {
    $event = Event::factory()->create();

    $allChannels = TicketTier::factory()->for($event)->create(['sales_channels' => null]);
    $webOnly = TicketTier::factory()->for($event)->create(['sales_channels' => ['web']]);
    $boxOfficeOnly = TicketTier::factory()->for($event)->create(['sales_channels' => ['box_office']]);

    $results = TicketTier::query()->availableOnChannel('web')->pluck('id');

    expect($results)->toContain($allChannels->id, $webOnly->id);
    expect($results)->not->toContain($boxOfficeOnly->id);
});

it('publishes a ticket tier', function () {
    $tier = TicketTier::factory()->create(['is_published' => false]);

    $response = $this->patchJson("/api/ticket-tiers/{$tier->id}/publish");

    $response->assertOk();
    $response->assertJsonPath('data.is_published', true);

    $this->assertDatabaseHas('ticket_tiers', [
        'id' => $tier->id,
        'is_published' => true,
    ]);
});

it('soft deletes a ticket tier and excludes it from the index', function () {
    $tier = TicketTier::factory()->create();

    $response = $this->deleteJson("/api/ticket-tiers/{$tier->id}");
    $response->assertOk();
    $response->assertJsonPath('message', __('ticket_tiers.deleted'));

    $this->assertSoftDeleted('ticket_tiers', ['id' => $tier->id]);

    $index = $this->getJson('/api/ticket-tiers');
    $index->assertOk();
    $index->assertJsonMissing(['id' => $tier->id]);
});
