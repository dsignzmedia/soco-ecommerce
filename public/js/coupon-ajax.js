// Add this JavaScript to handle AJAX coupon generation
document.getElementById('generateCouponBtn').addEventListener('click', function() {
    const btn = this;
    const originalHTML = btn.innerHTML;
    
    // Disable button and show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Generating...';
    
    // Make AJAX request
    fetch('{{ route("master.admin.coupons.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        
        if (data.success) {
            // Success - reload page to show new coupon
            window.location.reload();
        } else {
            // Show modal with active coupon info
            document.getElementById('modalCouponCode').textContent = data.coupon.code;
            document.getElementById('modalExpiresAt').textContent = data.coupon.expires_at;
            document.getElementById('modalExpiresIn').innerHTML = '<i class="fas fa-clock me-1"></i> ' + data.coupon.expires_in;
            
            const modal = new bootstrap.Modal(document.getElementById('activeCouponModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        alert('An error occurred while generating the coupon. Please try again.');
    });
});

function copyFromModal() {
    const code = document.getElementById('modalCouponCode').textContent;
    navigator.clipboard.writeText(code).then(function() {
        alert('Coupon code copied: ' + code);
    });
}
