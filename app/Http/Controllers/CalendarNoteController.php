<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarNote;
use Illuminate\Support\Facades\Auth;

class CalendarNoteController extends Controller
{
    public function index()
    {
        return CalendarNote::where('user_id', Auth::id())->get();
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'date' => 'required|date',
        'note' => 'required|string',
    ]);

    $note = CalendarNote::create([
        'user_id' => Auth::id(),
        'date' => $validated['date'],
        'note' => $validated['note'],
    ]);

    return response()->json($note, 201);
}


public function destroy($id)
{
    $note = CalendarNote::where('user_id', Auth::id())
        ->where('id', $id)
        ->firstOrFail();

    $note->delete();

    return response()->json(['message' => 'Nota eliminada']);
}

}
