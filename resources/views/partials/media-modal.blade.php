<div id="media-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/70 p-4">
  <div id="media-modal-panel" class="mx-auto flex h-full max-h-[90vh] w-full max-w-[min(72rem,92vw)] flex-col overflow-hidden rounded-lg bg-white shadow-2xl">
    <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-3">
      <h3 class="text-sm font-semibold text-gray-900">Select Media</h3>
      <button type="button" class="js-media-modal-close text-gray-500 hover:text-gray-900" aria-label="Close">✕</button>
    </div>
    <div class="shrink-0 border-b border-gray-200 p-4">
      <input id="media-search" type="search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Search media..."/>
    </div>
    <div class="shrink-0 border-b border-gray-200 bg-gray-50 p-4">
      <form id="media-upload-form" method="post" action="{{ $mediaUploadUrl ?? route('admin.media.upload') }}" enctype="multipart/form-data" class="flex flex-col gap-2 sm:flex-row sm:items-center">
        @csrf
        <input id="media-upload-input" type="file" name="files[]" accept="image/jpeg,image/jpg,image/png,image/webp" class="block w-full text-sm text-gray-700" multiple />
        <button type="submit" id="media-upload-submit" class="whitespace-nowrap rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">Upload</button>
      </form>
      <div id="media-upload-status" class="mt-2 hidden" role="status" aria-live="polite">
        <div class="flex items-center gap-2 text-sm text-indigo-700">
          <span class="inline-block h-4 w-4 shrink-0 animate-spin rounded-full border-2 border-indigo-200 border-t-indigo-600" aria-hidden="true"></span>
          <span class="media-upload-status-text">{{ __('Uploading…') }}</span>
        </div>
      </div>
    </div>
    <div id="media-grid" class="min-h-0 flex-1 overflow-auto p-4 grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4"></div>
    <div class="shrink-0 border-t border-gray-200 px-4 py-3 flex flex-wrap items-center justify-end gap-2">
      <button type="button" id="media-modal-insert" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500" disabled>
        {{ __('Use selected image') }}
      </button>
      <button type="button" class="js-media-modal-close rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Close</button>
    </div>
  </div>
</div>
