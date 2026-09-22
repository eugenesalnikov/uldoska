<?php

namespace App\Services;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Str;

final readonly class PhotoOwnerToken
{
    public function __construct(
        private Session $session,
    )
    {
    }

    public function get(): string
    {
        return $this->session->remember(
            'photo_owner_token',
            fn() => (string)Str::uuid(),
        );
    }

}
