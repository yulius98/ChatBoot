<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $res = Http::post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
    }
}