<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleCondition extends Model
{
    protected $table = 'rule_conditions';

    protected $fillable = [
        'rule_id',
        'field',
        'operator',
        'value',
        'data_type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ── RELACIONES ───────────────────────────────────────────────────────────

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    // ── EVALUACIÓN ───────────────────────────────────────────────────────────

    /**
     * Evalúa esta condición contra un valor del contexto.
     */
    public function evaluate(array $context): bool
    {
        $haystack = data_get($context, $this->field);

        if ($haystack === null) {
            return false;
        }

        $reference = $this->castValue($this->value);

        return match ($this->operator) {
            '>' => $haystack > $reference,
            '>=' => $haystack >= $reference,
            '<' => $haystack < $reference,
            '<=' => $haystack <= $reference,
            '==' => $haystack == $reference,
            '!=' => $haystack != $reference,
            'in' => in_array($haystack, (array) $reference),
            'not_in' => !in_array($haystack, (array) $reference),
            'contains' => str_contains((string) $haystack, (string) $reference),
            default => false,
        };
    }

    private function castValue(string $raw): mixed
    {
        return match ($this->data_type) {
            'numeric' => is_numeric($raw) ? (float) $raw : $raw,
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'array' => json_decode($raw, true) ?? explode(',', $raw),
            default => $raw,
        };
    }
}
