@extends('layouts.app')

@section('title','Ver venta')

@push('css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Ver Venta</h1>
    <nav class="flex text-sm text-gray-600 mb-6">
        <a href="{{ route('panel') }}" class="hover:text-gray-900">Inicio</a>
        <span class="mx-2">/</span>
        <a href="{{ route('ventas.index')}}" class="hover:text-gray-900">Ventas</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-semibold">Ver Venta</span>
    </nav>
</div>

<div class="max-w-7xl mx-auto mt-4 px-4">

    <div class="bg-white rounded-lg shadow mb-4">

        <div class="p-4 border-b border-gray-200 font-semibold text-gray-900 flex justify-between items-center">
            <span>Datos generales de la venta</span>
            <!-- Payment Status Badge -->
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($venta->estado_pago === 'PAGADA')
                    bg-green-100 text-green-800
                @elseif($venta->estado_pago === 'FALLIDA')
                    bg-red-100 text-red-800
                @elseif($venta->estado_pago === 'CANCELADA')
                    bg-gray-100 text-gray-800
                @else
                    bg-yellow-100 text-yellow-800
                @endif
            ">
                {{ $venta->estado_pago }}
            </span>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">Comprobante:</span> {{$venta->comprobante->nombre}} ({{$venta->numero_comprobante}})</p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">Cliente:</span> {{$venta->cliente->persona->razon_social}}</p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">Vendedor:</span> {{$venta->user->name}}</p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">Método de pago:</span> {{$venta->metodo_pago}}</p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">Fecha y hora:</span> {{$venta->fecha}} - {{$venta->hora}}</p>
            </div>
        </div>
    </div>


    <!---Tabla--->
    <div class="bg-white rounded-lg shadow mb-2">
        <div class="p-4 border-b border-gray-200 flex items-center gap-2">
            <i class="fas fa-table"></i>
            <span class="font-semibold text-gray-900">Tabla de detalle de la venta</span>
        </div>
        <div class="overflow-x-auto p-6">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="border border-gray-300 p-3 text-left font-semibold">Producto</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold">Presentación</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold">Cantidad</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold">Precio de venta</th>
                        <th class="border border-gray-300 p-3 text-right font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta->productos as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-3">
                            {{$item->nombre}}
                        </td>
                        <td class="border border-gray-300 p-3">
                            {{$item->presentacione->sigla}}
                        </td>
                        <td class="border border-gray-300 p-3">
                            {{$item->pivot->cantidad}}
                        </td>
                        <td class="border border-gray-300 p-3">
                            {{$item->pivot->precio_venta}}
                        </td>
                        <td class="border border-gray-300 p-3 text-right td-subtotal">
                            {{($item->pivot->cantidad) * ($item->pivot->precio_venta)}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="font-semibold bg-gray-100 border-t-2 border-gray-300">
                    <tr>
                        <th colspan="5" class="border border-gray-300 p-3"></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="border border-gray-300 p-3 text-right">Sumas:</th>
                        <th class="border border-gray-300 p-3 text-right">
                            {{$venta->subtotal}} {{$empresa->moneda->simbolo}}
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" class="border border-gray-300 p-3 text-right">{{$empresa->abreviatura_impuesto}} ({{$empresa->porcentaje_impuesto}}%):</th>
                        <th class="border border-gray-300 p-3 text-right">
                            {{$venta->impuesto}} {{$empresa->moneda->simbolo}}
                        </th>
                    </tr>
                    <tr class="bg-blue-50">
                        <th colspan="4" class="border border-gray-300 p-3 text-right">Total:</th>
                        <th class="border border-gray-300 p-3 text-right">
                            {{$venta->total}} {{$empresa->moneda->simbolo}}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Payment Action Section -->
    @if($venta->estado_pago === 'PENDIENTE')
    <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-6 rounded">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-blue-900">Pago Pendiente</h3>
                <p class="text-sm text-blue-700">Esta venta está lista para procesar el pago</p>
            </div>
            <button
                onclick="initializePaymentFlow({{ $venta->id }})"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                Pagar con Stripe
            </button>
        </div>
    </div>
    @endif

</div>

<!-- Stripe Payment Modal Component -->
@include('components.stripe-payment-modal')

@endsection

@push('js')
<script src="{{ asset('js/stripe-payment.js') }}"></script>
