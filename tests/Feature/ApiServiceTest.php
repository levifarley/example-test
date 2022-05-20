<?php

use App\Services\ApiService;

beforeEach(function () {
    $this->service = new ApiService();
});

it('gets users data from API', function () {
    // Ensure data collection is returned
    $this->assertIsObject($this->service->getUsers(10, 10));
});
