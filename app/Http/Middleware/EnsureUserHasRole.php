<?php

namespace App\Http\Middleware;

use App\Enums\Perfil;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Permite o acesso somente quando o funcionário autenticado possui um dos perfis informados.
     * Administradores têm acesso irrestrito.
     */
    public function handle(Request $request, Closure $next, string ...$perfis): Response
    {
        $funcionario = $request->user();

        if (! $funcionario) {
            abort(401);
        }

        $perfilAtual = Perfil::tryFrom((string) $funcionario->perfil);

        if ($perfilAtual === Perfil::Administrador) {
            return $next($request);
        }

        $perfisPermitidos = array_filter(
            array_map(fn (string $perfil) => Perfil::tryFrom($perfil), $perfis)
        );

        if (! $perfilAtual || ! in_array($perfilAtual, $perfisPermitidos, true)) {
            abort(403, 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
