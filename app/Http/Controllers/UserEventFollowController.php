<?php

namespace App\Http\Controllers;

use App\Models\User_Event_Follow;
use App\Http\Requests\StoreUser_Event_FollowRequest;
use App\Http\Requests\UpdateUser_Event_FollowRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserEventFollowController extends Controller {
    public function unfollowEvent($id, Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $eventUser =  User_Event_Follow::where('event_id', $id)->where('user_id', $user->id)->first();
        $eventUser->delete();
        if ($eventUser != null) {
            $eventUser->delete();
            return response([
                "following" => false
            ]);
        } else {
            return response('', 404);
        }
    }

    public function followEvent($id, Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $eventUserTest =  User_Event_Follow::where('event_id', $id)->where('user_id', $user->id)->first();
        if ($eventUserTest == null) {
            $eventUser = new User_Event_Follow;
            $eventUser->event_id = $id;
            $eventUser->user_id = $user->id;
            $eventUser->save();
            return response([
                "following" => true
            ]);
        } else {
            return response('', 400);
        }
    }
}
