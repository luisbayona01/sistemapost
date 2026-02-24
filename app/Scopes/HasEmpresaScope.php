<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Log;

class HasEmpresaScope implements Scope
{
    private static $processing = [];

    public function apply(Builder $builder, Model $model)
    {
        $class = get_class($model);

        if ($class === \App\Models\User::class || in_array($class, self::$processing)) {
            return; // NUNCA aplicar a User ni recursión
        }

        self::$processing[] = $class;

        try {
            $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

            if (!$tenantId) {
                $tenantId = request()->get('tenant_id');
            }

            if ($tenantId) {
                $builder->where($model->getTable() . '.empresa_id', $tenantId);
            }
        } finally {
            array_pop(self::$processing);
        }
    }
}
