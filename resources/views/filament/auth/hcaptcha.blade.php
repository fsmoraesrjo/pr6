@php
    $sitekey = config('services.hcaptcha.sitekey');
@endphp

@if($sitekey)
<div wire:ignore class="fi-fo-field-wrp" style="display: flex; justify-content: center; margin: .5rem 0 .25rem;">
    <div class="h-captcha"
         data-sitekey="{{ $sitekey }}"
         data-callback="onHcaptchaSuccess"
         data-expired-callback="onHcaptchaExpired"></div>
</div>
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
<script>
function onHcaptchaSuccess(token) {
    const input = document.querySelector('[name="data.hcaptcha_token"], [name="hcaptcha_token"]');
    if (input) {
        input.value = token;
        input.dispatchEvent(new Event('input', {bubbles: true}));
        input.dispatchEvent(new Event('change', {bubbles: true}));
    }
    window.Livewire?.dispatch('refresh-form');
}
function onHcaptchaExpired() {
    const input = document.querySelector('[name="data.hcaptcha_token"], [name="hcaptcha_token"]');
    if (input) input.value = '';
}
</script>
<input type="hidden" wire:model="data.hcaptcha_token">
@endif
