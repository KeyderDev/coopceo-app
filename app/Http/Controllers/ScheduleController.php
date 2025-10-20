<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScheduleMail;
use App\Models\User;
use App\Models\Schedule;

class ScheduleController extends Controller
{

    public function users()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.user_id' => 'required|exists:users,id',
            'schedules.*.day' => 'required|string',
            'schedules.*.start_time' => 'required',
            'schedules.*.end_time' => 'required',
        ]);

        foreach ($data['schedules'] as $sched) {
            Schedule::create($sched);
        }

        return response()->json(['message' => 'Horarios guardados']);
    }

public function sendEmail(Request $request)
{
    $schedules = $request->input('schedules');

    if (empty($schedules)) {
        return response()->json(['message' => 'No se recibieron horarios'], 400);
    }

    $userId = $schedules[0]['user_id'] ?? null;
    $user = User::find($userId);

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    foreach ($schedules as &$s) {
        $s['nombre'] = $user->nombre;
    }

    Mail::to($user->email)->send(new ScheduleMail($schedules));

    return response()->json(['message' => 'Correo enviado correctamente']);
}

}
