<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Get stored users data
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Return user data from db
        return response()->json($this->userService->getUsers());
    }

    /**
     * Update stored users data
     */
    public function update(): string
    {
        // TODO: Handle when can't get data (null)
        return $this->userService->updateStoredData();
    }
}
