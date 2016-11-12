{assign var="invalid" value=$form->IsInvalid($field)}
{if $invalid neq false}
	<div class="form-group has-error">
{else}
	<div class="form-group"{if $config->required eq true} data-toggle="tooltip" title="Dit veld is vereist" data-trigger="focus"{/if}>
{/if}
		<label class="control-label">{$config->label|escape}</label>
		<div class="form-control">
			{foreach $form->GetSelectOptions($field) as $opt}
				<div class="checkbox-inline">
					<label class="control-label">
						<input type="checkbox"{if $form->IsSuccess()} disabled{/if} value="{$opt->Value()|escape}" {$opt->Data()} {if $opt->Value() eq $form->GetValue($field)}checked{/if} name="{$actionid}{$field}">
						{$opt->Label()|escape}
					</label>
				</div>
			{/foreach}
		</div>
		{if $invalid neq false}
			<p class="help-block" id="help-block-{$field}">{$invalid|escape}</p>
		{/if}
	</div>