<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Artist;
use Illuminate\Http\Request;

class UserController extends Controller {

    /*
     *
     * Create new user
     * 
     */
    public function create(Request $request) {
        if (
            Artist::where('email', $request->email)->first() != null ||
            User::where('email', $request->email)->first() != null
        ) {
            return response('emailUsed', 400);
        }
        if (
            Artist::where('userName', $request->userName)->first() != null ||
            User::where('userName', $request->userName)->first() != null
        ) {
            return response('userNameUsed', 400);
        }
        $newUser = new User;
        $newUser->userName = $request->userName;
        $newUser->email = $request->email;
        $newUser->fullName = $request->fullName;
        $newUser->password = PASSWORD_HASH($request->password, PASSWORD_DEFAULT);
        $newUser->access_token = PASSWORD_HASH($request->email . $request->password . $request->userName, PASSWORD_DEFAULT);
        $newUser->profileImage = 'default';
        $newUser->save();

        return response(json_encode([
            "rol" => "user",
            "access_token" => $newUser->access_token
        ]));
    }

    public function saveImg(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imgPath = $request->image->store('public/img');
            $user->profileImage = str_replace('public/img/', '', $imgPath);
            $user->save();
            return response(json_encode([
                "saved" => "OK",
                "path" => $imgPath
            ]));
        } else {
            return response(json_encode([
                "saved" => "ERROR"
            ]), 400);
        }
    }
}
