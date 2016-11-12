{function name="buttons"}
	<div class="pageoverflow">
		<p class="pageinput">
			<input type="submit" value="Opslaan" name="{$actionid}submit">
			<input type="submit" value="Annuleren" name="{$actionid}cancel">
		</p>
	</div>
{/function}

{form_start}
{buttons}

{foreach $properties as $property}
	<div class="pageoverflow">
		<p class="pagetext">
			<label for="{$property@key}">{$property|htmlentities}</label>
		</p>
		<p class="pageinput">
			<input type="text" id="{$property@key}" name="{$actionid}{$property@key}" value="{$values[$property@key]|htmlentities}" size="30" maxlength="255" required/>
		</p>
	</div>
{/foreach}

{buttons}
{form_end}