<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoFiscalLinea extends Model
{
    protected $table = 'documento_fiscal_lineas';

    protected $fillable = [
        'documento_fiscal_id',
        'linea',
        'tipo_item',
        'codigo',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal_linea',
        'aplica_inc',
        'valor_inc',
        'total_linea',
    ];

    protected $casts = [
        'linea' => 'integer',
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal_linea' => 'decimal:2',
        'aplica_inc' => 'boolean',
        'valor_inc' => 'decimal:2',
        'total_linea' => 'decimal:2',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoFiscal::class, 'documento_fiscal_id');
    }
}
