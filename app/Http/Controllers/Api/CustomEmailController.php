<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Mail\CustomEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomEmailController extends Controller
{
    public function sendCustomEmail(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new CustomEmail($request->subject, $request->body));
        }

        return response()->json(['message' => 'Emails enviados correctamente']);
    }
}
