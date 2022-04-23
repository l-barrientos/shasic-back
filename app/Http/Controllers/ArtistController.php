<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Models\Artist_Event_Performance;
use App\Models\User;
use App\Models\User_Artist_Follow;
use Illuminate\Http\Request;

class ArtistController extends Controller {

    /*
     *
     * Create new artist
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

        $newArtist = new Artist;
        $newArtist->userName = $request->userName;
        $newArtist->email = $request->email;
        $newArtist->fullName = $request->fullName;
        $newArtist->password = PASSWORD_HASH($request->password, PASSWORD_DEFAULT);
        $newArtist->access_token = PASSWORD_HASH($request->email . $request->password . $request->userName, PASSWORD_DEFAULT);
        $newArtist->profileImage = 'default-user.png';
        $newArtist->save();

        return response(json_encode([
            "rol" => 'artist',
            "access_token" => $newArtist->access_token
        ]));
    }

    /*
     *
     * Get Artists by User
     * 
     */
    public function getArtistsByUser(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $artistsUser = User_Artist_Follow::where('user_id', $user->id)->get();
        $artists = [];
        foreach ($artistsUser as $artist) {
            $artistObj = Artist::find($artist->artist_id)->makeHidden(['password', 'access_token', 'created_at', 'updated_at']);
            $eventsNum = Artist_Event_Performance::where('artist_id', $artist->artist_id)->count();
            $artistObj['eventsNum'] = $eventsNum;
            array_push($artists, $artistObj);
        }
        return $artists;
    }

    public function saveImg(Request $request) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imgPath = $request->image->store('public/img');
            $artist->profileImage = str_replace('public/img/', '', $imgPath);
            $artist->save();
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
