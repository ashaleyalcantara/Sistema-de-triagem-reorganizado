<?php

namespace App\Http\Controllers;

use App\Enums\Perfil;
use App\Http\Requests\LoginRequest;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $redirect = $this->redirectRouteName(Auth::user());

            if ($redirect) {
                return redirect()->route($redirect);
            }
        }

        return view('login.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $perfilSelecionado = match ($credentials['perfil']) {
            'administrador' => Perfil::Administrador,
            'recepcionista' => Perfil::Recepcionista,
            'enfermeiro' => Perfil::Enfermeiro,
            'medico' => Perfil::Medico,
        };

        $funcionario = Funcionario::where('email', $credentials['email'])->first();

        if (! $funcionario || ! Hash::check($credentials['senha'], $funcionario->senha)) {
            return back()->withErrors([
                'login_error' => 'Usuário ou senha inválidos.',
            ])->withInput($request->only('email', 'perfil'));
        }

        if ($funcionario->perfil !== $perfilSelecionado->value) {
            return back()->withErrors([
                'login_error' => 'Este usuário não possui o cargo de '.$perfilSelecionado->label().'.',
            ])->withInput($request->only('email', 'perfil'));
        }

        Auth::login($funcionario);
        $request->session()->regenerate();

        $redirect = $this->redirectRouteName($funcionario);

        return $redirect ? redirect()->route($redirect) : redirect('/login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function redirectRouteName(Funcionario $funcionario): ?string
    {
        return $funcionario->redirectRouteName();
    }
}
