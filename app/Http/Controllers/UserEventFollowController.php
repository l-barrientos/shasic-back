<?php

namespace App\Http\Controllers;

use App\Models\User_Event_Follow;
use App\Http\Requests\StoreUser_Event_FollowRequest;
use App\Http\Requests\UpdateUser_Event_FollowRequest;

class UserEventFollowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUser_Event_FollowRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser_Event_FollowRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User_Event_Follow  $user_Event_Follow
     * @return \Illuminate\Http\Response
     */
    public function show(User_Event_Follow $user_Event_Follow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User_Event_Follow  $user_Event_Follow
     * @return \Illuminate\Http\Response
     */
    public function edit(User_Event_Follow $user_Event_Follow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUser_Event_FollowRequest  $request
     * @param  \App\Models\User_Event_Follow  $user_Event_Follow
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser_Event_FollowRequest $request, User_Event_Follow $user_Event_Follow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User_Event_Follow  $user_Event_Follow
     * @return \Illuminate\Http\Response
     */
    public function destroy(User_Event_Follow $user_Event_Follow)
    {
        //
    }
}
