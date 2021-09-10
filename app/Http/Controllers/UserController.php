<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display user detail .
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = resolve(UserRepository::class)->profile();
        return response([
            'user'=>$user
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request)
    {
        //find Logged in user
        $user = User::find(Auth::user()->getAuthIdentifier());

        //Use UserRepository
        resolve(UserRepository::class)->update($request , $user);

        return response()->json([
            'user'=>$user,
            'message' => 'user updated successfully'
        ],Response::HTTP_OK);
    }

    public function changePassword(UserRequest $request)
    {
        //Find Logged in user
        $user = User::find(Auth::user()->getAuthIdentifier());
        if (! $user || ! Hash::check($request['oldPassword'] , $user->password)){
            return  response([
                'message' => 'Old Password is incorrect'
            ],Response::HTTP_UNAUTHORIZED);
        }
        //Use UserRepository
        resolve(UserRepository::class)->changePassword($request,$user);
        return response()->json([
            "message" => "Password has been updated successfully"
        ],Response::HTTP_FOUND);
    }

    /**
     * Remove user from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $user =User::find(Auth::user()->getAuthIdentifier());
        $user ->delete();

        return response()->json([
            'message' => 'user has been deleted successfully'
        ],Response::HTTP_OK);
    }
}
