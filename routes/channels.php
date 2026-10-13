<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->user_id === (int) $id;
});

Broadcast::channel('admin-notifications', function (User $user): bool {
    if (! $user->is_active) {
        return false;
    }

    return count(array_intersect(
        $user->effectivePermissionSlugs(),
        ['pengelolaan-surat', 'verifikasi-surat', 'pengaduan'],
    )) > 0;
});
