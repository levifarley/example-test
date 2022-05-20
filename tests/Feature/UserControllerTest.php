<?php

use App\Http\Controllers\UserController;
use App\Services\UserService;

beforeEach(function () {
    // Get access to protected property
    $this->controller = new UserController();
    $reflectedClass = new ReflectionClass('App\Http\Controllers\UserController');
    $userService = $reflectedClass->getProperty('userService');
    $userService->setAccessible(true);

    // Mock the UserService - we are only concerned with testing the controller methods here
    $mockService = Mockery::mock(UserService::class);
    $mockService->shouldReceive('getUsers')->andReturn(collect(['test' => 'data']));
    $mockService->shouldReceive('updateStoredData')->andReturn('Update complete.');
    $userService->setValue($this->controller, $mockService);
});

it('returns users data', function () {
    // Ensure method returns collection
    $result = $this->controller->index();
    $this->assertJson($result->getContent());
});

it('updates stored users data', function () {
    // Ensure method returns string response
    $this->assertEquals('Update complete.', $this->controller->update());
});