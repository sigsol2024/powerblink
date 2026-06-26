<x-app-layout>
  <x-admin.page-header :subtitle="__('Branding, login background, footer, top bar, and contact/newsletter routing.')" />

  <x-admin.page-content class="space-y-6">
    @include('admin.partials.flash')

    @if (session('mail_test_status'))
      <div class="rounded-lg bg-secondary/10 border border-secondary/30 p-3 text-sm text-secondary">{{ session('mail_test_status') }}</div>
    @endif

    @if ($errors->any())
      <div class="rounded-lg border border-error/30 bg-error-container/30 px-4 py-3 text-sm text-on-error-container">
        <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="post" class="pb-admin-form pb-admin-form--wide space-y-6" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <x-admin.panel :title="__('General & branding')">
        <div class="pb-field">
          <label for="site_display_name">{{ __('Site display name') }}</label>
          <input type="text" name="site_display_name" id="site_display_name" value="{{ old('site_display_name', $settings['site_display_name']) }}"/>
          <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('Shown in titles and loaders when set; defaults to APP name.') }}</p>
        </div>
        <div class="pb-field">
          <label for="copyright_line">{{ __('Copyright / brand line (footer)') }}</label>
          <input type="text" name="copyright_line" id="copyright_line" value="{{ old('copyright_line', $settings['copyright_line']) }}"/>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
          <div class="pb-field">
            <label for="logo_file">{{ __('Logo (dark variant)') }}</label>
            <input type="file" name="logo_file" id="logo_file" accept=".jpg,.jpeg,.png,.webp,.svg"/>
            <input type="text" name="logo_path" id="logo_path" value="{{ old('logo_path', $settings['logo_path']) }}" placeholder="Optional manual path (storage/...)" class="mt-2 font-mono text-xs"/>
            @if (!empty(old('logo_path', $settings['logo_path'])))
              <img id="logo-preview-live" src="{{ \App\Support\MediaImageUrl::url(old('logo_path', $settings['logo_path'])) }}" alt="" class="mt-2 h-12 w-auto max-w-[200px] object-contain"/>
            @else
              <img id="logo-preview-live" src="" alt="" class="mt-2 hidden h-12 w-auto max-w-[200px] object-contain"/>
            @endif
          </div>
          <div class="pb-field">
            <label for="logo_light_file">{{ __('Logo light variant (optional)') }}</label>
            <input type="file" name="logo_light_file" id="logo_light_file" accept=".jpg,.jpeg,.png,.webp,.svg"/>
            <input type="text" name="logo_light_path" id="logo_light_path" value="{{ old('logo_light_path', $settings['logo_light_path']) }}" placeholder="Optional manual path (storage/...)" class="mt-2 font-mono text-xs"/>
            @if (!empty(old('logo_light_path', $settings['logo_light_path'])))
              <img id="logo-light-preview-live" src="{{ \App\Support\MediaImageUrl::url(old('logo_light_path', $settings['logo_light_path'])) }}" alt="" class="mt-2 h-12 w-auto max-w-[200px] rounded bg-surface-container-high object-contain p-2"/>
            @else
              <img id="logo-light-preview-live" src="" alt="" class="mt-2 hidden h-12 w-auto max-w-[200px] rounded bg-surface-container-high object-contain p-2"/>
            @endif
          </div>
          <div class="pb-field">
            <label for="favicon_file">{{ __('Favicon') }}</label>
            <input type="file" name="favicon_file" id="favicon_file" accept=".ico,.png,.jpg,.jpeg,.webp"/>
            <input type="text" name="favicon_path" id="favicon_path" value="{{ old('favicon_path', $settings['favicon_path']) }}" placeholder="Optional manual path (storage/...)" class="mt-2 font-mono text-xs"/>
            @if (!empty(old('favicon_path', $settings['favicon_path'])))
              <img id="favicon-preview-live" src="{{ \App\Support\MediaImageUrl::url(old('favicon_path', $settings['favicon_path'])) }}" alt="" class="mt-2 h-12 w-12 object-contain"/>
            @else
              <img id="favicon-preview-live" src="" alt="" class="mt-2 hidden h-12 w-12 object-contain"/>
            @endif
          </div>
        </div>
      </x-admin.panel>

      <x-admin.panel
        :title="__('Login & registration page')"
        :description="__('Background image for the sign-in, register, and password-reset screens (split layout on desktop).')"
      >
        <div class="pb-field">
          <label for="auth_panel_image_file">{{ __('Panel background image') }}</label>
          <p class="mb-2 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('On desktop this fills the left half of the screen beside the form; on phones it appears above the form. Use a wide or tall photo (e.g. 1600×1200 or portrait).') }}</p>
          <input type="file" name="auth_panel_image_file" id="auth_panel_image_file" accept=".jpg,.jpeg,.png,.webp"/>
          <input type="text" name="auth_panel_image_path" id="auth_panel_image_path" value="{{ old('auth_panel_image_path', $settings['auth_panel_image_path'] ?? '') }}" placeholder="{{ __('Optional manual path (storage/...)') }}" class="mt-2 font-mono text-xs"/>
          @php $authPanelPreview = trim((string) old('auth_panel_image_path', $settings['auth_panel_image_path'] ?? '')); @endphp
          @if ($authPanelPreview !== '')
            <img id="auth-panel-preview-live" src="{{ \App\Support\MediaImageUrl::url($authPanelPreview) }}" alt="" class="mt-3 max-h-56 w-full max-w-xl rounded-xl border border-outline-variant/60 object-cover"/>
          @else
            <img id="auth-panel-preview-live" src="" alt="" class="mt-3 hidden max-h-56 w-full max-w-xl rounded-xl border border-outline-variant/60 object-cover"/>
          @endif
        </div>
      </x-admin.panel>

      <x-admin.panel :title="__('Top bar')">
        <div class="grid gap-4 md:grid-cols-2">
          <div class="pb-field">
            <label for="dealer_hours_label">{{ __('Hours label') }}</label>
            <input type="text" name="dealer_hours_label" id="dealer_hours_label" value="{{ old('dealer_hours_label', $settings['dealer_hours_label']) }}"/>
          </div>
          <div class="pb-field">
            <label for="dealer_address">{{ __('Address') }}</label>
            <input type="text" name="dealer_address" id="dealer_address" value="{{ old('dealer_address', $settings['dealer_address']) }}"/>
          </div>
          <div class="pb-field">
            <label for="dealer_phone">{{ __('Main phone') }}</label>
            <input type="text" name="dealer_phone" id="dealer_phone" value="{{ old('dealer_phone', $settings['dealer_phone']) }}"/>
          </div>
          <div class="pb-field">
            <label for="dealer_sales_phone">{{ __('Sales phone (optional)') }}</label>
            <input type="text" name="dealer_sales_phone" id="dealer_sales_phone" value="{{ old('dealer_sales_phone', $settings['dealer_sales_phone']) }}"/>
          </div>
          <div class="grid gap-4 md:col-span-2 md:grid-cols-2">
            @foreach ([['social_facebook', 'Facebook'], ['social_instagram', 'Instagram'], ['social_linkedin', 'LinkedIn'], ['social_youtube', 'YouTube / video']] as [$key, $label])
              <div class="pb-field">
                <label for="{{ $key }}">{{ $label }} URL</label>
                <input type="text" name="{{ $key }}" id="{{ $key }}" value="{{ old($key, $settings[$key]) }}" class="font-mono text-sm"/>
              </div>
            @endforeach
          </div>
        </div>
      </x-admin.panel>

      <x-admin.panel :title="__('Hours')">
        @foreach ([
          ['dealer_sales_hours', __('Sales hours')],
          ['dealer_service_hours', __('Service hours')],
          ['dealer_parts_hours', __('Parts hours')],
        ] as [$fld, $lbl])
          <div class="pb-field">
            <label for="{{ $fld }}">{{ $lbl }}</label>
            <textarea name="{{ $fld }}" id="{{ $fld }}" rows="4" class="font-mono text-sm">{{ old($fld, $settings[$fld]) }}</textarea>
            <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('One line per row.') }}</p>
          </div>
        @endforeach
      </x-admin.panel>

      <x-admin.panel :title="__('Footer')">
        <div class="pb-field">
          <label for="footer_tagline">{{ __('Tagline paragraph') }}</label>
          <textarea name="footer_tagline" id="footer_tagline" rows="3">{{ old('footer_tagline', $settings['footer_tagline']) }}</textarea>
        </div>
        <div class="pb-field">
          <label for="footer_blog_title">{{ __('Blog / highlights column title') }}</label>
          <input type="text" name="footer_blog_title" id="footer_blog_title" value="{{ old('footer_blog_title', $settings['footer_blog_title']) }}"/>
        </div>
        <div class="pb-field">
          <label for="footer_blog_entries_json">{{ __('Blog highlights (JSON array)') }}</label>
          <textarea name="footer_blog_entries_json" id="footer_blog_entries_json" rows="10" placeholder='[{"title":"Example","url":"/faq","meta":"NO COMMENTS"}]' class="font-mono text-xs">{{ old('footer_blog_entries_json', $settings['footer_blog_entries_json']) }}</textarea>
          <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('Objects: title, optional url or href, optional meta. Empty array hides entries.') }}</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div class="pb-field">
            <label for="footer_privacy_url">{{ __('Privacy policy URL') }}</label>
            <input type="text" name="footer_privacy_url" id="footer_privacy_url" value="{{ old('footer_privacy_url', $settings['footer_privacy_url']) }}"/>
          </div>
          <div class="pb-field">
            <label for="footer_terms_url">{{ __('Terms of service URL') }}</label>
            <input type="text" name="footer_terms_url" id="footer_terms_url" value="{{ old('footer_terms_url', $settings['footer_terms_url']) }}"/>
          </div>
        </div>
      </x-admin.panel>

      <x-admin.panel :title="__('Newsletter & contact')">
        <label class="inline-flex items-center gap-3 text-sm text-on-surface normal-case tracking-normal font-normal">
          <input type="checkbox" name="newsletter_enabled" value="1" @checked(old('newsletter_enabled', $settings['newsletter_enabled']) === '1')/>
          {{ __('Enable footer newsletter form') }}
        </label>
        <div class="pb-field">
          <label for="newsletter_note">{{ __('Footer newsletter helper text') }}</label>
          <input type="text" name="newsletter_note" id="newsletter_note" value="{{ old('newsletter_note', $settings['newsletter_note']) }}"/>
        </div>
        <div class="pb-field">
          <label for="dealer_public_email">{{ __('Public dealer email (staff listings)') }}</label>
          <input type="email" name="dealer_public_email" id="dealer_public_email" value="{{ old('dealer_public_email', $settings['dealer_public_email'] ?? '') }}" placeholder="{{ config('mail.from.address') }}"/>
          <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('Shown on vehicle detail when the listing is staff-owned. Separate from admin notification email.') }}</p>
        </div>
        <div class="pb-field">
          <label for="contact_notify_email">{{ __('Contact & newsletter notifications email') }}</label>
          <input type="email" name="contact_notify_email" id="contact_notify_email" value="{{ old('contact_notify_email', $settings['contact_notify_email']) }}" placeholder="{{ config('mail.outbound.admin_to') }}"/>
          <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('Overrides MAIL_TO_ADMIN when set.') }}</p>
        </div>
        <div class="pb-field">
          <label for="contact_from_name">{{ __('Outbound “to” display name for contact mails') }}</label>
          <input type="text" name="contact_from_name" id="contact_from_name" value="{{ old('contact_from_name', $settings['contact_from_name']) }}" placeholder="Admin"/>
        </div>
      </x-admin.panel>

      <x-admin.panel
        :title="__('Payment methods')"
        :description="__('Enable checkout options. Paystack uses PAYSTACK_SECRET_KEY from .env when enabled.')"
      >
        <div class="space-y-4">
          <div class="flex items-start gap-3 rounded-xl border border-outline-variant/60 p-4">
            <input type="hidden" name="payment_paystack_enabled" value="0" />
            <input type="checkbox" name="payment_paystack_enabled" id="payment_paystack_enabled" value="1" class="mt-1" @checked(old('payment_paystack_enabled', $settings['payment_paystack_enabled'] ?? '1') === '1') />
            <div class="min-w-0 flex-1">
              <label for="payment_paystack_enabled" class="block text-sm font-semibold text-on-surface normal-case tracking-normal">{{ __('Paystack (card, bank, USSD)') }}</label>
              <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('Shows Paystack on checkout when enabled. Set PAYSTACK_SECRET_KEY and PAYSTACK_PUBLIC_KEY in .env to process payments.') }}</p>
            </div>
          </div>
          <div class="rounded-xl border border-outline-variant/60 p-4 space-y-4">
            <div class="flex items-start gap-3">
              <input type="hidden" name="payment_bank_transfer_enabled" value="0" />
              <input type="checkbox" name="payment_bank_transfer_enabled" id="payment_bank_transfer_enabled" value="1" class="mt-1" @checked(old('payment_bank_transfer_enabled', $settings['payment_bank_transfer_enabled'] ?? '0') === '1') />
              <div class="min-w-0 flex-1">
                <label for="payment_bank_transfer_enabled" class="block text-sm font-semibold text-on-surface normal-case tracking-normal">{{ __('Bank transfer') }}</label>
                <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('Customer places order and pays via your bank account.') }}</p>
              </div>
            </div>
            <div class="pb-field">
              <label for="payment_bank_transfer_details">{{ __('Bank account details') }}</label>
              <textarea name="payment_bank_transfer_details" id="payment_bank_transfer_details" rows="5" class="font-mono text-sm" placeholder="Bank name&#10;Account name&#10;Account number">{{ old('payment_bank_transfer_details', $settings['payment_bank_transfer_details'] ?? '') }}</textarea>
            </div>
          </div>
          <div class="rounded-xl border border-outline-variant/60 p-4 space-y-4">
            <div class="flex items-start gap-3">
              <input type="hidden" name="payment_pay_on_delivery_enabled" value="0" />
              <input type="checkbox" name="payment_pay_on_delivery_enabled" id="payment_pay_on_delivery_enabled" value="1" class="mt-1" @checked(old('payment_pay_on_delivery_enabled', $settings['payment_pay_on_delivery_enabled'] ?? '0') === '1') />
              <div class="min-w-0 flex-1">
                <label for="payment_pay_on_delivery_enabled" class="block text-sm font-semibold text-on-surface normal-case tracking-normal">{{ __('Pay on delivery') }}</label>
                <p class="mt-1 text-xs text-on-surface-variant normal-case tracking-normal font-normal">{{ __('Customer pays when the order is delivered.') }}</p>
              </div>
            </div>
            <div class="pb-field">
              <label for="payment_pay_on_delivery_note">{{ __('Instructions (optional)') }}</label>
              <textarea name="payment_pay_on_delivery_note" id="payment_pay_on_delivery_note" rows="3">{{ old('payment_pay_on_delivery_note', $settings['payment_pay_on_delivery_note'] ?? '') }}</textarea>
            </div>
          </div>
        </div>
      </x-admin.panel>

      <div class="flex flex-wrap justify-end gap-3">
        <x-admin.button variant="secondary" :href="route('admin.dashboard')">{{ __('Cancel') }}</x-admin.button>
        <x-admin.button type="submit">{{ __('Save settings') }}</x-admin.button>
      </div>
    </form>

    <x-admin.panel
      :title="__('Test outbound email (PHPMailer)')"
      :description="__('Sends a short message using MAIL_PHPMAILER_* and MAIL_FROM_* from .env (separate from the contact notification fields above).')"
    >
      <form action="{{ route('admin.settings.mail-test') }}" method="post" class="flex flex-col gap-3 sm:flex-row sm:items-end pb-admin-form pb-admin-form--wide !gap-3">
        @csrf
        <div class="pb-field min-w-0 flex-1">
          <label for="test_email">{{ __('Recipient email') }}</label>
          <input type="email" name="test_email" id="test_email" value="{{ old('test_email', auth()->user()->email ?? '') }}" required @class(['border-error' => $errors->has('test_email')])/>
          @error('test_email')
            <p class="mt-1 text-xs text-error normal-case tracking-normal font-normal">{{ $message }}</p>
          @enderror
        </div>
        <x-admin.button type="submit" variant="secondary">{{ __('Send test email') }}</x-admin.button>
      </form>
    </x-admin.panel>
  </x-admin.page-content>

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
</x-app-layout>
