@extends('layouts.app')

@section('title', 'Ver compra')

@push('css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Ver Compra</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item :href="route('compras.index')" content="Compras" />
        <x-breadcrumb.item active='true' content="Ver Compra" />
    </x-breadcrumb.template>
</div>

<div class="w-full max-w-6xl mx-auto px-4 md:px-6 py-4 space-y-6">

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900">
            Datos generales de la compra
        </div>

        <div class="px-6 py-4 space-y-3">
            <h6 class="text-gray-600">
                Comprobante: {{$compra->comprobante->nombre}} ({{$compra->numero_comprobante}})</h6>
            <h6 class="text-gray-600">
                Proveedor: {{$compra->proveedore->persona->razon_social}}</h6>
            <h6 class="text-gray-600">
                Usuario: {{$compra->user->name}}</h6>
            <h6 class="text-gray-600">
                Método de pago: {{$compra->metodo_pago}}</h6>
            <h6 class="text-gray-600">
                Fecha y hora: {{$compra->fecha}} - {{$compra->hora}}</h6>
        </div>
    </div>


    <!---Tabla--->
    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla de detalle de la compra
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-blue-600">
                    <tr class="text-white">
                        <th class="p-3 text-left">Producto</th>
                        <th class="p-3 text-left">Presentación</th>
                        <th class="p-3 text-left">Cantidad</th>
                        <th class="p-3 text-left">Precio de compra</th>
                        <th class="p-3 text-left">Fecha de vencimiento</th>
                        <th class="p-3 text-left">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compra->productos as $item)
                    <tr class="border-b border-gray-300 hover:bg-gray-50">
                        <td class="p-3">
                            {{$item->nombre}}
                        </td>
                        <td class="p-3">
                            {{$item->presentacione->sigla ?? 'N/A'}}
                        </td>
                        <td class="p-3">
                            {{$item->pivot->cantidad}}
                        </td>
                        <td class="p-3">
                            {{$item->pivot->precio_compra}}
                        </td>
                        <td class="p-3">
                            {{$item->pivot->fecha_vencimiento}}
                        </td>
                        <td class="p-3 td-subtotal">
                            {{($item->pivot->cantidad) * ($item->pivot->precio_compra)}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                    <tr>
                        <th colspan="6" class="p-3"></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="p-3 text-right font-semibold">Sumas:</th>
                        <th class="p-3">
                            <span id="th-suma"></span>
                            <span>{{$empresa->moneda->simbolo}}</span>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="5" class="p-3 text-right font-semibold">{{$empresa->abreviatura_impuesto}}:</th>
                        <th class="p-3">{{$compra->impuesto}} {{$empresa->moneda->simbolo}}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="p-3 text-right font-semibold">Total:</th>
                        <th class="p-3">{{$compra->total}} {{$empresa->moneda->simbolo}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    //Variables
    let filasSubtotal = document.getElementsByClassName('td-subtotal');
    let cont = 0;

    $(document).ready(function () {
        calcularValores();
    });

    function calcularValores() {
        for (let i = 0; i < filasSubtotal.length; i++) {
            cont += parseFloat(filasSubtotal[i].innerHTML);
        }

        $('#th-suma').html(cont);
    }
</script>
@endpush
