<?php

use App\Events\NotificationAvailable;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

it('broadcasts only safe metadata on the private admin channel', function () {
    $event = new NotificationAvailable('complaint', 42, 'created', '2026-10-06T14:30:00Z');

    expect($event->broadcastAs())->toBe('notification.available')
        ->and($event->broadcastOn()[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($event->broadcastOn()[0]->name)->toBe('private-admin-notifications')
        ->and($event->broadcastWith())->toBe([
            'type' => 'complaint',
            'id' => 42,
            'action' => 'created',
            'occurred_at' => '2026-10-06T14:30:00Z',
        ]);
});

it('broadcasts a refresh event when a public complaint is submitted', function () {
    Event::fake([NotificationAvailable::class]);

    $response = $this->postJson('/api/public/complaints', [
        'reporter_name' => null,
        'reporter_phone' => null,
        'category' => 'Infrastructure',
        'title' => 'Jalan lingkungan perlu diperbaiki',
        'description' => 'Terdapat lubang pada jalan lingkungan yang perlu ditangani.',
    ]);

    $response->assertCreated();
    $complaintId = (int) $response->json('data.complaint_id');
    Event::assertDispatched(NotificationAvailable::class, function (NotificationAvailable $event) use ($complaintId): bool {
        return $event->type === 'complaint'
            && $event->id === $complaintId
            && $event->action === 'created';
    });
});
