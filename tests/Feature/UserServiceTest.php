<?php

use App\Models\ApiUser;
use App\Services\ApiService;
use App\Services\UserService;

beforeEach(function () {
    // Setup
    $this->service = new UserService();
    $this->reflectedClass = new ReflectionClass(UserService::class);

    // Get access to protected methods and properties
    $this->getUpdatedUsersData = $this->reflectedClass->getMethod('getUpdatedUsersData');
    $this->getUpdatedUsersData->setAccessible(true);

    $this->saveToDb = $this->reflectedClass->getMethod('saveUsersToDatabase');
    $this->saveToDb->setAccessible(true);

    $this->saveToFile = $this->reflectedClass->getMethod('saveUsersToFile');
    $this->saveToFile->setAccessible(true);

    $model = $this->reflectedClass->getProperty('model');
    $model->setAccessible(true);

    // Mock model so we don't make any table modifications
    $mockModel = Mockery::mock(ApiUser::class);
    $mockModel->shouldReceive('all')->andReturn(collect()); // No users
    $mockModel->shouldReceive('create')->andReturn(null);

    // Set our mock model
    $model->setValue($this->service, $mockModel);

    // Set test user data
    $this->testUser = collect([
        [
            'createdAt' => '2022-02-06T20:25:51.966Z',
            'first_name' => 'Bobby',
            'last_name' => 'Tables',
            'address' => '123 Test Ave',
            'job_title' => 'Developer',
            'id' => '1'
        ]
    ]);
});

it('gets updated users data from external API', function () {
    // Ensure data collection returned (not null) - Only need to check first page
    $this->assertIsObject($this->getUpdatedUsersData->invoke($this->service));
});

it('updates stored users data', function () {
    // Test successful update
    $this->assertEquals('Update complete.', $this->service->updateStoredData('testfile'));

    // Mock the API service so we don't make any calls
    $mockApiService = Mockery::mock(ApiService::class);
    $mockApiService->shouldReceive('getUsers')->andReturn(collect());

    // Set mock API service
    $apiService = $this->reflectedClass->getProperty('apiService');
    $apiService->setAccessible(true);
    $apiService->setValue($this->service, $mockApiService);

    $this->assertEquals('Update unsuccessful : could not retrieve required users data', $this->service->updateStoredData('testfile'));
});

it('updates database with fresh users data', function () {
    // Test our method
    $this->assertNull($this->saveToDb->invoke($this->service, $this->testUser));
});

it('creates a stored CSV file with fresh users data', function () {
    // Write test data to a test file
    $this->saveToFile->invoke($this->service, $this->testUser, 'testfile');

    // Ensure file was created
    $this->assertTrue(file_exists('testfile.csv'));
});

it('gets stored users data and formats it for consumption', function () {
    $this->assertIsObject($this->service->getUsers());
});

afterAll(function () {
    // Remove test file
    unlink('testfile.csv');
});