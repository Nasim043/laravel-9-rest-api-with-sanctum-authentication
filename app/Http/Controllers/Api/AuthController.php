<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    /**
     * Create User
     * @param Request $request
     * @return User
     *
     */
    public function createUser(Request $request) {

        try {
            // Validate user
            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'NOTOK',
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 401);
            }

            // Create User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status'  => 'OK',
                'message' => 'User Createad Successfully',
                'token'   => $user->createToken('API_TOKEN')->plainTextToken,
            ], 401);

        } catch (\Throwable$th) {
            return response()->json([
                'status'  => 'NOTOK',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function loginUser(Request $request) {
        try {
            // Validate user
            // 401 Unauthorized
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'NOTOK',
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status'  => 'OK',
                'message' => 'User Logged In Successfully',
                'token'   => $user->createToken('API_TOKEN')->plainTextToken,
            ], 200);

        } catch (\Throwable$th) {
            return response()->json([
                'status'  => 'NOTOK',
                'message' => $th->getMessage(),
            ]);
        }
    }
}
