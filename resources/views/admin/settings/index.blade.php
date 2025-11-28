@extends('admin.layouts.base')

@section('title', 'System Settings | The Skool Store')
@section('page_heading', 'System Settings')
@section('page_subheading', 'Configure payment gateways, templates, branding, and backups')

@section('content')
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
        <a href="{{ route('master.admin.settings.payment-gateways') }}" class="card" style="display:block;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#f0f9ff;display:grid;place-items:center;color:#0ea5e9;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 style="margin:0;color:var(--heading);font-size:18px;">Payment Gateways</h3>
            </div>
            <p style="margin:0;color:var(--text);font-size:14px;">Configure payment providers like Stripe, Razorpay, PayPal, and manage credentials.</p>
        </a>

        <a href="{{ route('master.admin.settings.invoice-templates') }}" class="card" style="display:block;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#f0fdf4;display:grid;place-items:center;color:#22c55e;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 style="margin:0;color:var(--heading);font-size:18px;">Invoice Templates</h3>
            </div>
            <p style="margin:0;color:var(--text);font-size:14px;">Design and manage invoice templates with custom headers, footers, and branding.</p>
        </a>

        <a href="{{ route('master.admin.settings.email-templates') }}" class="card" style="display:block;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#fef3f2;display:grid;place-items:center;color:#ef4444;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 style="margin:0;color:var(--heading);font-size:18px;">Email Templates</h3>
            </div>
            <p style="margin:0;color:var(--text);font-size:14px;">Create and manage email templates for order confirmations, shipping notifications, etc.</p>
        </a>

        <a href="{{ route('master.admin.settings.sms-templates') }}" class="card" style="display:block;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#f5f3ff;display:grid;place-items:center;color:#a855f7;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 style="margin:0;color:var(--heading);font-size:18px;">SMS Templates</h3>
            </div>
            <p style="margin:0;color:var(--text);font-size:14px;">Manage SMS templates for order updates, OTPs, and notifications.</p>
        </a>

        <a href="{{ route('master.admin.settings.app-branding') }}" class="card" style="display:block;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#fff7ed;display:grid;place-items:center;color:#f97316;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <h3 style="margin:0;color:var(--heading);font-size:18px;">App Branding</h3>
            </div>
            <p style="margin:0;color:var(--text);font-size:14px;">Customize app logo, colors, fonts, and meta information for SEO.</p>
        </a>

        <a href="{{ route('master.admin.settings.backups') }}" class="card" style="display:block;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#f0f9ff;display:grid;place-items:center;color:#3b82f6;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <h3 style="margin:0;color:var(--heading);font-size:18px;">Backup & Restore</h3>
            </div>
            <p style="margin:0;color:var(--text);font-size:14px;">Create backups of database and files, restore from previous backups.</p>
        </a>

        <a href="{{ route('master.admin.settings.audit-logs') }}" class="card" style="display:block;text-decoration:none;color:inherit;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#eef2ff;display:grid;place-items:center;color:#6366f1;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-2-4V7a2 2 0 10-4 0v6m-4 4h12a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 style="margin:0;color:var(--heading);font-size:18px;">Audit Logs</h3>
            </div>
            <p style="margin:0;color:var(--text);font-size:14px;">Review who changed prices, stock, orders, shipping, or tax settings.</p>
        </a>
    </div>
@endsection

