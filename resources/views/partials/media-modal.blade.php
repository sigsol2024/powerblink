<div id="media-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/70 p-4">
  <div id="media-modal-panel" class="mx-auto flex h-full max-h-[90vh] w-full max-w-[min(72rem,92vw)] flex-col overflow-hidden rounded-3xl bg-surface shadow-2xl">
    <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/60 px-4 py-3">
      <h3 class="text-sm font-semibold text-on-surface">{{ __('Select media') }}</h3>
      <button type="button" class="js-media-modal-close text-on-surface-variant hover:text-on-surface min-h-11 min-w-11 inline-flex items-center justify-center" aria-label="{{ __('Close') }}">
        <x-icon name="close" class="w-5 h-5" />
      </button>
    </div>
    <div class="shrink-0 border-b border-outline-variant/60 p-4">
      <input id="media-search" type="search" class="w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-sm text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-secondary/20" placeholder="{{ __('Search media...') }}"/>
    </div>
    <div class="shrink-0 border-b border-outline-variant/60 bg-surface-container-low p-4">
      <form id="media-upload-form" method="post" action="{{ $mediaUploadUrl ?? route('admin.media.upload') }}" enctype="multipart/form-data" class="flex flex-col gap-2 sm:flex-row sm:items-center">
        @csrf
        <input id="media-upload-input" type="file" name="files[]" accept="image/jpeg,image/jpg,image/png,image/webp" class="block w-full text-sm text-on-surface-variant" multiple />
        <button type="submit" id="media-upload-submit" class="whitespace-nowrap rounded-xl bg-secondary px-3 py-2 text-sm font-semibold text-on-secondary hover:brightness-110">{{ __('Upload') }}</button>
      </form>
      <div id="media-upload-status" class="mt-2 hidden" role="status" aria-live="polite">
        <div class="flex items-center gap-2 text-sm text-secondary">
          <span class="inline-block h-4 w-4 shrink-0 animate-spin rounded-full border-2 border-secondary/20 border-t-secondary" aria-hidden="true"></span>
          <span class="media-upload-status-text">{{ __('Uploading…') }}</span>
        </div>
      </div>
    </div>
    <div id="media-grid" class="min-h-0 flex-1 overflow-auto p-4 grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4"></div>
    <div class="shrink-0 border-t border-outline-variant/60 px-4 py-3 flex flex-wrap items-center justify-end gap-2">
      <button type="button" id="media-modal-insert" class="inline-flex items-center rounded-xl bg-secondary px-4 py-2 text-sm font-semibold text-on-secondary shadow-sm hover:brightness-110 disabled:cursor-not-allowed disabled:bg-surface-container-high disabled:text-on-surface-variant" disabled>
        {{ __('Use selected image') }}
      </button>
      <button type="button" class="js-media-modal-close rounded-xl border border-outline-variant/60 bg-surface px-4 py-2 text-sm font-semibold text-on-surface-variant hover:bg-surface-container-low">{{ __('Close') }}</button>
    </div>
  </div>
</div>
