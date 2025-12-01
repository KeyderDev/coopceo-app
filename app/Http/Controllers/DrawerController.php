<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drawer;

class DrawerController extends Controller
{
    public function index()
    {
        return Drawer::with('assignedUser:id,nombre,email')->get();
    }

    public function assign(Request $request)
    {
        $request->validate([
            'drawer_id' => 'required|exists:drawers,id',
            'user_id' => 'required|exists:users,id',
            'petty' => 'required|numeric|min:0'
        ]);

        $drawer = Drawer::find($request->drawer_id);

        $drawer->assigned_user_id = $request->user_id;
        $drawer->petty = $request->petty;
        $drawer->is_open = true;
        $drawer->save();

        return response()->json(['message' => 'Gaveta asignada']);
    }

public function myDrawer(Request $request)
{
    $drawer = Drawer::where('assigned_user_id', $request->user()->id)
        ->where('is_open', true)
        ->first();

    if (!$drawer) {
        return response()->json(null, 200); // <-- CLAVE
    }

    return response()->json($drawer);
}

    public function unassign(Request $request)
    {
        $drawer = Drawer::where('assigned_user_id', $request->user()->id)
            ->where('is_open', true)
            ->first();

        if (!$drawer) {
            return response()->json(['message' => 'No tiene gaveta abierta'], 404);
        }

        $drawer->is_open = false;
        $drawer->assigned_user_id = null;
        $drawer->save();

        return response()->json(['message' => 'Gaveta cerrada']);
    }
}
