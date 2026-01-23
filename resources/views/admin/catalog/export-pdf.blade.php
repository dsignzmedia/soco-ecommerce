<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Catalog Export</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #111827;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { 
            text-align: left; 
            padding: 8px; 
            border-bottom: 2px solid #333; 
            font-weight: bold; 
            background-color: #f3f4f6;
        }
        td { 
            padding: 8px; 
            border-bottom: 1px solid #e5e7eb; 
            vertical-align: top;
        }
        tr:nth-child(even) { background-color: #f9fafb; }
        .header { margin-bottom: 30px; }
        .school-group { 
            margin-top: 30px; 
        }
        h2 { margin: 0; font-size: 18px; }
        h3 { margin: 15px 0 10px; font-size: 14px; color: #490d59; border-bottom: 1px solid #490d59; padding-bottom: 5px; }
        .meta { color: #6b7280; font-size: 10px; margin-top: 5px; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 9px;
            border-radius: 4px;
            background: #e5e7eb;
        }
        .status-live { color: #027a48; background: #ecfdf3; }
        .status-draft { color: #344054; background: #f2f4f7; }
        .status-archived { color: #b42318; background: #fef3f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Products & Catalog Export</h2>
        <div class="meta">Generated: {{ now()->format('d M Y, h:i A') }} | Total Products: {{ $products->count() }}</div>
    </div>

    @php
        $grouped = $products->groupBy(function ($product) {
            return $product->school ? $product->school->name : 'Unassigned';
        });

        // Helper logic for grade formatting (closure)
        $formatGrades = function($product) {
            if ($product->gradePricing && $product->gradePricing->count() > 0) {
                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $grades = $product->gradePricing->pluck('grade')->toArray();
                
                if (empty($grades)) {
                    return $product->grade ?? 'All';
                }
                
                usort($grades, function($a, $b) use ($gradeOrder) {
                    $aIndex = array_search($a, $gradeOrder);
                    $bIndex = array_search($b, $gradeOrder);
                    if ($aIndex === false) $aIndex = 999;
                    if ($bIndex === false) $bIndex = 999;
                    return $aIndex <=> $bIndex;
                });
                
                $displayGrades = [];
                $currentRange = null;
                foreach ($grades as $i => $grade) {
                    $gradeIndex = array_search($grade, $gradeOrder);
                    if ($i === 0) {
                        $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                    } else {
                        $prevIndex = array_search($grades[$i-1], $gradeOrder);
                        if ($gradeIndex === $prevIndex + 1) {
                            $currentRange['end'] = $grade;
                        } else {
                            if ($currentRange['start'] === $currentRange['end']) {
                                $displayGrades[] = $currentRange['start'];
                            } else {
                                $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                            }
                            $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                        }
                    }
                }
                if ($currentRange) {
                    if ($currentRange['start'] === $currentRange['end']) {
                        $displayGrades[] = $currentRange['start'];
                    } else {
                        $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                    }
                }
                return implode(', ', $displayGrades);
            } else {
                return $product->grade ?? 'All';
            }
        };
    @endphp

    @foreach($grouped as $schoolName => $schoolProducts)
        <div class="school-group">
            <h3>{{ $schoolName }} ({{ $schoolProducts->count() }})</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Product Name</th>
                        <th style="width: 12%;">Grade</th>
                        <th style="width: 12%;">Category / Type</th>
                        <th style="width: 8%;">Gender</th>
                        <th style="width: 12%;">Prices</th>
                        <th style="width: 15%;">Grade Pricing</th>
                        <th style="width: 15%;">Variants</th>
                        <th style="width: 8%;">Stock</th>
                        <th style="width: 7%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schoolProducts as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                            </td>
                            <td>{{ $formatGrades($product) }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $product->category)) }}<br>
                                <span style="color: #6b7280; font-size: 9px;">{{ ucfirst(str_replace('_', ' ', $product->product_type)) }}</span>
                            </td>
                            <td>
                                {{ ucfirst($product->gender) }}
                            </td>
                            <td>
                                ₹{{ number_format($product->getDisplayPrice(), 2) }}
                                @if($product->price_inclusive_tax)
                                    <br><span style="color: #027a48; font-size: 8px;">(Incl. Tax)</span>
                                @endif
                            </td>
                            <td style="font-size: 9px;">
                                @if($product->gradePricing && $product->gradePricing->count() > 0)
                                    @php
                                        $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                                        
                                        // Group by price
                                        $pricingByPrice = [];
                                        foreach ($product->gradePricing as $gp) {
                                            if (!isset($pricingByPrice[$gp->price])) {
                                                $pricingByPrice[$gp->price] = [];
                                            }
                                            $pricingByPrice[$gp->price][] = $gp->grade;
                                        }
                                        
                                        $ranges = [];
                                        foreach ($pricingByPrice as $price => $gradeList) {
                                            // Sort by grade order
                                            usort($gradeList, function($a, $b) use ($gradeOrder) {
                                                $aIndex = array_search($a, $gradeOrder);
                                                $bIndex = array_search($b, $gradeOrder);
                                                if ($aIndex === false) $aIndex = 999;
                                                if ($bIndex === false) $bIndex = 999;
                                                return $aIndex <=> $bIndex;
                                            });
                                            
                                            // Group consecutive grades into ranges
                                            $currentRange = ['from' => $gradeList[0], 'to' => $gradeList[0]];
                                            foreach ($gradeList as $i => $grade) {
                                                if ($i === 0) continue;
                                                $prevIndex = array_search($gradeList[$i-1], $gradeOrder);
                                                $currIndex = array_search($grade, $gradeOrder);
                                                if ($currIndex === $prevIndex + 1) {
                                                    $currentRange['to'] = $grade;
                                                } else {
                                                    $rangeStr = $currentRange['from'] === $currentRange['to'] 
                                                        ? $currentRange['from'] 
                                                        : $currentRange['from'] . '-' . $currentRange['to'];
                                                    $ranges[] = ['range' => $rangeStr, 'price' => $price];
                                                    $currentRange = ['from' => $grade, 'to' => $grade];
                                                }
                                            }
                                            $rangeStr = $currentRange['from'] === $currentRange['to'] 
                                                ? $currentRange['from'] 
                                                : $currentRange['from'] . '-' . $currentRange['to'];
                                            $ranges[] = ['range' => $rangeStr, 'price' => $price];
                                        }
                                    @endphp
                                    @foreach($ranges as $range)
                                        <div style="margin-bottom: 2px; line-height: 1.3;">
                                            <strong>{{ $range['range'] }}:</strong> ₹{{ number_format($range['price'], 2) }}
                                        </div>
                                    @endforeach
                                @else
                                    @php
                                        $regularPrice = $product->price_regular ?? 0;
                                    @endphp
                                    @if($regularPrice > 0)
                                        <div style="margin-bottom: 2px; line-height: 1.3;">
                                            <strong>Pre-KG–12:</strong> ₹{{ number_format($regularPrice, 2) }}
                                        </div>
                                    @else
                                        <span style="color: #9ca3af;">N/A</span>
                                    @endif
                                @endif
                            </td>
                            <td style="font-size: 9px;">
                                @if($product->variants && $product->variants->count() > 0)
                                    @foreach($product->variants as $variant)
                                        <div style="margin-bottom: 3px; line-height: 1.4;">
                                            <strong>{{ $variant->option }}:</strong><br>
                                            <span style="color: #6b7280;">Weight: {{ $variant->weight ? number_format($variant->weight, 2) : 'N/A' }}</span><br>
                                            <span style="color: #6b7280;">Stock: {{ number_format($variant->stock ?? 0) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span style="color: #9ca3af;">N/A</span>
                                @endif
                            </td>
                            <td>
                                {{ $product->inventory_stock }}
                                @if($product->variants_count > 0)
                                    <br><span style="color: #6b7280; font-size: 8px;">(Variants)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge status-{{ $product->status }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
