@extends('admin.layouts.base')

@section('title', 'Payment Gateways | The Skool Store')
@section('page_heading', 'Payment Gateways')
@section('page_subheading', 'Configure payment providers and credentials')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h3 style="margin:0;color:#111827;">Payment Gateways</h3>
            <button type="button" onclick="document.getElementById('addGatewayForm').style.display='block'" style="padding:10px 16px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">+ Add Gateway</button>
        </div>

        <div id="addGatewayForm" style="display:none;border:1px solid #e5e7eb;border-radius:16px;padding:20px;margin-bottom:20px;background:#f9fafb;">
            <h4 style="margin:0 0 16px;color:#111827;">Add New Payment Gateway</h4>
            <form method="POST" action="{{ route('master.admin.settings.payment-gateways.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Gateway Name *</span>
                        <input type="text" name="name" required>
                    </label>
                    <label>
                        <span>Provider *</span>
                        <select name="provider" required>
                            <option value="stripe">Stripe</option>
                            <option value="razorpay">Razorpay</option>
                            <option value="paypal">PayPal</option>
                            <option value="payu">PayU</option>
                            <option value="other">Other</option>
                        </select>
                    </label>
                    <label>
                        <span>Sort Order</span>
                        <input type="number" name="sort_order" value="0" min="0">
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Credentials (JSON)</span>
                        <textarea name="credentials_json" rows="4" placeholder='{"api_key":"...","secret_key":"..."}'></textarea>
                        <small style="color:#6b7280;font-size:12px;">Enter credentials as JSON object</small>
                    </label>
                </div>
                <div style="margin-top:16px;display:flex;gap:12px;">
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_active" value="1">
                        <span>Active</span>
                    </label>
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="test_mode" value="1" checked>
                        <span>Test Mode</span>
                    </label>
                </div>
                <div style="margin-top:20px;display:flex;gap:12px;">
                    <button type="submit" style="padding:10px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Save</button>
                    <button type="button" onclick="document.getElementById('addGatewayForm').style.display='none'" style="padding:10px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;background:#fff;cursor:pointer;">Cancel</button>
                </div>
            </form>
        </div>

        <div style="display:flex;flex-direction:column;gap:12px;">
            @forelse($gateways as $gateway)
                <div style="border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:start;">
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                                <h4 style="margin:0;color:#111827;">{{ $gateway->name }}</h4>
                                <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#e5e7eb;color:#475467;text-transform:uppercase;">{{ $gateway->provider }}</span>
                                @if($gateway->is_active)
                                    <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#ecfdf3;color:#027a48;">Active</span>
                                @else
                                    <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#fef3f2;color:#b42318;">Inactive</span>
                                @endif
                                @if($gateway->test_mode)
                                    <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#fef3c7;color:#92400e;">Test Mode</span>
                                @endif
                            </div>
                            <p style="margin:0;color:#6b7280;font-size:14px;">Sort Order: {{ $gateway->sort_order }}</p>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="editGateway({{ $gateway->id }})" style="padding:8px 12px;border-radius:8px;border:1px solid #d0d5dd;color:#475467;background:#fff;cursor:pointer;font-size:14px;">Edit</button>
                            <form method="POST" action="{{ route('master.admin.settings.payment-gateways.destroy', $gateway) }}" style="margin:0;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:8px 12px;border-radius:8px;border:1px solid #fef3f2;color:#b42318;background:#fef3f2;cursor:pointer;font-size:14px;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:40px;color:#6b7280;">
                    <p>No payment gateways configured yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    @foreach($gateways as $gateway)
        <div id="editForm{{ $gateway->id }}" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;max-width:600px;width:90%;max-height:90vh;overflow-y:auto;z-index:1000;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <h4 style="margin:0 0 20px;color:#111827;">Edit Payment Gateway</h4>
            <form method="POST" action="{{ route('master.admin.settings.payment-gateways.update', $gateway) }}">
                @csrf
                @method('PUT')
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Gateway Name *</span>
                        <input type="text" name="name" value="{{ $gateway->name }}" required>
                    </label>
                    <label>
                        <span>Provider *</span>
                        <select name="provider" required>
                            <option value="stripe" @selected($gateway->provider === 'stripe')>Stripe</option>
                            <option value="razorpay" @selected($gateway->provider === 'razorpay')>Razorpay</option>
                            <option value="paypal" @selected($gateway->provider === 'paypal')>PayPal</option>
                            <option value="payu" @selected($gateway->provider === 'payu')>PayU</option>
                            <option value="other" @selected($gateway->provider === 'other')>Other</option>
                        </select>
                    </label>
                    <label>
                        <span>Sort Order</span>
                        <input type="number" name="sort_order" value="{{ $gateway->sort_order }}" min="0">
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Credentials (JSON)</span>
                        <textarea name="credentials_json" rows="4">{{ json_encode($gateway->credentials, JSON_PRETTY_PRINT) }}</textarea>
                    </label>
                </div>
                <div style="margin-top:16px;display:flex;gap:12px;">
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_active" value="1" @checked($gateway->is_active)>
                        <span>Active</span>
                    </label>
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="test_mode" value="1" @checked($gateway->test_mode)>
                        <span>Test Mode</span>
                    </label>
                </div>
                <div style="margin-top:20px;display:flex;gap:12px;">
                    <button type="submit" style="padding:10px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Update</button>
                    <button type="button" onclick="closeEditForm({{ $gateway->id }})" style="padding:10px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;background:#fff;cursor:pointer;">Cancel</button>
                </div>
            </form>
        </div>
    @endforeach

    <script>
        function editGateway(id) {
            document.getElementById('editForm' + id).style.display = 'block';
        }
        function closeEditForm(id) {
            document.getElementById('editForm' + id).style.display = 'none';
        }
        // Handle credentials JSON parsing
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const textarea = form.querySelector('textarea[name="credentials_json"]');
                if (textarea && textarea.value) {
                    try {
                        const json = JSON.parse(textarea.value);
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'credentials';
                        hiddenInput.value = JSON.stringify(json);
                        form.appendChild(hiddenInput);
                    } catch (err) {
                        e.preventDefault();
                        alert('Invalid JSON in credentials field');
                        return false;
                    }
                }
            });
        });
    </script>
@endsection

