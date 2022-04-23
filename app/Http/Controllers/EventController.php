<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Artist;
use App\Models\Artist_Event_Performance;
use App\Models\User;
use App\Models\User_Artist_Follow;
use App\Models\User_Event_Follow;
use Illuminate\Http\Request;

class EventController extends Controller {
    public function getEventsByUser(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();

        $eventsUser = User_Event_Follow::where('user_id', $user->id)->get();
        $events = [];
        foreach ($eventsUser as $eventUser) {
            array_push($events, Event::find($eventUser->event_id)->makeHidden(['created_at', 'updated_at']));
        }
        return $events;
    }

    public function getEventById($id) {
        $event = Event::find($id);
        $artistsEvent = Artist_Event_Performance::where('event_id', $id)->get();
        $artists = [];
        foreach ($artistsEvent as $artEv) {
            array_push($artists, Artist::find($artEv->artist_id)->makeHidden(['password', 'access_token', 'created_at', 'updated_at']));
        }

        return response(json_encode([
            'event' => $event,
            'artists' => $artists
        ]));
    }
}
