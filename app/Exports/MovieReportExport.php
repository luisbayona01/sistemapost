<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovieReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $fecha;
    protected $salaId;
    protected $empresaId;

    public function __construct($fecha = null, $salaId = null)
    {
        $this->fecha = $fecha;
        $this->salaId = $salaId;
        $this->empresaId = auth()->user()->empresa_id;
    }

    public function collection()
    {
        $query = DB::table('funcion_asientos as fa')
            ->join('ventas as v', 'v.id', '=', 'fa.venta_id')
            ->join('funciones as f', 'f.id', '=', 'fa.funcion_id')
            ->join('peliculas as p', 'p.id', '=', 'f.pelicula_id')
            ->join('salas as s', 's.id', '=', 'f.sala_id')
            ->where('v.empresa_id', $this->empresaId)
            ->where('v.estado_pago', 'PAGADA');

        if ($this->fecha) {
            $query->whereDate('f.fecha_hora', $this->fecha);
        }

        if ($this->salaId) {
            $query->where('f.sala_id', $this->salaId);
        }

        return $query->select(
            'p.titulo as pelicula',
            's.nombre as sala',
            'f.fecha_hora',
            DB::raw('COUNT(fa.id) as boletos'),
            DB::raw('SUM(f.precio) as monto')
        )
            ->groupBy('p.id', 'p.titulo', 's.id', 's.nombre', 'f.id', 'f.fecha_hora')
            ->orderBy('f.fecha_hora', 'asc')
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->pelicula,
            $row->sala,
            \Carbon\Carbon::parse($row->fecha_hora)->format('d/m/Y'),
            \Carbon\Carbon::parse($row->fecha_hora)->format('h:i A'),
            $row->boletos,
            $row->monto
        ];
    }

    public function headings(): array
    {
        return [
            'Película',
            'Sala',
            'Fecha Función',
            'Hora',
            'Boletos Vendidos',
            'Monto Recaudado'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
