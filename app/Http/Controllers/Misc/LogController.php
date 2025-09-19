<?php

namespace App\Http\Controllers\Misc;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Log;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($logs);
    }
}
