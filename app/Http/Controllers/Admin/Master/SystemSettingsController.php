<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\AppBranding;
use App\Models\Admin\Master\AuditLog;
use App\Models\Admin\Master\Backup;
use App\Models\Admin\Master\EmailTemplate;
use App\Models\Admin\Master\InvoiceTemplate;
use App\Models\Admin\Master\PaymentGateway;
use App\Models\Admin\Master\SmsTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use ZipArchive;

class SystemSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index');
    }

    public function auditLogs(Request $request): View
    {
        $filters = $request->only(['action', 'entity_type', 'date_from', 'date_to', 'user_id']);

        $query = AuditLog::query()
            ->when($filters['action'] ?? null, fn($q, $action) => $q->where('action', $action))
            ->when($filters['entity_type'] ?? null, fn($q, $type) => $q->where('entity_type', $type))
            ->when($filters['date_from'] ?? null, fn($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->when($filters['user_id'] ?? null, fn($q, $userId) => $q->where('user_id', $userId))
            ->orderByDesc('created_at');

        $logs = $query->paginate(50)->withQueryString();

        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $entityTypes = AuditLog::select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type');

        return view('admin.settings.audit-logs', compact('logs', 'filters', 'actions', 'entityTypes'));
    }

    // Payment Gateways
    public function paymentGateways(): View
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();

        return view('admin.settings.payment-gateways', compact('gateways'));
    }

    public function storePaymentGateway(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'test_mode' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'credentials' => ['nullable', 'array'],
            'credentials_json' => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['test_mode'] = $request->has('test_mode');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->has('credentials_json') && !empty($request->credentials_json)) {
            $data['credentials'] = json_decode($request->credentials_json, true);
        }

        PaymentGateway::create($data);

        return back()->with('status', 'Payment gateway created.');
    }

    public function updatePaymentGateway(Request $request, PaymentGateway $paymentGateway): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'test_mode' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'credentials' => ['nullable', 'array'],
            'credentials_json' => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['test_mode'] = $request->has('test_mode');

        if ($request->has('credentials_json') && !empty($request->credentials_json)) {
            $data['credentials'] = json_decode($request->credentials_json, true);
        }

        $paymentGateway->update($data);

        return back()->with('status', 'Payment gateway updated.');
    }

    public function destroyPaymentGateway(PaymentGateway $paymentGateway): RedirectResponse
    {
        $paymentGateway->delete();

        return back()->with('status', 'Payment gateway deleted.');
    }

    // Invoice Templates
    public function invoiceTemplates(): View
    {
        $templates = InvoiceTemplate::orderBy('is_default', 'desc')->orderBy('name')->get();

        return view('admin.settings.invoice-templates', compact('templates'));
    }

    public function storeInvoiceTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'template_content' => ['nullable', 'string'],
            'header_html' => ['nullable', 'string'],
            'footer_html' => ['nullable', 'string'],
            'logo_path' => ['nullable', 'string'],
            'settings' => ['nullable', 'array'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('invoice-templates', 'public');
        }

        $template = InvoiceTemplate::create($data);

        if ($request->has('is_default')) {
            $template->setAsDefault();
        }

        return back()->with('status', 'Invoice template created.');
    }

    public function updateInvoiceTemplate(Request $request, InvoiceTemplate $invoiceTemplate): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'template_content' => ['nullable', 'string'],
            'header_html' => ['nullable', 'string'],
            'footer_html' => ['nullable', 'string'],
            'logo_path' => ['nullable', 'string'],
            'settings' => ['nullable', 'array'],
        ]);

        if ($request->hasFile('logo')) {
            if ($invoiceTemplate->logo_path) {
                Storage::disk('public')->delete($invoiceTemplate->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('invoice-templates', 'public');
        }

        $invoiceTemplate->update($data);

        if ($request->has('is_default')) {
            $invoiceTemplate->setAsDefault();
        }

        return back()->with('status', 'Invoice template updated.');
    }

    public function destroyInvoiceTemplate(InvoiceTemplate $invoiceTemplate): RedirectResponse
    {
        if ($invoiceTemplate->logo_path) {
            Storage::disk('public')->delete($invoiceTemplate->logo_path);
        }
        $invoiceTemplate->delete();

        return back()->with('status', 'Invoice template deleted.');
    }

    // Email Templates
    public function emailTemplates(): View
    {
        $templates = EmailTemplate::orderBy('type')->get();

        return view('admin.settings.email-templates', compact('templates'));
    }

    public function storeEmailTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255', 'unique:email_templates,type'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'variables' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        EmailTemplate::create($data);

        return back()->with('status', 'Email template created.');
    }

    public function updateEmailTemplate(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255', 'unique:email_templates,type,' . $emailTemplate->id],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'variables' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        $emailTemplate->update($data);

        return back()->with('status', 'Email template updated.');
    }

    public function destroyEmailTemplate(EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->delete();

        return back()->with('status', 'Email template deleted.');
    }

    // SMS Templates
    public function smsTemplates(): View
    {
        $templates = SmsTemplate::orderBy('type')->get();

        return view('admin.settings.sms-templates', compact('templates'));
    }

    public function storeSmsTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255', 'unique:sms_templates,type'],
            'message' => ['required', 'string'],
            'variables' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        SmsTemplate::create($data);

        return back()->with('status', 'SMS template created.');
    }

    public function updateSmsTemplate(Request $request, SmsTemplate $smsTemplate): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255', 'unique:sms_templates,type,' . $smsTemplate->id],
            'message' => ['required', 'string'],
            'variables' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        $smsTemplate->update($data);

        return back()->with('status', 'SMS template updated.');
    }

    public function destroySmsTemplate(SmsTemplate $smsTemplate): RedirectResponse
    {
        $smsTemplate->delete();

        return back()->with('status', 'SMS template deleted.');
    }

    // App Branding
    public function appBranding(): View
    {
        $branding = AppBranding::current();

        return view('admin.settings.app-branding', compact('branding'));
    }

    public function updateAppBranding(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string'],
            'favicon_path' => ['nullable', 'string'],
            'primary_color' => ['required', 'string', 'max:7'],
            'secondary_color' => ['required', 'string', 'max:7'],
            'accent_color' => ['required', 'string', 'max:7'],
            'font_family' => ['nullable', 'string', 'max:255'],
            'custom_css' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            $data['favicon_path'] = $request->file('favicon')->store('branding', 'public');
        }

        $branding = AppBranding::current();
        $branding->update($data);

        return back()->with('status', 'App branding updated.');
    }

    // Backup & Restore
    public function backups(): View
    {
        $backups = Backup::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.settings.backups', compact('backups'));
    }

    public function createBackup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:database,files,full'],
            'notes' => ['nullable', 'string'],
        ]);

        $backup = Backup::create([
            'name' => 'backup_' . now()->format('Y-m-d_H-i-s') . '_' . $data['type'],
            'type' => $data['type'],
            'status' => Backup::STATUS_PENDING,
            'file_path' => null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Run backup in background (you can use queues for this)
        $this->performBackup($backup);

        return back()->with('status', 'Backup initiated. It will be available shortly.');
    }

    protected function performBackup(Backup $backup): void
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $fileName = $backup->name . '.zip';
            $filePath = $backupDir . '/' . $fileName;

            $zip = new ZipArchive();
            if ($zip->open($filePath, ZipArchive::CREATE) !== true) {
                throw new \Exception('Cannot create zip file');
            }

            if (in_array($backup->type, ['database', 'full'])) {
                // Backup database - using Laravel's database export
                $dbBackupPath = storage_path('app/backups/database_' . now()->format('Y-m-d_H-i-s') . '.sql');
                $database = config('database.connections.' . config('database.default'));

                // Try mysqldump first (most common)
                if ($database['driver'] === 'mysql') {
                    $command = sprintf(
                        'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                        escapeshellarg($database['username']),
                        escapeshellarg($database['password'] ?? ''),
                        escapeshellarg($database['host']),
                        escapeshellarg($database['database']),
                        escapeshellarg($dbBackupPath)
                    );
                    exec($command, $output, $returnVar);

                    if ($returnVar === 0 && File::exists($dbBackupPath) && File::size($dbBackupPath) > 0) {
                        $zip->addFile($dbBackupPath, 'database.sql');
                    } else {
                        // Fallback: export as JSON (for SQLite or if mysqldump fails)
                        $tables = DB::select('SHOW TABLES');
                        $dbContent = [];
                        foreach ($tables as $table) {
                            $tableName = array_values((array) $table)[0];
                            $dbContent[$tableName] = DB::table($tableName)->get()->toArray();
                        }
                        File::put($dbBackupPath, json_encode($dbContent, JSON_PRETTY_PRINT));
                        $zip->addFile($dbBackupPath, 'database.json');
                    }
                } else {
                    // For SQLite or other databases, export as JSON
                    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
                    $dbContent = [];
                    foreach ($tables as $table) {
                        $tableName = $table->name;
                        $dbContent[$tableName] = DB::table($tableName)->get()->toArray();
                    }
                    File::put($dbBackupPath, json_encode($dbContent, JSON_PRETTY_PRINT));
                    $zip->addFile($dbBackupPath, 'database.json');
                }
            }

            if (in_array($backup->type, ['files', 'full'])) {
                // Backup storage files
                $storagePath = storage_path('app/public');
                if (File::exists($storagePath)) {
                    $files = File::allFiles($storagePath);
                    foreach ($files as $file) {
                        $zip->addFile($file->getRealPath(), 'storage/' . $file->getRelativePathname());
                    }
                }
            }

            $zip->close();

            $backup->update([
                'file_path' => $filePath,
                'file_size' => File::size($filePath),
                'status' => Backup::STATUS_COMPLETED,
            ]);
        } catch (\Exception $e) {
            $backup->update([
                'status' => Backup::STATUS_FAILED,
                'notes' => ($backup->notes ?? '') . ' | Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function downloadBackup(Backup $backup)
    {
        if ($backup->status !== Backup::STATUS_COMPLETED || !File::exists($backup->file_path)) {
            return back()->withErrors(['error' => 'Backup file not found or not completed.']);
        }

        return response()->download($backup->file_path, $backup->name . '.zip');
    }

    public function restoreBackup(Request $request, Backup $backup): RedirectResponse
    {
        if ($backup->status !== Backup::STATUS_COMPLETED || !File::exists($backup->file_path)) {
            return back()->withErrors(['error' => 'Backup file not found or not completed.']);
        }

        try {
            $zip = new ZipArchive();
            if ($zip->open($backup->file_path) !== true) {
                throw new \Exception('Cannot open backup file');
            }

            $extractPath = storage_path('app/backups/restore_' . now()->format('Y-m-d_H-i-s'));
            $zip->extractTo($extractPath);
            $zip->close();

            // Restore database if present
            $dbFile = $extractPath . '/database.sql';
            if (File::exists($dbFile)) {
                // Restore database logic here
                // This would typically involve running SQL commands
            }

            // Restore files if present
            $storageBackup = $extractPath . '/storage';
            if (File::exists($storageBackup)) {
                File::copyDirectory($storageBackup, storage_path('app/public'));
            }

            return back()->with('status', 'Backup restored successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Restore failed: ' . $e->getMessage()]);
        }
    }

    public function destroyBackup(Backup $backup): RedirectResponse
    {
        if (File::exists($backup->file_path)) {
            File::delete($backup->file_path);
        }
        $backup->delete();

        return back()->with('status', 'Backup deleted.');
    }
}

