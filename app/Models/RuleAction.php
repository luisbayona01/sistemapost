<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleAction extends Model
{
    protected $table = 'rule_actions';

    protected $fillable = [
        'rule_id',
        'action_type',
        'parameters',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'parameters' => 'array',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── TIPOS DE ACCIÓN PREDEFINIDOS ─────────────────────────────────────────

    public const TYPE_ALERT = 'alert';
    public const TYPE_PRICE_ADJUSTMENT = 'price_adjustment';
    public const TYPE_FLAG = 'flag';
    public const TYPE_NOTIFICATION = 'notification';
    public const TYPE_UPSELL = 'upsell';
    public const TYPE_WEBHOOK = 'webhook';

    public const AVAILABLE_TYPES = [
        self::TYPE_ALERT,
        self::TYPE_PRICE_ADJUSTMENT,
        self::TYPE_FLAG,
        self::TYPE_NOTIFICATION,
        self::TYPE_UPSELL,
        self::TYPE_WEBHOOK,
    ];

    // ── RELACIONES ───────────────────────────────────────────────────────────

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    // ── HELPERS ──────────────────────────────────────────────────────────────

    public function getParameter(string $key, mixed $default = null): mixed
    {
        return data_get($this->parameters, $key, $default);
    }
}
