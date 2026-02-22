<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DocumentoFiscal extends Model
{
    protected $table = 'documentos_fiscales';

    protected $fillable = [
        'empresa_id',
        'venta_id',
        'tipo_documento',
        'prefijo',
        'numero',
        'numero_completo',
        'estado',
        'cufe',
        'cude',
        'qr_code',
        'xml_path',
        'pdf_path',
        'cliente_tipo_documento',
        'cliente_documento',
        'cliente_nombre',
        'cliente_email',
        'cliente_telefono',
        'cliente_direccion',
        'subtotal',
        'impuesto_inc',
        'otros_impuestos',
        'total',
        'respuesta_proveedor',
        'mensaje_error',
        'intentos_envio',
        'fecha_emision',
        'fecha_aceptacion_dian',
        'es_contingencia',
        'motivo_contingencia',
        'fecha_contingencia',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuesto_inc' => 'decimal:2',
        'otros_impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'intentos_envio' => 'integer',
        'fecha_emision' => 'datetime',
        'fecha_aceptacion_dian' => 'datetime',
        'es_contingencia' => 'boolean',
        'fecha_contingencia' => 'datetime',
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function lineas()
    {
        return $this->hasMany(DocumentoFiscalLinea::class);
    }

    // Scopes útiles
    public function scopeEmitidos($query)
    {
        return $query->whereIn('estado', ['emitido', 'enviado', 'aceptado']);
    }

    public function scopeEnContingencia($query)
    {
        return $query->where('es_contingencia', true)
            ->where('estado', 'contingencia');
    }

    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Métodos de negocio
    public function marcarComoContingencia($motivo)
    {
        $this->update([
            'estado' => 'contingencia',
            'es_contingencia' => true,
            'motivo_contingencia' => $motivo,
            'fecha_contingencia' => now(),
        ]);
    }

    public function marcarComoEmitido($cufe, $xmlPath, $pdfPath)
    {
        $this->update([
            'estado' => 'emitido',
            'cufe' => $cufe,
            'xml_path' => $xmlPath,
            'pdf_path' => $pdfPath,
            'fecha_emision' => now(),
        ]);
    }

    public function marcarComoAceptado()
    {
        $this->update([
            'estado' => 'aceptado',
            'fecha_aceptacion_dian' => now(),
        ]);
    }

    public function marcarComoRechazado($mensajeError)
    {
        $this->update([
            'estado' => 'rechazado',
            'mensaje_error' => $mensajeError,
        ]);
    }

    public function incrementarIntentos()
    {
        $this->increment('intentos_envio');
    }
}
