@extends('layouts.app')

@section('title','Realizar venta')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Realizar Venta</h1>
    <nav class="flex text-sm text-gray-600 mb-6">
        <a href="{{ route('panel') }}" class="hover:text-gray-900">Inicio</a>
        <span class="mx-2">/</span>
        <a href="{{ route('ventas.index')}}" class="hover:text-gray-900">Ventas</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-semibold">Realizar Venta</span>
    </nav>
</div>

<form action="{{ route('ventas.store') }}" method="post">
    @csrf
    <div class="max-w-7xl mx-auto mt-4 px-4">
        <div class="space-y-6">

            <!-----Venta---->
            <div class="bg-white rounded-lg shadow">
                <div class="bg-green-600 text-white p-3 font-semibold text-center rounded-t-lg">
                    Datos generales
                </div>
                <div class="p-6 border-2 border-green-600">
                    <div class="grid grid-cols-1 gap-6">

                        <!--Cliente-->
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Cliente:</label>
                            <select name="cliente_id" id="cliente_id"
                                class="form-control selectpicker show-tick w-full"
                                data-live-search="true" title="Selecciona"
                                data-size='2'>
                                @foreach ($clientes as $item)
                                <option value="{{$item->id}}">{{$item->nombre_documento}}</option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                            <small class="text-red-600 text-xs mt-1 block">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Tipo de comprobante y Método de pago-->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="comprobante_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Comprobante:</label>
                                <select name="comprobante_id" id="comprobante_id"
                                    class="form-control selectpicker w-full"
                                    title="Selecciona">
                                    @foreach ($comprobantes as $item)
                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('comprobante_id')
                                <small class="text-red-600 text-xs mt-1 block">{{ '*'.$message }}</small>
                                @enderror
                            </div>

                            <div>
                                <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                                    Método de pago:</label>
                                <select required name="metodo_pago"
                                    id="metodo_pago"
                                    class="form-control selectpicker w-full"
                                    title="Selecciona">
                                    @foreach ($optionsMetodoPago as $item)
                                    <option value="{{$item->value}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('metodo_pago')
                                <small class="text-red-600 text-xs mt-1 block">{{ '*'.$message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!------venta producto---->
            <div class="bg-white rounded-lg shadow">
                <div class="bg-blue-600 text-white p-3 font-semibold text-center rounded-t-lg">
                    Detalles de la venta
                </div>
                <div class="p-6 border-2 border-blue-600">
                    <div class="space-y-6">

                        <!-----Producto---->
                        <div>
                            <select id="producto_id"
                                class="form-control selectpicker w-full"
                                data-live-search="true" data-size="1"
                                title="Busque un producto aquí">
                                @foreach ($productos as $item)
                                <option value="{{$item->id}}-{{$item->cantidad}}-{{$item->precio}}-{{$item->nombre}}-{{$item->sigla}}">
                                    {{'Código: '. $item->codigo.' - '. $item->nombre.' - '.$item->sigla}}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-----Stock y Precio (lado derecho)-->
                        <div class="flex flex-col md:flex-row justify-end gap-6">
                            <div class="md:w-1/2">
                                <div class="flex items-center gap-4">
                                    <label for="stock" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                                        En stock:</label>
                                    <div class="flex-1">
                                        <input disabled id="stock"
                                            type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                                    </div>
                                </div>
                            </div>
                            <div class="md:w-1/2">
                                <div class="flex items-center gap-4">
                                    <label for="precio" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                                        Precio:</label>
                                    <div class="flex-1">
                                        <input disabled id="precio"
                                            type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                            step="any">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-----Cantidad y Botón---->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                            <div>
                                <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cantidad:</label>
                                <input type="number" id="cantidad"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <button id="btn_agregar" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors" type="button">
                                Agregar
                            </button>
                        </div>

                        <!-----Tabla para el detalle de la venta--->
                        <div class="overflow-x-auto">
                            <table id="tabla_detalle" class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-blue-600 text-white">
                                        <th class="border border-gray-300 p-3 text-left text-sm font-semibold">Producto</th>
                                        <th class="border border-gray-300 p-3 text-left text-sm font-semibold">Presentación</th>
                                        <th class="border border-gray-300 p-3 text-center text-sm font-semibold">Cantidad</th>
                                        <th class="border border-gray-300 p-3 text-right text-sm font-semibold">Precio</th>
                                        <th class="border border-gray-300 p-3 text-right text-sm font-semibold">Subtotal</th>
                                        <th class="border border-gray-300 p-3 text-center text-sm font-semibold">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot class="font-semibold bg-gray-100 border-t-2 border-gray-300">
                                    <tr>
                                        <td colspan="4" class="border border-gray-300 p-3 text-right">Subtotal</td>
                                        <td colspan="2" class="border border-gray-300 p-3">
                                            <input type="hidden" name="subtotal"
                                                value="0"
                                                id="inputSubtotal">
                                            <span id="sumas">0</span>
                                            <span>{{$empresa->moneda->simbolo}}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="border border-gray-300 p-3 text-right">
                                            {{$empresa->abreviatura_impuesto}} ({{$empresa->porcentaje_impuesto}})%
                                        </td>
                                        <td colspan="2" class="border border-gray-300 p-3">
                                            <input type="hidden" name="impuesto"
                                                id="inputImpuesto"
                                                value="0">
                                            <span id="igv">0</span>
                                            <span>{{$empresa->moneda->simbolo}}</span>
                                        </td>
                                    </tr>
                                    <tr class="bg-blue-50">
                                        <td colspan="4" class="border border-gray-300 p-3 text-right font-bold text-lg">Total</td>
                                        <td colspan="2" class="border border-gray-300 p-3 font-bold text-lg">
                                            <input type="hidden" name="total" value="0" id="inputTotal">
                                            <span id="total">0</span>
                                            <span>{{$empresa->moneda->simbolo}}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!--Boton para cancelar venta--->
                        <div>
                            <button id="cancelar" type="button"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                                data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Cancelar venta
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!----Finalizar venta-->
            <div class="bg-white rounded-lg shadow">
                <div class="bg-blue-600 text-white p-3 font-semibold text-center rounded-t-lg">
                    Finalizar venta
                </div>

                <div class="p-6 border-2 border-blue-600">

                    <div class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="dinero_recibido" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ingrese dinero recibido:</label>
                                <input type="number" id="dinero_recibido"
                                    name="monto_recibido" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    step="any">
                            </div>

                            <div>
                                <label for="vuelto" class="block text-sm font-medium text-gray-700 mb-2">
                                    Vuelto:</label>
                                <input readonly type="number" name="vuelto_entregado"
                                    id="vuelto" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" step="any">
                            </div>
                        </div>

                        <!--Botones--->
                        <div class="flex justify-center">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors text-lg" id="guardar">
                                Realizar venta</button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Modal para cancelar la venta -->
    <div class="hidden fixed inset-0 z-50 overflow-y-auto" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen">
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('exampleModal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900" id="exampleModalLabel">Advertencia</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('exampleModal').classList.add('hidden')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 text-gray-700">
                    ¿Seguro que quieres cancelar la venta?
                </div>
                <div class="flex gap-3 p-4 border-t border-gray-200">
                    <button type="button" class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 rounded-lg transition-colors" onclick="document.getElementById('exampleModal').classList.add('hidden')">Cerrar</button>
                    <button id="btnCancelarVenta" type="button" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" onclick="document.getElementById('exampleModal').classList.add('hidden')">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

</form>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {

        $('#producto_id').change(mostrarValores);


        $('#btn_agregar').click(function() {
            agregarProducto();
        });

        $('#btnCancelarVenta').click(function() {
            cancelarVenta();
        });

        disableButtons();

        $('#dinero_recibido').on('input', function() {
            let dineroRecibido = parseFloat($(this).val());

            if (!isNaN(dineroRecibido) && dineroRecibido >= total && total > 0) {
                let vuelto = dineroRecibido - total;
                $('#vuelto').val(vuelto.toFixed(2));
            } else {
                $('#vuelto').val('');
            }
        });

    });

    //Variables
    let cont = 0;
    let subtotal = [];
    let sumas = 0;
    let igv = 0;
    let total = 0;
    let arrayIdProductos = [];

    //Constantes
    const impuesto = @json($empresa->porcentaje_impuesto);

    function mostrarValores() {
        let dataProducto = document.getElementById('producto_id').value.split('-');
        $('#stock').val(dataProducto[1]);
        $('#precio').val(dataProducto[2]);
    }

    function agregarProducto() {
        let dataProducto = document.getElementById('producto_id').value.split('-');
        //Obtener valores de los campos
        let idProducto = dataProducto[0];
        let nameProducto = dataProducto[3];
        let presentacioneProducto = dataProducto[4];
        let cantidad = $('#cantidad').val();
        let precioVenta = $('#precio').val();
        let stock = $('#stock').val();

        //Validaciones
        //1.Para que los campos no esten vacíos
        if (idProducto != '' && cantidad != '') {

            //2. Para que los valores ingresados sean los correctos
            if (parseInt(cantidad) > 0 && (cantidad % 1 == 0)) {

                //3. Para que la cantidad no supere el stock
                if (parseInt(cantidad) <= parseInt(stock)) {

                    //4.No permitir el ingreso del mismo producto
                    if (!arrayIdProductos.includes(idProducto)) {

                        //Calcular valores
                        subtotal[cont] = round(cantidad * precioVenta);
                        sumas = round(sumas + subtotal[cont]);
                        igv = round(sumas / 100 * impuesto);
                        total = round(sumas + igv);

                        //Crear la fila
                        let fila = '<tr id="fila' + cont + '">' +
                            '<td><input type="hidden" name="arrayidproducto[]" value="' + idProducto + '">' + nameProducto + '</td>' +
                            '<td>' + presentacioneProducto + '</td>' +
                            '<td><input type="hidden" name="arraycantidad[]" value="' + cantidad + '">' + cantidad + '</td>' +
                            '<td><input type="hidden" name="arrayprecioventa[]" value="' + precioVenta + '">' + precioVenta + '</td>' +
                            '<td>' + subtotal[cont] + '</td>' +
                            '<td><button class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" type="button" onClick="eliminarProducto(' + cont + ',' + idProducto + ')"><i class="fa-solid fa-trash"></i></button></td>' +
                            '</tr>';

                        //Acciones después de añadir la fila
                        $('#tabla_detalle').append(fila);
                        limpiarCampos();
                        cont++;
                        disableButtons();

                        //Mostrar los campos calculados
                        $('#sumas').html(sumas);
                        $('#igv').html(igv);
                        $('#total').html(total);
                        $('#inputImpuesto').val(igv);
                        $('#inputTotal').val(total);
                        $('#inputSubtotal').val(sumas);

                        //Agregar el id del producto al arreglo
                        arrayIdProductos.push(idProducto);
                    } else {
                        showModal('Ya ha ingresado el producto');
                    }

                } else {
                    showModal('Cantidad incorrecta');
                }

            } else {
                showModal('Valores incorrectos');
            }

        } else {
            showModal('Le faltan campos por llenar');
        }

    }

    function eliminarProducto(indice, idProducto) {
        //Calcular valores
        sumas -= round(subtotal[indice]);
        igv = round(sumas / 100 * impuesto);
        total = round(sumas + igv);

        //Mostrar los campos calculados
        $('#sumas').html(sumas);
        $('#igv').html(igv);
        $('#total').html(total);
        $('#inputImpuesto').val(igv);
        $('#inputTotal').val(total);
        $('#inputSubtotal').val(sumas);

        //Eliminar el fila de la tabla
        $('#fila' + indice).remove();

        //Eliminar id del arreglo
        let index = arrayIdProductos.indexOf(idProducto.toString());
        arrayIdProductos.splice(index, 1);

        disableButtons();
    }

    function cancelarVenta() {
        //Elimar el tbody de la tabla
        $('#tabla_detalle tbody').empty();

        //Añadir una nueva fila a la tabla
        let fila = '<tr>' +
            '<th></th>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '</tr>';
        $('#tabla_detalle').append(fila);

        //Reiniciar valores de las variables
        cont = 0;
        subtotal = [];
        sumas = 0;
        igv = 0;
        total = 0;
        arrayIdProductos = [];

        //Mostrar los campos calculados
        $('#sumas').html(sumas);
        $('#igv').html(igv);
        $('#total').html(total);
        $('#inputImpuesto').val(igv);
        $('#inputTotal').val(total);
        $('#inputSubtotal').val(sumas);

        limpiarCampos();
        disableButtons();
    }

    function disableButtons() {
        if (total == 0) {
            $('#guardar').hide();
            $('#cancelar').hide();
        } else {
            $('#guardar').show();
            $('#cancelar').show();
        }
    }

    function limpiarCampos() {
        let select = $('#producto_id');
        select.selectpicker('val', '');
        $('#cantidad').val('');
        $('#precio').val('');
        $('#stock').val('');
    }

    function showModal(message, icon = 'error') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Toast.fire({
            icon: icon,
            title: message
        })
    }

    function round(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }
    //Fuente: https://es.stackoverflow.com/questions/48958/redondear-a-dos-decimales-cuando-sea-necesario
</script>
@endpush
