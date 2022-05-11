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

    public function getAllEvents(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $events = Event::all();
        foreach ($events as $event) {
            $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
            $userEvent = User_Event_Follow::where('event_id', $event->id)->where('user_id', $user->id)->first();
            $event['following'] = $userEvent != null ? true : false;
        }
        return $events;
    }

    public function getEventsByUser(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();

        $eventsUser = User_Event_Follow::where('user_id', $user->id)->get();
        $events = [];
        foreach ($eventsUser as $eventUser) {
            $event = Event::find($eventUser->event_id)->makeHidden(['created_at', 'updated_at']);
            $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
            array_push($events, $event);
        }

        return $events;
    }

    public function getEventById($id, Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $event = Event::find($id);
        $artistsEvent = Artist_Event_Performance::where('event_id', $id)->get();
        $artists = [];
        foreach ($artistsEvent as $artEv) {
            array_push($artists, Artist::find($artEv->artist_id)->makeHidden(['password', 'access_token', 'created_at', 'updated_at']));
        }
        $event['artists'] = $artists;
        $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
        $userEvent = User_Event_Follow::where('event_id', $id)->where('user_id', $user->id)->first();
        $event['following'] = $userEvent != null ? true : false;

        return response($event);
    }
}
