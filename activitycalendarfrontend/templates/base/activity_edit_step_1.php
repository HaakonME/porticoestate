<?php ?>
<script type="text/javascript">
function isOK()
{
	if(document.getElementById('activity_id').value == null || document.getElementById('activity_id').value == '')
	{
		alert("Du må velge en aktivitet som skal endres!");
		return false;
	}
	else
	{
		return true;
	}
}
</script>
<div class="yui-content" style="width: 100%;">
	<h1><?php echo lang('edit_activity');?></h1>
	<form action="#" method="post">
		<dl class="proplist-col" style="width: 200%">
			<dt>
				<?php echo lang('activity_edit_helptext');?><br/><br/>
			</dt>
			<dd>
			<?php if($message){?>
			<?php echo $message;?>
			<?php }else{?>
				<select name="activity_id" id="activity_id">
					<option value="">Ingen aktivitet valgt</option>
					<?php
					foreach($activities as $activity)
					{
						echo "<option value=\"{$activity->get_id()}\">".$activity->get_title()."</option>";
					}
					?>
				</select>
				<br/><br/>
			<?php }?>
			</dd>
			<?php if(!$message){?>
			<div class="form-buttons">
				<input type="submit" name="step_1" value="<?php echo lang('send_change_request') ?>" onclick="return isOK();"/>
			</div>
			<?php }?>
		</dl>
		
	</form>
</div>