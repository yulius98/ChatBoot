<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'content' => 'required'
        ]);

        $existingmessages = DB::table('tbl_chats')
                            -> where('session_id', $request->post('session_id'))
                            ->get();

        $session_id = $request->post('session_id');                    
        
        $messages=[];                    

        if (count($existingmessages)<1){
            
            $messages = [
            ['role' => 'system', 'content' => 'Kamu adalah asisten pribadi'],
            ['role' => 'user' , 'content' => $request->post('content')]
            ];
            $messageDb = array_map(function($data) use($session_id) {
                $data['session_id']= $session_id;
                return $data;
            }, $messages);
            DB::table('tbl_chats')->insert($messageDb);
        } else {
            $messages = $existingmessages->map(function($data){
               return ['role' => $data->role, 'content' => $data->content]; 
            });
            $messageDb=[
                'session_id' => $session_id,
                'role' => 'user',
                'content' => $request->post('content')
            ];
            DB::table('tbl_chats')->insert($messageDb);
            $messages[]=['role'=>'user','content'=>$request->post('content')];
        }                    


        $res = Http::withToken('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')
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'=>'deepseek/deepseek-r1-0528-qwen3-8b:free',
                'messages' => $messages,
                'stream' => false,
            ]);

        $content = $res->json('choices.{first}.message.content');

        return ['content' => $content]; 
    }
}