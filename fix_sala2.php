<?php

use App\Models\Sala;
use App\Models\Funcion;
use App\Models\FuncionAsiento;
use Illuminate\Support\Carbon;

// 1. Prepare JSON Configuration
$rows = ['A', 'B', 'C', 'D', 'E'];
$layout = [];
$capacidad = 0;

foreach ($rows as $row) {
    // Seat 1
    $layout[] = ['fila' => $row, 'num' => '1', 'col' => 1, 'tipo' => 'asiento'];
    $capacidad++;
    // Seat 2
    $layout[] = ['fila' => $row, 'num' => '2', 'col' => 2, 'tipo' => 'asiento'];
    $capacidad++;
    // Aisle Middle (Col 3)
    $layout[] = ['fila' => $row, 'num' => null, 'col' => 3, 'tipo' => 'pasillo'];
    // Seat 3
    $layout[] = ['fila' => $row, 'num' => '3', 'col' => 4, 'tipo' => 'asiento'];
    $capacidad++;
    // Seat 4
    $layout[] = ['fila' => $row, 'num' => '4', 'col' => 5, 'tipo' => 'asiento'];
    $capacidad++;
    // Aisle Right (Col 6)
    $layout[] = ['fila' => $row, 'num' => null, 'col' => 6, 'tipo' => 'pasillo'];
}

// 2. Update Sala
$sala = Sala::find(2); // ID 2 found in previous step "Sala 2 (20)"
if ($sala) {
    $sala->configuracion_json = json_encode($layout);
    $sala->capacidad = $capacidad;
    $sala->save();
    echo "Sala 2 updated. Capacidad: $capacidad\n";
} else {
    echo "Sala 2 not found!\n";
    exit;
}

// 3. Fix Future Functions (Regenerate Seats)
$futureFunctions = Funcion::where('sala_id', 2)
    ->where('fecha_hora', '>=', Carbon::now())
    ->get();

foreach ($futureFunctions as $funcion) {
    // Check for sold seats
    $soldCount = FuncionAsiento::where('funcion_id', $funcion->id)
        ->where('estado', 'vendido')
        ->count();

    if ($soldCount > 0) {
        echo "Funcion {$funcion->id} skip (Has sales).\n";
        continue;
    }

    // Delete existing available/blocked seats
    FuncionAsiento::where('funcion_id', $funcion->id)->delete();

    // Regenerate from new layout
    foreach ($layout as $seat) {
        if ($seat['tipo'] === 'asiento') {
            FuncionAsiento::create([
                'funcion_id' => $funcion->id,
                'codigo_asiento' => $seat['fila'] . $seat['num'],
                'estado' => 'disponible'
            ]);
        }
    }
    echo "Funcion {$funcion->id} seats regenerated.\n";
}
