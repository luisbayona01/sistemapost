<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class loginController extends Controller
{
    public function __construct()
    {
        $this->middleware('check-user-estado', ['only' => ['login']]);
    }

    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('panel');
        }
        return view('auth.login');
    }

    public function login(loginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email','password');
        $validated = Auth::validate($credentials);

        if (!$validated) {
            return redirect()->to('login')->withErrors('Credenciales incorrectas');
        }

        //Crear una sesión
        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        Auth::login($user);

        return redirect()->route('panel')->with('login', 'Bienvenido ' . $user->name);
    }
}
