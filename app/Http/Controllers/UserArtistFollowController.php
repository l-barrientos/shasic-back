<?php

namespace App\Http\Controllers;

use App\Models\User_Artist_Follow;
use App\Http\Requests\StoreUser_Artist_FollowRequest;
use App\Http\Requests\UpdateUser_Artist_FollowRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserArtistFollowController extends Controller {
    public function unfollowArtist($id, Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $artistUser =  User_Artist_Follow::where('artist_id', $id)->where('user_id', $user->id)->first();
        $artistUser->delete();
        if ($artistUser != null) {
            $artistUser->delete();
            return response([
                "following" => false
            ]);
        } else {
            return response('', 404);
        }
    }

    public function followArtist($id, Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $artistUserTest =  User_Artist_Follow::where('artist_id', $id)->where('user_id', $user->id)->first();
        if ($artistUserTest == null) {
            $artistUser = new User_Artist_Follow;
            $artistUser->artist_id = $id;
            $artistUser->user_id = $user->id;
            $artistUser->save();
            return response([
                "following" => true
            ]);
        } else {
            return response('', 400);
        }
    }
}
