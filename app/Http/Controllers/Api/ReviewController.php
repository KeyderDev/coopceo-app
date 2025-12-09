<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('REVIEWS INDEX HEADERS', [
            'token' => $request->bearerToken(),
            'X-Coop-Code' => $request->header('X-Coop-Code'),
            'tenant_db' => config('database.connections.tenant.database'),
            'default_db' => config('database.default')
        ]);

        $user = auth()->user();

        \Log::info('REVIEWS INDEX USER', [
            'user_id' => optional($user)->id,
            'user_email' => optional($user)->email
        ]);

        $reviews = Review::with(['user:id,nombre,apellido'])
            ->orderBy('id', 'desc')
            ->get();

        \Log::info('REVIEWS INDEX RESULT COUNT', [
            'count' => $reviews->count()
        ]);

        return $reviews;
    }

    public function store(Request $request)
    {
        \Log::info('REVIEWS STORE HEADERS', [
            'token' => $request->bearerToken(),
            'X-Coop-Code' => $request->header('X-Coop-Code'),
            'tenant_db' => config('database.connections.tenant.database'),
            'default_db' => config('database.default')
        ]);

        $user = auth()->user();

        \Log::info('REVIEWS STORE USER', [
            'user_id' => optional($user)->id,
            'user_email' => optional($user)->email
        ]);

        $data = $request->validate([
            'estrellas' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string'
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'estrellas' => $data['estrellas'],
            'comentario' => $data['comentario'] ?? null,
            'fecha' => now()
        ]);

        \Log::info('REVIEWS STORE CREATED', [
            'review_id' => $review->id
        ]);

        return $review->load('user:id,nombre,apellido');
    }
}
