<?php

namespace App\Events;

use App\Models\Rule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado DESPUÉS de que una regla es evaluada.
 * Permite que otros módulos reaccionen a los resultados del Motor de Reglas.
 *
 * Uso:
 *   event(new RuleEvaluatedEvent($rule, $context, $matched, $actionsResult));
 *
 * Escuchar:
 *   Event::listen(RuleEvaluatedEvent::class, function(RuleEvaluatedEvent $event) { ... });
 */
class RuleEvaluatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Rule $rule,
        public readonly array $context,
        public readonly bool $matched,
        public readonly array $actionsResult = [],
        public readonly string $eventType = '',
    ) {
    }

    /**
     * Retorna true si la regla coincidió Y todas las acciones se ejecutaron sin error.
     */
    public function wasSuccessful(): bool
    {
        if (!$this->matched) {
            return false;
        }

        foreach ($this->actionsResult as $result) {
            if (isset($result['error'])) {
                return false;
            }
        }

        return true;
    }
}
