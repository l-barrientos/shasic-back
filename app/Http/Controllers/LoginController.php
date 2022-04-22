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
                    ]), 200);
                } else {
                    return response('invalidCredentials', 400);
                }
            }
        } else {
            if (PASSWORD_VERIFY($request->password, $user->password)) {

                return response(json_encode([
                    "rol" => "user",
                    "access_token" => $user->access_token
                ]), 200);
            } else {
                return response('invalidCredentials', 400);
            }
        }
    }
}
