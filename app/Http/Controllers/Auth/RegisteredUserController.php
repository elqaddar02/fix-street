<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => trans('validation.required', ['attribute' => 'name']),
            'name.string' => trans('validation.string', ['attribute' => 'name']),
            'name.max' => trans('validation.max.string', ['attribute' => 'name', 'max' => 255]),
            'email.required' => trans('auth.email.required'),
            'email.string' => trans('validation.string', ['attribute' => 'email']),
            'email.email' => trans('auth.email.invalid'),
            'email.unique' => trans('auth.email_unique'),
            'email.max' => trans('validation.max.string', ['attribute' => 'email', 'max' => 255]),
            'password.required' => trans('auth.password.required'),
            'password.confirmed' => trans('auth.confirmation_mismatch'),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
