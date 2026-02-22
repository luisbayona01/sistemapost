<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rule extends Model
{
    use SoftDeletes;

    protected $table = 'rules';

    protected $fillable = [
        'empresa_id',
        'name',
        'description',
        'event_type',
        'logical_operator',
        'priority',
        'active',
        'stop_on_match',
        'execution_count',
        'last_executed_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'stop_on_match' => 'boolean',
        'priority' => 'integer',
        'execution_count' => 'integer',
        'last_executed_at' => 'datetime',
    ];

    // ── RELACIONES ───────────────────────────────────────────────────────────

    public function conditions(): HasMany
    {
        return $this->hasMany(RuleCondition::class)->orderBy('sort_order');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(RuleAction::class)->where('active', true)->orderBy('sort_order');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(RuleExecution::class);
    }

    // ── SCOPES ───────────────────────────────────────────────────────────────

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForEvent($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority');
    }

    // ── HELPERS ──────────────────────────────────────────────────────────────

    public function incrementExecutionCount(): void
    {
        $this->increment('execution_count');
        $this->update(['last_executed_at' => now()]);
    }

    public function isAndLogic(): bool
    {
        return strtoupper($this->logical_operator) === 'AND';
    }
}
