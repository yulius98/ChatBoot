<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'content' => 'required',
        ]);

        $existingMessage = DB::table('chats')
            ->where('session_id', $request->post('session_id'))
            ->get();

        $sessionId = $request->post('session_id');

        $messages = [];

        if (count($existingMessage) < 1) {
            // If no previous messages, start with system message and user message
            $messages = [
                ['role' => 'system', 'content' => 'Kamu adalah asisten AI yang membantu pengguna dengan pertanyaan mereka.'],
                ['role' => 'user', 'content' => $request->post('content')],
            ];
            $messageDb = array_map(function($data) use ($sessionId) {
                $data['session_id'] = $sessionId;
                return $data;
            }, $messages);
            DB::table('chats')->insert($messageDb);
        } else {
            $messages = $existingMessage->map(function ($data) {
                return [
                    'role' => $data->role,
                    'content' => $data->content,
                ];
            })->toArray();
            $messageDb = [
                'session_id' => $sessionId,
                'role' => 'user',
                'content' => $request->post('content'),
            ];
            DB::table('chats')->insert($messageDb);
            $messages[] = ['role' => 'user', 'content' => $request->post('content')];
        }

        $res = Http::withToken('aaf6ae6aaab2494d85b62c7745783c96')
            ->post('https://api.deepseek.com/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' =>  $messages,
                'stream' => false,
            ]);

        $content = $res->json('choices.{first}.message.content');

        return response()->json(['message' => $content]);
    }
}