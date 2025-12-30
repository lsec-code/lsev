<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getMessages()
    {
        $is_enabled = \App\Models\SiteSetting::where('setting_key', 'global_chat_enabled')->value('setting_value') === 'true';

        $messages = ChatMessage::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'username' => $msg->user->username,
                    'message' => $msg->message,
                    'avatar' => $msg->user->getAvatarUrl(),
                    'avatar_type' => $msg->user->getAvatarType(),
                    'is_admin' => $msg->user->is_admin || $msg->user->id === 1,
                    'is_mine' => Auth::id() === $msg->user_id,
                    'time' => $msg->created_at->diffForHumans(null, true, true),
                ];
            })->values();

        $onlineCount = \App\Models\User::where('last_activity_at', '>=', now()->subSeconds(5))->count();
        $is_admin = Auth::user()->is_admin || Auth::id() === 1;

        return response()->json([
            'is_enabled' => $is_enabled,
            'is_admin' => $is_admin,
            'messages' => $messages,
            'online_count' => $onlineCount
        ]);
    }

    public function sendMessage(Request $request)
    {
        $is_enabled = \App\Models\SiteSetting::where('setting_key', 'global_chat_enabled')->value('setting_value') === 'true';
        $user = Auth::user();
        $is_admin = $user->is_admin || $user->id === 1;

        if (!$is_enabled && !$is_admin) {
             return response()->json(['status' => 'error', 'message' => 'Chat sedang dinonaktifkan oleh Admin.'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:150',
        ]);

        $user = Auth::user();

        // 1-minute Cooldown for non-admins
        if (!$user->is_admin && $user->id !== 1) {
            $lastMessage = ChatMessage::where('user_id', $user->id)->latest()->first();
            if ($lastMessage && $lastMessage->created_at->diffInSeconds(now()) < 60) {
                $remaining = 60 - $lastMessage->created_at->diffInSeconds(now());
                return response()->json(['status' => 'error', 'message' => "Tunggu {$remaining} detik lagi.", 'cooldown' => $remaining], 429);
            }
        }

        $message = ChatMessage::create([
            'user_id' => $user->id,
            'message' => strip_tags($request->message),
        ]);

        // Pruning: Keep only latest 50 messages to optimize server load
        $totalMessages = ChatMessage::count();
        if ($totalMessages > 50) {
            $idsToDelete = ChatMessage::orderBy('id', 'desc')
                ->skip(50)
                ->pluck('id');
            ChatMessage::whereIn('id', $idsToDelete)->delete();
        }

        return response()->json(['status' => 'success']);
    }
}
