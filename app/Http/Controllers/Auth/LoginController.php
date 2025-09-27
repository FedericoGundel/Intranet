<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Si el usuario inicia sesión, pero no está aprobado, lo desloguea y lo redirige con un error.
     */
    protected function authenticated(Request $request, $user)
    {
        if (!$user->approved) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta aún no ha sido aprobada por un administrador.',
            ]);
        }
    }
}
