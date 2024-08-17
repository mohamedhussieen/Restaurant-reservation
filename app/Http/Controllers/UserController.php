<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\UserasAuthenticatable;


class UserController extends Controller{
    use ApiResponse;
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return$this->success([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        }

        return$this->error('The provided credentials are incorrect.', 401);
    }
}
