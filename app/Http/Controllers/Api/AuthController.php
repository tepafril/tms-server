<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'data' => [
                        'message' => 'validation error',
                    ],
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'data' => [
                        'message' => 'Email & Password does not match with our record.',
                    ],
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'data' => [
                    'message' => 'User Logged In Successfully',
                ],
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'data' => [
                    'message' => $th->getMessage()
                ],
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Get bearer token from the request
        $accessToken = $request->bearerToken();
        
        // Get access token from database
        $token = PersonalAccessToken::findToken($accessToken);

        // Revoke token
        $token->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function user(Request $request)
    {
        try{
            return response()->json(
                $request->user()
            , 200);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'data' => [
                    'message' => $th->getMessage()
                ],
            ], 500);
        }
    }

}
