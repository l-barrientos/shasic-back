<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Artist;
use App\Models\User_Event_Follow;
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
            $imgPath = $request->image->store('public/img/users');
            if ($user->profileImage != 'default') {
                unlink(public_path('storage/img/users/' . $user->profileImage));
            }
            $user->profileImage = str_replace('public/img/users/', '', $imgPath);
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

    public function getUsersByEvent(Request $request, $event_id) {
        $usersEvent = User_Event_Follow::where('event_id', $event_id)->get();
        $user = User::where('access_token', $request->header('access_token'))->first();

        $userId = $user->id;
        $users  = [];
        foreach ($usersEvent as $userEv) {
            if ($userEv->user_id != $userId) {
                $user =  User::find($userEv->user_id)->makeHidden('password', 'email', 'access_token');
                if ($user->profileImage != "default") {
                    $user->profileImage = asset('storage/img/users/' . $user->profileImage);
                }
                array_push($users, $user);
            }
        }
        return response($users);
    }

    public function getUserProfile(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first()->makeHidden('password', 'access_token');
        if ($user->profileImage != "default") {
            $user->profileImage = asset('storage/img/users/' . $user->profileImage);
        }
        return response($user);
    }

    public function updateProfile(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        if (
            (Artist::where('userName', $request->userName)->first() != null ||
                User::where('userName', $request->userName)->first() != null) && $request->userName != $user->userName
        ) {
            return response('userNameUsed', 400);
        }
        if (
            (Artist::where('email', $request->email)->first() != null ||
                User::where('email', $request->email)->first() != null) && $request->email != $user->email
        ) {
            return response('emailUsed', 400);
        }
        $user->userName = $request->userName;
        $user->email = $request->email;
        $user->fullName = $request->fullName;
        $user->description = $request->description;
        $user->save();
        return response([
            "updated" => "OK"
        ]);
    }

    public function updatePassword(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        if (!password_verify($request->oldPassword, $user->password)) {
            return response('wrongPassword', 403);
        }
        $user->password = password_hash($request->newPassword, PASSWORD_DEFAULT);
        $user->save();
        return response(['passwordUpdated' => 'OK']);
    }
}
