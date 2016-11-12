{assign var="invalid" value=$form->IsInvalid($field)}
{if !$form->IsSuccess()}
	{if $invalid neq false}
		<div class="has-error">
			<div class="g-recaptcha" data-sitekey="{$config->captcha}"></div>
			<p class="help-block" id="help-block-{$field}">{$invalid|escape}</p>
		</div>
	{else}
		<div class="g-recaptcha" data-sitekey="{$config->captcha}"></div>
	{/if}
{/if}