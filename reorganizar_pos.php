<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Caracteristica;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // 1. Identificar categorías principales
    $catComidas = Categoria::find(21);
    $catBebidas = Categoria::find(22);
    $catTragos = Categoria::find(23);
    $catPostres = Categoria::find(7);

    // 2. Renombrar las características (que es donde reside el nombre real mostrado)
    if ($catComidas && $catComidas->caracteristica) {
        $catComidas->caracteristica->update(['nombre' => 'comidas']);
        echo "Renombrado ID 21 a comidas\n";
    }
    if ($catBebidas && $catBebidas->caracteristica) {
        $catBebidas->caracteristica->update(['nombre' => 'bebidas']);
        echo "Renombrado ID 22 a bebidas\n";
    }
    if ($catTragos && $catTragos->caracteristica) {
        $catTragos->caracteristica->update(['nombre' => 'tragos o cocteles']);
        echo "Renombrado ID 23 a tragos o cocteles\n";
    }
    if ($catPostres && $catPostres->caracteristica) {
        $catPostres->caracteristica->update(['nombre' => 'postres']);
        echo "Renombrado ID 7 a postres\n";
    } else if ($catPostres) {
        // Si no tiene caracteristica (raro), crearla
        $carac = Caracteristica::create(['nombre' => 'postres', 'empresa_id' => $catPostres->empresa_id]);
        $catPostres->update(['caracteristica_id' => $carac->id]);
        echo "Creada caracteristica postres para ID 7\n";
    }

    // 3. Reagrupar productos
    // Brownie (63) -> Postres (7)
    $brownie = Producto::find(63);
    if ($brownie) {
        $brownie->update(['categoria_id' => 7]);
        echo "Movido Brownie a Postres\n";
    }

    // Cerveza (66) -> Tragos o Cocteles (23)
    $cerveza = Producto::find(66);
    if ($cerveza) {
        $cerveza->update(['categoria_id' => 23]);
        echo "Movida Cerveza a Tragos o Cocteles\n";
    }

    // Gaseosa (65) debe estar en Bebidas (22) - Ya está ahí según el log anterior.

    // Crispetas, Perro, Pizzas (64, 60, 62, 61) deben estar en Comidas (21) - Ya están ahí.

    DB::commit();
    echo "¡Proceso de agrupación completado con éxito!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
