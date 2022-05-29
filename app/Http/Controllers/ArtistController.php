<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Models\Artist_Event_Performance;
use App\Models\Event;
use App\Models\User;
use App\Models\User_Artist_Follow;
use Illuminate\Http\Request;

class ArtistController extends Controller {

    /**
     * Cretae new artists
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
        $newArtist->profileImage = 'default';
        $newArtist->save();

        return response(json_encode([
            "rol" => 'artist',
            "access_token" => $newArtist->access_token
        ]));
    }

    /**
     * Get all artists
     */
    public function getAllArtists(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $artists = Artist::all();
        foreach ($artists as $artist) {
            if ($artist->profileImage != 'default') {
                $artist->profileImage = asset('storage/img/artists/' . $artist->profileImage);
            }
            $artistsEventsRelation = Artist_Event_Performance::where('artist_id', $artist->id)->get();
            $events = [];
            foreach ($artistsEventsRelation as $artEvt) {
                $event = Event::find($artEvt->event_id);
                $event->eventImage = asset('storage/img/events/' . $event->eventImage);
                array_push($events, $event);
            }
            $artist['events'] = $events;
            $artist['followers'] = User_Artist_Follow::where('artist_id', $artist->id)->count();
            $userArtist = User_Artist_Follow::where('artist_id', $artist->id)->where('user_id', $user->id)->first();
            $artist['following'] = $userArtist != null ? true : false;
        }
        return $artists;
    }

    /**
     * Get artists by user
     */
    public function getArtistsByUser(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $artistsUser = User_Artist_Follow::where('user_id', $user->id)->get();
        $artists = [];
        foreach ($artistsUser as $artist) {
            $artistObj = Artist::find($artist->artist_id)->makeHidden(['password', 'access_token', 'created_at', 'updated_at']);
            if ($artistObj->profileImage != 'default') {
                $artistObj->profileImage = asset('storage/img/artists/' . $artistObj->profileImage);
            }
            $artistsEventsRelation = Artist_Event_Performance::where('artist_id', $artist->artist_id)->get();
            $events = [];
            foreach ($artistsEventsRelation as $artEvt) {
                $event = Event::find($artEvt->event_id);
                $event->eventImage = asset('storage/img/events/' . $event->eventImage);
                array_push($events, $event);
            }
            $artistObj['events'] = $events;
            $artistObj['followers'] = User_Artist_Follow::where('artist_id', $artist->artist_id)->count();
            array_push($artists, $artistObj);
        }
        return response($artists);
    }

    /**
     * Get artists by user
     */
    public function getArtistByUserName($userName, Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $owner = Artist::where('access_token', $request->header('access_token'))->first();
        $artist =  Artist::where('userName', $userName)->first()->makeHidden(['password', 'access_token', 'created_at', 'updated_at']);
        if ($artist == null) {
            return response('Artist not found', 404);
        } else if ($artist->profileImage != 'default') {
            $artist->profileImage = asset('storage/img/artists/' . $artist->profileImage);
        }
        $eventsArtist = Artist_Event_Performance::where('artist_id', $artist->id)->get();
        $events = [];
        foreach ($eventsArtist as $eventArt) {
            $event = Event::find($eventArt->event_id);
            $event->eventImage = asset('storage/img/events/' . $event->eventImage);
            array_push($events, $event);
        }
        $artist['events'] = $events;
        $artist['followers'] = User_Artist_Follow::where('artist_id', $artist->id)->count();
        if ($user != null) {
            $userArtist = User_Artist_Follow::where('artist_id', $artist->id)->where('user_id', $user->id)->first();
            $artist['following'] = $userArtist != null ? true : false;
        }
        if ($owner != null && $owner->id == $artist->id) {
            $artist['editionAllowed'] = true;
        } else {
            $artist['editionAllowed'] = false;
        }
        return response($artist);
    }

    public function getAllArtistsIds() {
        $artists = Artist::select('id', 'userName', 'fullName')->get();
        return response($artists);
    }

    public function getArtistProfileInfo(Request $request) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first()->makeHidden('access_token', 'password');
        if ($artist->profileImage != 'default') {
            $artist->profileImage = asset('storage/img/artists/' . $artist->profileImage);
        }
        return response($artist);
    }

    public function updateProfile(Request $request) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        if (
            (Artist::where('userName', $request->userName)->first() != null ||
                User::where('userName', $request->userName)->first() != null) && $request->userName != $artist->userName
        ) {
            return response('userNameUsed', 400);
        }
        if (
            (Artist::where('email', $request->email)->first() != null ||
                User::where('email', $request->email)->first() != null) && $request->email != $artist->email
        ) {
            return response('emailUsed', 400);
        }
        $artist->userName = $request->userName;
        $artist->email = $request->email;
        $artist->fullName = $request->fullName;
        $artist->bio = $request->bio;
        $artist->location = $request->location;
        $artist->save();
        return response([
            "updated" => "OK"
        ]);
    }

    public function updatePassword(Request $request) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        if (!password_verify($request->oldPassword, $artist->password)) {
            return response('wrongPassword', 403);
        }
        $artist->password = password_hash($request->newPassword, PASSWORD_DEFAULT);
        $artist->save();
        return response(['passwordUpdated' => 'OK']);
    }

    /**
     * Save profile image
     */
    public function saveImg(Request $request) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imgPath = $request->image->store('public/img/artists');
            if ($artist->profileImage != 'default') {
                unlink(public_path('storage/img/artists/' . $artist->profileImage));
            }
            $artist->profileImage = str_replace('public/img/artists/', '', $imgPath);
            $artist->save();
            return response([
                "saved" => "OK",
                "path" => $imgPath
            ]);
        } else {
            return response([
                "saved" => "ERROR"
            ], 400);
        }
    }
}
