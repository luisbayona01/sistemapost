<?php

namespace App\Modules\Core\Contracts;

use App\Models\Rule;

interface RuleEngineInterface
{
    /**
     * Evalúa todas las reglas activas para un tipo de evento y empresa.
     * Retorna un array de resultados por regla evaluada.
     *
     * @param  string  $eventType  Identificador del evento. Ej: 'stock.low', 'sala.high_occupancy'
     * @param  array   $context    Datos del evento. Ej: ['stock' => 5, 'producto_id' => 12]
     * @param  int     $empresaId  Tenant ID
     * @return array<int, array{rule_id: int, matched: bool, actions_result: array}>
     */
    public function evaluate(string $eventType, array $context, int $empresaId): array;

    /**
     * Ejecuta las acciones de una regla específica contra un contexto dado.
     *
     * @param  Rule   $rule     La regla cuyos actions se ejecutarán
     * @param  array  $context  Datos de ejecución
     * @return array<string, mixed>  Resultado de cada acción ejecutada
     */
    public function execute(Rule $rule, array $context): array;

    /**
     * Registra un handler personalizado para un tipo de acción.
     * Permite extensibilidad sin tocar el núcleo.
     *
     * @param  string    $actionType  Ej: 'send_sms', 'whatsapp_alert'
     * @param  callable  $handler     fn(RuleAction $action, array $context): array
     */
    public function registerActionHandler(string $actionType, callable $handler): void;

    /**
     * Retorna todos los tipos de eventos registrados en el sistema.
     *
     * @return array<string, string>  ['stock.low' => 'Stock bajo en producto', ...]
     */
    public function getRegisteredEventTypes(): array;

    /**
     * Registra un nuevo tipo de evento disponible para configurar reglas.
     *
     * @param  string  $eventType    Identificador único. Ej: 'custom.event'
     * @param  string  $label        Nombre legible para la UI
     * @param  array   $contextKeys  Claves disponibles en el contexto. Ej: ['stock', 'producto_id']
     */
    public function registerEventType(string $eventType, string $label, array $contextKeys = []): void;
}
