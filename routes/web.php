<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Services\EmailService;

use Illuminate\Http\Request;

Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {

    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return redirect('http://localhost:5173/email-verified');

})->middleware(['signed'])->name('verification.verify');
