<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\Mail\OutboundMailService;
use App\Support\SiteBrand;
use App\Support\SiteSettingDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PHPMailer\PHPMailer\Exception as PhpMailerException;
use RuntimeException;

class AdminSiteSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $settings = SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed());

        return view('admin.settings.edit', [
            'title' => __('Site settings'),
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_display_name' => ['nullable', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string', 'max:2048'],
            'logo_light_path' => ['nullable', 'string', 'max:2048'],
            'favicon_path' => ['nullable', 'string', 'max:2048'],
            'auth_panel_image_path' => ['nullable', 'string', 'max:2048'],
            'auth_panel_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'logo_light_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'favicon_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:2048'],
            'dealer_phone' => ['nullable', 'string', 'max:64'],
            'dealer_sales_phone' => ['nullable', 'string', 'max:64'],
            'dealer_address' => ['nullable', 'string', 'max:500'],
            'dealer_hours_label' => ['nullable', 'string', 'max:120'],
            'dealer_sales_hours' => ['nullable', 'string', 'max:5000'],
            'dealer_service_hours' => ['nullable', 'string', 'max:5000'],
            'dealer_parts_hours' => ['nullable', 'string', 'max:5000'],
            'social_facebook' => ['nullable', 'string', 'max:500'],
            'social_instagram' => ['nullable', 'string', 'max:500'],
            'social_linkedin' => ['nullable', 'string', 'max:500'],
            'social_youtube' => ['nullable', 'string', 'max:500'],
            'copyright_line' => ['nullable', 'string', 'max:255'],
            'footer_tagline' => ['nullable', 'string', 'max:2000'],
            'footer_blog_title' => ['nullable', 'string', 'max:255'],
            'footer_blog_entries_json' => ['nullable', 'string', 'max:20000'],
            'newsletter_note' => ['nullable', 'string', 'max:500'],
            'footer_privacy_url' => ['nullable', 'string', 'max:500'],
            'footer_terms_url' => ['nullable', 'string', 'max:500'],
            'contact_notify_email' => ['nullable', 'email', 'max:255'],
            'contact_from_name' => ['nullable', 'string', 'max:255'],
            'dealer_public_email' => ['nullable', 'email', 'max:255'],
            'payment_bank_transfer_details' => ['nullable', 'string', 'max:10000'],
            'payment_pay_on_delivery_note' => ['nullable', 'string', 'max:5000'],
            'payment_paystack_enabled' => ['nullable', 'string', 'max:1'],
            'payment_bank_transfer_enabled' => ['nullable', 'string', 'max:1'],
            'payment_pay_on_delivery_enabled' => ['nullable', 'string', 'max:1'],
        ]);

        if ($request->hasFile('logo_file')) {
            $validated['logo_path'] = $this->storeBrandAsset($request->file('logo_file'), 'logo');
        }
        if ($request->hasFile('logo_light_file')) {
            $validated['logo_light_path'] = $this->storeBrandAsset($request->file('logo_light_file'), 'logo-light');
        }
        if ($request->hasFile('favicon_file')) {
            $validated['favicon_path'] = $this->storeBrandAsset($request->file('favicon_file'), 'favicon');
        }
        if ($request->hasFile('auth_panel_image_file')) {
            $validated['auth_panel_image_path'] = $this->storeBrandAsset($request->file('auth_panel_image_file'), 'auth-panel');
        }

        $blogJson = (string) ($validated['footer_blog_entries_json'] ?? '');
        if (trim($blogJson) !== '') {
            $decoded = json_decode($blogJson, true);
            if (! is_array($decoded)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['footer_blog_entries_json' => __('Blog entries must be valid JSON.')]);
            }
            foreach ($decoded as $row) {
                if (! is_array($row)) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['footer_blog_entries_json' => __('Each blog entry must be an object.')]);
                }
            }
            $validated['footer_blog_entries_json'] = json_encode($decoded);
        } else {
            $validated['footer_blog_entries_json'] = '[]';
        }

        $validated['newsletter_enabled'] = $request->boolean('newsletter_enabled') ? '1' : '0';
        $validated['payment_paystack_enabled'] = $request->boolean('payment_paystack_enabled') ? '1' : '0';
        $validated['payment_bank_transfer_enabled'] = $request->boolean('payment_bank_transfer_enabled') ? '1' : '0';
        $validated['payment_pay_on_delivery_enabled'] = $request->boolean('payment_pay_on_delivery_enabled') ? '1' : '0';

        foreach (SiteSettingDefaults::managedKeys() as $key) {
            $value = $validated[$key] ?? '';
            if (! is_string($value)) {
                $value = (string) $value;
            }
            $value = trim($value);
            $this->persistKey($key, $value);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', __('Site settings saved.'));
    }

    public function sendTestMail(Request $request, OutboundMailService $mailer): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        $to = $validated['test_email'];
        $user = $request->user();
        $subject = '[' . SiteBrand::displayName() . '] ' . __('Test email');
        $html = '<p>' . e(__('This is a test message from the site admin. If you received it, PHPMailer SMTP is working.')) . '</p>';

        try {
            $mailer->send($to, $to, $subject, $html, $user?->email, $user?->name);
        } catch (RuntimeException $e) {
            return back()->withErrors(['test_email' => $e->getMessage()]);
        } catch (PhpMailerException $e) {
            report($e);

            return back()->withErrors(['test_email' => $e->getMessage()]);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['test_email' => __('Could not send test email. Check SMTP settings and logs.')]);
        }

        return back()->with('mail_test_status', __('Test email sent to :email.', ['email' => $to]));
    }

    private function storeBrandAsset(\Illuminate\Http\UploadedFile $file, string $prefix): string
    {
        $safePrefix = Str::slug($prefix);
        $filename = $safePrefix . '-' . Str::uuid() . '.' . strtolower($file->getClientOriginalExtension() ?: 'png');
        $stored = $file->storePubliclyAs('site-settings', $filename, 'public');

        return 'storage/' . ltrim((string) $stored, '/');
    }

    private function persistKey(string $key, string $value): void
    {
        if ($value === '') {
            SiteSetting::query()->where('key', $key)->delete();
        } else {
            SiteSetting::setValue($key, $value);
        }

        Cache::store('file')->forget('site_settings_merged_v1');
    }
}
