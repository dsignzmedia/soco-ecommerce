@extends('admin.layouts.base')

@section('title', 'Payment Gateways | The Skool Store')
@section('page_heading', 'Payment Gateways')
@section('page_subheading', 'Configure payment providers and credentials')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:15px;">
                <a href="{{ route('master.admin.settings.index') }}" style="color:#6b7280;text-decoration:none;display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1px solid #d1d5db;background:#fff;transition:all 0.2s;" onmouseover="this.style.borderColor='#490d59';this.style.color='#490d59'" onmouseout="this.style.borderColor='#d1d5db';this.style.color='#6b7280'">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h3 style="margin:0;color:#111827;">Payment Gateways</h3>
            </div>
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
                <div style="margin-top:20px;padding:16px;background-color:#f3f4f6;border-radius:12px;border:1px solid #e5e7eb;">
                    <h5 style="margin:0 0 12px;font-size:16px;color:#1f2937;">Credentials</h5>
                    <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                        <label>
                            <span style="display:block;margin-bottom:6px;font-weight:500;color:#374151;">API Key / Key ID</span>
                            <input type="text" name="credential_key" placeholder="Enter API Key or Key ID" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;">
                        </label>
                        <label>
                            <span style="display:block;margin-bottom:6px;font-weight:500;color:#374151;">Secret Key / Key Secret</span>
                            <div style="position:relative;">
                                <input type="password" name="credential_secret" id="new_secret" placeholder="Enter Secret Key" style="width:100%;padding:10px;padding-right:40px;border:1px solid #d1d5db;border-radius:8px;">
                                <button type="button" onclick="togglePassword('new_secret')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7280;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </label>
                    </div>
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
                        <span style="display:block;margin-bottom:6px;font-weight:500;color:#374151;">Gateway Name *</span>
                        <input type="text" name="name" value="{{ $gateway->name }}" required style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;">
                    </label>
                    <label>
                        <span style="display:block;margin-bottom:6px;font-weight:500;color:#374151;">Provider *</span>
                        <select name="provider" required style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;">
                            <option value="stripe" @selected($gateway->provider === 'stripe')>Stripe</option>
                            <option value="razorpay" @selected($gateway->provider === 'razorpay')>Razorpay</option>
                            <option value="paypal" @selected($gateway->provider === 'paypal')>PayPal</option>
                            <option value="payu" @selected($gateway->provider === 'payu')>PayU</option>
                            <option value="other" @selected($gateway->provider === 'other')>Other</option>
                        </select>
                    </label>
                    <label>
                        <span style="display:block;margin-bottom:6px;font-weight:500;color:#374151;">Sort Order</span>
                        <input type="number" name="sort_order" value="{{ $gateway->sort_order }}" min="0" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;">
                    </label>
                </div>
                
                <div style="margin-top:20px;padding:16px;background-color:#f3f4f6;border-radius:12px;border:1px solid #e5e7eb;">
                    <h5 style="margin:0 0 12px;font-size:16px;color:#1f2937;">Credentials</h5>
                    <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                        <label>
                            <span style="display:block;margin-bottom:6px;font-weight:500;color:#374151;">API Key / Key ID</span>
                            <input type="text" name="credential_key" value="{{ $gateway->credentials['key_id'] ?? ($gateway->credentials['key'] ?? '') }}" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;">
                        </label>
                        <label>
                            <span style="display:block;margin-bottom:6px;font-weight:500;color:#374151;">Secret Key / Key Secret</span>
                            <div style="position:relative;">
                                <input type="password" name="credential_secret" id="secret_{{ $gateway->id }}" value="{{ $gateway->credentials['key_secret'] ?? ($gateway->credentials['secret'] ?? '') }}" style="width:100%;padding:10px;padding-right:40px;border:1px solid #d1d5db;border-radius:8px;">
                                <button type="button" onclick="togglePassword('secret_{{ $gateway->id }}')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7280;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top:16px;display:flex;gap:20px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" @checked($gateway->is_active) style="width:16px;height:16px;accent-color:#490d59;">
                        <span style="font-weight:500;color:#374151;">Active</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="test_mode" value="1" @checked($gateway->test_mode) style="width:16px;height:16px;accent-color:#490d59;">
                        <span style="font-weight:500;color:#374151;">Test Mode</span>
                    </label>
                </div>
                <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end;">
                    <button type="button" onclick="closeEditForm({{ $gateway->id }})" style="padding:10px 20px;border-radius:8px;border:1px solid #d1d5db;color:#374151;background:#fff;cursor:pointer;font-weight:500;">Cancel</button>
                    <button type="submit" style="padding:10px 24px;border:none;border-radius:8px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;box-shadow:0 2px 4px rgba(73, 13, 89, 0.2);">Update Gateway</button>
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
        
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection

