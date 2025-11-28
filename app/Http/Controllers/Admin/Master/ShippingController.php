<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ShippingSetting;
use App\Models\Admin\Master\ShippingZone;
use App\Models\Admin\Master\ShippingZonePincode;
use App\Models\Admin\Master\TaxAssignment;
use App\Models\Admin\Master\TaxCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Support\AuditLogger;

class ShippingController extends Controller
{
    public function edit(): View
    {
        $settings = ShippingSetting::current();
        $zones = ShippingZone::with('pincodes')->get();
        $taxCategories = TaxCategory::with('assignments')->get();

        return view('admin.shipping.edit', compact('settings', 'zones', 'taxCategories'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'default_cost' => ['required', 'numeric', 'min:0'],
            'overrides' => ['nullable', 'string'],
            'zones' => ['array'],
            'zones.*.name' => ['required_with:zones.*.cost', 'string'],
            'zones.*.cost' => ['nullable', 'numeric', 'min:0'],
            'zones.*.free_shipping' => ['nullable', 'boolean'],
            'zones.*.free_threshold' => ['nullable', 'integer', 'min:0'],
            'zones.*.pincodes' => ['nullable', 'string'],
            'tax' => ['array'],
            'tax.*.name' => ['nullable', 'string'],
            'tax.*.rate' => ['nullable', 'numeric', 'min:0'],
            'tax.*.assignments' => ['nullable', 'string'],
        ]);

        $settings = ShippingSetting::current();
        $settings->update([
            'default_cost' => $data['default_cost'],
            'overrides' => $this->decodePairs($data['overrides'] ?? ''),
        ]);

        ShippingZone::query()->delete();
        if (! empty($data['zones'])) {
            foreach ($data['zones'] as $zoneData) {
                if (blank($zoneData['name'])) {
                    continue;
                }
                $zone = ShippingZone::create([
                    'name' => $zoneData['name'],
                    'cost' => $zoneData['cost'] ?? 0,
                    'free_shipping' => (bool) ($zoneData['free_shipping'] ?? false),
                    'free_threshold' => $zoneData['free_threshold'] ?? null,
                ]);

                $pins = collect(explode(',', $zoneData['pincodes'] ?? ''))
                    ->map(fn ($pin) => trim($pin))
                    ->filter()
                    ->map(fn ($pin) => ['pincode' => $pin]);

                if ($pins->isNotEmpty()) {
                    $zone->pincodes()->createMany($pins->toArray());
                }
            }
        }

        TaxCategory::query()->delete();
        if (! empty($data['tax'])) {
            foreach ($data['tax'] as $taxRow) {
                if (blank($taxRow['name'])) {
                    continue;
                }

                $category = TaxCategory::create([
                    'name' => $taxRow['name'],
                    'rate' => $taxRow['rate'] ?? 0,
                ]);

                $assignments = collect(json_decode($taxRow['assignments'] ?? '[]', true))
                    ->filter(fn ($item) => isset($item['type'], $item['value']))
                    ->map(fn ($item) => [
                        'type' => $item['type'],
                        'value' => $item['value'],
                    ]);

                if ($assignments->isNotEmpty()) {
                    $category->assignments()->createMany($assignments->toArray());
                }
            }
        }

        AuditLogger::record(
            'shipping_settings_update',
            $settings,
            [
                'default_cost' => $settings->default_cost,
                'zones_updated' => count($data['zones'] ?? []),
                'tax_categories_updated' => count($data['tax'] ?? []),
            ],
            'Shipping / tax configuration updated'
        );

        return back()->with('status', 'Shipping & tax settings updated.');
    }

    protected function decodePairs(string $payload): array
    {
        if (blank($payload)) {
            return [];
        }

        return collect(json_decode($payload, true) ?? [])
            ->filter(fn ($item) => isset($item['label']) && isset($item['value']))
            ->values()
            ->all();
    }
}

