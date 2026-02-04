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
            $response = $this->dtdcService->generateLabel($reference, 'pdf');

            // Assuming response contains a URL or Base64
            // Example handling: If base64 is returned:
            if (isset($response['data']['label_content'])) {
                 $pdfContent = base64_decode($response['data']['label_content']);
                 return response($pdfContent)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="label-'.$reference.'.pdf"');
            }
            
            // If URL is returned
            if (isset($response['data']['label_url'])) {
                return redirect($response['data']['label_url']);
            }

            return back()->with('error', 'Label generated but content not found in response.');

        } catch (\Exception $e) {
            return back()->with('error', 'Label Generation Failed: ' . $e->getMessage());
        }
    }
}
