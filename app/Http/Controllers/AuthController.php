<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ShopListResource;
use App\Http\Requests\AuthRequest;
use App\Models\SalesManager;
use App\Models\User;
use App\Models\Shop;

class AuthController extends Controller
{
    public const ADMIN_USER = 1;
    public const SALES_MANAGER_USER = 2;

    final public function login(AuthRequest $request): JsonResponse
    {
        if ($request->input('user_type') == self::ADMIN_USER) {
            $user = (new User())->getUserByEmailOrPhone($request->all());
            $role = self::ADMIN_USER;
        } else {
            $user = (new SalesManager())->getUserByEmailOrPhone($request->all());
            $role = self::SALES_MANAGER_USER;
        }

        if ($user) {
            if (Hash::check($request->input('password'), $user->password)) {
                $branch = null;

                if ($role === self::SALES_MANAGER_USER && $user->shop_id) {
                    $branch = (new Shop())->getShopDetailsById($user->shop_id);
                }

                $user_data['token'] = $user->createToken($user->email)->plainTextToken;
                $user_data['name'] = $user->name;
                $user_data['phone'] = $user->phone;
                $user_data['photo'] = $user->photo;
                $user_data['email'] = $user->email;
                $user_data['role'] = $role;
                $user_data['branch'] = $branch ? new ShopListResource($branch) : null;
                return response()->json($user_data);
            } else {
                throw ValidationException::withMessages([
                    'password' => ['The provided password is incorrect.']
                ]);
            }
        } else {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }
    }

    final public function logout(): JsonResponse
    {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json(['msg' => 'You have successfully logged out']);
        }

        return response()->json(['msg' => 'No authenticated user found'], 401);
    }
}