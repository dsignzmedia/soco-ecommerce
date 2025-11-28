@extends('admin.layouts.base')

@section('title', 'Shipping Settings | The Skool Store')
@section('page_heading', 'Shipping Settings')
@section('page_subheading', 'Configure default cost, zones, and overrides')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <form method="POST" action="{{ route('master.admin.shipping.update') }}">
            @csrf
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;">
                <label>
                    <span>Default shipping cost *</span>
                    <input type="number" step="0.01" min="0" name="default_cost" value="{{ old('default_cost', $settings->default_cost) }}" required>
                </label>
            </div>

            <div style="margin-top:32px;">
                <h3 style="margin:0 0 12px;color:#111827;">Shipping zones</h3>
                <p style="margin:0 0 12px;color:#475467;">Define zone name, cost, optional free shipping threshold, and list pincodes separated by commas.</p>
                @php
                    $zonePrototype = [
                        'name' => '',
                        'cost' => '',
                        'free_shipping' => false,
                        'free_threshold' => '',
                        'pincodes' => ''
                    ];
                @endphp
                <div id="zoneRepeater" data-prototype='@json($zonePrototype)'>
                    @php($zonePayload = old('zones', $zones->map(fn($zone) => [
                        'name' => $zone->name,
                        'cost' => $zone->cost,
                        'free_shipping' => $zone->free_shipping,
                        'free_threshold' => $zone->free_threshold,
                        'pincodes' => $zone->pincodes->pluck('pincode')->implode(', '),
                    ])->toArray()))
                    @foreach($zonePayload as $index => $zone)
                        <div class="zone-card" style="border:1px solid #e5e7eb;border-radius:16px;padding:16px;margin-bottom:12px;">
                            <label>
                                <span>Zone name *</span>
                                <input type="text" name="zones[{{ $index }}][name]" value="{{ $zone['name'] }}" required>
                            </label>
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-top:12px;">
                                <label>
                                    <span>Shipping cost</span>
                                    <input type="number" step="0.01" name="zones[{{ $index }}][cost]" value="{{ $zone['cost'] }}">
                                </label>
                                <label>
                                    <span>Free shipping</span>
                                    <select name="zones[{{ $index }}][free_shipping]">
                                        <option value="0" @selected(!$zone['free_shipping'])>Disabled</option>
                                        <option value="1" @selected($zone['free_shipping'])>Enabled</option>
                                    </select>
                                </label>
                                <label>
                                    <span>Free shipping threshold</span>
                                    <input type="number" name="zones[{{ $index }}][free_threshold]" value="{{ $zone['free_threshold'] }}">
                                </label>
                            </div>
                            <label style="margin-top:12px;">
                                <span>Pincodes (comma separated)</span>
                                <textarea name="zones[{{ $index }}][pincodes]" rows="2">{{ $zone['pincodes'] }}</textarea>
                            </label>
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="addZone()" style="margin-top:12px;border:1px dashed #d0d5dd;border-radius:12px;padding:10px 16px;background:#fff;color:#490d59;font-weight:600;">+ Add zone</button>
            </div>

            <div style="margin-top:32px;">
                <h3 style="margin:0 0 12px;color:#111827;">Free shipping overrides</h3>
                <p style="margin:0 0 12px;color:#475467;">Optional overrides per school or campaign using JSON pattern <code>[{"label":"PSG","value":0}]</code>.</p>
                <textarea name="overrides" rows="4">{{ old('overrides', json_encode($settings->overrides ?? [])) }}</textarea>
            </div>

            <div style="margin-top:32px;">
                <h3 style="margin:0 0 12px;color:#111827;">Tax settings</h3>
                <p style="margin:0 0 12px;color:#475467;">Define tax categories with rates and JSON assignments like <code>[{"type":"category","value":"Uniform"}]</code> or <code>{"type":"product","value":"Shirt"}</code>.</p>
                @php($taxPayload = old('tax', $taxCategories->map(fn($tax) => [
                    'name' => $tax->name,
                    'rate' => $tax->rate,
                    'assignments' => $tax->assignments->map(fn($assignment) => [
                        'type' => $assignment->type,
                        'value' => $assignment->value,
                    ])->toJson(),
                ])->toArray()))
                <div id="taxRepeater">
                    @foreach($taxPayload as $index => $tax)
                        <div style="border:1px solid #e5e7eb;border-radius:16px;padding:16px;margin-bottom:12px;">
                            <label>
                                <span>Tax category name *</span>
                                <input type="text" name="tax[{{ $index }}][name]" value="{{ $tax['name'] }}" required>
                            </label>
                            <label style="margin-top:12px;">
                                <span>Tax %</span>
                                <input type="number" name="tax[{{ $index }}][rate]" step="0.01" value="{{ $tax['rate'] }}">
                            </label>
                            <label style="margin-top:12px;">
                                <span>Assignments (JSON)</span>
                                <textarea name="tax[{{ $index }}][assignments]" rows="3" placeholder='[{"type":"category","value":"Uniform"}]'>{{ $tax['assignments'] ?? '[]' }}</textarea>
                            </label>
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="addTax()" style="margin-top:12px;border:1px dashed #d0d5dd;border-radius:12px;padding:10px 16px;background:#fff;color:#490d59;font-weight:600;">+ Add tax category</button>
            </div>

            <div style="margin-top:32px;display:flex;gap:12px;">
                <button type="submit" style="padding:12px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;">Save changes</button>
                <a href="{{ route('master.admin.dashboard') }}" style="padding:12px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        let zoneIndex = {{ count($zonePayload) }};
        function addZone() {
            const repeater = document.getElementById('zoneRepeater');
            const template = `
            <div class="zone-card" style="border:1px solid #e5e7eb;border-radius:16px;padding:16px;margin-bottom:12px;">
                <label>
                    <span>Zone name *</span>
                    <input type="text" name="zones[${zoneIndex}][name]" required>
                </label>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-top:12px;">
                    <label>
                        <span>Shipping cost</span>
                        <input type="number" step="0.01" name="zones[${zoneIndex}][cost]">
                    </label>
                    <label>
                        <span>Free shipping</span>
                        <select name="zones[${zoneIndex}][free_shipping]">
                            <option value="0">Disabled</option>
                            <option value="1">Enabled</option>
                        </select>
                    </label>
                    <label>
                        <span>Free shipping threshold</span>
                        <input type="number" name="zones[${zoneIndex}][free_threshold]">
                    </label>
                </div>
                <label style="margin-top:12px;">
                    <span>Pincodes (comma separated)</span>
                    <textarea name="zones[${zoneIndex}][pincodes]" rows="2"></textarea>
                </label>
            </div>`;
            repeater.insertAdjacentHTML('beforeend', template);
            zoneIndex++;
        }

        let taxIndex = {{ count($taxPayload) }};
        function addTax() {
            const repeater = document.getElementById('taxRepeater');
            const template = `
            <div style="border:1px solid #e5e7eb;border-radius:16px;padding:16px;margin-bottom:12px;">
                <label>
                    <span>Tax category name *</span>
                    <input type="text" name="tax[${taxIndex}][name]" required>
                </label>
                <label style="margin-top:12px;">
                    <span>Tax %</span>
                    <input type="number" name="tax[${taxIndex}][rate]" step="0.01">
                </label>
                <label style="margin-top:12px;">
                    <span>Assignments (JSON)</span>
                    <textarea name="tax[${taxIndex}][assignments]" rows="3" placeholder='[{"type":"category","value":"Uniform"}]'></textarea>
                </label>
            </div>`;
            repeater.insertAdjacentHTML('beforeend', template);
            taxIndex++;
        }
    </script>
@endsection

