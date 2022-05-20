<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ApiUser;
use Illuminate\Support\Collection;

class UserService
{
    protected $apiService;
    protected $model;

    public function __construct()
    {
        $this->apiService = new ApiService();
        $this->model = new ApiUser();
    }

    /**
     * Return all users data
     *
     * @return Collection
     */
    public function getUsers(): Collection
    {
        // Get users data from database and remove timestamps
        return collect($this->model::all())->forget(['created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * Update stored users data
     *
     * @param string $fileName
     * @return string
     */
    public function updateStoredData(string $fileName = 'users'): string
    {
        // Get users data from API and process to database and csv file
        $users = $this->getUpdatedUsersData();

        // Handle if we can't get the data we need
        if (is_null($users)) {
            return 'Update unsuccessful : could not retrieve required users data';
        }

        // Update database with updated users data
        $this->saveUsersToDatabase($users);

        // Save users data to CSV file
        $this->saveUsersToFile($users, $fileName);

        return 'Update complete.';
    }

    /**
     * Get updated users data from API
     *
     * @return Collection|null
     */
    protected function getUpdatedUsersData(): ?Collection
    {
        // Start with page 1, 10 users per page
        $page = 1;
        $perPage = 10;
        $usersCollection = collect();

        // Get updated users data from API (first 10 users)
        $data = $this->apiService->getUsers($page, $perPage);

        // If data is successfully returned
        while ($data->isNotEmpty()) {
            // Add data to our users collection
            $usersCollection->push($data);

            // Next page
            $page++;

            // Keep collecting users
            $data = $this->apiService->getUsers($page, $perPage);
        }

        // Return all users data or fallback to null
        return $usersCollection->isNotEmpty() ? $usersCollection->flatten(1)->sortBy('id') : null;
    }

    /**
     * Update api_users table in our database
     *
     * @param Collection $users
     */
    protected function saveUsersToDatabase(Collection $users): void
    {
        // Get any existing users that we have
        $existingUsers = collect($this->model::all())->pluck('id');

        /*
        * If user doesn't already exists in stored data, add them to it. Normally we would compare each existing
        * record to the fresh data and update the existing records as needed, but we will keep this simple and just
        * look for new users we don't already have.
        */
        foreach ($users as $user) {
            // Collect user data
            $user = collect($user);

            // Add new record if user does not already exist in our data
            if (!$existingUsers->contains($user->get('id'))) {
                $this->model::create($user->toArray());
            }
        }
    }

    /**
     * Save data to CSV file
     *
     * @param Collection $users
     * @param string $fileName
     */
    protected function saveUsersToFile(Collection $users, string $fileName): void
    {
        // Open a CSV file
        $file = fopen($fileName . '.csv', 'w');

        // Loop through users data
        foreach ($users as $user) {
            // Add to CSV file ensuring data is passed as an array
            fputcsv($file, collect($user)->toArray());
        }

        // All done. Normally would have additional checks to ensure file created properly and nicely formatted.
        fclose($file);
    }
}