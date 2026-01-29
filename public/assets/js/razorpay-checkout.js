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

    // Debug: log config being used
    console.log('[Razorpay] initiateRazorpayPayment config:', {
        initiateRoute,
        verifyRoute,
        totalAmount,
        csrfTokenPresent: !!csrfToken
    });

    // AJAX call to initiate payment order
    console.log('[Razorpay] Sending initiate request:', {
        url: initiateRoute,
        method: 'POST',
        body: { total: totalAmount },
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? 'Present' : 'Missing'
        }
    });
    
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
        .then(response => {
            console.log('[Razorpay] initiate response raw:', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok,
                url: response.url,
                headers: Object.fromEntries(response.headers.entries())
            });
            
            if (!response.ok) {
                console.error('[Razorpay] HTTP Error:', response.status, response.statusText);
                return response.text().then(text => {
                    console.error('[Razorpay] Error response body:', text);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('[Razorpay] initiate response JSON:', data);
            
            if (!data) {
                console.error('[Razorpay] ERROR: Empty response data');
                throw new Error('Empty response from server');
            }
            if (data.success) {
                var options = {
                    "key": data.key,
                    // "amount": data.amount, 
                    // "currency": "INR",
                    "name": data.name,
                    "description": data.description,
                    "order_id": data.order_id,
                    "retry": { "enabled": false }, // Disable retry to isolate error
                    "handler": function (response) {
                        console.log('[Razorpay] payment success handler response:', response);
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

                // Show Test Mode Helper if using Test Key
                if (data.key && data.key.startsWith('rzp_test_')) {
                    const testModal = new bootstrap.Modal(document.getElementById('testCredentialsModal'));
                    testModal.show();
                }

                if (data.key && data.key.startsWith('rzp_test_')) {
                    const testModal = new bootstrap.Modal(document.getElementById('testCredentialsModal'));
                    testModal.show();
                }

                console.log('[Razorpay] Options used to open checkout:', options); // Debugging

                console.log('[Razorpay] Creating Razorpay instance...');
                var rzp1 = new Razorpay(options);
                
                // Add event listeners for all Razorpay events
                rzp1.on('payment.failed', function (response) {
                    console.group('[Razorpay] payment.failed event');
                    console.error('Full response object:', response);
                    console.error('Response type:', typeof response);
                    console.error('Response keys:', Object.keys(response || {}));
                    
                    if (response && response.error) {
                        console.error('Error code:', response.error.code);
                        console.error('Description:', response.error.description);
                        console.error('Source:', response.error.source);
                        console.error('Step:', response.error.step);
                        console.error('Reason:', response.error.reason);
                        console.error('Metadata:', response.error.metadata);
                        console.error('Error object keys:', Object.keys(response.error));
                        
                        alert(
                            'Payment Failed:\n'
                            + 'Code: ' + response.error.code + '\n'
                            + 'Description: ' + response.error.description + '\n'
                            + 'Source: ' + response.error.source + '\n'
                            + 'Step: ' + response.error.step + '\n'
                            + 'Reason: ' + (response.error.reason || 'N/A')
                        );
                    } else {
                        console.error('No error object found on response.');
                        console.error('Response structure:', JSON.stringify(response, null, 2));
                        alert('Payment Failed. Please check browser console logs starting with [Razorpay].');
                    }
                    console.groupEnd();
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
                
                rzp1.on('payment.authorized', function(response) {
                    console.log('[Razorpay] payment.authorized event:', response);
                });
                
                rzp1.on('payment.captured', function(response) {
                    console.log('[Razorpay] payment.captured event:', response);
                });
                
                console.log('[Razorpay] Opening Razorpay checkout modal...');
                rzp1.open();
            } else {
                alert(data.message || 'Error initiating payment');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.group('[Razorpay] initiate fetch error');
            console.error('Error type:', error.constructor.name);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            console.error('Full error object:', error);
            console.groupEnd();
            
            alert('Error initiating payment: ' + error.message + '\n\nPlease check console for details.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}

function verifyRazorpayPayment(paymentResponse, verifyRoute) {
    console.group('[Razorpay] verifyRazorpayPayment - Starting verification');
    console.log('[Razorpay] Payment response received:', paymentResponse);
    console.log('[Razorpay] Verify route:', verifyRoute);
    
    const form = document.getElementById('checkoutForm');
    if (!form) {
        console.error('[Razorpay] ERROR: checkoutForm not found!');
        alert('Error: Checkout form not found. Please refresh and try again.');
        return;
    }
    
    console.log('[Razorpay] Form found:', {
        formId: form.id,
        currentAction: form.action,
        method: form.method
    });

    // Validate payment response
    if (!paymentResponse.razorpay_payment_id) {
        console.error('[Razorpay] ERROR: Missing razorpay_payment_id');
        alert('Error: Payment ID missing. Please try again.');
        return;
    }
    
    if (!paymentResponse.razorpay_order_id) {
        console.error('[Razorpay] ERROR: Missing razorpay_order_id');
        alert('Error: Order ID missing. Please try again.');
        return;
    }
    
    if (!paymentResponse.razorpay_signature) {
        console.error('[Razorpay] ERROR: Missing razorpay_signature');
        alert('Error: Payment signature missing. Please try again.');
        return;
    }

    console.log('[Razorpay] Payment details validated:', {
        payment_id: paymentResponse.razorpay_payment_id,
        order_id: paymentResponse.razorpay_order_id,
        signature_present: !!paymentResponse.razorpay_signature
    });

    // Create hidden inputs for payment details
    const addInput = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
        console.log(`[Razorpay] Added hidden input: ${name} = ${value.substring(0, 20)}...`);
    };

    addInput('razorpay_payment_id', paymentResponse.razorpay_payment_id);
    addInput('razorpay_order_id', paymentResponse.razorpay_order_id);
    addInput('razorpay_signature', paymentResponse.razorpay_signature);

    // Change action to verify route
    console.log('[Razorpay] Updating form action from', form.action, 'to', verifyRoute);
    form.action = verifyRoute;
    
    console.log('[Razorpay] Form ready to submit:', {
        action: form.action,
        method: form.method,
        inputsCount: form.querySelectorAll('input').length
    });
    
    console.log('[Razorpay] Submitting form to verify payment...');
    console.groupEnd();
    
    // Submit form
    form.submit();
}
