<x-app-layout>
  <div class="flex flex-col">
    <x-admin.page-header :title="__('Site settings')" :subtitle="__('Branding, login background, footer, top bar, and contact/newsletter routing.')" />

    <x-admin.page-content class="space-y-8">
    <p class="text-sm text-zinc-600">{{ __('Branding, login page background, footer, top bar, and contact/newsletter routing. Upload logos/favicons directly here, or paste media library paths if needed.') }}</p>

    @if (session('status'))
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
    @endif

    @if (session('mail_test_status'))
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('mail_test_status') }}</div>
    @endif

    @if ($errors->any())
      <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="post" class="space-y-8" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('General & branding') }}</h2>
        </div>
        <div class="space-y-4 px-6 py-5">
          <div>
            <label for="site_display_name" class="block text-sm font-medium text-zinc-700">{{ __('Site display name') }}</label>
            <input type="text" name="site_display_name" id="site_display_name" value="{{ old('site_display_name', $settings['site_display_name']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
            <p class="mt-1 text-xs text-zinc-500">{{ __('Shown in titles and loaders when set; defaults to APP name.') }}</p>
          </div>
          <div>
            <label for="copyright_line" class="block text-sm font-medium text-zinc-700">{{ __('Copyright / brand line (footer)') }}</label>
            <input type="text" name="copyright_line" id="copyright_line" value="{{ old('copyright_line', $settings['copyright_line']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
          </div>
          <div class="grid gap-4 md:grid-cols-3">
            <div>
              <label for="logo_file" class="block text-sm font-medium text-zinc-700">{{ __('Logo (dark variant)') }}</label>
              <input type="file" name="logo_file" id="logo_file" accept=".jpg,.jpeg,.png,.webp,.svg" class="mt-1 block w-full rounded-md border border-zinc-300 bg-white text-sm shadow-sm file:mr-3 file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200"/>
              <input type="text" name="logo_path" id="logo_path" value="{{ old('logo_path', $settings['logo_path']) }}" placeholder="Optional manual path (storage/...)" class="mt-2 block w-full rounded-md border-zinc-300 font-mono text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"/>
              @if (!empty(old('logo_path', $settings['logo_path'])))
                <img id="logo-preview-live" src="{{ \App\Support\MediaImageUrl::url(old('logo_path', $settings['logo_path'])) }}" alt="" class="mt-2 h-12 w-auto max-w-[200px] object-contain"/>
              @else
                <img id="logo-preview-live" src="" alt="" class="mt-2 hidden h-12 w-auto max-w-[200px] object-contain"/>
              @endif
            </div>
            <div>
              <label for="logo_light_file" class="block text-sm font-medium text-zinc-700">{{ __('Logo light variant (optional)') }}</label>
              <input type="file" name="logo_light_file" id="logo_light_file" accept=".jpg,.jpeg,.png,.webp,.svg" class="mt-1 block w-full rounded-md border border-zinc-300 bg-white text-sm shadow-sm file:mr-3 file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200"/>
              <input type="text" name="logo_light_path" id="logo_light_path" value="{{ old('logo_light_path', $settings['logo_light_path']) }}" placeholder="Optional manual path (storage/...)" class="mt-2 block w-full rounded-md border-zinc-300 font-mono text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"/>
              @if (!empty(old('logo_light_path', $settings['logo_light_path'])))
                <img id="logo-light-preview-live" src="{{ \App\Support\MediaImageUrl::url(old('logo_light_path', $settings['logo_light_path'])) }}" alt="" class="mt-2 h-12 w-auto max-w-[200px] rounded bg-zinc-800 object-contain p-2"/>
              @else
                <img id="logo-light-preview-live" src="" alt="" class="mt-2 hidden h-12 w-auto max-w-[200px] rounded bg-zinc-800 object-contain p-2"/>
              @endif
            </div>
            <div>
              <label for="favicon_file" class="block text-sm font-medium text-zinc-700">{{ __('Favicon') }}</label>
              <input type="file" name="favicon_file" id="favicon_file" accept=".ico,.png,.jpg,.jpeg,.webp" class="mt-1 block w-full rounded-md border border-zinc-300 bg-white text-sm shadow-sm file:mr-3 file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200"/>
              <input type="text" name="favicon_path" id="favicon_path" value="{{ old('favicon_path', $settings['favicon_path']) }}" placeholder="Optional manual path (storage/...)" class="mt-2 block w-full rounded-md border-zinc-300 font-mono text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"/>
              @if (!empty(old('favicon_path', $settings['favicon_path'])))
                <img id="favicon-preview-live" src="{{ \App\Support\MediaImageUrl::url(old('favicon_path', $settings['favicon_path'])) }}" alt="" class="mt-2 h-12 w-12 object-contain"/>
              @else
                <img id="favicon-preview-live" src="" alt="" class="mt-2 hidden h-12 w-12 object-contain"/>
              @endif
            </div>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Login & registration page') }}</h2>
          <p class="mt-1 text-xs text-zinc-500">{{ __('Background image for the sign-in, register, and password-reset screens (split layout on desktop).') }}</p>
        </div>
        <div class="space-y-4 px-6 py-5">
          <div>
            <label for="auth_panel_image_file" class="block text-sm font-medium text-zinc-700">{{ __('Panel background image') }}</label>
            <p class="mt-1 text-xs text-zinc-500">{{ __('On desktop this fills the left half of the screen beside the form; on phones it appears above the form. Use a wide or tall photo (e.g. 1600×1200 or portrait).') }}</p>
            <input type="file" name="auth_panel_image_file" id="auth_panel_image_file" accept=".jpg,.jpeg,.png,.webp" class="mt-2 block w-full rounded-md border border-zinc-300 bg-white text-sm shadow-sm file:mr-3 file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200"/>
            <input type="text" name="auth_panel_image_path" id="auth_panel_image_path" value="{{ old('auth_panel_image_path', $settings['auth_panel_image_path'] ?? '') }}" placeholder="{{ __('Optional manual path (storage/...)') }}" class="mt-2 block w-full rounded-md border-zinc-300 font-mono text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"/>
            @php $authPanelPreview = trim((string) old('auth_panel_image_path', $settings['auth_panel_image_path'] ?? '')); @endphp
            @if ($authPanelPreview !== '')
              <img id="auth-panel-preview-live" src="{{ \App\Support\MediaImageUrl::url($authPanelPreview) }}" alt="" class="mt-3 max-h-56 w-full max-w-xl rounded-lg border border-zinc-200 object-cover shadow-sm"/>
            @else
              <img id="auth-panel-preview-live" src="" alt="" class="mt-3 hidden max-h-56 w-full max-w-xl rounded-lg border border-zinc-200 object-cover shadow-sm"/>
            @endif
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Top bar') }}</h2>
        </div>
        <div class="grid gap-4 px-6 py-5 md:grid-cols-2">
          <div>
            <label for="dealer_hours_label" class="block text-sm font-medium text-zinc-700">{{ __('Hours label') }}</label>
            <input type="text" name="dealer_hours_label" id="dealer_hours_label" value="{{ old('dealer_hours_label', $settings['dealer_hours_label']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
          </div>
          <div>
            <label for="dealer_address" class="block text-sm font-medium text-zinc-700">{{ __('Address') }}</label>
            <input type="text" name="dealer_address" id="dealer_address" value="{{ old('dealer_address', $settings['dealer_address']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
          </div>
          <div>
            <label for="dealer_phone" class="block text-sm font-medium text-zinc-700">{{ __('Main phone') }}</label>
            <input type="text" name="dealer_phone" id="dealer_phone" value="{{ old('dealer_phone', $settings['dealer_phone']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
          </div>
          <div>
            <label for="dealer_sales_phone" class="block text-sm font-medium text-zinc-700">{{ __('Sales phone (optional)') }}</label>
            <input type="text" name="dealer_sales_phone" id="dealer_sales_phone" value="{{ old('dealer_sales_phone', $settings['dealer_sales_phone']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
          </div>
          <div class="grid gap-4 md:col-span-2 md:grid-cols-2">
            @foreach ([['social_facebook', 'Facebook'], ['social_instagram', 'Instagram'], ['social_linkedin', 'LinkedIn'], ['social_youtube', 'YouTube / video']] as [$key, $label])
              <div>
                <label for="{{ $key }}" class="block text-sm font-medium text-zinc-700">{{ $label }} URL</label>
                <input type="text" name="{{ $key }}" id="{{ $key }}" value="{{ old($key, $settings[$key]) }}" class="mt-1 block w-full rounded-md border-zinc-300 font-mono text-sm shadow-sm"/>
              </div>
            @endforeach
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Hours') }}</h2>
        </div>
        <div class="space-y-4 px-6 py-5">
          @foreach ([
            ['dealer_sales_hours', __('Sales hours')],
            ['dealer_service_hours', __('Service hours')],
            ['dealer_parts_hours', __('Parts hours')],
          ] as [$fld, $lbl])
            <div>
              <label for="{{ $fld }}" class="block text-sm font-medium text-zinc-700">{{ $lbl }}</label>
              <textarea name="{{ $fld }}" id="{{ $fld }}" rows="4" class="mt-1 block w-full rounded-md border-zinc-300 font-mono text-sm shadow-sm">{{ old($fld, $settings[$fld]) }}</textarea>
              <p class="mt-1 text-xs text-zinc-500">{{ __('One line per row.') }}</p>
            </div>
          @endforeach
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Footer') }}</h2>
        </div>
        <div class="space-y-4 px-6 py-5">
          <div>
            <label for="footer_tagline" class="block text-sm font-medium text-zinc-700">{{ __('Tagline paragraph') }}</label>
            <textarea name="footer_tagline" id="footer_tagline" rows="3" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm">{{ old('footer_tagline', $settings['footer_tagline']) }}</textarea>
          </div>
          <div>
            <label for="footer_blog_title" class="block text-sm font-medium text-zinc-700">{{ __('Blog / highlights column title') }}</label>
            <input type="text" name="footer_blog_title" id="footer_blog_title" value="{{ old('footer_blog_title', $settings['footer_blog_title']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
          </div>
          <div>
            <label for="footer_blog_entries_json" class="block text-sm font-medium text-zinc-700">{{ __('Blog highlights (JSON array)') }}</label>
            <textarea name="footer_blog_entries_json" id="footer_blog_entries_json" rows="10" placeholder='[{"title":"Example","url":"/faq","meta":"NO COMMENTS"}]' class="mt-1 block w-full rounded-md border-zinc-300 font-mono text-xs shadow-sm">{{ old('footer_blog_entries_json', $settings['footer_blog_entries_json']) }}</textarea>
            <p class="mt-1 text-xs text-zinc-500">{{ __('Objects: title, optional url or href, optional meta. Empty array hides entries.') }}</p>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label for="footer_privacy_url" class="block text-sm font-medium text-zinc-700">{{ __('Privacy policy URL') }}</label>
              <input type="text" name="footer_privacy_url" id="footer_privacy_url" value="{{ old('footer_privacy_url', $settings['footer_privacy_url']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
            </div>
            <div>
              <label for="footer_terms_url" class="block text-sm font-medium text-zinc-700">{{ __('Terms of service URL') }}</label>
              <input type="text" name="footer_terms_url" id="footer_terms_url" value="{{ old('footer_terms_url', $settings['footer_terms_url']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
            </div>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Newsletter & contact') }}</h2>
        </div>
        <div class="space-y-4 px-6 py-5">
          <label class="flex items-center gap-3">
            <input type="checkbox" name="newsletter_enabled" value="1" @checked(old('newsletter_enabled', $settings['newsletter_enabled']) === '1') class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"/>
            <span class="text-sm text-zinc-800">{{ __('Enable footer newsletter form') }}</span>
          </label>
          <div>
            <label for="newsletter_note" class="block text-sm font-medium text-zinc-700">{{ __('Footer newsletter helper text') }}</label>
            <input type="text" name="newsletter_note" id="newsletter_note" value="{{ old('newsletter_note', $settings['newsletter_note']) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
          </div>
          <div>
            <label for="dealer_public_email" class="block text-sm font-medium text-zinc-700">{{ __('Public dealer email (staff listings)') }}</label>
            <input type="email" name="dealer_public_email" id="dealer_public_email" value="{{ old('dealer_public_email', $settings['dealer_public_email'] ?? '') }}" placeholder="{{ config('mail.from.address') }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
            <p class="mt-1 text-xs text-zinc-500">{{ __('Shown on vehicle detail when the listing is staff-owned. Separate from admin notification email.') }}</p>
          </div>
          <div>
            <label for="contact_notify_email" class="block text-sm font-medium text-zinc-700">{{ __('Contact & newsletter notifications email') }}</label>
            <input type="email" name="contact_notify_email" id="contact_notify_email" value="{{ old('contact_notify_email', $settings['contact_notify_email']) }}" placeholder="{{ config('mail.outbound.admin_to') }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
            <p class="mt-1 text-xs text-zinc-500">{{ __('Overrides MAIL_TO_ADMIN when set.') }}</p>
          </div>
          <div>
            <label for="contact_from_name" class="block text-sm font-medium text-zinc-700">{{ __('Outbound “to” display name for contact mails') }}</label>
            <input type="text" name="contact_from_name" id="contact_from_name" value="{{ old('contact_from_name', $settings['contact_from_name']) }}" placeholder="Admin" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm"/>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Payment methods') }}</h2>
          <p class="mt-1 text-xs text-zinc-500">{{ __('Enable checkout options. Paystack uses PAYSTACK_SECRET_KEY from .env when enabled.') }}</p>
        </div>
        <div class="space-y-6 px-6 py-5">
          <div class="flex items-start gap-3 rounded-lg border border-zinc-200 p-4">
            <input type="hidden" name="payment_paystack_enabled" value="0" />
            <input type="checkbox" name="payment_paystack_enabled" id="payment_paystack_enabled" value="1" class="mt-1 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" @checked(old('payment_paystack_enabled', $settings['payment_paystack_enabled'] ?? '1') === '1') />
            <div class="min-w-0 flex-1">
              <label for="payment_paystack_enabled" class="block text-sm font-semibold text-zinc-900">{{ __('Paystack (card, bank, USSD)') }}</label>
              <p class="mt-1 text-xs text-zinc-500">{{ __('Shows Paystack on checkout when enabled. Set PAYSTACK_SECRET_KEY and PAYSTACK_PUBLIC_KEY in .env to process payments.') }}</p>
            </div>
          </div>
          <div class="rounded-lg border border-zinc-200 p-4 space-y-4">
            <div class="flex items-start gap-3">
              <input type="hidden" name="payment_bank_transfer_enabled" value="0" />
              <input type="checkbox" name="payment_bank_transfer_enabled" id="payment_bank_transfer_enabled" value="1" class="mt-1 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" @checked(old('payment_bank_transfer_enabled', $settings['payment_bank_transfer_enabled'] ?? '0') === '1') />
              <div class="min-w-0 flex-1">
                <label for="payment_bank_transfer_enabled" class="block text-sm font-semibold text-zinc-900">{{ __('Bank transfer') }}</label>
                <p class="mt-1 text-xs text-zinc-500">{{ __('Customer places order and pays via your bank account.') }}</p>
              </div>
            </div>
            <div>
              <label for="payment_bank_transfer_details" class="block text-sm font-medium text-zinc-700">{{ __('Bank account details') }}</label>
              <textarea name="payment_bank_transfer_details" id="payment_bank_transfer_details" rows="5" class="mt-1 block w-full rounded-md border-zinc-300 font-mono text-sm shadow-sm" placeholder="Bank name&#10;Account name&#10;Account number">{{ old('payment_bank_transfer_details', $settings['payment_bank_transfer_details'] ?? '') }}</textarea>
            </div>
          </div>
          <div class="rounded-lg border border-zinc-200 p-4 space-y-4">
            <div class="flex items-start gap-3">
              <input type="hidden" name="payment_pay_on_delivery_enabled" value="0" />
              <input type="checkbox" name="payment_pay_on_delivery_enabled" id="payment_pay_on_delivery_enabled" value="1" class="mt-1 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" @checked(old('payment_pay_on_delivery_enabled', $settings['payment_pay_on_delivery_enabled'] ?? '0') === '1') />
              <div class="min-w-0 flex-1">
                <label for="payment_pay_on_delivery_enabled" class="block text-sm font-semibold text-zinc-900">{{ __('Pay on delivery') }}</label>
                <p class="mt-1 text-xs text-zinc-500">{{ __('Customer pays when the order is delivered.') }}</p>
              </div>
            </div>
            <div>
              <label for="payment_pay_on_delivery_note" class="block text-sm font-medium text-zinc-700">{{ __('Instructions (optional)') }}</label>
              <textarea name="payment_pay_on_delivery_note" id="payment_pay_on_delivery_note" rows="3" class="mt-1 block w-full rounded-md border-zinc-300 text-sm shadow-sm">{{ old('payment_pay_on_delivery_note', $settings['payment_pay_on_delivery_note'] ?? '') }}</textarea>
            </div>
          </div>
        </div>
      </section>

      <div class="flex justify-end gap-3">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-md border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">{{ __('Cancel') }}</a>
        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ __('Save settings') }}</button>
      </div>
    </form>

    <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
      <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Test outbound email (PHPMailer)') }}</h2>
        <p class="mt-1 text-xs text-zinc-500">{{ __('Sends a short message using MAIL_PHPMAILER_* and MAIL_FROM_* from .env (separate from the contact notification fields above).') }}</p>
      </div>
      <div class="px-6 py-5">
        <form action="{{ route('admin.settings.mail-test') }}" method="post" class="flex flex-col gap-3 sm:flex-row sm:items-end">
          @csrf
          <div class="min-w-0 flex-1">
            <label for="test_email" class="block text-sm font-medium text-zinc-700">{{ __('Recipient email') }}</label>
            <input type="email" name="test_email" id="test_email" value="{{ old('test_email', auth()->user()->email ?? '') }}" required class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('test_email') border-red-400 @enderror"/>
            @error('test_email')
              <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
          </div>
          <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2">{{ __('Send test email') }}</button>
        </form>
      </div>
    </section>
    </x-admin.page-content>
  </div>
  <script>
    (() => {
      const wirePreview = (fileId, imgId) => {
        const fileInput = document.getElementById(fileId);
        const img = document.getElementById(imgId);
        if (!fileInput || !img) return;

        fileInput.addEventListener('change', () => {
          const file = fileInput.files && fileInput.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (ev) => {
            if (ev && ev.target && typeof ev.target.result === 'string') {
              img.src = ev.target.result;
              img.classList.remove('hidden');
            }
          };
          reader.readAsDataURL(file);
        });
      };

      wirePreview('logo_file', 'logo-preview-live');
      wirePreview('logo_light_file', 'logo-light-preview-live');
      wirePreview('favicon_file', 'favicon-preview-live');
      wirePreview('auth_panel_image_file', 'auth-panel-preview-live');
    })();
  </script>
  @include('admin.partials.luxe-footer', ['footerClass' => 'mt-8'])
</x-app-layout>
