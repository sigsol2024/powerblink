@php
  $sizeOptions = $sizeOptions ?? collect();
  $colorOptions = $colorOptions ?? collect();
  $selectedDimensions = old('variant_dimensions', $selectedDimensions ?? []);
  $selectedSizeIds = array_map('intval', old('dimension_options.size', $selectedSizeIds ?? []));
  $selectedColorIds = array_map('intval', old('dimension_options.color', $selectedColorIds ?? []));
  $variantMatrix = old('variant_matrix', $variantMatrix ?? []);
@endphp

<section
  class="rounded-lg border border-wp-border p-4 space-y-4 bg-white"
  x-data="{
    dimensions: @js($selectedDimensions),
    sizeIds: @js($selectedSizeIds),
    colorIds: @js($selectedColorIds),
    matrix: @js($variantMatrix),
    sizeOptions: @js($sizeOptions->map(fn ($o) => ['id' => $o->id, 'value' => $o->value])->values()->all()),
    colorOptions: @js($colorOptions->map(fn ($o) => ['id' => $o->id, 'value' => $o->value])->values()->all()),
    hasDim(d) { return this.dimensions.includes(d); },
    toggleDim(d) {
      if (this.hasDim(d)) {
        this.dimensions = this.dimensions.filter((x) => x !== d);
        if (d === 'size') this.sizeIds = [];
        if (d === 'color') this.colorIds = [];
      } else {
        this.dimensions = [...this.dimensions, d];
      }
      this.pruneMatrix();
    },
    pruneMatrix() {
      const valid = new Set(this.rows().map((r) => r.key));
      Object.keys(this.matrix).forEach((k) => { if (!valid.has(k)) delete this.matrix[k]; });
    },
    idSelected(ids, id) {
      const n = Number(id);
      return (ids || []).some((x) => Number(x) === n);
    },
    rows() {
      const out = [];
      const sizes = this.hasDim('size') ? this.sizeOptions.filter((o) => this.idSelected(this.sizeIds, o.id)) : [{ id: null, value: '—' }];
      const colors = this.hasDim('color') ? this.colorOptions.filter((o) => this.idSelected(this.colorIds, o.id)) : [{ id: null, value: '—' }];
      if (!this.hasDim('size') && !this.hasDim('color')) return out;
      if (this.hasDim('size') && this.sizeIds.length === 0) return out;
      if (this.hasDim('color') && this.colorIds.length === 0) return out;
      sizes.forEach((s) => {
        colors.forEach((c) => {
          const key = `${s.id ?? ''}_${c.id ?? ''}`;
          out.push({ key, sizeId: s.id, colorId: c.id, label: [s.value, c.value].filter((x) => x && x !== '—').join(' / ') });
        });
      });
      return out;
    },
    stockFor(key) { return this.matrix[key]?.stock ?? 0; },
    setStock(key, val) {
      if (!this.matrix[key]) this.matrix[key] = {};
      this.matrix[key].stock = parseInt(val, 10) || 0;
    },
  }"
>
  <h3 class="text-base font-semibold text-wp-text">{{ __('Variant dimensions') }}</h3>
  <p class="text-xs text-wp-text-muted">{{ __('Select which dimensions apply to this product. Options are managed under Admin → Variants.') }}</p>

  <div class="flex flex-wrap gap-4">
    <label class="flex items-center gap-2 text-sm">
      <input type="checkbox" name="variant_dimensions[]" value="size" @checked(in_array('size', $selectedDimensions, true)) @change="toggleDim('size')" class="rounded border-wp-border" />
      <span>{{ __('Size') }}</span>
    </label>
    <label class="flex items-center gap-2 text-sm">
      <input type="checkbox" name="variant_dimensions[]" value="color" @checked(in_array('color', $selectedDimensions, true)) @change="toggleDim('color')" class="rounded border-wp-border" />
      <span>{{ __('Color') }}</span>
    </label>
  </div>

  <div x-show="hasDim('size')" x-cloak class="space-y-2">
    <label class="block text-sm font-medium text-wp-text">{{ __('Sizes') }}</label>
    <select name="dimension_options[size][]" multiple class="mt-1 block w-full rounded-md border-wp-border text-sm min-h-[8rem]" x-model="sizeIds" @change="pruneMatrix()">
      @foreach ($sizeOptions as $opt)
        <option value="{{ $opt->id }}" @selected(in_array((int) $opt->id, $selectedSizeIds, true))>{{ $opt->value }}</option>
      @endforeach
    </select>
    <p class="text-xs text-wp-text-muted">{{ __('Hold Ctrl/Cmd to select multiple sizes.') }}</p>
  </div>

  <div x-show="hasDim('color')" x-cloak class="space-y-2">
    <label class="block text-sm font-medium text-wp-text">{{ __('Colors') }}</label>
    <select name="dimension_options[color][]" multiple class="mt-1 block w-full rounded-md border-wp-border text-sm min-h-[8rem]" x-model="colorIds" @change="pruneMatrix()">
      @foreach ($colorOptions as $opt)
        <option value="{{ $opt->id }}" @selected(in_array((int) $opt->id, $selectedColorIds, true))>{{ $opt->value }}</option>
      @endforeach
    </select>
    <p class="text-xs text-wp-text-muted">{{ __('Hold Ctrl/Cmd to select multiple colors.') }}</p>
  </div>

  <div x-show="rows().length > 0" x-cloak>
    <h4 class="text-sm font-medium text-wp-text mb-2">{{ __('Variant stock matrix') }}</h4>
    <div class="overflow-x-auto border border-wp-border rounded">
      <table class="w-full text-sm text-left">
        <thead class="bg-wp-bg">
          <tr>
            <th class="px-3 py-2 text-xs uppercase text-wp-text-muted">{{ __('Combination') }}</th>
            <th class="px-3 py-2 text-xs uppercase text-wp-text-muted w-28">{{ __('Stock') }}</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="row in rows()" :key="row.key">
            <tr class="border-t border-wp-border">
              <td class="px-3 py-2 text-wp-text" x-text="row.label"></td>
              <td class="px-3 py-2">
                <input type="hidden" :name="'variant_matrix[' + row.key + '][stock]'" :value="stockFor(row.key)" />
                <input type="number" min="0" class="w-full text-sm border border-wp-border rounded px-2 py-1"
                  :value="stockFor(row.key)"
                  @input="setStock(row.key, $event.target.value); $el.previousElementSibling.value = stockFor(row.key)" />
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <p x-show="dimensions.length === 0" class="text-xs text-wp-text-muted">{{ __('No variant dimensions selected — product-level stock field below applies.') }}</p>
</section>
