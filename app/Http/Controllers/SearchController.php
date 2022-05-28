<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Artist_Event_Performance;
use App\Models\Event;
use App\Models\User;
use App\Models\User_Artist_Follow;
use App\Models\User_Event_Follow;
use Illuminate\Http\Request;

class SearchController extends Controller {

    public function getResults(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $artists = Artist::where('fullName', 'like', '%' . $request->header('query') . '%')->orWhere('userName', 'like', '%' . $request->header('query') . '%')->get();
        $events = Event::where('eventName', 'like', '%' . $request->header('query') . '%')->get();
        foreach ($events as $event) {
            $event->eventImage = asset('storage/img/events/' . $event->eventImage);
            $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
            $userEvent = User_Event_Follow::where('event_id', $event->id)->where('user_id', $user->id)->first();
            $event['following'] = $userEvent != null ? true : false;
        }

        foreach ($artists as $artist) {
            if ($artist->profileImage != 'default') {
                $artist->profileImage = asset('storage/img/' . $artist->profileImage);
            }
            $artist['followers'] = User_Artist_Follow::where('artist_id', $artist->id)->count();
            $artist['eventsNum'] = Artist_Event_Performance::where('artist_id', $artist->id)->count();
            $userArtist = User_Artist_Follow::where('artist_id', $artist->id)->where('user_id', $user->id)->first();
            $artist['following'] = $userArtist != null ? true : false;
        }

        return response([
            "events" => $events,
            "artists" => $artists
        ]);
    }
}
