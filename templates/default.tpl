{assign var="invalid" value=$form->isInvalid()}
{form_start module="BootstrapForm" action="default" form=$form->GetName() id=$form->GetName() mid=$actionid}
	{if $form->IsSubmit()}
		{if $invalid !== false}
			<p class="alert alert-danger">{$invalid|escape}</p>
		{else}
			<p class="alert alert-success">Uw bericht werd succesvol verzonden. Wij nemen zo snel mogelijk contact met u op.</p>
		{/if}
	{/if}
	<div class="row">
	{foreach $form->GetFields() as $field => $config}
		{if !$config->visible}{continue}{/if}
		{if $config->row}</div><div class="row {" "|implode:$config->classesOnly}">{/if}
		<div class="{" "|implode:$config->classes}"{if $config->autoSubmit} data-auto-submit="1"{/if}>
			{include file="module_file_tpl:BootstrapForm;input-`$config->input`.tpl" form=$form field=$field config=$config}
		</div>
	{/foreach}
	</div>
{form_end}