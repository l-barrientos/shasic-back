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

    public function newEvent(Request $request) {
        $newEvent = new Event;
        $newEvent->eventName = $request->eventName;
        $newEvent->eventLocation = $request->eventLocation;
        $newEvent->eventDate = $request->eventDate;
        $newEvent->eventImage = '';

        if (isset($request->ticketsUrl)) {
            $newEvent->ticketsUrl = $request->ticketsUrl;
        }
        if (isset($request->details)) {
            $newEvent->details = $request->details;
        }
        $newEvent->save();
        foreach ($request->artists as $artist) {
            $artistEvent = new Artist_Event_Performance;
            $artistEvent->event_id = $newEvent->id;
            $artistEvent->artist_id = $artist->id;
            $artistEvent->save();
        }
        return response([
            "status" => "created",
            "id" => $newEvent->id
        ]);
    }

    public function saveImg(Request $request, $id) {
        $event = Event::find($id);
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imgPath = $request->image->store('public/img/events');
            $event->eventImage = str_replace('public/img/events/', '', $imgPath);
            $event->save();
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
