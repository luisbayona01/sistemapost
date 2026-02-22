<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class LoginController extends Controller
{
    public function __construct()
    {
        // El estado se valida dentro del método login post-Auth
    }

    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard.index');
        }
        return view('auth.login');
    }

    public function login(loginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 🛡️ VALIDACIÓN DE ESTADO (User & Empresa)
            // User.estado: 1=activo, Empresa.estado: 'activa'
            if ($user->estado != 1 || ($user->empresa && $user->empresa->estado != 'activa')) {
                Auth::logout();
                return redirect()->back()->withErrors('Su cuenta o empresa se encuentra inactiva.');
            }

            return redirect()->intended(route('admin.dashboard.index'))->with('login', 'Bienvenido ' . $user->name);
        }

        return redirect()->route('login.index')->withErrors('Credenciales incorrectas');
    }
}
