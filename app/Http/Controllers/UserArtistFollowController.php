<?php

namespace App\Http\Controllers;

use App\Models\User_Artist_Follow;
use App\Http\Requests\StoreUser_Artist_FollowRequest;
use App\Http\Requests\UpdateUser_Artist_FollowRequest;

class UserArtistFollowController extends Controller
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
     * @param  \App\Http\Requests\StoreUser_Artist_FollowRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser_Artist_FollowRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User_Artist_Follow  $user_Artist_Follow
     * @return \Illuminate\Http\Response
     */
    public function show(User_Artist_Follow $user_Artist_Follow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User_Artist_Follow  $user_Artist_Follow
     * @return \Illuminate\Http\Response
     */
    public function edit(User_Artist_Follow $user_Artist_Follow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUser_Artist_FollowRequest  $request
     * @param  \App\Models\User_Artist_Follow  $user_Artist_Follow
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser_Artist_FollowRequest $request, User_Artist_Follow $user_Artist_Follow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User_Artist_Follow  $user_Artist_Follow
     * @return \Illuminate\Http\Response
     */
    public function destroy(User_Artist_Follow $user_Artist_Follow)
    {
        //
    }
}
