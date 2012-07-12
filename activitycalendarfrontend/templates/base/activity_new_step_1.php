<?php ?>
 <script type="text/javascript">
function isOK()
{
	if(document.getElementById('organization_id').value == null || document.getElementById('organization_id').value == '')
	{
		alert("Du må velge om aktiviteten skal knyttes mot en eksisterende\norganisasjon, eller om det skal registreres en ny organisasjon!");
		return false;
	}
	else
	{
		return true;
	}
}
</script>

<div class="yui-content" style="width: 100%;">
	<h1><?php echo lang('new_activity') ?></h1>
	<form action="#" method="post">
		<dl class="proplist-col" style="width: 200%">
			<dt>
				<?php echo lang('org_helptext_step1')?><br/><br/>
			</dt>
			<dd>
				<select name="organization_id" id="organization_id">
					<option value="">Ingen organisasjon valgt</option>
					<option value="new_org">Ny organisasjon</option>
					<?php
					foreach($organizations as $organization)
					{
						echo "<option value=\"{$organization->get_id()}\">".$organization->get_name()."</option>";
					}
					?>
				</select>
				<br/><br/>
			</dd>
			<div class="form-buttons">
				<input type="submit" name="step_1" value="<?php echo lang('next') ?>" onclick="return isOK();"/>
			</div>
		</dl>
		
	</form>
</div>