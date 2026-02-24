<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarSaludMultiTenant extends Command
{
    protected $signature = 'health:multi-tenant';
    protected $description = 'Verifica la integridad del aislamiento multi-tenant';

    public function handle()
    {
        $this->info('🏥 Verificando salud del sistema multi-tenant...');
        $this->newLine();

        $errores = 0;

        // 1. Verificar tablas críticas
        $tablasConEmpresaId = [
            'users',
            'ventas',
            'productos',
            'cajas',
            'movimientos',
            'funciones',
            'funcion_asientos',
            'salas',
            'peliculas',
            'facturas_compra',
            'factura_compra_items',
            'devoluciones',
            'documentos_fiscales',
            'rules',
            'rule_executions',
            'activity_logs',
        ];

        $this->info('📊 Verificando tablas críticas...');

        foreach ($tablasConEmpresaId as $tabla) {
            if (!$this->tablaExiste($tabla)) {
                $this->warn("⚠️  Tabla {$tabla} no existe (puede ser normal)");
                continue;
            }

            $huerfanos = DB::table($tabla)
                ->whereNull('empresa_id')
                ->count();

            if ($huerfanos > 0) {
                $this->error("❌ {$tabla}: {$huerfanos} registros SIN empresa_id");
                $errores++;
            } else {
                $this->info("✅ {$tabla}: Todos los registros tienen empresa_id");
            }
        }

        $this->newLine();

        // 2. Verificar relaciones cruzadas
        $this->info('🔗 Verificando relaciones cruzadas...');

        // Ventas con productos de otra empresa (vía tabla pivote si existe)
        if ($this->tablaExiste('producto_venta')) {
            $cruzadas = DB::table('ventas as v')
                ->join('producto_venta as pv', 'v.id', '=', 'pv.venta_id')
                ->join('productos as p', 'pv.producto_id', '=', 'p.id')
                ->whereColumn('v.empresa_id', '!=', 'p.empresa_id')
                ->count();

            if ($cruzadas > 0) {
                $this->error("❌ {$cruzadas} ventas con productos de otra empresa");
                $errores++;
            } else {
                $this->info("✅ Ventas/Productos: Sin cruces");
            }
        }

        // Ventas con funciones de otra empresa (vía tabla pivote)
        if ($this->tablaExiste('venta_funcion_asientos')) {
            $cruzadas = DB::table('ventas as v')
                ->join('venta_funcion_asientos as vfa', 'v.id', '=', 'vfa.venta_id')
                ->join('funcion_asientos as fa', 'vfa.funcion_asiento_id', '=', 'fa.id')
                ->join('funciones as f', 'fa.funcion_id', '=', 'f.id')
                ->whereColumn('v.empresa_id', '!=', 'f.empresa_id')
                ->count();

            if ($cruzadas > 0) {
                $this->error("❌ {$cruzadas} ventas con funciones de otra empresa");
                $errores++;
            } else {
                $this->info("✅ Ventas/Funciones: Sin cruces");
            }
        }

        $this->newLine();

        // 3. Verificar roles y permisos aislados
        $this->info('🔐 Verificando aislamiento de roles...');

        if ($this->tablaExiste('roles')) {
            $rolesSinEmpresa = DB::table('roles')
                ->whereNull('empresa_id')
                ->count();

            if ($rolesSinEmpresa > 0) {
                $this->error("❌ {$rolesSinEmpresa} roles SIN empresa_id");
                $errores++;
            } else {
                $this->info("✅ Roles: Todos tienen empresa_id");
            }
        }

        $this->newLine();

        // Resultado final
        if ($errores === 0) {
            $this->info('🟢 SISTEMA SALUDABLE');
            $this->info('✅ Aislamiento multi-tenant: CORRECTO');
            return 0;
        } else {
            $this->error("🔴 ENCONTRADOS {$errores} PROBLEMAS");
            $this->warn('⚠️  Revisar y corregir antes de continuar');
            return 1;
        }
    }

    private function tablaExiste(string $tabla): bool
    {
        try {
            DB::table($tabla)->limit(1)->get();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
