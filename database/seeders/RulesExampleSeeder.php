<?php

namespace Database\Seeders;

use App\Models\Rule;
use App\Models\RuleAction;
use App\Models\RuleCondition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder de 3 reglas de ejemplo listas para productión en CinemaPOS.
 *
 * Uso:
 *   php artisan db:seed --class=RulesExampleSeeder
 *
 * Nota: Ajusta el empresa_id según el entorno antes de ejecutar.
 */
class RulesExampleSeeder extends Seeder
{
    public function run(): void
    {
        // Ajusta según tu empresa de prueba
        $empresaId = DB::table('empresas')->value('id') ?? 1;

        $this->rule1_StockBajoCombo($empresaId);
        $this->rule2_AltaOcupacionSobreprecio($empresaId);
        $this->rule3_VentaGrandeSinEfectivo($empresaId);

        $this->command?->info('✅ 3 reglas de ejemplo creadas para empresa_id=' . $empresaId);
    }

    // ─── REGLA 1: Stock de combo < 10 → Alerta + Upsell en caja ─────────────
    private function rule1_StockBajoCombo(int $empresaId): void
    {
        $rule = Rule::create([
            'empresa_id' => $empresaId,
            'name' => 'Alerta Stock Bajo — Combo',
            'description' => 'Cuando el stock de cualquier combo baja de 10 unidades, crear alerta operativa y sugerir upsell al cajero.',
            'event_type' => 'stock.low',
            'logical_operator' => 'AND',
            'priority' => 10,
            'active' => true,
            'stop_on_match' => false,
        ]);

        // Condición: stock < 10
        RuleCondition::create([
            'rule_id' => $rule->id,
            'field' => 'stock',
            'operator' => '<',
            'value' => '10',
            'data_type' => 'numeric',
            'sort_order' => 1,
        ]);

        // Acción 1: Crear alerta
        RuleAction::create([
            'rule_id' => $rule->id,
            'action_type' => RuleAction::TYPE_ALERT,
            'parameters' => [
                'title' => '📦 Stock Crítico',
                'message' => 'El producto {producto_nombre} tiene solo {stock} unidades. Reabastecer urgente.',
                'severity' => 'warning',
                'icon' => 'fas fa-box-open',
            ],
            'sort_order' => 1,
        ]);

        // Acción 2: Sugerir upsell al cajero
        RuleAction::create([
            'rule_id' => $rule->id,
            'action_type' => RuleAction::TYPE_UPSELL,
            'parameters' => [
                'message' => '💡 Ofrece el Combo Grande a tu cliente — quedan pocas unidades del {producto_nombre}.',
                'producto_ids' => [], // poblar con IDs reales de combos
            ],
            'sort_order' => 2,
        ]);
    }

    // ─── REGLA 2: Ocupación sala > 90 % → Sobreprecio +15 % ─────────────────
    private function rule2_AltaOcupacionSobreprecio(int $empresaId): void
    {
        $rule = Rule::create([
            'empresa_id' => $empresaId,
            'name' => 'Sobreprecio Demanda Alta — Sala +90%',
            'description' => 'Cuando una función supera el 90% de ocupación, activar sobreprecio del 15% automáticamente en cache.',
            'event_type' => 'sala.high_occupancy',
            'logical_operator' => 'AND',
            'priority' => 20,
            'active' => true,
            'stop_on_match' => true,
        ]);

        // Condición: occupancy_pct >= 90
        RuleCondition::create([
            'rule_id' => $rule->id,
            'field' => 'occupancy_pct',
            'operator' => '>=',
            'value' => '90',
            'data_type' => 'numeric',
            'sort_order' => 1,
        ]);

        // Acción: Ajuste de precio +15 %
        RuleAction::create([
            'rule_id' => $rule->id,
            'action_type' => RuleAction::TYPE_PRICE_ADJUSTMENT,
            'parameters' => [
                'percentage' => 15,
                'reason' => 'Alta demanda — ocupación {occupancy_pct}%',
                'target' => 'funcion',
            ],
            'sort_order' => 1,
        ]);

        // Acción: Alerta al gerente
        RuleAction::create([
            'rule_id' => $rule->id,
            'action_type' => RuleAction::TYPE_NOTIFICATION,
            'parameters' => [
                'channel' => 'log',
                'message' => 'Función {funcion_id} al {occupancy_pct}% — sobreprecio 15% activado automáticamente.',
            ],
            'sort_order' => 2,
        ]);
    }

    // ─── REGLA 3: Caja con > $2M sin efectivo → Flag revisión ───────────────
    private function rule3_VentaGrandeSinEfectivo(int $empresaId): void
    {
        $rule = Rule::create([
            'empresa_id' => $empresaId,
            'name' => 'Flag Revisión — Venta Grande Sin Efectivo',
            'description' => 'Si una venta supera $2.000.000 sin efectivo, marcar para revisión y crear alerta de auditoría.',
            'event_type' => 'caja.no_cash_high_total',
            'logical_operator' => 'AND',
            'priority' => 5,   // Máxima prioridad
            'active' => true,
            'stop_on_match' => false,
        ]);

        // Condición 1: total_venta > 2_000_000
        RuleCondition::create([
            'rule_id' => $rule->id,
            'field' => 'total_venta',
            'operator' => '>',
            'value' => '2000000',
            'data_type' => 'numeric',
            'sort_order' => 1,
        ]);

        // Condición 2: metodo_pago != EFECTIVO
        RuleCondition::create([
            'rule_id' => $rule->id,
            'field' => 'metodo_pago',
            'operator' => '!=',
            'value' => 'EFECTIVO',
            'data_type' => 'string',
            'sort_order' => 2,
        ]);

        // Acción: Flag de revisión
        RuleAction::create([
            'rule_id' => $rule->id,
            'action_type' => RuleAction::TYPE_FLAG,
            'parameters' => [
                'flag_key' => 'high_value_no_cash',
                'entity' => 'venta',
                'entity_id_field' => 'caja_id',
                'message' => 'Venta de ${total_venta} procesada por {metodo_pago} en caja {caja_id}. Requiere revisión gerencial.',
            ],
            'sort_order' => 1,
        ]);

        // Acción: Alerta auditoría
        RuleAction::create([
            'rule_id' => $rule->id,
            'action_type' => RuleAction::TYPE_ALERT,
            'parameters' => [
                'title' => '🚩 Transacción de Alto Valor Sin Efectivo',
                'message' => 'Se registró una venta de ${total_venta} vía {metodo_pago} en caja {caja_id}. Verifique con el cajero.',
                'severity' => 'danger',
                'icon' => 'fas fa-exclamation-circle',
            ],
            'sort_order' => 2,
        ]);
    }
}
