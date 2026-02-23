<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleExecution extends Model
{
    protected $table = 'rule_executions';

    public $timestamps = false;

    protected $fillable = [
        'rule_id',
        'empresa_id',
        'event_type',
        'context',
        'conditions_result',
        'actions_result',
        'matched',
        'executed',
        'error_message',
        'execution_time_ms',
        'executed_at',
    ];

    protected $casts = [
        'context' => 'array',
        'conditions_result' => 'array',
        'actions_result' => 'array',
        'matched' => 'boolean',
        'executed' => 'boolean',
        'executed_at' => 'datetime',
    ];

    // ── RELACIONES ───────────────────────────────────────────────────────────

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    // ── SCOPES ───────────────────────────────────────────────────────────────

    public function scopeMatched($query)
    {
        return $query->where('matched', true);
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForEvent($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('executed_at', '>=', now()->subDays($days));
    }
}
