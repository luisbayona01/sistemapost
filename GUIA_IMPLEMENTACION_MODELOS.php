<?php
/**
 * GUÍA DE IMPLEMENTACIÓN - MODELOS ELOQUENT
 *
 * Este archivo contiene ejemplos de código para actualizar los modelos
 * después de ejecutar las migraciones.
 *
 * @date 30 de enero de 2026
 */

// ============================================================================
// 1. MODELO USER
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class User extends Model
{
    // ... código existente ...

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * AGREGAR: Scope para filtrar por empresa
     *
     * Uso: User::forEmpresa(1)->get()
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * AGREGAR: Obtener usuario actual con empresa
     *
     * Uso: $user = Auth::user()->load('empresa');
     */
    public function cargarEmpresa()
    {
        return $this->load('empresa');
    }
}

// ============================================================================
// 2. MODELO VENTA (CRÍTICO)
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Venta extends Model
{
    protected $guarded = ['id'];

    // === RELACIONES NUEVAS ===

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * AGREGAR: Relación a Transacción de Pago
     * Una venta puede tener múltiples pagos (split payment future)
     */
    public function paymentTransaction(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class);
    }

    /**
     * AGREGAR: Relación a Movimientos de Caja
     */
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    // === GLOBAL SCOPE PARA MULTI-TENANCY ===

    /**
     * AGREGAR: Global scope para filtrar siempre por empresa
     *
     * Esto asegura que:
     * - Venta::all() solo trae ventas de la empresa actual
     * - Queries se filtran automáticamente
     * - No hay riesgo de "fugas" de datos entre empresas
     */
    protected static function booted()
    {
        static::addGlobalScope('empresa', function ($query) {
            if (Auth::check() && Auth::user()->empresa_id) {
                $query->where('empresa_id', Auth::user()->empresa_id);
            }
        });
    }

    // === MÉTODOS NUEVOS PARA TARIFA ===

    /**
     * AGREGAR: Calcular tarifa por servicio
     *
     * Ejemplo:
     * $venta->calcularTarifa(3.50);
     *
     * @param float|null $porcentaje Porcentaje de tarifa (ej: 3.50)
     * @return float Monto de la tarifa calculada
     */
    public function calcularTarifa($porcentaje = null)
    {
        // Si no se proporciona porcentaje, usar el de la empresa
        if ($porcentaje === null) {
            $porcentaje = $this->empresa->tarifa_servicio_defecto ?? 0;
        }

        $this->tarifa_servicio = $porcentaje;
        $this->monto_tarifa = ($this->subtotal * $porcentaje) / 100;

        return $this->monto_tarifa;
    }

    /**
     * AGREGAR: Obtener total incluyendo tarifa
     *
     * IMPORTANTE: La tarifa ya se incluye en el campo 'total' en BD
     * Este accesor es para cuando calculas el total dinámicamente
     */
    public function getTotalAttribute()
    {
        return ($this->subtotal ?? 0)
            + ($this->impuesto ?? 0)
            + ($this->monto_tarifa ?? 0);
    }

    /**
     * AGREGAR: Scope para sumar tarifas en período
     *
     * Uso: $total_tarifas = Venta::tarifaEnPeriodo($desde, $hasta)->get();
     */
    public function scopeTarifaEnPeriodo($query, $desde, $hasta)
    {
        return $query
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('monto_tarifa');
    }

    /**
     * AGREGAR: Scope para sumar tarifas por empresa
     *
     * Uso: $tarifas = Venta::tarifaPorEmpresa(1)->get();
     */
    public function scopeTarifaPorEmpresa($query, $empresaId)
    {
        return $query
            ->where('empresa_id', $empresaId)
            ->sum('monto_tarifa');
    }

    // === MÉTODOS PARA STRIPE (FUTURO) ===

    /**
     * AGREGAR: Marcar como pendiente de pago Stripe
     */
    public function establecerPaymentIntent($intentId)
    {
        $this->stripe_payment_intent_id = $intentId;
        $this->save();

        return $this;
    }

    /**
     * AGREGAR: Verificar si tiene payment intent
     */
    public function tienePaymentIntent()
    {
        return !is_null($this->stripe_payment_intent_id);
    }
}

// ============================================================================
// 3. MODELO MOVIMIENTO
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    protected $guarded = ['id'];

    // === RELACIONES NUEVAS ===

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * AGREGAR: Relación a Venta (nullable)
     *
     * Permite saber: "¿Qué venta generó este movimiento?"
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class)->nullable();
    }

    /**
     * AGREGAR: Relación a Caja (ya existe, mantener)
     */
    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    // === SCOPES ===

    /**
     * AGREGAR: Scope para filtrar por empresa
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * AGREGAR: Scope para obtener movimientos de cierto tipo
     *
     * Uso: Movimiento::tipo('VENTA')->get();
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * AGREGAR: Scope para movimientos en período (para reportes)
     *
     * Uso: Movimiento::enPeriodo($desde, $hasta)->get();
     */
    public function scopeEnPeriodo($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }
}

// ============================================================================
// 4. MODELO CAJA
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    protected $guarded = ['id'];

    // === RELACIONES NUEVAS ===

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * AGREGAR: Relación a Movimientos (ya existe probablemente)
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    // === SCOPES ===

    /**
     * AGREGAR: Scope para cajas abiertas
     *
     * Uso: Caja::abierta()->get();
     */
    public function scopeAbierta($query)
    {
        return $query->where('estado', true);
    }

    /**
     * AGREGAR: Scope para cajas cerradas
     *
     * Uso: Caja::cerrada()->get();
     */
    public function scopeCerrada($query)
    {
        return $query->where('estado', false);
    }

    /**
     * AGREGAR: Scope para empresa
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // === MÉTODOS NUEVOS ===

    /**
     * AGREGAR: Calcular saldo final basado en movimientos
     *
     * @return float
     */
    public function calcularSaldoFinal()
    {
        $suma_movimientos = $this->movimientos()
            ->get()
            ->reduce(function($carry, $movimiento) {
                if ($movimiento->tipo === 'VENTA') {
                    return $carry + $movimiento->monto;
                } elseif ($movimiento->tipo === 'RETIRO') {
                    return $carry - $movimiento->monto;
                }
                return $carry;
            }, 0);

        return $this->saldo_inicial + $suma_movimientos;
    }

    /**
     * AGREGAR: Cerrar caja
     *
     * Calcula saldo final y marca como cerrada
     */
    public function cerrar()
    {
        $this->saldo_final = $this->calcularSaldoFinal();
        $this->fecha_hora_cierre = now();
        $this->estado = false;
        $this->save();

        return $this;
    }
}

// ============================================================================
// 5. MODELO EMPLEADO
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    protected $guarded = ['id'];

    // === RELACIONES NUEVAS ===

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * AGREGAR: Relación a Usuarios (inversa)
     * Un empleado puede tener múltiples usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

// ============================================================================
// 6. MODELO PRODUCTO
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $guarded = ['id'];

    // === RELACIONES NUEVAS ===

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // === SCOPES ===

    /**
     * AGREGAR: Scope para productos activos
     *
     * Uso: Producto::activos()->get();
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * AGREGAR: Scope por empresa
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}

// ============================================================================
// 7. MODELO CLIENTE
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $guarded = ['id'];

    // === RELACIONES NUEVAS ===

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // === SCOPES ===

    /**
     * AGREGAR: Scope por empresa
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}

// ============================================================================
// 8. MODELO COMPRA
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compra extends Model
{
    protected $guarded = ['id'];

    // === RELACIONES NUEVAS ===

    /**
     * AGREGAR: Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // === SCOPES ===

    /**
     * AGREGAR: Scope por empresa
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}

// ============================================================================
// 9. MODELO NUEVO: PaymentTransaction
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    // === RELACIONES ===

    /**
     * Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación a Venta
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    // === SCOPES ===

    /**
     * Scope para transacciones exitosas
     */
    public function scopeExitosas($query)
    {
        return $query->where('status', 'SUCCESS');
    }

    /**
     * Scope para transacciones fallidas
     */
    public function scopeFallidas($query)
    {
        return $query->where('status', 'FAILED');
    }

    /**
     * Scope por método de pago
     */
    public function scopeMetodo($query, $metodo)
    {
        return $query->where('payment_method', $metodo);
    }

    /**
     * Scope por empresa
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // === MÉTODOS ===

    /**
     * Marcar como pagada
     */
    public function marcarExitosa($chargeId = null)
    {
        $this->status = 'SUCCESS';
        if ($chargeId) {
            $this->stripe_charge_id = $chargeId;
        }
        $this->save();

        return $this;
    }

    /**
     * Marcar como fallida
     */
    public function marcarFallida($error = null)
    {
        $this->status = 'FAILED';
        if ($error) {
            $this->error_message = $error;
        }
        $this->save();

        return $this;
    }
}

// ============================================================================
// 10. MODELO NUEVO: StripeConfig
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeConfig extends Model
{
    protected $guarded = ['id'];

    protected $encrypted = [
        'secret_key',
        'webhook_secret',
    ];

    // === RELACIONES ===

    /**
     * Relación a Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // === SCOPES ===

    /**
     * Scope para configuraciones habilitadas
     */
    public function scopeHabilitada($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope para configuraciones en modo test
     */
    public function scopeTest($query)
    {
        return $query->where('test_mode', true);
    }

    /**
     * Scope para configuraciones en modo live
     */
    public function scopeLive($query)
    {
        return $query->where('test_mode', false);
    }

    // === MÉTODOS ===

    /**
     * Obtener claves de forma segura
     */
    public function obtenerPublicKey()
    {
        return $this->public_key;
    }

    public function obtenerSecretKey()
    {
        return decrypt($this->secret_key);
    }

    public function obtenerWebhookSecret()
    {
        return decrypt($this->webhook_secret);
    }
}

// ============================================================================
// MIDDLEWARE: EnsureEmpresaAccess
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmpresaAccess
{
    /**
     * CREAR: Middleware para garantizar acceso a empresa
     *
     * Verificar:
     * 1. Usuario autenticado
     * 2. Usuario tiene empresa asignada
     * 3. Pasar empresa_id a request
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $user = Auth::user();

        if (!$user->empresa_id) {
            return response()->json(
                ['error' => 'No empresa assigned to user'],
                403
            );
        }

        // Pasar empresa_id al request para acceso en controllers
        $request->merge(['empresa_id' => $user->empresa_id]);

        return $next($request);
    }
}

// ============================================================================
// USO EN CONTROLLERS
// ============================================================================

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    /**
     * ACTUALIZAR: Método para crear venta con tarifa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'caja_id' => 'required|exists:cajas,id',
            'metodo_pago' => 'required|in:EFECTIVO,TARJETA',
            'productos' => 'required|array',
            'tarifa_servicio' => 'nullable|numeric|min:0',
        ]);

        // Crear venta
        $venta = Venta::create([
            'empresa_id' => Auth::user()->empresa_id,
            'cliente_id' => $validated['cliente_id'],
            'caja_id' => $validated['caja_id'],
            'user_id' => Auth::user()->id,
            'comprobante_id' => 1, // ó calcular
            'numero_comprobante' => $this->generarNumero(),
            'metodo_pago' => $validated['metodo_pago'],
            'fecha_hora' => now(),
            'subtotal' => 0, // Calcular
            'impuesto' => 0, // Calcular
            'total' => 0, // Calcular
            'monto_recibido' => $request->monto_recibido,
            'vuelto_entregado' => 0, // Calcular
        ]);

        // Calcular tarifa (NUEVO)
        $tarifa_porcentaje = $validated['tarifa_servicio']
            ?? Auth::user()->empresa->tarifa_servicio_defecto;
        $venta->calcularTarifa($tarifa_porcentaje);
        $venta->save();

        return response()->json($venta);
    }

    /**
     * AGREGAR: Obtener ventas filtradas por empresa
     */
    public function index()
    {
        $ventas = Venta::forEmpresa(Auth::user()->empresa_id)
            ->with('cliente', 'productos')
            ->paginate();

        return response()->json($ventas);
    }

    /**
     * AGREGAR: Reporte de tarifa
     */
    public function reporteTarifa(Request $request)
    {
        $desde = $request->desde ?? now()->startOfMonth();
        $hasta = $request->hasta ?? now()->endOfMonth();

        $total_tarifas = Venta::forEmpresa(Auth::user()->empresa_id)
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('monto_tarifa');

        return response()->json([
            'total_tarifas' => $total_tarifas,
            'periodo' => [
                'desde' => $desde,
                'hasta' => $hasta
            ]
        ]);
    }
}

// ============================================================================
// FIN DE LA GUÍA
// ============================================================================

/**
 * RESUMEN DE CAMBIOS POR ARCHIVO:
 *
 * app/Models/User.php
 *   + empresa() BelongsTo
 *   + scopeForEmpresa($query, $empresaId)
 *
 * app/Models/Venta.php
 *   + empresa() BelongsTo
 *   + paymentTransaction() HasOne
 *   + movimientos() HasMany
 *   + booted() global scope
 *   + calcularTarifa($porcentaje)
 *   + getTotalAttribute()
 *   + scopeTarifaEnPeriodo($query, $desde, $hasta)
 *   + scopeTarifaPorEmpresa($query, $empresaId)
 *   + establecerPaymentIntent($intentId)
 *   + tienePaymentIntent()
 *
 * app/Models/Movimiento.php
 *   + empresa() BelongsTo
 *   + venta() BelongsTo nullable
 *   + scopeForEmpresa($query, $empresaId)
 *   + scopeTipo($query, $tipo)
 *   + scopeEnPeriodo($query, $desde, $hasta)
 *
 * app/Models/Caja.php
 *   + empresa() BelongsTo
 *   + scopeAbierta($query)
 *   + scopeCerrada($query)
 *   + scopeForEmpresa($query, $empresaId)
 *   + calcularSaldoFinal()
 *   + cerrar()
 *
 * app/Models/Empleado.php
 *   + empresa() BelongsTo
 *   + users() HasMany
 *
 * app/Models/Producto.php
 *   + empresa() BelongsTo
 *   + scopeActivos($query)
 *   + scopeForEmpresa($query, $empresaId)
 *
 * app/Models/Cliente.php
 *   + empresa() BelongsTo
 *   + scopeForEmpresa($query, $empresaId)
 *
 * app/Models/Compra.php
 *   + empresa() BelongsTo
 *   + scopeForEmpresa($query, $empresaId)
 *
 * app/Models/PaymentTransaction.php (NUEVO)
 *   + Toda la clase
 *
 * app/Models/StripeConfig.php (NUEVO)
 *   + Toda la clase
 *
 * app/Http/Middleware/EnsureEmpresaAccess.php (NUEVO)
 *   + Toda la clase
 *
 * app/Http/Controllers/VentaController.php (ACTUALIZAR)
 *   + store() - agregar lógica de tarifa
 *   + index() - filtrar por empresa
 *   + reporteTarifa() - nuevo método
 */
