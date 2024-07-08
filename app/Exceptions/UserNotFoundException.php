<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UserNotFoundException extends Exception
{
    protected int $userId;
    public function __construct($userId)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public function report(): bool
    {
        return false;
    }

    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => "The User ID: $this->userId is not found.",
        ], 404);
    }
}
