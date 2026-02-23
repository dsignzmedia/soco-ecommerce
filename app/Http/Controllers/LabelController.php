<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtdcService;
use Illuminate\Support\Facades\Log;

class LabelController extends Controller
{
    protected $dtdcService;

    public function __construct(DtdcService $dtdcService)
    {
        $this->dtdcService = $dtdcService;
    }

    public function show($reference)
    {
        return view('label.show', compact('reference'));
    }

    public function generate(Request $request, $reference)
    {
        try {
            $env = $request->query('env', 'production');
            $response = $this->dtdcService->generateLabel($reference, 'pdf', $env);

            // 1. If response is a raw string (PDF content)
            if (is_string($response) && (str_starts_with($response, '%PDF') || strlen($response) > 100)) {
                 return response($response)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="label-'.$reference.'.pdf"');
            }

            // 2. If response is JSON array
            if (is_array($response)) {
                if (isset($response['data']['label_content'])) {
                     $pdfContent = base64_decode($response['data']['label_content']);
                     return response($pdfContent)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'inline; filename="label-'.$reference.'.pdf"');
                }
                
                if (isset($response['success']) && !$response['success']) {
                    return back()->with('error', 'Label API Message: ' . ($response['message'] ?? 'Unknown Error'));
                }
            }

            return back()->with('error', 'Label generated but format not recognized.');

        } catch (\Exception $e) {
            Log::error("Label Gen Error: " . $e->getMessage());
            return back()->with('error', 'Label Generation Failed: ' . $e->getMessage());
        }
    }
}
