/**
 * Razorpay Payment Gateway Integration
 */

function initiateRazorpayPayment(config) {
    const { initiateRoute, verifyRoute, csrfToken, totalAmount } = config;

    // Validate address selection
    const selectedAddressInput = document.getElementById('selected_address');
    let addressSelected = false;

    if (selectedAddressInput && selectedAddressInput.value && selectedAddressInput.value !== '') {
        addressSelected = true;
    }

    // Better check: force update from radio/checkbox
    const checkedCheckbox = document.querySelector('input[class*="address-checkbox"]:checked');
    if (checkedCheckbox) {
        const selectedInput = document.getElementById('selected_address');
        if (selectedInput) {
            selectedInput.value = checkedCheckbox.value;
            addressSelected = true;
        }
    }

    if (!addressSelected) {
        const selectedInput = document.getElementById('selected_address');
        if (!selectedInput || selectedInput.value === '') {
            alert('Please select a shipping address.');
            return;
        }
    }

    const btn = document.getElementById('payWithRazorpayBtn');
    if (!btn) return;

    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';

    // AJAX call to initiate payment order
    fetch(initiateRoute, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            total: totalAmount
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var options = {
                    "key": data.key,
                    "amount": data.amount,
                    "currency": "INR",
                    "name": data.name,
                    "description": data.description,
                    "order_id": data.order_id,
                    "handler": function (response) {
                        // Submit to verify route
                        verifyRazorpayPayment(response, verifyRoute);
                    },
                    "prefill": data.prefill,
                    "theme": {
                        "color": "#490D59"
                    },
                    "modal": {
                        "ondismiss": function () {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    }
                };

                // Check if Razorpay is loaded
                if (typeof Razorpay === 'undefined') {
                    alert('Payment gateway failed to load. Please refresh and try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    return;
                }

                var rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response) {
                    alert(response.error.description);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
                rzp1.open();
            } else {
                alert(data.message || 'Error initiating payment');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}

function verifyRazorpayPayment(paymentResponse, verifyRoute) {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    // Create hidden inputs for payment details
    const addInput = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    };

    addInput('razorpay_payment_id', paymentResponse.razorpay_payment_id);
    addInput('razorpay_order_id', paymentResponse.razorpay_order_id);
    addInput('razorpay_signature', paymentResponse.razorpay_signature);

    // Change action to verify route
    form.action = verifyRoute;
    form.submit();
}
