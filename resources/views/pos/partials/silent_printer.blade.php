@if (request()->is('pos*'))
    <iframe id="silent_printer" style="display:none; width:0; height:0; border:0;" title="Impresión silenciosa"></iframe>

    <script>
        function imprimirRecibo(url) {
            const frame = document.getElementById('silent_printer');
            frame.src = url;
            frame.onload = () => {
                // Funciona en Chrome/Kiosk mode de las cajas
                frame.contentWindow.print();
            };
        }
    </script>
@endif