<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Merchandise\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['school'])->withCount('variants');

        if ($request->has('q')) {
            $query->where(function($builder) use ($request) {
                $builder->where('product_name', 'like', '%' . $request->q . '%')
                    ->orWhere('sku', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('school_id')) {
            $query->whereHas('schools', function($q) use ($request) {
                $q->where('schools.id', $request->school_id);
            });
        }

        // Apply shared filters
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('stock_status')) {
             if ($request->stock_status === 'in_stock') {
                $query->where('inventory_stock', '>', 0);
            } else {
                $query->where('inventory_stock', '<=', 0);
            }
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mappings = $query->paginate(15)->withQueryString();
        
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();
        // Fetch distinct values for filters
        $grades = Product::whereNotNull('grade')->distinct()->pluck('grade', 'grade');
        $categories = Product::whereNotNull('category')->distinct()->pluck('category', 'category');
        $categories = Product::whereNotNull('category')->distinct()->pluck('category', 'category');
        $filters = $request->all();

        return view('admin.merchandise.products.index', compact('mappings', 'schools', 'grades', 'categories', 'filters'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'csv');
        $query = Product::with(['school', 'variants', 'gradePricing']);

        if ($request->has('q')) {
            $query->where(function($builder) use ($request) {
                $builder->where('product_name', 'like', '%' . $request->q . '%')
                    ->orWhere('sku', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('school_id')) {
            $query->whereHas('schools', function($q) use ($request) {
                $q->where('schools.id', $request->school_id);
            });
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->orderBy('product_name')->get();
        
        // Filter out any empty/null products to ensure no blank rows
        $products = $products->filter(function($product) {
            return $product !== null && !empty($product->product_name);
        })->values();

        switch (strtolower($type)) {
            case 'csv':
                return $this->downloadDelimited($products);
            
            case 'excel':
                return $this->downloadExcelFormatted($products);
            
            case 'pdf':
                return $this->downloadPdf($products);
            
            default:
                return redirect()->back()->with('error', 'Invalid export type. Supported types: csv, excel, pdf.');
        }
    }

    protected function downloadDelimited($products)
    {
        $filename = 'products_merch_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        // Helper function to format variants
        $formatVariants = function($product) {
            if ($product->variants && $product->variants->count() > 0) {
                $variantLines = [];
                foreach ($product->variants as $variant) {
                    $weight = $variant->weight ? number_format($variant->weight, 2) : 'N/A';
                    $stock = $variant->stock ?? 0;
                    $variantLines[] = $variant->option . ' | Weight: ' . $weight . ' | Stock: ' . $stock;
                }
                return implode('; ', $variantLines);
            }
            return 'N/A';
        };

        // Helper function to format grade pricing
        $formatGradePricing = function($product) {
            if ($product->gradePricing && $product->gradePricing->count() > 0) {
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
                            $ranges[] = $rangeStr . ': Rs.' . number_format($price, 2);
                            $currentRange = ['from' => $grade, 'to' => $grade];
                        }
                    }
                    $rangeStr = $currentRange['from'] === $currentRange['to'] 
                        ? $currentRange['from'] 
                        : $currentRange['from'] . '-' . $currentRange['to'];
                    $ranges[] = $rangeStr . ': Rs.' . number_format($price, 2);
                }
                
                return implode('; ', $ranges);
            }
            // If no grade pricing, show all grades with regular price
            $regularPrice = $product->price_regular ?? 0;
            if ($regularPrice > 0) {
                return 'Pre-KG–12: Rs.' . number_format($regularPrice, 2);
            }
            return 'N/A';
        };
        
        return response()->streamDownload(function () use ($products, $formatVariants, $formatGradePricing) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No', 'Product', 'School', 'Grade', 'Category', 'Product Type', 'Gender', 'Regular', 'Tax', 'Tax Profile', 'Grade Pricing', 'Variants', 'Stock', 'Low Stock', 'Delivery Duration', 'Status', 'Stock Status']);
            
            $serialNumber = 0;
            foreach ($products as $product) {
                $serialNumber++;
                fputcsv($file, [
                    $serialNumber,
                    $product->product_name,
                    $product->school ? $product->school->name : 'N/A',
                    $product->grade ?? 'All',
                    $product->category ?? 'N/A',
                    $product->product_type ?? 'N/A',
                    ucfirst($product->gender ?? 'N/A'),
                    $product->price_regular ?? '0.00',
                    $product->price_tax ?? '0.00',
                    $product->tax_profile ?? 'N/A',
                    $formatGradePricing($product),
                    $formatVariants($product),
                    $product->inventory_stock ?? 0,
                    $product->low_stock_threshold ?? 0,
                    $product->delivery_duration ?? 'N/A',
                    ucfirst($product->status ?? 'N/A'),
                    ucfirst($product->stock_status ?? 'N/A'),
                ]);
            }
            fclose($file);
        }, $filename, $headers);
    }

    protected function downloadExcelFormatted($products)
    {
        // Helper functions
        $formatGrades = function($product) {
            return $product->grade ?? 'All';
        };

        // Format grade pricing as line-by-line structured format with HTML formatting
        $formatGradePricing = function($product) {
            if ($product->gradePricing && $product->gradePricing->count() > 0) {
                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                
                // Group by price
                $pricingByPrice = [];
                foreach ($product->gradePricing as $gp) {
                    if (!isset($pricingByPrice[$gp->price])) {
                        $pricingByPrice[$gp->price] = [];
                    }
                    $pricingByPrice[$gp->price][] = $gp->grade;
                }
                
                $allRanges = [];
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
                                : $currentRange['from'] . '–' . $currentRange['to'];
                            $allRanges[] = [
                                'range' => $rangeStr,
                                'fromGrade' => $currentRange['from'],
                                'price' => $price
                            ];
                            $currentRange = ['from' => $grade, 'to' => $grade];
                        }
                    }
                    $rangeStr = $currentRange['from'] === $currentRange['to'] 
                        ? $currentRange['from'] 
                        : $currentRange['from'] . '–' . $currentRange['to'];
                    $allRanges[] = [
                        'range' => $rangeStr,
                        'fromGrade' => $currentRange['from'],
                        'price' => $price
                    ];
                }
                
                // Sort all ranges by starting grade order
                usort($allRanges, function($a, $b) use ($gradeOrder) {
                    $aIndex = array_search($a['fromGrade'], $gradeOrder);
                    $bIndex = array_search($b['fromGrade'], $gradeOrder);
                    if ($aIndex === false) $aIndex = 999;
                    if ($bIndex === false) $bIndex = 999;
                    return $aIndex <=> $bIndex;
                });
                
                // Build formatted lines
                $lines = [];
                foreach ($allRanges as $rangeData) {
                    // Pad grade range to fixed width for alignment
                    $paddedRange = str_pad($rangeData['range'], 8, ' ', STR_PAD_RIGHT);
                    $lines[] = '<span class="grade-range">' . htmlspecialchars($paddedRange) . '</span><span class="grade-price-value">→   ₹' . number_format($rangeData['price'], 2) . '</span>';
                }
                
                return implode("\n", $lines);
            }
            // If no grade pricing, show all grades with regular price
            $regularPrice = $product->price_regular ?? 0;
            if ($regularPrice > 0) {
                $paddedRange = str_pad('Pre-KG–12', 8, ' ', STR_PAD_RIGHT);
                return '<span class="grade-range">' . htmlspecialchars($paddedRange) . '</span><span class="grade-price-value">→   ₹' . number_format($regularPrice, 2) . '</span>';
            }
            return 'N/A';
        };

        // Determine row class for zebra striping
        $getRowClass = function($index) {
            return ($index % 2 === 0) ? 'row-even' : 'row-odd';
        };

        // Get status styling
        $getStatusStyle = function($status) {
            $status = strtolower($status ?? '');
            if ($status === 'live') {
                return 'status-live';
            } elseif ($status === 'draft') {
                return 'status-draft';
            } else {
                return 'status-archived';
            }
        };

        // Get stock status styling
        $getStockStatusStyle = function($stockStatus, $stock, $lowStockThreshold) {
            $stockStatus = strtolower($stockStatus ?? '');
            $stock = (int)($stock ?? 0);
            $threshold = (int)($lowStockThreshold ?? 0);
            
            if ($stockStatus === 'in_stock') {
                if ($stock > 0 && $stock <= $threshold) {
                    return 'stock-low';
                }
                return 'stock-in';
            } else {
                return 'stock-out';
            }
        };

        // Format N/A values
        $formatNA = function($value) {
            if (empty($value) || $value === 'N/A' || $value === null) {
                return '<span class="text-na">N/A</span>';
            }
            return htmlspecialchars($value);
        };

        // Format numeric values
        $formatPrice = function($value) {
            $val = (float)($value ?? 0);
            if ($val == 0) {
                return '<span class="text-zero">0.00</span>';
            }
            return '<span class="price-regular">₹' . number_format($val, 2) . '</span>';
        };

        // Format weight (numeric without currency symbol)
        $formatWeight = function($value) {
            if (empty($value) || $value === null) {
                return '<span class="text-na">N/A</span>';
            }
            $val = (float)$value;
            if ($val == 0) {
                return '<span class="text-zero">0.00</span>';
            }
            return number_format($val, 2);
        };

        $formatStock = function($stock, $lowStockThreshold) {
            $stockVal = (int)($stock ?? 0);
            $threshold = (int)($lowStockThreshold ?? 0);
            if ($stockVal == 0) {
                return '<span class="text-zero">0</span>';
            }
            // Bold if > 0
            $class = 'stock-bold';
            if ($stockVal > 0 && $stockVal <= $threshold) {
                $class .= ' stock-low-warning';
            }
            return '<span class="' . $class . '">' . number_format($stockVal) . '</span>';
        };

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]><xml>
    <x:ExcelWorkbook>
        <x:ExcelWorksheets>
            <x:ExcelWorksheet>
                <x:Name>Products</x:Name>
                <x:WorksheetOptions>
                    <x:DefaultRowHeight>576</x:DefaultRowHeight>
                    <x:FreezePanes/>
                    <x:FrozenNoSplit/>
                    <x:SplitHorizontal>1</x:SplitHorizontal>
                    <x:SplitVertical>1</x:SplitVertical>
                    <x:TopRowBottomPane>1</x:TopRowBottomPane>
                    <x:LeftColumnRightPane>1</x:LeftColumnRightPane>
                    <x:ActivePane>0</x:ActivePane>
                    <x:Panes>
                        <x:Pane>
                            <x:Number>3</x:Number>
                        </x:Pane>
                        <x:Pane>
                            <x:Number>1</x:Number>
                            <x:ActiveRow>0</x:ActiveRow>
                        </x:Pane>
                        <x:Pane>
                            <x:Number>2</x:Number>
                            <x:ActiveCol>0</x:ActiveCol>
                        </x:Pane>
                        <x:Pane>
                            <x:Number>0</x:Number>
                            <x:ActiveRow>0</x:ActiveRow>
                            <x:ActiveCol>0</x:ActiveCol>
                        </x:Pane>
                    </x:Panes>
                    <x:ProtectContents>False</x:ProtectContents>
                    <x:Zoom>100</x:Zoom>
                </x:WorksheetOptions>
                <x:AutoFilter x:Range="A1:P1"/>
            </x:ExcelWorksheet>
        </x:ExcelWorksheets>
    </x:ExcelWorkbook>
</xml><![endif]-->
<style>
    * {
        font-family: "Segoe UI", "Roboto", "Inter", Arial, sans-serif;
    }
    body {
        background-color: #f8f9fa;
        margin: 0;
        padding: 8px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        background-color: #ffffff;
        font-size: 11pt;
        border-left: none;
    }
    th {
        background-color: #FFF3B0;
        color: #333333;
        font-weight: bold;
        font-size: 11pt;
        text-align: center;
        vertical-align: middle;
        padding: 8px 6px;
        border: 1px solid #000000;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    td {
        padding: 8px 6px;
        border: 1px solid #000000;
        font-size: 11pt;
        color: #000000;
        vertical-align: middle;
    }
    td:first-child {
        border-left: none;
        border-right: 1px solid #000000;
    }
    th:first-child {
        border-left: none;
        border-right: 1px solid #000000;
    }
    .row-even {
        background-color: #FFFFFF;
    }
    .row-odd {
        background-color: #F8FAFC;
    }
    .col-left {
        text-align: left;
    }
    .col-center {
        text-align: center;
    }
    .col-right {
        text-align: right;
    }
    .product-name {
        font-weight: 600;
        color: #111827;
        padding-left: 18px;
    }
    .school-name {
        font-weight: normal;
        color: #6B7280;
    }
    .grade-pricing {
        background-color: #F5F3FF;
        border-left: 4px solid #6366F1;
        padding: 14px 16px;
        line-height: 2.2;
        font-family: "Segoe UI Variable", "Consolas", "Courier New", monospace;
        font-size: 10.5pt;
        min-width: 280px;
        vertical-align: top;
    }
    .grade-range {
        font-weight: normal;
        color: #374151;
        display: inline-block;
        min-width: 60px;
        text-align: left;
    }
    .grade-price-value {
        font-weight: 600;
        color: #1F2937;
        font-family: "Segoe UI Variable", "Consolas", "Courier New", monospace;
        margin-left: 8px;
    }
    .status-live {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 50px;
        max-width: 65px;
        text-align: center;
    }
    .status-draft {
        background-color: #f3f4f6;
        color: #4b5563;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 50px;
        max-width: 65px;
        text-align: center;
    }
    .status-archived {
        background-color: #fee2e2;
        color: #991b1b;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 50px;
        max-width: 65px;
        text-align: center;
    }
    .stock-in {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 60px;
        max-width: 80px;
        text-align: center;
    }
    .stock-low {
        background-color: #fef3c7;
        color: #92400e;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 60px;
        max-width: 80px;
        text-align: center;
    }
    .stock-out {
        background-color: #fee2e2;
        color: #991b1b;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 60px;
        max-width: 80px;
        text-align: center;
    }
    .price-regular {
        color: #374151;
        font-weight: normal;
    }
    .text-na {
        color: #9CA3AF;
        font-style: italic;
    }
    .text-zero {
        color: #B0B0B0;
    }
    .stock-bold {
        font-weight: bold;
        color: #1F2937;
    }
    .stock-low-warning {
        color: #dc2626;
    }
    .col-grade-pricing {
        min-width: 280px;
        max-width: 350px;
    }
    .col-status {
        min-width: 50px;
        max-width: 65px;
    }
    .col-stock-status {
        min-width: 60px;
        max-width: 80px;
    }
</style>
</head>
<body>
<table>
<thead>
<tr>
<th>S.No</th>
<th>Product</th>
<th>School</th>
<th>Grade</th>
<th>Category</th>
<th>Product Type</th>
<th>Gender</th>
<th>Regular</th>
<th>Tax</th>
<th>Tax Profile</th>
<th class="col-grade-pricing">Grade Pricing</th>
<th class="col-grade-pricing">Variants</th>
<th>Stock</th>
<th>Low Stock</th>
<th class="col-status">Status</th>
<th class="col-stock-status">Stock Status</th>
</tr>
</thead>
<tbody>';

        // Use a separate serial number counter to ensure continuous numbering
        $serialNumber = 0;
        foreach ($products as $index => $product) {
            // Skip if product is null or empty
            if (empty($product) || empty($product->product_name)) {
                continue;
            }
            
            $serialNumber++; // Increment only for valid products
            
            $rowClass = $getRowClass($index);
            $statusClass = $getStatusStyle($product->status);
            $stockStatusClass = $getStockStatusStyle($product->stock_status ?? 'in_stock', $product->inventory_stock, $product->low_stock_threshold ?? 0);
            
            $html .= '<tr class="' . $rowClass . '">';
            
            // Serial Number - Center aligned, continuous numbering
            $html .= '<td class="col-center">' . $serialNumber . '</td>';
            
            // Product Name - Left aligned, semi-bold, darker
            $html .= '<td class="col-left product-name">' . htmlspecialchars($product->product_name) . '</td>';
            
            // School - Left aligned, muted color
            $schoolName = optional($product->school)->name;
            if (empty($schoolName) || $schoolName === 'N/A') {
                $html .= '<td class="col-left"><span class="text-na">N/A</span></td>';
            } else {
                $html .= '<td class="col-left"><span class="school-name">' . htmlspecialchars($schoolName) . '</span></td>';
            }
            
            // Grade - Center aligned
            $gradeValue = $formatGrades($product);
            $html .= '<td class="col-center">' . ($gradeValue === 'All' || empty($gradeValue) ? $formatNA($gradeValue) : htmlspecialchars($gradeValue)) . '</td>';
            
            // Category - Left aligned
            $html .= '<td class="col-left">' . $formatNA($product->category) . '</td>';
            
            // Product Type - Left aligned
            $html .= '<td class="col-left">' . $formatNA($product->product_type) . '</td>';
            
            // Gender - Center aligned
            $html .= '<td class="col-center">' . htmlspecialchars(ucfirst($product->gender ?? 'N/A')) . '</td>';
            
            // Regular Price - Right aligned
            $html .= '<td class="col-right">' . $formatPrice($product->price_regular) . '</td>';
            
            // Tax - Right aligned
            $html .= '<td class="col-right">' . $formatPrice($product->price_tax) . '</td>';
            
            // Tax Profile - Left aligned
            $html .= '<td class="col-left" title="Tax Profile: ' . htmlspecialchars($product->tax_profile ?? 'N/A') . '">' . $formatNA($product->tax_profile) . '</td>';
            
            // Grade Pricing - Left aligned, structured format with card-like styling
            $gradePricingHtml = $formatGradePricing($product);
            if ($gradePricingHtml === 'N/A') {
                $html .= '<td class="col-left grade-pricing col-grade-pricing"><span class="text-na">N/A</span></td>';
            } else {
                // Convert newlines to <br> tags for proper line breaks in Excel
                $gradePricingHtmlWithBreaks = str_replace("\n", "<br>", $gradePricingHtml);
                $html .= '<td class="col-left grade-pricing col-grade-pricing" title="Grade-wise pricing breakdown">' . $gradePricingHtmlWithBreaks . '</td>';
            }
            
            // Variants - Left aligned, structured format
            $formatVariants = function($product) {
                if ($product->variants && $product->variants->count() > 0) {
                    $variantLines = [];
                    foreach ($product->variants as $variant) {
                        $weight = $variant->weight ? number_format($variant->weight, 2) : 'N/A';
                        $stock = $variant->stock ?? 0;
                        $variantLines[] = $variant->option . ' → Weight: ' . $weight . ' | Stock: ' . number_format($stock);
                    }
                    return implode("\n", $variantLines);
                }
                return 'N/A';
            };
            $variantsHtml = $formatVariants($product);
            if ($variantsHtml === 'N/A') {
                $html .= '<td class="col-left grade-pricing col-grade-pricing" title="Product variants"><span class="text-na">N/A</span></td>';
            } else {
                $variantsHtmlWithBreaks = str_replace("\n", "<br>", $variantsHtml);
                $html .= '<td class="col-left grade-pricing col-grade-pricing" title="Product variants">' . $variantsHtmlWithBreaks . '</td>';
            }
            
            // Stock - Right aligned, bold if low
            $html .= '<td class="col-right">' . $formatStock($product->inventory_stock, $product->low_stock_threshold ?? 0) . '</td>';
            
            // Low Stock - Right aligned
            $lowStockVal = (int)($product->low_stock_threshold ?? 0);
            $html .= '<td class="col-right">' . ($lowStockVal == 0 ? '<span class="text-zero">0</span>' : number_format($lowStockVal)) . '</td>';
            
            // Status - Center aligned with color coding, narrower column
            $statusLabel = ucfirst($product->status ?? 'N/A');
            $html .= '<td class="col-center col-status"><span class="' . $statusClass . '">' . htmlspecialchars($statusLabel) . '</span></td>';
            
            // Stock Status - Center aligned with color coding, narrower column
            $stockStatusLabel = ucfirst(str_replace('_', ' ', $product->stock_status ?? 'in_stock'));
            $html .= '<td class="col-center col-stock-status"><span class="' . $stockStatusClass . '">' . htmlspecialchars($stockStatusLabel) . '</span></td>';
            
            $html .= '</tr>';
        }

        $html .= '</tbody></table>
<script>
// Add filter functionality (Excel will handle this natively)
// Freeze panes is handled via XML configuration above
</script>
</body></html>';

        return response($html, 200)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="products_merch_export-' . date('Y-m-d') . '.xls"');
    }

    protected function downloadPdf($products)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.merchandise.products.export-pdf', ['products' => $products]);
        return $pdf->download('products_merch_export-' . date('Y-m-d') . '.pdf');
    }

    public function create(): View
    {
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();
        // Provide typical defaults for Merch products
        $grades = [
            'Pre-KG' => 'Pre-KG',
            'LKG' => 'LKG',
            'UKG' => 'UKG',
            '1' => 'Class 1',
            '2' => 'Class 2',
            '3' => 'Class 3',
            '4' => 'Class 4',
            '5' => 'Class 5',
            '6' => 'Class 6',
            '7' => 'Class 7',
            '8' => 'Class 8',
            '9' => 'Class 9',
            '10' => 'Class 10',
            '11' => 'Class 11',
            '12' => 'Class 12',
        ];
        // Fetch categories scoped to 'merchandise'
        $categories = \App\Models\Admin\Master\Category::where('type', 'merchandise')
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get(['name', 'slug', 'type']);

        if (empty($categories)) {
            $categories = ['T-Shirts' => 'T-Shirts', 'Hoodies' => 'Hoodies', 'Caps' => 'Caps', 'Mugs' => 'Mugs', 'Accessories' => 'Accessories'];
        }
        // Fetch product types from database, fallback to defaults if empty
        $productTypes = \App\Models\Admin\Master\ProductType::getForSelect();
        if (empty($productTypes)) {
            $productTypes = ['merchandised' => 'Merchandise'];
        }

        $product = new Product();
        $product->gender = 'unisex';
        $product->product_type = 'merchandised'; // Set default product type

        return view('admin.merchandise.products.form', [
            'product' => $product, 
            'mode' => 'create',
            'schools' => $schools,
            'grades' => $grades,
            'categories' => $categories,
            'productTypes' => $productTypes,
            'productTypeTags' => \App\Models\Admin\Master\ProductType::getActive()->pluck('product_tag', 'slug')->toArray(),
            'selectedSchoolIds' => old('school_ids', []),
            'allSchoolsCount' => $schools->count()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Check if variant-based pricing is enabled
        $variantBasedPricing = $request->has('variant_based_pricing') && $request->input('variant_based_pricing') == '1';
        
        $validationRules = [
            'product_name' => 'required|string|max:255',
            'category' => 'nullable|string',
            'grade' => 'nullable|string',
            'school_ids' => 'nullable|array',
            'school_ids.*' => 'exists:schools,id',
            'school_id' => 'nullable', // Legacy
            'gender' => 'nullable|string',
            'tag_name' => 'nullable|string',
            'product_tag' => 'nullable|string|max:255',
            'price_regular' => $variantBasedPricing ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'price_tax' => 'nullable|numeric|min:0',
            'tax_profile' => 'nullable|string',
            'price_inclusive_tax' => 'nullable|boolean',
            'product_weight' => 'nullable|numeric|min:0',
            'inventory_stock' => 'required|integer|min:0',
            'status' => 'required|in:live,draft,archived',
            'description' => 'nullable|string',
            'featured_image' => [
                'nullable',
                'image',
                function ($attribute, $value, $fail) {
                    if ($value && $value->getSize() < 1024) { // 1 KB = 1024 bytes
                        $fail('The featured image must be at least 1 KB in size.');
                    }
                }
            ],
            'size_chart_path' => 'nullable|image',
            'size_measurement_image' => 'nullable|image',
            'video_url' => 'nullable|url',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mov,avi,wmv,flv,mkv|max:102400', // 100MB max
            'delivery_duration' => 'nullable|string|max:255',
            'variants' => 'nullable|array',
            'variants.*.option' => 'nullable|string',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
        ];
        
        // Add file validation rules for media_images
        if ($request->hasFile('media_images')) {
            $validationRules['media_images.*'] = [
                'file',
                'mimes:jpeg,jpg,png,gif,webp,mp4,webm,ogg,mov,avi,wmv,flv,mkv,m3u8',
                'max:20480' // 20MB for videos
            ];
        }
        
        $data = $request->validate($validationRules);
        
        // Handle checkbox
        $data['price_inclusive_tax'] = $request->has('price_inclusive_tax') ? 1 : 0;
        $data['show_product_tag'] = $request->has('show_product_tag') ? 1 : 0;
        
        // Validate variant prices when variant-based pricing is enabled
        if ($variantBasedPricing && $request->has('variants')) {
            $hasVariantPrice = false;
            $errors = [];
            foreach ($request->input('variants', []) as $index => $variant) {
                if (!empty($variant['option'])) {
                    // If variant has an option, it must have a price
                    if (empty($variant['price']) || $variant['price'] <= 0) {
                        $errors["variants.{$index}.price"] = "Price is required for variant '{$variant['option']}' when variant-based pricing is enabled.";
                    } else {
                        $hasVariantPrice = true;
                    }
                }
            }
            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }
            if (!$hasVariantPrice) {
                return redirect()->back()->withErrors(['variants' => 'At least one variant with an option must have a price when variant-based pricing is enabled.'])->withInput();
            }
        }

        $data['product_type'] = 'merchandised';
        $data['stock_status'] = $data['inventory_stock'] > 0 ? 'in_stock' : 'out_of_stock';
        // Merch might not have School ID, or it is null (Public/Custom)
        
        // Handle Gallery Images
        $paths = [];
        if ($request->hasFile('media_images')) {
            foreach ($request->file('media_images') as $file) {
                $paths[] = $file->store('products', 'public');
            }
        }

        // Handle gallery images with reordering support
        if ($request->filled('media_order_ids')) {
            $existing = $request->input('existing_media_images', []);
            $orderMap = explode(',', $request->input('media_order_ids'));
            $finalImages = [];
            
            $existingIndex = 0;
            $newIndex = 0;

            foreach ($orderMap as $type) {
                if ($type === 'existing') {
                    if (isset($existing[$existingIndex])) {
                        $finalImages[] = $existing[$existingIndex];
                        $existingIndex++;
                    }
                } elseif ($type === 'new') {
                    if (isset($paths[$newIndex])) {
                        $finalImages[] = $paths[$newIndex];
                        $newIndex++;
                    }
                }
            }
            
            // Safety: Append any remaining
            while(isset($existing[$existingIndex])) {
                $finalImages[] = $existing[$existingIndex++];
            }
            while(isset($paths[$newIndex])) {
                $finalImages[] = $paths[$newIndex++];
            }

            $data['media_images'] = $finalImages;
        } elseif ($request->exists('existing_media_images') || $request->has('media_list_modified')) {
            $existing = $request->input('existing_media_images', []);
            $data['media_images'] = array_merge($existing, $paths);
        } elseif (!empty($paths)) {
            $data['media_images'] = $paths;
        }
        
        // Handle file uploads
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }
        if ($request->hasFile('size_chart_path')) {
            $data['size_chart_path'] = $request->file('size_chart_path')->store('size_charts', 'public');
        }
        if ($request->hasFile('size_measurement_image')) {
            $data['size_measurement_image'] = $request->file('size_measurement_image')->store('size_charts', 'public');
        }
        // Handle video file upload (independent of video_url)
        if ($request->hasFile('video_file')) {
            $data['video_file'] = $request->file('video_file')->store('videos', 'public');
        }

        // Auto-set product_type for Merchandise products
        $data['product_type'] = 'merchandised';

        $product = Product::create($data);

        if ($request->has('variants')) {
            $this->saveVariants($product, $request->input('variants'));
        }

        // Handle Schools
        if ($request->has('school_ids')) {
            $product->schools()->sync($request->school_ids);
            // Update legacy school_id
            $firstSchool = $request->input('school_ids')[0] ?? null;
            $product->school_id = $firstSchool;
            $product->saveQuietly();
        }

        return redirect()->route('admin.merchandise.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id): View
    {
        $product = Product::findOrFail($id);
        
        // Set default product_type if not set
        if (empty($product->product_type)) {
            $product->product_type = 'merchandised';
        }
        
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();
        $grades = [
            'Pre-KG' => 'Pre-KG',
            'LKG' => 'LKG',
            'UKG' => 'UKG',
            '1' => 'Class 1',
            '2' => 'Class 2',
            '3' => 'Class 3',
            '4' => 'Class 4',
            '5' => 'Class 5',
            '6' => 'Class 6',
            '7' => 'Class 7',
            '8' => 'Class 8',
            '9' => 'Class 9',
            '10' => 'Class 10',
            '11' => 'Class 11',
            '12' => 'Class 12',
        ];
        // Fetch categories scoped to 'merchandise'
        $categories = \App\Models\Admin\Master\Category::where('type', 'merchandise')
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get(['name', 'slug', 'type']);

        if (empty($categories)) {
            $categories = ['T-Shirts' => 'T-Shirts', 'Hoodies' => 'Hoodies', 'Caps' => 'Caps', 'Mugs' => 'Mugs', 'Accessories' => 'Accessories'];
        }
        // Fetch product types from database, fallback to defaults if empty
        $productTypes = \App\Models\Admin\Master\ProductType::getForSelect();
        if (empty($productTypes)) {
            $productTypes = ['merchandised' => 'Merchandise'];
        }

        return view('admin.merchandise.products.form', [
            'product' => $product,
            'mode' => 'edit',
            'schools' => $schools,
            'grades' => $grades,
            'categories' => $categories,
            'productTypes' => $productTypes,
            'productTypeTags' => \App\Models\Admin\Master\ProductType::getActive()->pluck('product_tag', 'slug')->toArray(),
            'selectedSchoolIds' => old('school_ids', $product->schools->pluck('id')->toArray()),
            'allSchoolsCount' => $schools->count()
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        
        // Check if variant-based pricing is enabled
        $variantBasedPricing = $request->has('variant_based_pricing') && $request->input('variant_based_pricing') == '1';

        $validationRules = [
            'product_name' => 'required|string|max:255',
            'category' => 'nullable|string',
            'grade' => 'nullable|string',
            'school_ids' => 'nullable|array',
            'school_ids.*' => 'exists:schools,id',
            'school_id' => 'nullable', // Legacy
            'gender' => 'nullable|string',
            'tag_name' => 'nullable|string',
            'product_tag' => 'nullable|string|max:255',
            'price_regular' => $variantBasedPricing ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'price_tax' => 'nullable|numeric|min:0',
            'tax_profile' => 'nullable|string',
            'price_inclusive_tax' => 'nullable|boolean',
            'product_weight' => 'nullable|numeric|min:0',
            'inventory_stock' => 'required|integer|min:0',
            'product_type' => 'nullable|string',
            'status' => 'required|in:live,draft,archived',
            'description' => 'nullable|string',
            'featured_image' => [
                'nullable',
                'image',
                function ($attribute, $value, $fail) {
                    if ($value && $value->getSize() < 1024) { // 1 KB = 1024 bytes
                        $fail('The featured image must be at least 1 KB in size.');
                    }
                }
            ],
            'size_chart_path' => 'nullable|image',
            'size_measurement_image' => 'nullable|image',
            'video_url' => 'nullable|url',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mov,avi,wmv,flv,mkv|max:102400', // 100MB max
            'delivery_duration' => 'nullable|string|max:255',
            'variants' => 'nullable|array',
            'variants.*.option' => 'nullable|string',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
        ];
        
        // Add file validation rules for media_images
        if ($request->hasFile('media_images')) {
            $validationRules['media_images.*'] = [
                'file',
                'mimes:jpeg,jpg,png,gif,webp,mp4,webm,ogg,mov,avi,wmv,flv,mkv,m3u8',
                'max:20480' // 20MB for videos
            ];
        }
        
        $data = $request->validate($validationRules);
        
        // Always ensure product_type is set to 'merchandised' for merchandise products
        // This prevents products from disappearing if product_type is missing or changed
        $data['product_type'] = 'merchandised';
        
        // Handle checkbox
        $data['price_inclusive_tax'] = $request->has('price_inclusive_tax') ? 1 : 0;
        $data['show_product_tag'] = $request->has('show_product_tag') ? 1 : 0;
        
        // Validate variant prices when variant-based pricing is enabled
        if ($variantBasedPricing && $request->has('variants')) {
            $hasVariantPrice = false;
            $errors = [];
            foreach ($request->input('variants', []) as $index => $variant) {
                if (!empty($variant['option'])) {
                    // If variant has an option, it must have a price
                    if (empty($variant['price']) || $variant['price'] <= 0) {
                        $errors["variants.{$index}.price"] = "Price is required for variant '{$variant['option']}' when variant-based pricing is enabled.";
                    } else {
                        $hasVariantPrice = true;
                    }
                }
            }
            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }
            if (!$hasVariantPrice) {
                return redirect()->back()->withErrors(['variants' => 'At least one variant with an option must have a price when variant-based pricing is enabled.'])->withInput();
            }
        }
        
        $data['stock_status'] = $data['inventory_stock'] > 0 ? 'in_stock' : 'out_of_stock';
        
        // Handle Gallery Images
        $paths = [];
        if ($request->hasFile('media_images')) {
            foreach ($request->file('media_images') as $file) {
                $paths[] = $file->store('products', 'public');
            }
        }

        // Handle gallery images with reordering support
        if ($request->filled('media_order_ids')) {
            $existing = $request->input('existing_media_images', []);
            $orderMap = explode(',', $request->input('media_order_ids'));
            $finalImages = [];
            
            $existingIndex = 0;
            $newIndex = 0;

            foreach ($orderMap as $type) {
                if ($type === 'existing') {
                    if (isset($existing[$existingIndex])) {
                        $finalImages[] = $existing[$existingIndex];
                        $existingIndex++;
                    }
                } elseif ($type === 'new') {
                    if (isset($paths[$newIndex])) {
                        $finalImages[] = $paths[$newIndex];
                        $newIndex++;
                    }
                }
            }
            
            // Safety: Append any remaining
            while(isset($existing[$existingIndex])) {
                $finalImages[] = $existing[$existingIndex++];
            }
            while(isset($paths[$newIndex])) {
                $finalImages[] = $paths[$newIndex++];
            }

            $data['media_images'] = $finalImages;
        } elseif ($request->exists('existing_media_images') || $request->has('media_list_modified')) {
            $existing = $request->input('existing_media_images', []);
            $data['media_images'] = array_merge($existing, $paths);
        } elseif (!empty($paths)) {
            // Fallback: Append only mode
            if ($product && $product->media_images) {
                $data['media_images'] = array_merge($product->media_images, $paths);
            } else {
                $data['media_images'] = $paths;
            }
        }
        
        // Handle file uploads
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }
        if ($request->hasFile('size_chart_path')) {
            $data['size_chart_path'] = $request->file('size_chart_path')->store('size_charts', 'public');
        }
        if ($request->hasFile('size_measurement_image')) {
            $data['size_measurement_image'] = $request->file('size_measurement_image')->store('size_charts', 'public');
        }
        // Handle video file upload (independent of video_url)
        if ($request->hasFile('video_file')) {
            // Delete old video file if exists
            if ($product->video_file && \Storage::disk('public')->exists($product->video_file)) {
                \Storage::disk('public')->delete($product->video_file);
            }
            $data['video_file'] = $request->file('video_file')->store('videos', 'public');
        }
        
        // Handle video file removal
        if ($request->has('remove_video_file') && $request->input('remove_video_file') == '1') {
            if ($product->video_file && \Storage::disk('public')->exists($product->video_file)) {
                \Storage::disk('public')->delete($product->video_file);
            }
            $data['video_file'] = null;
        }
        
        // Handle video URL removal (independent of video_file)
        if ($request->has('remove_video_url') && $request->input('remove_video_url') == '1') {
            $data['video_url'] = null;
        }

        $product->update($data);

        if ($request->has('variants')) {
            $this->saveVariants($product, $request->input('variants'));
        }

        // Handle Schools
        if ($request->has('school_ids')) {
            $product->schools()->sync($request->school_ids);
            // Update legacy school_id
            $firstSchool = $request->input('school_ids')[0] ?? null;
            $product->school_id = $firstSchool;
            $product->saveQuietly();
        }

        return redirect()->route('admin.merchandise.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.merchandise.products.index')->with('success', 'Product deleted successfully.');
    }

    protected function saveVariants($product, $variants)
    {
        if (!is_array($variants)) return;

        $existingIds = $product->variants()->pluck('id')->toArray();
        $processedIds = [];

        foreach ($variants as $variantData) {
            if (empty($variantData['option'])) continue;

            $stock = isset($variantData['stock']) ? (int)$variantData['stock'] : 0;
            $lowStock = isset($variantData['low_stock_threshold']) ? (int)$variantData['low_stock_threshold'] : 5;

            if (isset($variantData['id']) && in_array($variantData['id'], $existingIds)) {
                ProductVariant::where('id', $variantData['id'])->update([
                    'option' => $variantData['option'],
                    'price' => $variantData['price'] ?? null,
                    'weight' => $variantData['weight'] ?? null,
                    'stock' => $stock,
                    'low_stock_threshold' => $lowStock,
                    'name' => 'Size'
                ]);
                $processedIds[] = $variantData['id'];
            } else {
                $product->variants()->create([
                    'option' => $variantData['option'],
                    'price' => $variantData['price'] ?? null,
                    'weight' => $variantData['weight'] ?? null,
                    'stock' => $stock,
                    'low_stock_threshold' => $lowStock,
                    'name' => 'Size'
                ]);
            }
        }

        $variantsToDelete = array_diff($existingIds, $processedIds);
        if (!empty($variantsToDelete)) {
            ProductVariant::destroy($variantsToDelete);
        }

        $product->updateTotalStock();
    }
}
