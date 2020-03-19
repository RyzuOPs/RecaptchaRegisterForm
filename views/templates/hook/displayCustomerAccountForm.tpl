<div class="form-group row">
    <div class="col-md-12">
        <div class="g-recaptcha" 
            data-sitekey="{$RX_RECAPTCHAREGISTERFORM_SITE_KEY}"
            data-size="{$RX_RECAPTCHAREGISTERFORM_SIZE}"
            data-theme="{$RX_RECAPTCHAREGISTERFORM_THEME}"
            data-tabindex="{$RX_RECAPTCHAREGISTERFORM_TABINDEX}">
        </div>
        <span class="form-control-comment">
         {l s='Please check reCaptcha before submit.' mod='rx_recaptcharegisterform'}
        </span>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js{if $RX_RECAPTCHAREGISTERFORM_FORCE_LANGUAGE}?hl={$RX_RECAPTCHAREGISTERFORM_FORCE_LANGUAGE}{/if}" async defer></script>