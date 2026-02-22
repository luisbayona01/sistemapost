<?php

namespace App\Services;

use App\Events\RuleEvaluatedEvent;
use App\Models\Alerta;
use App\Models\Rule;
use App\Models\RuleAction;
use App\Models\RuleExecution;
use App\Modules\Core\Contracts\RuleEngineInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RuleEngineService implements RuleEngineInterface
{
    /** @var array<string, callable> Action handlers registrados externamente */
    private array $actionHandlers = [];

    /** @var array<string, array{label: string, context_keys: array}> Tipos de evento disponibles */
    private array $eventTypes = [];

    public function __construct()
    {
        $this->bootBuiltInEventTypes();
        $this->bootBuiltInActionHandlers();
    }

    // ═══════════════════════════════════════════════════════════════════════
    // IMPLEMENTACIÓN DEL CONTRATO
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Evalúa todas las reglas activas del tipo de evento para la empresa dada.
     */
    public function evaluate(string $eventType, array $context, int $empresaId): array
    {
        $rules = Rule::forEmpresa($empresaId)
            ->active()
            ->forEvent($eventType)
            ->with(['conditions', 'actions'])
            ->ordered()
            ->get();

        $results = [];

        foreach ($rules as $rule) {
            $startMs = round(microtime(true) * 1000);

            try {
                [$matched, $conditionsResult] = $this->evaluateConditions($rule, $context);

                $actionsResult = [];
                if ($matched) {
                    $actionsResult = $this->execute($rule, $context);
                    $rule->incrementExecutionCount();
                }

                $execTimeMs = (int) round(microtime(true) * 1000 - $startMs);

                // Persistir log de ejecución
                RuleExecution::create([
                    'rule_id' => $rule->id,
                    'empresa_id' => $empresaId,
                    'event_type' => $eventType,
                    'context' => $context,
                    'conditions_result' => $conditionsResult,
                    'actions_result' => $actionsResult,
                    'matched' => $matched,
                    'executed' => $matched && !empty($actionsResult),
                    'execution_time_ms' => $execTimeMs,
                    'executed_at' => now(),
                ]);

                // Disparar evento para que otros módulos reaccionen
                event(new RuleEvaluatedEvent($rule, $context, $matched, $actionsResult, $eventType));

                $results[] = [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'matched' => $matched,
                    'actions_result' => $actionsResult,
                ];

                // Si la regla tiene stop_on_match y coincidió, detener cascada
                if ($matched && $rule->stop_on_match) {
                    break;
                }

            } catch (\Throwable $e) {
                Log::error('[RuleEngine] Error evaluando regla', [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'event_type' => $eventType,
                    'error' => $e->getMessage(),
                ]);

                RuleExecution::create([
                    'rule_id' => $rule->id,
                    'empresa_id' => $empresaId,
                    'event_type' => $eventType,
                    'context' => $context,
                    'matched' => false,
                    'executed' => false,
                    'error_message' => $e->getMessage(),
                    'executed_at' => now(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Ejecuta todas las acciones activas de una regla.
     */
    public function execute(Rule $rule, array $context): array
    {
        $results = [];

        foreach ($rule->actions as $action) {
            try {
                // Buscar handler registrado (built-in o externo)
                $handler = $this->actionHandlers[$action->action_type] ?? null;

                if ($handler === null) {
                    $results[$action->action_type] = ['error' => "Handler no registrado para: {$action->action_type}"];
                    continue;
                }

                $result = ($handler)($action, $context);
                $results[$action->action_type] = $result;

            } catch (\Throwable $e) {
                $results[$action->action_type] = ['error' => $e->getMessage()];
                Log::warning('[RuleEngine] Fallo en acción', [
                    'action_type' => $action->action_type,
                    'rule_id' => $rule->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Registra un handler externo para un tipo de acción.
     */
    public function registerActionHandler(string $actionType, callable $handler): void
    {
        $this->actionHandlers[$actionType] = $handler;
    }

    /**
     * Retorna todos los tipos de eventos disponibles.
     */
    public function getRegisteredEventTypes(): array
    {
        return $this->eventTypes;
    }

    /**
     * Registra un nuevo tipo de evento.
     */
    public function registerEventType(string $eventType, string $label, array $contextKeys = []): void
    {
        $this->eventTypes[$eventType] = [
            'label' => $label,
            'context_keys' => $contextKeys,
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════
    // INTERNAL: EVALUACIÓN DE CONDICIONES
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * @return array{bool, array}  [matched, conditionsResult]
     */
    private function evaluateConditions(Rule $rule, array $context): array
    {
        $conditionsResult = [];
        $results = [];

        foreach ($rule->conditions as $condition) {
            $result = $condition->evaluate($context);
            $conditionsResult[] = [
                'field' => $condition->field,
                'operator' => $condition->operator,
                'value' => $condition->value,
                'result' => $result,
            ];
            $results[] = $result;
        }

        if (empty($results)) {
            return [true, $conditionsResult]; // Regla sin condiciones → siempre coincide
        }

        $matched = $rule->isAndLogic()
            ? !in_array(false, $results, true)   // AND: todas deben ser true
            : in_array(true, $results, true);     // OR: al menos una debe ser true

        return [$matched, $conditionsResult];
    }

    // ═══════════════════════════════════════════════════════════════════════
    // BOOT: TIPOS DE EVENTO BUILT-IN
    // ═══════════════════════════════════════════════════════════════════════

    private function bootBuiltInEventTypes(): void
    {
        $this->registerEventType('stock.low', 'Stock bajo en producto', [
            'stock',
            'producto_id',
            'producto_nombre',
            'empresa_id',
        ]);

        $this->registerEventType('sala.high_occupancy', 'Alta ocupación de sala', [
            'occupancy_pct',
            'asientos_libres',
            'funcion_id',
            'sala_id',
            'empresa_id',
        ]);

        $this->registerEventType('caja.sale_completed', 'Venta completada en caja', [
            'total_venta',
            'metodo_pago',
            'caja_id',
            'user_id',
            'empresa_id',
            'canal',
        ]);

        $this->registerEventType('caja.no_cash_high_total', 'Venta grande sin efectivo', [
            'total_venta',
            'metodo_pago',
            'caja_id',
            'empresa_id',
        ]);

        $this->registerEventType('venta.cortesia', 'Cortesía registrada', [
            'venta_id',
            'user_id',
            'empresa_id',
            'total_venta',
        ]);

        $this->registerEventType('pelicula.bajo_rendimiento', 'Película con baja ocupación', [
            'pelicula_id',
            'funcion_id',
            'occupancy_pct',
            'empresa_id',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // BOOT: HANDLERS BUILT-IN
    // ═══════════════════════════════════════════════════════════════════════

    private function bootBuiltInActionHandlers(): void
    {
        // ── ALERTA ────────────────────────────────────────────────────────────
        $this->registerActionHandler(RuleAction::TYPE_ALERT, function (RuleAction $action, array $context): array {
            $mensaje = $action->getParameter('message', 'Alerta automática del sistema');
            $severity = $action->getParameter('severity', 'warning');
            $icon = $action->getParameter('icon', 'fas fa-bell');

            try {
                Alerta::create([
                    'empresa_id' => $context['empresa_id'] ?? null,
                    'tipo' => 'rule_engine',
                    'severity' => $severity,
                    'titulo' => $action->getParameter('title', 'Regla Activada: ' . $action->rule->name),
                    'mensaje' => $this->interpolate($mensaje, $context),
                    'icon' => $icon,
                    'data' => json_encode([
                        'rule_id' => $action->rule_id,
                        'event_type' => $context['_event_type'] ?? '',
                        'context' => array_slice($context, 0, 10), // evitar payload masivo
                    ]),
                    'resuelta' => false,
                ]);
                return ['status' => 'ok', 'type' => 'alert_created'];
            } catch (\Throwable $e) {
                return ['status' => 'error', 'error' => $e->getMessage()];
            }
        });

        // ── SUGERIR UPSELL ────────────────────────────────────────────────────
        $this->registerActionHandler(RuleAction::TYPE_UPSELL, function (RuleAction $action, array $context): array {
            // Almacena la sugerencia en caché para que el POS la muestre al cajero
            $cacheKey = 'upsell_suggestion_' . ($context['empresa_id'] ?? 0);
            $suggestion = [
                'mensaje' => $this->interpolate($action->getParameter('message', 'Ofrece un combo al cliente'), $context),
                'producto_ids' => $action->getParameter('producto_ids', []),
                'expires_at' => now()->addMinutes(30)->toISOString(),
                'rule_id' => $action->rule_id,
            ];
            cache()->put($cacheKey, $suggestion, now()->addMinutes(30));
            return ['status' => 'ok', 'type' => 'upsell_queued', 'suggestion' => $suggestion];
        });

        // ── AJUSTE DE PRECIO ──────────────────────────────────────────────────
        $this->registerActionHandler(RuleAction::TYPE_PRICE_ADJUSTMENT, function (RuleAction $action, array $context): array {
            $pct = (float) $action->getParameter('percentage', 0);
            $funcion = $action->getParameter('target', 'all');
            $cacheKey = 'price_adjustment_' . ($context['empresa_id'] ?? 0) . '_' . ($context['funcion_id'] ?? 'all');

            cache()->put($cacheKey, [
                'percentage' => $pct,
                'reason' => $action->getParameter('reason', 'Alta demanda'),
                'funcion_id' => $context['funcion_id'] ?? null,
                'expires_at' => now()->addHours(2)->toISOString(),
                'rule_id' => $action->rule_id,
            ], now()->addHours(2));

            return ['status' => 'ok', 'type' => 'price_adjustment_cached', 'adjustment_pct' => $pct];
        });

        // ── FLAG / REVISIÓN ───────────────────────────────────────────────────
        $this->registerActionHandler(RuleAction::TYPE_FLAG, function (RuleAction $action, array $context): array {
            $flagKey = $action->getParameter('flag_key', 'manual_review');
            $entity = $action->getParameter('entity', 'unknown');
            $entityId = data_get($context, $action->getParameter('entity_id_field', 'id'));

            Log::channel('audit')->warning('[RuleEngine FLAG]', [
                'flag' => $flagKey,
                'entity' => $entity,
                'entity_id' => $entityId,
                'rule_id' => $action->rule_id,
                'context' => $context,
            ]);

            // También crear alerta visible
            try {
                Alerta::create([
                    'empresa_id' => $context['empresa_id'] ?? null,
                    'tipo' => 'rule_flag',
                    'severity' => 'danger',
                    'titulo' => '🚩 Revisión Requerida: ' . $action->rule->name,
                    'mensaje' => $this->interpolate($action->getParameter('message', 'Se requiere revisión manual'), $context),
                    'resuelta' => false,
                    'data' => json_encode(['flag_key' => $flagKey, 'rule_id' => $action->rule_id]),
                ]);
            } catch (\Throwable) {
            }

            return ['status' => 'ok', 'type' => 'flagged', 'flag_key' => $flagKey];
        });

        // ── NOTIFICACIÓN (Log para extensión futura) ──────────────────────────
        $this->registerActionHandler(RuleAction::TYPE_NOTIFICATION, function (RuleAction $action, array $context): array {
            Log::info('[RuleEngine NOTIFICATION]', [
                'channel' => $action->getParameter('channel', 'log'),
                'message' => $this->interpolate($action->getParameter('message', ''), $context),
                'rule_id' => $action->rule_id,
            ]);
            return ['status' => 'ok', 'type' => 'notification_sent'];
        });

        // ── WEBHOOK ───────────────────────────────────────────────────────────
        $this->registerActionHandler(RuleAction::TYPE_WEBHOOK, function (RuleAction $action, array $context): array {
            $url = $action->getParameter('url', '');
            if (empty($url)) {
                return ['status' => 'skipped', 'reason' => 'no_url'];
            }

            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->post($url, [
                        'rule_id' => $action->rule_id,
                        'event_type' => $context['_event_type'] ?? '',
                        'context' => $context,
                        'timestamp' => now()->toISOString(),
                    ]);

                return ['status' => 'ok', 'type' => 'webhook_fired', 'http_status' => $response->status()];
            } catch (\Throwable $e) {
                return ['status' => 'error', 'error' => $e->getMessage()];
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPER: INTERPOLACIÓN DE MENSAJES
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Sustituye {variables} en un texto usando el contexto.
     * Ej: "Stock de {producto_nombre} está en {stock} unidades"
     */
    private function interpolate(string $template, array $context): string
    {
        foreach ($context as $key => $value) {
            if (is_scalar($value)) {
                $template = str_replace("{{$key}}", (string) $value, $template);
            }
        }
        return $template;
    }
}
