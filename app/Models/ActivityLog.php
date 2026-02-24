<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEmpresaScope;

class ActivityLog extends Model
{
    use HasEmpresaScope;
    protected $fillable = ['empresa_id', 'user_id', 'action', 'module', 'data', 'ip_address', 'user_agent'];

    protected $casts = [
        'data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtFormattedAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('d/m/Y H:i');
    }
}
