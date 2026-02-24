<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Caja;
use App\Models\Movimiento;
use App\Models\Funcion;
use App\Models\FuncionAsiento;
use App\Models\Sala;
use App\Models\Pelicula;
use App\Models\FacturaCompra;
use App\Models\DocumentoFiscal;
use App\Models\Rule;
use App\Models\RuleExecution;
use App\Models\ActivityLog;

class AuditMultiTenantCommand extends Command
{
    protected $signature = 'audit:multi-tenant';
    protected $description = 'Auditoría completa de aislamiento multi-tenant';

    public function handle()
    {
        $this->info('🚀 Iniciando Auditoría Multi-Tenant...');

        $models = [
            'User' => User::class,
            'Venta' => Venta::class,
            'Producto' => Producto::class,
            'Caja' => Caja::class,
            'Movimiento' => Movimiento::class,
            'Funcion' => Funcion::class,
            'FuncionAsiento' => FuncionAsiento::class,
            'Sala' => Sala::class,
            'Pelicula' => Pelicula::class,
            'FacturaCompra' => FacturaCompra::class,
            'DocumentoFiscal' => DocumentoFiscal::class,
            'Rule' => Rule::class,
            'RuleExecution' => RuleExecution::class,
            'ActivityLog' => ActivityLog::class,
        ];

        $results = [];

        foreach ($models as $name => $class) {
            $hasScope = in_array('App\Traits\HasEmpresaScope', class_uses_recursive($class));
            $countTotal = DB::table((new $class)->getTable())->count();
            $countWithoutEmpresa = DB::table((new $class)->getTable())->whereNull('empresa_id')->count();

            $results[] = [
                'Modelo' => $name,
                'Trait Scope' => $hasScope ? '✅' : '❌',
                'Total Filas' => $countTotal,
                'Sin Empresa' => $countWithoutEmpresa > 0 ? "⚠️ $countWithoutEmpresa" : '✅ 0',
            ];
        }

        $this->table(['Modelo', 'Trait Scope', 'Total Filas', 'Sin Empresa'], $results);

        $this->info("\n🔍 Buscando inconsistencias en relaciones...");

        // 1. Ventas vs Clientes
        $vVsC = DB::table('ventas as v')
            ->join('clientes as c', 'c.id', '=', 'v.cliente_id')
            ->whereRaw('v.empresa_id != c.empresa_id')
            ->count();
        $this->reportResult('Ventas con Clientes de otra empresa', $vVsC);

        // 2. Ventas vs Usuarios (Vendedores)
        $vVsU = DB::table('ventas as v')
            ->join('users as u', 'u.id', '=', 'v.user_id')
            ->whereRaw('v.empresa_id != u.empresa_id')
            ->count();
        $this->reportResult('Ventas con Vendedores de otra empresa', $vVsU);

        // 3. Funciones vs Salas
        $fVsS = DB::table('funciones as f')
            ->join('salas as s', 's.id', '=', 'f.sala_id')
            ->whereRaw('f.empresa_id != s.empresa_id')
            ->count();
        $this->reportResult('Funciones con Salas de otra empresa', $fVsS);

        // 4. Asientos vs Funciones
        $aVsF = DB::table('funcion_asientos as fa')
            ->join('funciones as f', 'f.id', '=', 'fa.funcion_id')
            ->whereRaw('fa.empresa_id != f.empresa_id')
            ->count();
        $this->reportResult('Asientos con Funciones de otra empresa', $aVsF);

        // 5. Inventario vs Productos
        $iVsP = DB::table('inventario as i')
            ->join('productos as p', 'p.id', '=', 'i.producto_id')
            ->whereRaw('i.empresa_id != p.empresa_id')
            ->count();
        $this->reportResult('Stock con Productos de otra empresa', $iVsP);

        $this->info("\n✅ Auditoría finalizada.");
    }

    private function reportResult($label, $count)
    {
        if ($count > 0) {
            $this->error("❌ $label: $count");
        } else {
            $this->info("✅ $label: 0");
        }
    }
}
