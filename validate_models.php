#!/usr/bin/env php
<?php
/**
 * Script de Validación de Modelos - CinemaPOS SaaS
 *
 * Ejecutar desde la raíz del proyecto:
 * php artisan tinker < validate_models.php
 *
 * O mejor, crear un comando artisan:
 * php artisan make:command ValidateModels
 */

// =============================================================================
// VALIDACIÓN DE MODELOS - CHECKLIST AUTOMÁTICO
// =============================================================================

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    VALIDACIÓN DE MODELOS ELOQUENT                     ║\n";
echo "║                          CinemaPOS SaaS                               ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$modelsToValidate = [
    'User',
    'Venta',
    'Caja',
    'Movimiento',
    'Empresa',
    'Empleado',
    'Producto',
    'Cliente',
    'Compra',
    'Proveedore',
    'Inventario',
    'Kardex',
    'PaymentTransaction',
    'StripeConfig',
];

$checks = [];

// =============================================================================
// 1. VERIFICAR QUE LOS MODELOS EXISTEN Y SE CARGAN
// =============================================================================

echo "1️⃣  VERIFICANDO CARGA DE MODELOS...\n";
echo "─────────────────────────────────────\n";

foreach ($modelsToValidate as $model) {
    $class = "App\\Models\\{$model}";
    try {
        $instance = new $class;
        echo "✅ {$model}\n";
        $checks["model_exists_{$model}"] = true;
    } catch (Exception $e) {
        echo "❌ {$model}: {$e->getMessage()}\n";
        $checks["model_exists_{$model}"] = false;
    }
}

// =============================================================================
// 2. VERIFICAR QUE EMPRESA_ID ESTÁ EN FILLABLE (EXCEPTO EMPRESA)
// =============================================================================

echo "\n2️⃣  VERIFICANDO FILLABLE CON EMPRESA_ID...\n";
echo "────────────────────────────────────────────\n";

$modelsWithEmpresa = [
    'User' => ['empresa_id'],
    'Cliente' => ['empresa_id'],
    'Proveedore' => ['empresa_id'],
];

foreach ($modelsWithEmpresa as $model => $requiredFields) {
    $class = "App\\Models\\{$model}";
    try {
        $instance = new $class;
        $fillable = $instance->getFillable();

        $missing = array_diff($requiredFields, $fillable);
        if (empty($missing)) {
            echo "✅ {$model} tiene empresa_id en fillable\n";
            $checks["fillable_{$model}"] = true;
        } else {
            echo "❌ {$model} falta: " . implode(', ', $missing) . "\n";
            $checks["fillable_{$model}"] = false;
        }
    } catch (Exception $e) {
        echo "❌ {$model}: {$e->getMessage()}\n";
        $checks["fillable_{$model}"] = false;
    }
}

// =============================================================================
// 3. VERIFICAR RELACIONES EMPRESA
// =============================================================================

echo "\n3️⃣  VERIFICANDO RELACIONES CON EMPRESA...\n";
echo "──────────────────────────────────────────\n";

$modelsWithBelongsToEmpresa = [
    'User', 'Venta', 'Caja', 'Movimiento', 'Empleado',
    'Producto', 'Cliente', 'Compra', 'Proveedore',
    'Inventario', 'Kardex', 'PaymentTransaction', 'StripeConfig'
];

foreach ($modelsWithBelongsToEmpresa as $model) {
    $class = "App\\Models\\{$model}";
    try {
        $instance = new $class;

        // Intentar acceder al método de relación
        if (method_exists($instance, 'empresa')) {
            echo "✅ {$model}->empresa() relación existe\n";
            $checks["relation_empresa_{$model}"] = true;
        } else {
            echo "❌ {$model}->empresa() NO existe\n";
            $checks["relation_empresa_{$model}"] = false;
        }
    } catch (Exception $e) {
        echo "❌ {$model}: {$e->getMessage()}\n";
        $checks["relation_empresa_{$model}"] = false;
    }
}

// =============================================================================
// 4. VERIFICAR RELACIONES INVERSAS EN EMPRESA
// =============================================================================

echo "\n4️⃣  VERIFICANDO RELACIONES INVERSAS EN EMPRESA...\n";
echo "─────────────────────────────────────────────────\n";

$empresaRelations = [
    'users', 'empleados', 'cajas', 'ventas', 'productos',
    'compras', 'clientes', 'proveedores', 'movimientos',
    'paymentTransactions', 'inventarios', 'kardexes', 'stripeConfig'
];

try {
    $empresa = new App\Models\Empresa();
    foreach ($empresaRelations as $relation) {
        if (method_exists($empresa, $relation)) {
            echo "✅ Empresa->{$relation}() existe\n";
            $checks["empresa_relation_{$relation}"] = true;
        } else {
            echo "❌ Empresa->{$relation}() NO existe\n";
            $checks["empresa_relation_{$relation}"] = false;
        }
    }
} catch (Exception $e) {
    echo "❌ Error verificando Empresa: {$e->getMessage()}\n";
}

// =============================================================================
// 5. VERIFICAR GLOBAL SCOPES
// =============================================================================

echo "\n5️⃣  VERIFICANDO GLOBAL SCOPES...\n";
echo "────────────────────────────────\n";

$modelsWithGlobalScope = [
    'Venta', 'Caja', 'Movimiento', 'Producto',
    'Cliente', 'Compra', 'Proveedore', 'Inventario', 'Kardex'
];

foreach ($modelsWithGlobalScope as $model) {
    $class = "App\\Models\\{$model}";
    try {
        // Intentar compilar el modelo y verificar que booted() existe
        $reflection = new ReflectionClass($class);
        if ($reflection->hasMethod('booted')) {
            echo "✅ {$model} tiene booted() (Global Scope probablemente implementado)\n";
            $checks["global_scope_{$model}"] = true;
        } else {
            echo "⚠️  {$model} no tiene booted() visible\n";
            $checks["global_scope_{$model}"] = false;
        }
    } catch (Exception $e) {
        echo "❌ {$model}: {$e->getMessage()}\n";
        $checks["global_scope_{$model}"] = false;
    }
}

// =============================================================================
// 6. VERIFICAR CASTS
// =============================================================================

echo "\n6️⃣  VERIFICANDO CASTS DE DECIMALES...\n";
echo "──────────────────────────────────────\n";

$modelsCasts = [
    'Venta' => ['tarifa_servicio', 'monto_tarifa', 'subtotal', 'impuesto'],
    'Caja' => ['saldo_inicial', 'saldo_final'],
    'Movimiento' => ['monto'],
];

foreach ($modelsCasts as $model => $castFields) {
    $class = "App\\Models\\{$model}";
    try {
        $instance = new $class;
        $casts = $instance->getCasts();

        $allGood = true;
        foreach ($castFields as $field) {
            if (isset($casts[$field])) {
                if (strpos($casts[$field], 'decimal') !== false) {
                    echo "  ✅ {$model}.{$field} = {$casts[$field]}\n";
                } else {
                    echo "  ⚠️  {$model}.{$field} = {$casts[$field]} (debería ser decimal)\n";
                    $allGood = false;
                }
            } else {
                echo "  ❌ {$model}.{$field} NO tiene cast\n";
                $allGood = false;
            }
        }

        $checks["casts_{$model}"] = $allGood;
    } catch (Exception $e) {
        echo "❌ {$model}: {$e->getMessage()}\n";
        $checks["casts_{$model}"] = false;
    }
}

// =============================================================================
// 7. VERIFICAR MÉTODOS NUEVOS
// =============================================================================

echo "\n7️⃣  VERIFICANDO MÉTODOS NUEVOS...\n";
echo "──────────────────────────────────\n";

$methodChecks = [
    'Venta' => ['calcularTarifa', 'calcularTarifaUnitaria'],
    'Caja' => ['cerrar', 'calcularSaldo', 'estaAbierta', 'estaCerrada'],
    'Inventario' => ['aumentarStock', 'disminuirStock', 'estaVencido', 'esStockBajo'],
    'PaymentTransaction' => ['isSuccessful', 'isFailed', 'markAsSuccess', 'markAsFailed'],
];

foreach ($methodChecks as $model => $methods) {
    $class = "App\\Models\\{$model}";
    try {
        $instance = new $class;
        foreach ($methods as $method) {
            if (method_exists($instance, $method)) {
                echo "  ✅ {$model}->{$method}()\n";
                $checks["method_{$model}_{$method}"] = true;
            } else {
                echo "  ❌ {$model}->{$method}() NO existe\n";
                $checks["method_{$model}_{$method}"] = false;
            }
        }
    } catch (Exception $e) {
        echo "❌ {$model}: {$e->getMessage()}\n";
    }
}

// =============================================================================
// 8. VERIFICAR PIVOTS CON TARIFA_UNITARIA
// =============================================================================

echo "\n8️⃣  VERIFICANDO PIVOTS...\n";
echo "──────────────────────────\n";

// Esto es más difícil de validar sin datos reales, pero podemos verificar
// que los métodos existen

try {
    $venta = new App\Models\Venta();
    if (method_exists($venta, 'productos')) {
        echo "  ✅ Venta->productos() relación existe\n";
        $checks["pivot_venta_productos"] = true;
    } else {
        echo "  ❌ Venta->productos() NO existe\n";
        $checks["pivot_venta_productos"] = false;
    }

    $producto = new App\Models\Producto();
    if (method_exists($producto, 'ventas')) {
        echo "  ✅ Producto->ventas() relación existe\n";
        $checks["pivot_producto_ventas"] = true;
    } else {
        echo "  ❌ Producto->ventas() NO existe\n";
        $checks["pivot_producto_ventas"] = false;
    }
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

// =============================================================================
// 9. VERIFICAR SCOPES
// =============================================================================

echo "\n9️⃣  VERIFICANDO SCOPES...\n";
echo "──────────────────────────\n";

$scopeChecks = [
    'Venta' => ['forEmpresa', 'enPeriodo'],
    'Caja' => ['abierta', 'cerrada', 'forEmpresa'],
    'Movimiento' => ['ingresos', 'egresos', 'forEmpresa'],
    'Inventario' => ['stockBajo', 'proximoVencimiento'],
];

foreach ($scopeChecks as $model => $scopes) {
    $class = "App\\Models\\{$model}";
    try {
        $instance = new $class;
        foreach ($scopes as $scope) {
            $methodName = 'scope' . ucfirst($scope);
            if (method_exists($instance, $methodName)) {
                echo "  ✅ {$model}::{$scope}() scope existe\n";
                $checks["scope_{$model}_{$scope}"] = true;
            } else {
                echo "  ❌ {$model}::{$scope}() scope NO existe\n";
                $checks["scope_{$model}_{$scope}"] = false;
            }
        }
    } catch (Exception $e) {
        echo "❌ {$model}: {$e->getMessage()}\n";
    }
}

// =============================================================================
// RESUMEN FINAL
// =============================================================================

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           RESUMEN FINAL                               ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$passed = count(array_filter($checks));
$total = count($checks);
$percentage = ($passed / $total) * 100;

echo "Validaciones pasadas:  {$passed}/{$total}\n";
echo "Porcentaje:            " . round($percentage, 2) . "%\n";
echo "\n";

if ($percentage == 100) {
    echo "✅ TODAS LAS VALIDACIONES PASARON\n";
    echo "\n";
    echo "🎉 Los modelos están listos para producción\n";
} elseif ($percentage >= 90) {
    echo "⚠️  VALIDACIÓN PARCIAL - Revisar items fallidos\n";
    echo "\n";
    echo "Fallos encontrados:\n";
    foreach ($checks as $check => $result) {
        if (!$result) {
            echo "  ❌ {$check}\n";
        }
    }
} else {
    echo "❌ VALIDACIÓN CRÍTICA - Requiere correcciones\n";
    echo "\n";
    echo "Fallos encontrados:\n";
    foreach ($checks as $check => $result) {
        if (!$result) {
            echo "  ❌ {$check}\n";
        }
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════════════\n";
echo "\n";
?>
