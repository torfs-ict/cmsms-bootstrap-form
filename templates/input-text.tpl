{assign var="invalid" value=$form->IsInvalid($field)}
{if $invalid neq false}
	<div class="form-group has-error">
{else}
	<div class="form-group"{if $config->required eq true} data-toggle="tooltip" title="Dit veld is vereist" data-trigger="focus"{/if}>
{/if}
		<label for="{$field}" class="control-label">{$config->label|escape}</label>
		<input {if $form->IsSuccess()}disabled{/if} type="{$config->input}" {if $invalid neq false}aria-describedby="help-block-{$field}" aria-invalid="true"{/if} {block name="extra"}{/block} class="form-control {block name="classes"}{/block}" name="{$actionid}{$field}{if $config->input eq "file"}[]{/if}" id="{$field}" value="{$form->GetValue($field)|escape}">
		{if $invalid neq false}
			<p class="help-block" id="help-block-{$field}">{$invalid|escape}</p>
		{/if}
	</div>