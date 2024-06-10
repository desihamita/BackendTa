<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\AuthRequest;
use App\Models\SalesManager;
use App\Models\User;

class AuthController extends Controller
{
    public const ADMIN_USER = 1;
    public const SALES_MANAGER_USER = 2;

    final public function login(AuthRequest $request):JsonResponse
    {
        if ($request->input('user_type') == self::ADMIN_USER) {
            $user = (new User())->getUserByEmailOrPhone($request->all());
            $role = self::ADMIN_USER;
        } else {
            $user = (new SalesManager())->getUserByEmailOrPhone($request->all());
            $role = self::SALES_MANAGER_USER;
        }

        if($user && Hash::check($request->input('password'), $user->password)) {
            $user_data['token'] = $user->createToken($user->email)->plainTextToken;
            $user_data['name'] = $user->name;
            $user_data['phone'] = $user->phone;
            $user_data['photo'] = $user->photo;
            $user_data['email'] = $user->email;
            $user_data['role'] = $role;
            return response()->json($user_data);
        }
        throw ValidationException::withMessages([
            'email' => ['The Provided credentials are incorrect']
        ]);
    }

    final public function logout():JsonResponse
    {
        auth()->user()->tokens()->delete();
        return response()->json(['msg' => 'You have successfuly logged out']);
    }
}