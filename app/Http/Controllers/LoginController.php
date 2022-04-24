<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller {
    /*
     *
     * Login as user
     * 
     */
    public function login(Request $request) {
        $user = User::where('email', $request->email)->first();
        $artist = Artist::where('email', $request->email)->first();
        if ($user == null) {
            if ($artist == null) {
                return response('invalidCredentials', 400);
            } else {
                if (PASSWORD_VERIFY($request->password, $artist->password)) {

                    return response(json_encode([
                        "rol" => "artist",
                        "access_token" => $artist->access_token
                    ]));
                } else {
                    return response('invalidCredentials', 400);
                }
            }
        } else {
            if (PASSWORD_VERIFY($request->password, $user->password)) {

                return response(json_encode([
                    "rol" => "user",
                    "access_token" => $user->access_token
                ]));
            } else {
                return response('invalidCredentials', 400);
            }
        }
    }

    public function autoLogin(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        if ($user != null) {
            return response(json_encode([
                "rol" => "user"
            ]));
        } else {
            $artist = Artist::where('access_token', $request->header('access_token'))->first();
            if ($artist != null) {
                return response(json_encode([
                    "rol" => "artist"
                ]));
            } else {
                return response('', 403);
            }
        }
    }
}
