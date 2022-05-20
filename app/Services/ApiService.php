<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ApiService
{
    protected $url;

    public function __construct()
    {
        $this->url = config('services.mock_api.endpoint');
    }

    /**
     * Get users data from Mock Api
     *
     * @param int $page
     * @param int $perPage
     * @return Collection|null
     */
    public function getUsers(int $page, int $perPage): ?Collection
    {
        // Make our request
        $response = Http::get($this->url . '/v1/user/users?page=' . $page . '&limit=' . $perPage);

        // Collect response
        $responseCollection = collect(json_decode($response->body(), true));

        // If response is successful return the data collection
        return $response->successful() ? $responseCollection : null;
    }
}