<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'contact_number' => 'required|string|max:20',
            'address'        => 'required|string|max:255',
            'password'       => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'           => $request->full_name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
            'password'       => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}