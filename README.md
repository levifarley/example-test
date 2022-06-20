# Example Practical Test

## Problem to Solve

To start, we will need to get the paginated results from an API every 24 hours.  Then, save that data to a file and process it into a database.  Once that's complete, provide the ability retrieve those results using an API endpoint. Please provide tests for code written as well.

## Solution

The `/users` route in `/routes/api.php` hits the `/app/Http/Controllers/UserController` to retrieve a list of users from a local database. The `UserController` uses a `UserService` class that handles retrieving updated users data from an external API using an `ApiService` class, saving that data to a local database table, writing that data to a local `.csv` file, and formatting and displaying the data as an API endpoint. The `UpdateUsers` console command to update the local table data is triggered by the scheduler in Laravel to run once per day.

Run `pest --coverage` to run tests.
