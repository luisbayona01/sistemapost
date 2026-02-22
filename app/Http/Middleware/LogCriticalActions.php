<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de Auditoría: Registra automáticamente acciones críticas
 * 
 * Este middleware registra todas las operaciones POST, PUT, PATCH y DELETE
 * en módulos críticos del sistema para mantener un audit trail completo.
 * 
 * @package App\Http\Middleware
 * @since FASE 4.1
 */
class LogCriticalActions
{
    /**
     * Módulos y acciones que deben ser auditados
     */
    protected array $criticalModules = [
        'cajas' => 'Caja',
        'ventas' => 'Venta',
        'movimientos' => 'Movimiento',
        'compras' => 'Compra',
        'inventario' => 'Inventario',
        'productos' => 'Producto',
        'users' => 'Usuario',
        'empresas' => 'Empresa',
    ];

    /**
     * Métodos HTTP que deben ser auditados
     */
    protected array $criticalMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ejecutar el request
        $response = $next($request);

        // Solo auditar si el usuario está autenticado
        if (!Auth::check()) {
            return $response;
        }

        // Solo auditar métodos críticos
        if (!in_array($request->method(), $this->criticalMethods)) {
            return $response;
        }

        // Verificar si la ruta pertenece a un módulo crítico
        $module = $this->getModuleFromPath($request->path());

        if ($module) {
            $this->logAction($request, $response, $module);
        }

        return $response;
    }

    /**
     * Determinar el módulo basado en la ruta
     *
     * @param string $path
     * @return string|null
     */
    protected function getModuleFromPath(string $path): ?string
    {
        foreach ($this->criticalModules as $route => $module) {
            if (str_contains($path, $route)) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Registrar la acción en el log
     *
     * @param Request $request
     * @param Response $response
     * @param string $module
     * @return void
     */
    protected function logAction(Request $request, Response $response, string $module): void
    {
        $action = $this->getActionName($request->method());
        $statusCode = $response->getStatusCode();

        // Solo registrar si fue exitoso (2xx) o si fue un error de validación (422)
        if ($statusCode >= 200 && $statusCode < 300 || $statusCode === 422) {
            $data = [
                'method' => $request->method(),
                'path' => $request->path(),
                'status_code' => $statusCode,
                'empresa_id' => Auth::user()->empresa_id ?? null,
            ];

            // Agregar datos del request (sin datos sensibles)
            if ($request->method() !== 'DELETE') {
                $data['request_data'] = $this->sanitizeRequestData($request->all());
            }

            // Agregar ID del recurso si está disponible
            if ($request->route('id')) {
                $data['resource_id'] = $request->route('id');
            }

            ActivityLogService::log(
                action: "{$action} {$module}",
                module: $module,
                data: $data
            );
        }

        // Registrar accesos denegados (403, 401)
        if (in_array($statusCode, [401, 403])) {
            ActivityLogService::log(
                action: "Acceso Denegado - {$module}",
                module: 'Seguridad',
                data: [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'status_code' => $statusCode,
                    'empresa_id' => Auth::user()->empresa_id ?? null,
                ]
            );
        }
    }

    /**
     * Obtener nombre de acción basado en el método HTTP
     *
     * @param string $method
     * @return string
     */
    protected function getActionName(string $method): string
    {
        return match ($method) {
            'POST' => 'Crear',
            'PUT', 'PATCH' => 'Actualizar',
            'DELETE' => 'Eliminar',
            default => 'Acción',
        };
    }

    /**
     * Sanitizar datos del request antes de guardar
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'cvv',
            'card_number',
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***REDACTED***';
            }
        }

        // Limitar tamaño del array para evitar logs muy grandes
        if (count($data) > 50) {
            $data = array_slice($data, 0, 50);
            $data['_truncated'] = true;
        }

        return $data;
    }
}
