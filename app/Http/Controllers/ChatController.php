<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Http\Requests\StoreChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller {

    public function createNewChat(Request $request, $targetUserName) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $targetUser = User::where('userName', $targetUserName)->first();
        $openedChat = Chat::where('user1_id', $user->id)->where('user2_id', $targetUser->id)->first();
        if ($openedChat == null) {
            $openedChat = Chat::where('user2_id', $user->id)->where('user1_id', $targetUser->id)->first();
            if ($openedChat == null) {
                $newChat = new Chat;
                $newChat->user1_id = $user->id;
                $newChat->user2_id = $targetUser->id;
                $newChat->save();
                return response([
                    "status" => "created",
                    "chatId" => $newChat->id,
                    "createdBy" => $user->userName
                ]);
            } else {
                return response([
                    "status" => "alreadyExisted"
                ]);
            }
        } else {
            return response([
                "status" => "alreadyExisted"
            ]);
        }
    }
    public function getChatInfo(Request $request, $targetUserName) {
        $user = User::where('access_token', $request->header('access_token'))->first();
        $targetUser = User::where('userName', $targetUserName)->first()->makeHidden('password', 'email', 'access_token', 'created_at', 'updated_at');
        if ($targetUser->profileImage != 'default') {
            $targetUser->profileImage = asset('storage/img/' . $targetUser->profileImage);
        }
        $chat = Chat::where('user1_id', $user->id)->where('user2_id', $targetUser->id)->first();
        if ($chat == null) {
            $chat = Chat::where('user2_id', $user->id)->where('user1_id', $targetUser->id)->first();
            if ($chat == null) {
                return response('chatNotFound', 404);
            }
        }
        return response([
            "currentUserName" => $user->userName,
            "targetUser" => $targetUser,
            "chatId" => $chat->id
        ]);
    }
}
