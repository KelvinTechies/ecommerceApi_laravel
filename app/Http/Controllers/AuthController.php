<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /*  public function _construct()
    {
        $this->middleware('auth:api', ['except' => ['Login_in', 'register']]);
    } */
    public function register(Request $request)
    {
        /* $fields = $request->validate([
            'rName' => 'required|string',
            'rEmail' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);
 */

        $fields = Validator::make($request->all(), [
            'rName' => 'required|string',
            'rEmail' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);


        if ($fields->fails()) {
            return response()->json([
                'status' => 422,
                "errors" => $fields->messages()
            ]);
        } else {

            $user = User::create([
                'name' => $request->input('rName'),
                'email' => $request->input('rEmail'),
                'role' => 0,
                'password' => bcrypt($request->input('password')),
            ]);

            $token = $user->createToken('myAppToken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token,
                'status' => 201,
                'message' => "You Can Login in to your Account",
            ];

            return response($response);
        }
    }

    public function Login_in(Request $request)
    {
        $fields = $request->validate([
            'lEmail' => 'required|string',
            'lPwd' => 'required|string',
        ]);

        $user = User::where('email', $fields['lEmail'])->first();

        if (!$user || !Hash::check($fields['lPwd'], $user->password)) {
            return response([
                'message' => "User not Found",
                "status" => 401
            ]);
        }

        $token = $user->createToken('myAppToken')->plainTextToken;

        $response = [
            'user' => $user->name,
            'role' => $user->role,
            'token' => $token,
            "status" => 200
        ];
        return response($response);
    }



    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return $response = [
            'message' => 'Logged Out',
            'status' => 200
        ];
        return $response;
    }
}
