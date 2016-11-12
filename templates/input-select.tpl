{assign var="invalid" value=$form->IsInvalid($field)}
{if $invalid neq false}
	<div class="form-group has-error">
{else}
	<div class="form-group"{if $config->required eq true} data-toggle="tooltip" title="Dit veld is vereist" data-trigger="focus"{/if}>
{/if}
		<label for="{$field}" class="control-label">{$config->label|escape}</label>
		<select {if $form->IsSuccess()}disabled{/if} {block name="extra"}{/block} class="form-control {block name="classes"}{/block}" name="{$actionid}{$field}" id="{$field}">
			{foreach $form->GetSelectOptions($field) as $opt}
				<option value="{$opt->Value()|escape}" {$opt->Data()} {if $opt->Value() eq $form->GetValue($field)}selected{/if}>{$opt->Label()|escape}</option>
			{/foreach}
		</select>
		{if $invalid neq false}
			<p class="help-block" id="help-block-{$field}">{$invalid|escape}</p>
		{/if}
</div>