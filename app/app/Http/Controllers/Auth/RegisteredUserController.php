<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\Frontend\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(protected CartService $cartService) {}

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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // ✅ Assign default role
        $user->assignRole('customer');

        event(new Registered($user));

        Auth::login($user);

        // ✅ Merge guest session cart into database
        $this->cartService->mergeSessionToUser();

        return redirect()->intended(url()->previous());

        // return redirect(RouteServiceProvider::HOME);
    }
}