@extends('layouts.app')

@section('title','Realizar compra')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Compra</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('compras.index')" content="Compras" />
        <x-breadcrumb.item active='true' content="Crear Compra" />
    </x-breadcrumb.template>
</div>

<form action="{{ route('compras.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="w-full max-w-6xl mx-auto px-4 md:px-6 mt-4 space-y-6">
        <div class="space-y-6">

            <!-----Compra---->
            <div>
                <div class="text-white bg-green-600 p-3 text-center font-semibold">
                    Datos generales
                </div>
                <div class="p-4 border-4 border-green-600 bg-white rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!--Proveedor-->
                        <div class="md:col-span-2">
                            <label for="proveedore_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Proveedor:</label>
                            <select name="proveedore_id"
                                id="proveedore_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker show-tick"
                                data-live-search="true"
                                title="Selecciona" data-size='2'>
                                @foreach ($proveedores as $item)
                                <option value="{{$item->id}}">{{$item->nombre_documento}}</option>
                                @endforeach
                            </select>
                            @error('proveedore_id')
                            <small class="text-red-600">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Tipo de comprobante-->
                        <div>
                            <label for="comprobante_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Comprobante:</label>
                            <select name="comprobante_id"
                                id="comprobante_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker"
                                title="Selecciona">
                                @foreach ($comprobantes as $item)
                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                @endforeach
                            </select>
                            @error('comprobante_id')
                            <small class="text-red-600">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Numero de comprobante-->
                        <div>
                            <label for="numero_comprobante" class="block text-sm font-medium text-gray-700 mb-2">
                                Numero de comprobante:</label>
                            <input type="text"
                                name="numero_comprobante"
                                id="numero_comprobante"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('numero_comprobante')
                            <small class="text-red-600">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Comprobante Path----->
                        <div>
                            <label for="file_comprobante" class="block text-sm font-medium text-gray-700 mb-2">
                                Archivo:</label>
                            <input type="file"
                                name="file_comprobante"
                                id="file_comprobante"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                accept=".pdf">
                            @error('file_comprobante')
                            <small class="text-red-600">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!----Metodo de Pago--->
                        <div>
                            <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                                Método de pago:</label>
                            <select required name="metodo_pago"
                                id="metodo_pago"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker"
                                title="Selecciona">
                                @foreach ($optionsMetodoPago as $item)
                                <option value="{{$item->value}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                            @error('metodo_pago')
                            <small class="text-red-600">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <!--Fecha y hora--->
                        <div>
                            <label for="fecha_hora" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha y hora:</label>
                            <input
                                required
                                type="datetime-local"
                                name="fecha_hora"
                                id="fecha_hora"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                value="">
                            @error('fecha_hora')
                            <small class="text-red-600">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!------Compra producto---->
            <div>
                <div class="text-white bg-blue-600 p-3 text-center font-semibold">
                    Detalles de la compra
                </div>
                <div class="p-4 border-4 border-blue-600 bg-white rounded-lg">
                    <div class="grid grid-cols-1 gap-6">
                        <!-----Producto---->
                        <div>
                            <select id="producto_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker"
                                data-live-search="true"
                                data-size="1"
                                title="Busque un producto aquí">
                                @foreach ($productos as $item)
                                <option value="{{$item->id}}">{{$item->nombre_completo}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-----Cantidad---->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cantidad:</label>
                                <input type="number" id="cantidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-----Precio de compra---->
                            <div>
                                <label for="precio_compra" class="block text-sm font-medium text-gray-700 mb-2">
                                    Precio de compra:</label>
                                <input type="number" id="precio_compra" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" step="0.1">
                            </div>

                            <!-----Fecha de Vencimiento---->
                            <div>
                                <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de vencimiento:</label>
                                <input type="date" id="fecha_vencimiento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-----botón para agregar--->
                        <div class="text-right">
                            <button id="btn_agregar" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors" type="button">
                                Agregar</button>
                        </div>

                        <!-----Tabla para el detalle de la compra--->
                        <div class="w-full overflow-x-auto">
                            <table id="tabla_detalle" class="w-full border-collapse">
                                <thead class="bg-blue-600">
                                    <tr>
                                        <th class="text-white p-3 text-left">Producto</th>
                                        <th class="text-white p-3 text-left">Presentación</th>
                                        <th class="text-white p-3 text-left">Cantidad</th>
                                        <th class="text-white p-3 text-left">Precio</th>
                                        <th class="text-white p-3 text-left">Vencimiento</th>
                                        <th class="text-white p-3 text-left">Subtotal</th>
                                        <th class="text-white p-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="hover:bg-gray-50">
                                    <tr>
                                        <th></th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                                    <tr>
                                        <th colspan="5" class="p-3 text-right font-semibold">Sumas</th>
                                        <th colspan="2" class="p-3">
                                            <input type="hidden" name="subtotal" value="0" id="inputSubtotal">
                                            <span id="sumas">0</span>
                                            <span>{{$empresa->moneda->simbolo}}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="p-3 text-right font-semibold">{{$empresa->abreviatura_impuesto}} %</th>
                                        <th colspan="2" class="p-3">
                                            <input type="hidden" name="impuesto" value="0" id="inputImpuesto">
                                            <span id="igv">0</span>
                                            <span>{{$empresa->moneda->simbolo}}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="p-3 text-right font-semibold">Total</th>
                                        <th colspan="2" class="p-3">
                                            <input type="hidden" name="total" value="0" id="inputTotal">
                                            <span id="total">0</span>
                                            <span>{{$empresa->moneda->simbolo}}</span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!--Boton para cancelar compra-->
                        <div class="mt-4">
                            <button id="cancelar"
                                type="button"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                                onclick="document.getElementById('exampleModal').classList.remove('hidden')">
                                Cancelar compra
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--Botones--->
    <div class="w-full max-w-6xl mx-auto px-4 md:px-6 mt-6 text-center">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors" id="guardar">
            Realizar compra</button>
    </div>

    <!-- Modal para cancelar la compra -->
    <div id="exampleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                Advertencia
            </div>
            <div class="px-6 py-4">
                ¿Seguro que quieres cancelar la compra?
            </div>
            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end gap-2">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                    onclick="document.getElementById('exampleModal').classList.add('hidden')">
                    Cerrar</button>
                <button id="btnCancelarCompra" type="button" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                    onclick="document.getElementById('exampleModal').classList.add('hidden')">
                    Confirmar</button>
            </div>
        </div>
    </div>

</form>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        $('#btn_agregar').click(function() {
            agregarProducto();
        });

        $('#btnCancelarCompra').click(function() {
            cancelarCompra();
        });

        disableButtons();
    });

    //Variables
    let cont = 0;
    let subtotal = [];
    let sumas = 0;
    let igv = 0;
    let total = 0;
    let arrayIdProductos = [];

    //Constantes
    const impuesto = 0;

    function cancelarCompra() {
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
        $('#inputSubtotal').val(sumas);
        $('#inputTotal').val(total);

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

    function agregarProducto() {
        //Obtener valores de los campos
        let idProducto = $('#producto_id').val();
        let textProducto = $('#producto_id option:selected').text();
        let cantidad = $('#cantidad').val();
        let precioCompra = $('#precio_compra').val();
        let fechaVencimiento = $('#fecha_vencimiento').val();

        //Validaciones
        //1.Para que los campos no esten vacíos
        if (textProducto != '' && textProducto != undefined && cantidad != '' && precioCompra != '') {

            let nameProducto = textProducto.match(/-\s(.*?)\s-/)[1];
            let presentacionProducto = textProducto.match(/Presentación:\s(.*)/)[1];

            //2. Para que los valores ingresados sean los correctos
            if (parseInt(cantidad) > 0 && (cantidad % 1 == 0) && parseFloat(precioCompra) > 0) {

                //3. No permitir el ingreso del mismo producto
                if (!arrayIdProductos.includes(idProducto)) {
                    //Calcular valores
                    subtotal[cont] = round(cantidad * precioCompra);
                    sumas = round(sumas + subtotal[cont]);
                    igv = round(sumas / 100 * impuesto);
                    total = round(sumas + igv);

                    //Crear la fila
                    let fila = '<tr id="fila' + cont + '">' +
                        '<td><input type="hidden" name="arrayidproducto[]" value="' + idProducto + '">' + nameProducto + '</td>' +
                        '<td>' + presentacionProducto + '</td>' +
                        '<td><input type="hidden" name="arraycantidad[]" value="' + cantidad + '">' + cantidad + '</td>' +
                        '<td><input type="hidden" name="arraypreciocompra[]" value="' + precioCompra + '">' + precioCompra + '</td>' +
                        '<td><input type="hidden" name="arrayfechavencimiento[]" value="' + fechaVencimiento + '">' + fechaVencimiento + '</td>' +
                        '<td>' + subtotal[cont] + '</td>' +
                        '<td><button class="btn btn-danger" type="button" onClick="eliminarProducto(' + cont + ', ' + idProducto + ')"><i class="fa-solid fa-trash"></i></button></td>' +
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
                    $('#inputSubtotal').val(sumas);
                    $('#inputTotal').val(total);

                    //Agregar el id del producto al arreglo
                    arrayIdProductos.push(idProducto);

                } else {
                    showModal('Ya ha ingresado el producto');
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
        $('#inputSubtotal').val(sumas);
        $('#inputTotal').val(total);

        //Eliminar el fila de la tabla
        $('#fila' + indice).remove();

        //Eliminar el id del arreglo
        let index = arrayIdProductos.indexOf(idProducto.toString());
        arrayIdProductos.splice(index, 1);

        disableButtons();

    }

    function limpiarCampos() {
        let select = $('#producto_id');
        select.selectpicker('val', '');
        $('#cantidad').val('');
        $('#precio_compra').val('');
        $('#fecha_vencimiento').val('');
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
</script>
@endpush
