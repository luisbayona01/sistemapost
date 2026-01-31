/**
 * Stripe Payment Handler
 * Manages Stripe Elements Card integration and payment confirmation
 */

let stripe = null;
let cardElement = null;
let stripePublicKey = null;

/**
 * Initialize Stripe.js and Card Element
 * @param {string} publicKey - Stripe public key
 */
window.initializeStripe = function(publicKey) {
    // Store public key for later use
    stripePublicKey = publicKey;

    // Initialize Stripe
    stripe = Stripe(publicKey);

    // Create Card Element
    const elements = stripe.elements();
    cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                fontFamily: '"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                color: '#1f2937',
                '::placeholder': {
                    color: '#9ca3af',
                }
            },
            invalid: {
                color: '#dc2626',
                iconColor: '#dc2626'
            }
        },
        hidePostalCode: true // We don't need postal code for this POS
    });

    // Mount Card Element
    cardElement.mount('#card-element');

    // Handle Card Element changes
    cardElement.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-error');
        if (event.error) {
            displayError.textContent = event.error.message;
            displayError.classList.remove('hidden');
        } else {
            displayError.textContent = '';
            displayError.classList.add('hidden');
        }
    });

    // Setup form submission
    setupPaymentFormSubmission();

    console.log('✓ Stripe initialized successfully');
};

/**
 * Setup payment form submission handler
 */
function setupPaymentFormSubmission() {
    const form = document.getElementById('stripe-payment-form');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        await handlePaymentSubmit();
    });
}

/**
 * Handle payment form submission
 */
async function handlePaymentSubmit() {
    const form = document.getElementById('stripe-payment-form');
    const submitBtn = document.getElementById('payment-submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const messageElement = document.getElementById('payment-message');
    const errorElement = document.getElementById('card-error');
    const clientSecret = document.getElementById('client-secret').value;
    const ventaId = document.getElementById('venta-id').value;

    if (!stripe || !clientSecret || !ventaId) {
        showError('Error: Configuración de pago incompleta');
        return;
    }

    // Disable submit button and show loading state
    submitBtn.disabled = true;
    btnSpinner.classList.remove('hidden');
    btnText.textContent = 'Procesando...';

    try {
        // Confirm payment intent
        const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    // Optional: Add billing details if needed
                }
            }
        });

        if (error) {
            // Payment failed
            showError(error.message);
            submitBtn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = 'Pagar Ahora';
            return;
        }

        if (paymentIntent.status === 'succeeded') {
            // Payment successful
            showSuccess('¡Pago completado exitosamente!');

            // Wait 2 seconds then close modal and refresh
            setTimeout(() => {
                closeStripePaymentModal();
                // Refresh the venta details to show updated status
                location.reload();
            }, 2000);
        } else if (paymentIntent.status === 'processing') {
            showWarning('Pago en proceso. Verifica el estado en unos momentos.');
            submitBtn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = 'Pagar Ahora';
        } else {
            showError('Estado de pago inesperado: ' + paymentIntent.status);
            submitBtn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = 'Pagar Ahora';
        }
    } catch (error) {
        console.error('Payment error:', error);
        showError('Error procesando el pago: ' + error.message);
        submitBtn.disabled = false;
        btnSpinner.classList.add('hidden');
        btnText.textContent = 'Pagar Ahora';
    }
}

/**
 * Show error message
 * @param {string} message
 */
function showError(message) {
    const errorElement = document.getElementById('card-error');
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
    console.error('Payment error:', message);
}

/**
 * Show success message
 * @param {string} message
 */
function showSuccess(message) {
    const messageElement = document.getElementById('payment-message');
    messageElement.textContent = message;
    messageElement.className = 'hidden p-4 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200';
    messageElement.classList.remove('hidden');
    console.log('Payment success:', message);
}

/**
 * Show warning message
 * @param {string} message
 */
function showWarning(message) {
    const messageElement = document.getElementById('payment-message');
    messageElement.textContent = message;
    messageElement.className = 'hidden p-4 rounded-lg text-sm font-medium bg-yellow-50 text-yellow-700 border border-yellow-200';
    messageElement.classList.remove('hidden');
    console.warn('Payment warning:', message);
}

/**
 * Initialize payment flow for a venta
 * @param {number} ventaId
 */
window.initializePaymentFlow = async function(ventaId) {
    try {
        // Fetch Stripe configuration
        const configResponse = await fetch(`/admin/ventas/${ventaId}/pago/config`);
        if (!configResponse.ok) {
            throw new Error('No se pudo obtener configuración de pago');
        }

        const configData = await configResponse.json();
        if (!configData.success) {
            throw new Error(configData.message || 'Error en configuración');
        }

        // Initialize Stripe if not already initialized
        if (!stripe) {
            initializeStripe(configData.publicKey);
        }

        // Fetch payment intent
        const paymentResponse = await fetch(`/admin/ventas/${ventaId}/pago/iniciar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!paymentResponse.ok) {
            throw new Error('No se pudo iniciar el pago');
        }

        const paymentData = await paymentResponse.json();
        if (!paymentData.success) {
            throw new Error(paymentData.message || 'Error iniciando pago');
        }

        // Open payment modal with payment intent
        openStripePaymentModal(
            paymentData.venta_id,
            paymentData.amount,
            paymentData.client_secret
        );

        console.log('✓ Payment flow initialized');
    } catch (error) {
        console.error('Error initializing payment:', error);
        alert('Error: ' + error.message);
    }
};
