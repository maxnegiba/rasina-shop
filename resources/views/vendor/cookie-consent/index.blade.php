@php
    $locale = $app->getLocale();
    $cookieKey = (string) config('cookie-consent.cookie_key', '__cookie_consent');
    $hasConsentCookie = request()->cookie($cookieKey) !== null;
@endphp

<style data-cookie-consent-critical>
.js-lcc-active{overflow:hidden}.lcc-backdrop{position:fixed;inset:0;z-index:10000;background:rgba(26,18,13,.56)}.lcc-modal{box-sizing:border-box;position:fixed;left:50%;z-index:10001;width:min(92vw,540px);max-height:90vh;overflow:auto;background:#fffff0;color:#2c1e16;border:1px solid rgba(207,181,59,.35);box-shadow:0 24px 80px rgba(26,18,13,.32);padding:28px}.lcc-modal--alert{top:auto;bottom:20px;transform:translateX(-50%);max-height:none}.lcc-modal--settings{top:50%;bottom:auto;transform:translate(-50%,-50%);z-index:10002}.lcc-modal__title{margin:0 0 12px;font-family:'Cormorant Garamond',Georgia,serif;font-size:1.7rem;line-height:1.15;letter-spacing:.02em}.lcc-text{margin:0 0 18px;font-family:Manrope,ui-sans-serif,system-ui,sans-serif;font-size:.9rem;line-height:1.65;color:rgba(44,30,22,.78)}.lcc-modal__section{padding:16px 0;border-top:1px solid rgba(44,30,22,.1)}.lcc-modal__section .lcc-text{margin:8px 0 0 28px}.lcc-label{display:flex;align-items:center;gap:10px;font-family:Manrope,ui-sans-serif,system-ui,sans-serif;font-weight:700}.lcc-label input{width:18px;height:18px;accent-color:#3b2818}.lcc-modal__actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:22px}.lcc-modal__actions-center{justify-content:center}.lcc-button{appearance:none;border:1px solid #2c1e16;background:#2c1e16;color:#fff;padding:10px 16px;font-family:Manrope,ui-sans-serif,system-ui,sans-serif;font-size:.82rem;font-weight:700;letter-spacing:.03em;cursor:pointer;transition:background-color .2s ease,border-color .2s ease,color .2s ease,transform .2s ease}.lcc-button:hover,.lcc-button:focus-visible{background:#cfb53b;border-color:#cfb53b;color:#1a120d;outline:none;transform:translateY(-1px)}.lcc-button--ghost{background:transparent;color:#2c1e16}.lcc-modal__close{position:absolute;right:12px;top:8px;border:0;background:transparent;color:#2c1e16;font-size:1.7rem;line-height:1;cursor:pointer}.lcc-modal a{color:#6e5a14;text-decoration:underline;text-underline-offset:2px}.lcc-u-text-center{text-align:center}.lcc-u-sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}@media(max-width:640px){.lcc-modal{width:calc(100vw - 24px);padding:22px 18px}.lcc-modal--alert{bottom:12px}.lcc-modal__actions{flex-direction:column}.lcc-button{width:100%}.lcc-modal__title{font-size:1.45rem}}
</style>

<div
    role="dialog"
    aria-labelledby="lcc-modal-alert-label"
    aria-describedby="lcc-modal-alert-desc"
    aria-modal="false"
    class="lcc-modal lcc-modal--alert js-lcc-modal js-lcc-modal-alert"
    style="display: {{ $hasConsentCookie ? 'none' : 'block' }}"
    data-cookie-key="{{ config('cookie-consent.cookie_key') }}"
    data-cookie-value-analytics="{{ config('cookie-consent.cookie_value_analytics') }}"
    data-cookie-value-marketing="{{ config('cookie-consent.cookie_value_marketing') }}"
    data-cookie-value-both="{{ config('cookie-consent.cookie_value_both') }}"
    data-cookie-value-none="{{ config('cookie-consent.cookie_value_none') }}"
    data-cookie-expiration-days="{{ config('cookie-consent.cookie_expiration_days') }}"
    data-gtm-event="{{ config('cookie-consent.gtm_event') }}"
    data-session-domain="{{ config('session.domain', '') }}"
    data-cookie-secure="{{ config('cookie-consent.cookie_secure', false) ? 'true' : 'false' }}"
>
    <div class="lcc-modal__content">
        <h2 id="lcc-modal-alert-label" class="lcc-modal__title">@lang('cookie-consent::texts.alert_title')</h2>
        <p id="lcc-modal-alert-desc" class="lcc-text">{!! trans('cookie-consent::texts.alert_text') !!}</p>
    </div>

    <div class="lcc-modal__actions">
        <button type="button" class="lcc-button js-lcc-accept">@lang('cookie-consent::texts.alert_accept')</button>
        <button type="button" class="lcc-button js-lcc-essentials">@lang('cookie-consent::texts.alert_essential_only')</button>
        <button type="button" class="lcc-button lcc-button--ghost js-lcc-settings-toggle">@lang('cookie-consent::texts.alert_settings')</button>
    </div>
</div>

<div
    role="dialog"
    aria-labelledby="lcc-modal-settings-label"
    aria-describedby="lcc-modal-settings-desc"
    aria-modal="true"
    class="lcc-modal lcc-modal--settings js-lcc-modal js-lcc-modal-settings"
    style="display: none"
>
    <button class="lcc-modal__close js-lcc-settings-toggle" type="button" aria-label="@lang('cookie-consent::texts.settings_close')">&times;</button>

    <div class="lcc-modal__content">
        <h2 id="lcc-modal-settings-label" class="lcc-modal__title">@lang('cookie-consent::texts.settings_title')</h2>
        <p id="lcc-modal-settings-desc" class="lcc-text">
            {!! trans('cookie-consent::texts.settings_text', ['policyUrl' => config("cookie-consent.policy_url_$locale")]) !!}
        </p>

        <div class="lcc-modal__section lcc-u-text-center">
            <button type="button" class="lcc-button js-lcc-accept">@lang('cookie-consent::texts.settings_accept_all')</button>
        </div>

        <div class="lcc-modal__section">
            <label for="lcc-checkbox-essential" class="lcc-label">
                <input type="checkbox" id="lcc-checkbox-essential" disabled checked>
                <span>@lang('cookie-consent::texts.setting_essential')</span>
            </label>
            <p class="lcc-text">@lang('cookie-consent::texts.setting_essential_text')</p>
        </div>

        <div class="lcc-modal__section">
            <label for="lcc-checkbox-analytics" class="lcc-label">
                <input type="checkbox" id="lcc-checkbox-analytics">
                <span>@lang('cookie-consent::texts.setting_analytics')</span>
            </label>
            <p class="lcc-text">@lang('cookie-consent::texts.setting_analytics_text')</p>
        </div>

        <div class="lcc-modal__section">
            <label for="lcc-checkbox-marketing" class="lcc-label">
                <input type="checkbox" id="lcc-checkbox-marketing">
                <span>@lang('cookie-consent::texts.setting_marketing')</span>
            </label>
            <p class="lcc-text">@lang('cookie-consent::texts.setting_marketing_text')</p>
        </div>
    </div>

    <div class="lcc-modal__actions lcc-modal__actions-center">
        <button type="button" class="lcc-button js-lcc-settings-save">@lang('cookie-consent::texts.settings_save')</button>
    </div>
</div>

<div class="lcc-backdrop js-lcc-backdrop" style="display: none"></div>
