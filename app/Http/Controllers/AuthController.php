<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthController extends Controller {
    public function show() {
        $availableUsers = User::select('id', 'name')->get();

        return Inertia::render('Login', [
            'users' => $availableUsers
        ]);
    }

    public function auth(Request $request) {
        $validated = $request->validate([
            'id' => ['integer', 'required', 'exists:users,id']
        ]);

        Auth::loginUsingId($validated['id'], remember: true);

        return redirect()->route('game');
    }
}
