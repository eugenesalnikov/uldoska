<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Contracts\Session\Session;

final readonly class ManagedListings
{
    private const string KEY = 'manage.listings';

    public function __construct(
        private Session $session,
    )
    {
    }

    public function allow(Listing $listing): void
    {
        $ids = $this->ids();
        $ids[] = (int)$listing->id;

        $this->session->put(
            self::KEY,
            $ids
                |> array_unique(...)
                |> array_values(...),
        );
    }

    public function contains(Listing $listing): bool
    {
        return in_array((int)$listing->id, $this->ids(), true);
    }

    /**
     * @return list<int>
     */
    public function ids(): array
    {
        return collect($this->session->get(self::KEY, []))
            ->map(fn($id) => (int)$id)
            ->all();
    }

}
