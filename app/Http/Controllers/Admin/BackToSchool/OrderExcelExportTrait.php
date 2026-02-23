<?php

namespace App\Http\Controllers\Admin\BackToSchool;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color;

trait OrderExcelExportTrait
{
    protected function downloadOrdersExcel($orders)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('BTS Orders');
        
        // Define headers with S.No
        $headers = [
            'S.No',
            'Order Number',
            'Order Date',
            'Customer Name',
            'Customer Phone',
            'Customer Email',
            'Item Name',
            'Size',
            'Qty',
            'Total Amount (Rs)',
            'Shipping Cost (Rs)',
            'Payment Status',
            'Order Status',
            'School',
            'Student Name',
            'Grade',
            'Category',
            'Tracking Number',
        ];
        
        // Set headers in row 1
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'], // Dark gray
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        
        $sheet->getStyle('A1:R1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // Freeze header row
        $sheet->freezePane('A2');
        
        // Add data rows
        $row = 2;
        $sno = 1;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $sno);
            $sheet->setCellValue('B' . $row, $order->order_number);
            $sheet->setCellValue('C' . $row, $order->order_date ? $order->order_date->format('d-M-Y') : ($order->created_at ? $order->created_at->format('d-M-Y') : ''));
            $sheet->setCellValue('D' . $row, $order->customer_name);
            $sheet->setCellValueExplicit('E' . $row, $order->customer_phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, $order->customer_email);
            $sheet->setCellValue('G' . $row, $order->item_name);
            $sheet->setCellValue('H' . $row, $order->size);
            $sheet->setCellValue('I' . $row, $order->quantity);
            $sheet->setCellValue('J' . $row, $order->total_amount);
            $sheet->setCellValue('K' . $row, $order->shipping_cost);
            $sheet->setCellValue('L' . $row, ucfirst(str_replace('_', ' ', $order->payment_status)));
            $sheet->setCellValue('M' . $row, ucfirst(str_replace('_', ' ', $order->order_status)));
            $sheet->setCellValue('N' . $row, $order->school ? $order->school->name : '');
            $sheet->setCellValue('O' . $row, $order->student_name);
            $sheet->setCellValue('P' . $row, $order->grade ?? '');
            $sheet->setCellValue('Q' . $row, $order->category ?? '');
            $sheet->setCellValueExplicit('R' . $row, $order->tracking_number ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            
            // Apply zebra striping
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB'],
                    ],
                ]);
            }
            
            // Color code Payment Status
            $paymentStatus = strtolower($order->payment_status);
            if ($paymentStatus === 'paid') {
                $sheet->getStyle('L' . $row)->getFont()->setColor(new Color('10B981')); // Green
            } elseif ($paymentStatus === 'pending') {
                $sheet->getStyle('L' . $row)->getFont()->setColor(new Color('F59E0B')); // Orange
            }
            
            // Color code Order Status
            $orderStatus = strtolower($order->order_status);
            $statusColors = [
                'processing' => 'DBEAFE', // Light Blue
                'delivered' => 'D1FAE5', // Light Green
                'cancelled' => 'FEE2E2', // Light Red
                'pending' => 'FEF3C7', // Light Yellow
                'order_placed' => 'FEF3C7', // Light Yellow
                'shipped' => 'E9D5FF', // Light Purple
            ];
            
            if (isset($statusColors[$orderStatus])) {
                $sheet->getStyle('M' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $statusColors[$orderStatus]],
                    ],
                ]);
            }
            
            $row++;
            $sno++;
        }
        
        // Format currency columns
        $lastRow = $row - 1;
        $sheet->getStyle('J2:K' . $lastRow)->getNumberFormat()->setFormatCode('₹#,##0.00');
        
        // Set column alignments
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // S.No
        $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Order Number
        $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date
        $sheet->getStyle('D2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Text fields
        $sheet->getStyle('H2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Size, Qty
        $sheet->getStyle('J2:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Currency
        $sheet->getStyle('L2:M' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
        $sheet->getStyle('N2:Q' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // School, Student, etc.
        $sheet->getStyle('P2:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Grade
        
        // Vertical center all cells
        $sheet->getStyle('A1:R' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Auto-fit columns
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set row height
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(22);
        }
        
        // Add borders to all cells
        $sheet->getStyle('A1:R' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);
        
        // Thicker border around the table
        $sheet->getStyle('A1:R' . $lastRow)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '9CA3AF'],
                ],
            ],
        ]);
        
        // Enable filters on header row
        $sheet->setAutoFilter('A1:R1');
        
        // Text wrapping for long text
        $sheet->getStyle('D2:G' . $lastRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('N2:Q' . $lastRow)->getAlignment()->setWrapText(true);
        
        // Output to browser
        $filename = 'bts-orders-' . date('Y-m-d') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
