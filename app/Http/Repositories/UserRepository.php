<?php


namespace App\Http\Repositories;


use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

class UserRepository
{
    public function profile()
    {
        return Auth::user();
    }

    public function update(UserRequest $request,$user)
    {

        if (!$user) {
            return Response::json(['message' => 'Id not found'], Response::HTTP_NOT_FOUND);
        }
        if ($request->hasFile('avatar')) {
            $image_tmp = $request->file('avatar');
            if ($image_tmp->isValid()) {
                //get image extension
                $extension = $image_tmp->getClientOriginalExtension();
                //generate new name
                $imageName = Auth::user()->getAuthIdentifier();
                $avatarName = rand(111,999) . '.' . $extension;
                $avatarPath = 'image/' . $avatarName;
            }
         //upload the file
         Image::make($image_tmp)->save($avatarPath);
        }else{
            $avatarPath = $user['avatar'];
        }

        if (!empty($request['name']) ? $name = $request['name'] : $name = $user['name'] );
        if (!empty($request['lastName']) ? $lastName = $request['lastName'] : $lastName= $user['lastName']);
        if ($request["gender"] == 1 ? $gender = 1 : $gender = 0);

        $user->update([
            'name' => $name,
            'lastName' => $lastName,
            'gender' => $gender,
            'avatar' => $avatarPath
        ]);
    }

    public function changePassword(UserRequest $request,$user)
    {
        $user->update([
            'password' => bcrypt($request['newPassword'])
        ]);
    }
}
