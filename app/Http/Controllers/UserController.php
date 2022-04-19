<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller {

    /*
     *
     * Create new user
     * 
     */
    public function create(Request $request) {
        $newUser = new User;
        $newUser->userName = $request->userName;
        $newUser->email = $request->email;
        $newUser->fullName = $request->fullName;
        $newUser->password = PASSWORD_HASH($request->password, PASSWORD_DEFAULT);
        $newUser->profileImage = 'default.png';
        $newUser->save();

        return response(json_encode([
            'userName' => $request->userName,
            'email' => $request->email,
            'fullName' => $request->fullName
        ]), 200);
    }


    public function login(Request $request) {
        $answer = [];
        $user = User::where('email', $request->email)->first();
        if ($user == null) {
            return response('User does not exist', 410);
        } else {
            if (PASSWORD_VERIFY($request->password, $user->password)) {
                $token = PASSWORD_HASH($request->email . $request->password, PASSWORD_DEFAULT);
                return response(json_encode([
                    "token" => $token
                ]), 200);
            } else {
                return response('Wrong password', 403);
            }
        }
    }
}
