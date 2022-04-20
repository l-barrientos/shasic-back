<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ArtistController extends Controller {
    /*
     *
     * Create new artist
     * 
     */
    public function create(Request $request) {
        $newArtist = new Artist;
        if (Artist::where('email', $request->email)->first() != null) {
            return response('Email already used', 400);
        }
        if (Artist::where('userName', $request->userName)->first() != null) {
            return response('Username already used', 400);
        }
        if (User::where('email', $request->email)->first() != null) {
            return response('Email already used', 400);
        }
        if (User::where('userName', $request->userName)->first() != null) {
            return response('Username already used', 400);
        }
        $newArtist->userName = $request->userName;
        $newArtist->email = $request->email;
        $newArtist->fullName = $request->fullName;
        $newArtist->password = PASSWORD_HASH($request->password, PASSWORD_DEFAULT);
        $newArtist->access_token = PASSWORD_HASH($request->email . $request->password . $request->userName, PASSWORD_DEFAULT);
        $newArtist->profileImage = 'default.png';
        $newArtist->save();

        return response(json_encode([
            "rol" => 'artist',
            "access_token" => $newArtist->access_token
        ]), 200);
    }
}
