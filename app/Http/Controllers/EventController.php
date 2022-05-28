<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Artist;
use App\Models\Artist_Event_Performance;
use App\Models\User;
use App\Models\User_Event_Follow;
use App\Policies\ArtistEventPerformancePolicy;
use Illuminate\Http\Request;

class EventController extends Controller {

    /**
     * Get all events
     */
    public function getAllEvents(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $events = Event::all();
        foreach ($events as $event) {
            $event->eventImage = asset('storage/img/events/' . $event->eventImage);
            $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
            $userEvent = User_Event_Follow::where('event_id', $event->id)->where('user_id', $user->id)->first();
            $event['following'] = $userEvent != null ? true : false;
        }
        return $events;
    }

    /**
     * Get events followed by user
     */
    public function getEventsByUser(Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $eventsUser = User_Event_Follow::where('user_id', $user->id)->get();
        $events = [];
        foreach ($eventsUser as $eventUser) {
            $event = Event::find($eventUser->event_id)->makeHidden(['created_at', 'updated_at']);
            $event->eventImage = asset('storage/img/events/' . $event->eventImage);
            $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
            array_push($events, $event);
        }

        return $events;
    }

    /**
     * Get event by id
     */
    public function getEventById($id, Request $request) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $event = Event::find($id);
        $artistsEvent = Artist_Event_Performance::where('event_id', $id)->get();
        $artists = [];
        foreach ($artistsEvent as $artEv) {
            array_push($artists, Artist::find($artEv->artist_id)->makeHidden(['password', 'access_token', 'created_at', 'updated_at']));
        }
        $event->eventImage = asset('storage/img/events/' . $event->eventImage);
        $event['artists'] = $artists;
        $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
        if ($user != null) {
            $userEvent = User_Event_Follow::where('event_id', $id)->where('user_id', $user->id)->first();
            $event['following'] = $userEvent != null ? true : false;
        }

        return response($event);
    }

    /**
     * Get events created by an artist
     */
    public function getEventsByCreator(Request $request) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        $events = Event::where('createdBy', $artist->id)->get();

        foreach ($events as $event) {
            $event->eventImage = asset('storage/img/events/' . $event->eventImage);
            $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
        }

        return $events;
    }

    /**
     * Get events in which artist is performing
     */
    public function getEventsByArtist(Request $request) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        $eventsArtist = Artist_Event_Performance::where('artist_id', $artist->id)->get();
        $events = [];
        foreach ($eventsArtist as $eventArt) {
            $event = Event::find($eventArt->event_id);
            $event->eventImage = asset('storage/img/events/' . $event->eventImage);
            $event['followers'] = User_Event_Follow::where('event_id', $event->id)->count();
            array_push($events, $event);
        }
        return $events;
    }

    /**
     * Create new event
     */
    public function newEvent(Request $request) {
        $creator = Artist::where('access_token', $request->header('access_token'))->first();
        $newEvent = new Event;
        $newEvent->eventName = $request->eventName;
        $newEvent->eventLocation = $request->eventLocation;
        $newEvent->eventDate = $request->eventDate;
        $newEvent->eventImage = 'default';
        $newEvent->createdBy = $creator->id;
        $newEvent->ticketsUrl = $request->ticketsUrl;

        $newEvent->details = $request->details;

        $newEvent->save();
        foreach ($request->artists as $artist) {
            $artistEvent = new Artist_Event_Performance;
            $artistEvent->event_id = $newEvent->id;
            $artistEvent->artist_id = $artist['id'];
            $artistEvent->save();
        }
        return response([
            "status" => "created",
            "id" => $newEvent->id
        ]);
    }

    /**
     * Update event
     */
    public function updateEvent(Request $request, $id) {
        $updater = Artist::where('access_token', $request->header('access_token'))->first();
        $event = Event::find($id);
        if ($event->createdBy != $updater->id) {
            return response('Not the creator of the event', 403);
        }
        $event->eventName = $request->eventName;
        $event->eventLocation = $request->eventLocation;
        $event->eventDate = $request->eventDate;
        $event->ticketsUrl = $request->ticketsUrl;
        $event->details = $request->details;
        $event->save();
        Artist_Event_Performance::where('event_id', $event->id)->delete();
        foreach ($request->artists as $artist) {
            $artistEvent = new Artist_Event_Performance;
            $artistEvent->event_id = $event->id;
            $artistEvent->artist_id = $artist['id'];
            $artistEvent->save();
        }
        return response([
            "updated" => "OK"
        ]);
    }

    public function checkEditionAllowed(Request $request, $id) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        $event = Event::find($id);
        if ($event->createdBy == $artist->id) {
            return (["allowed" => true]);
        }
        return (["allowed" => false]);
    }

    /**
     * Save event image
     */
    public function saveImg(Request $request, $id) {
        $event = Event::find($id);
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imgPath = $request->image->store('public/img/events');
            if ($event->eventImage != 'default') {
                unlink(public_path('storage/img/events/' . $event->eventImage));
            }
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
