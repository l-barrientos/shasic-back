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
        $newUser = new User;
        if (User::where('email', $request->email)->first() != null) {
            return response('Email already used', 400);
        }
        if (User::where('userName', $request->userName)->first() != null) {
            return response('Username already used', 400);
        }
        if (Artist::where('email', $request->email)->first() != null) {
            return response('Email already used', 400);
        }
        if (Artist::where('userName', $request->userName)->first() != null) {
            return response('Username already used', 400);
        }
        $newUser->userName = $request->userName;
        $newUser->email = $request->email;
        $newUser->fullName = $request->fullName;
        $newUser->password = PASSWORD_HASH($request->password, PASSWORD_DEFAULT);
        $newUser->access_token = PASSWORD_HASH($request->email . $request->password . $request->userName, PASSWORD_DEFAULT);
        $newUser->profileImage = 'default.png';
        $newUser->save();

        return response(json_encode([
            "rol" => "user",
            "access_token" => $newUser->access_token
        ]), 200);
    }
}
