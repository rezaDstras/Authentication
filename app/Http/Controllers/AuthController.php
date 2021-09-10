<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'lastName' => 'required|string',
            'gender' => 'boolean',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'lastName' => $data['lastName'],
            'gender'   => $data['gender'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $token = $user->createToken('TaskToken')->plainTextToken;

        $response = [
          'user'  =>   $user,
          'token' =>   $token
        ];

        return response()->json([
            'user'=>$response,
            "message" => "user has been registered successfully"
        ],Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        //Check Email
        $user = User::where('email',$data['email'])->first();

        //Check Password
        if (! $user || ! Hash::check($data['password'] , $user->password) ){
            return  response([
                'message' => 'Invalid Email Or Password'
            ],Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('TaskToken')->plainTextToken;

        $response = [
            'user'  =>   $user,
            'token' =>   $token
        ];

        return response($response,Response::HTTP_CREATED);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
          'message' => 'Logged Out'
        ];
    }
}
