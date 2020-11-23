{{--@props([--}}
{{--'initialValue' => '',--}}
{{--])--}}
<div
    x-data="{ value: @entangle($attributes->wire('model')),
     isFocused() {
        return document.activeElement !== this.$refs.trix
        },
     setValue() {
          this.$refs.trix.editor.loadHTML(this.value);
        }
     }"
    x-init="
    setValue();
    $watch('value', () => isFocused() && setValue());
"
    {{--    @trix-blur="$dispatch('change', $event.target.value)"--}}
    x-on:trix-change="value = $event.target.value"
    class="rounded-md shadow-sm"
    {{ $attributes->whereDoesntStartWith('wire:model') }}
    wire:ignore
>
    <input id="x" type="hidden">
    <trix-editor input="x" x-ref="trix" class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"></trix-editor>
</div>

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@1.2.3/dist/trix.css">
@endpush

@push('scripts')
    <script src="https://unpkg.com/trix@1.2.3/dist/trix.js"></script>
@endpush

