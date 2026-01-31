{{-- Stripe Payment Modal Component --}}
<div id="stripe-payment-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-semibold text-white">Confirmar Pago</h3>
            <p class="text-blue-100 text-sm mt-1">Ingresa tu información de tarjeta de crédito</p>
        </div>

        <!-- Body -->
        <div class="px-6 py-6">
            <form id="stripe-payment-form" class="space-y-4">
                <!-- Amount Display -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total a Pagar:</span>
                        <span class="text-2xl font-bold text-gray-900">
                            $<span id="payment-amount">0.00</span>
                        </span>
                    </div>
                </div>

                <!-- Card Element -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Información de la Tarjeta
                    </label>
                    <div id="card-element" class="px-4 py-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></div>
                    <p id="card-error" class="text-red-600 text-sm mt-2 hidden"></p>
                </div>

                <!-- Payment status messages -->
                <div id="payment-message" class="hidden p-4 rounded-lg text-sm font-medium"></div>

                <!-- Hidden fields -->
                <input type="hidden" id="client-secret" />
                <input type="hidden" id="venta-id" />

                <!-- Buttons -->
                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        onclick="document.getElementById('stripe-payment-modal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition"
                    >
                        Cancelar
                    </button>
                    <button
                        id="payment-submit-btn"
                        type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <span id="btn-text">Pagar Ahora</span>
                        <svg id="btn-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Test card info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
                    <p class="font-semibold mb-1">Tarjeta de Prueba (Modo Test):</p>
                    <p>• Número: <code class="bg-white px-2 py-1 rounded">4242 4242 4242 4242</code></p>
                    <p>• Mes/Año: Cualquiera en el futuro</p>
                    <p>• CVC: Cualquier número</p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal JS Handler -->
<script>
    // Modal management
    window.openStripePaymentModal = function(ventaId, amount, clientSecret) {
        const modal = document.getElementById('stripe-payment-modal');
        const amountDisplay = document.getElementById('payment-amount');
        const clientSecretInput = document.getElementById('client-secret');
        const ventaIdInput = document.getElementById('venta-id');

        // Set amount in dollars
        amountDisplay.textContent = (amount / 100).toFixed(2);
        clientSecretInput.value = clientSecret;
        ventaIdInput.value = ventaId;

        // Clear previous error messages
        const errorElement = document.getElementById('card-error');
        errorElement.textContent = '';
        errorElement.classList.add('hidden');

        const messageElement = document.getElementById('payment-message');
        messageElement.textContent = '';
        messageElement.classList.add('hidden');

        // Show modal
        modal.classList.remove('hidden');
    };

    window.closeStripePaymentModal = function() {
        const modal = document.getElementById('stripe-payment-modal');
        modal.classList.add('hidden');
    };
</script>
