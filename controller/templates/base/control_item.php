<?php 	
	//include common logic for all templates
	include("common.php");
?>

<div class="identifier-header">
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/go-home.png" /> <?php echo lang('Control_item') ?></h1>
</div>

<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value="<?php if(!empty($control)){ echo $control->get_id(); } else { echo '0'; }  ?>"/>
				<dl class="proplist-col">
					<dt>
						<label for="title">Tittel</label>
					</dt>
					<dd>
						<input type="text" name="title" id="title" value="" />
					</dd>
					<dt>
						<label for="required">Obligatorisk</label>
					</dt>
					<dd>
						<input type="checkbox" value="" />
					</dd>
					<dt>
						<label for="what_to_do">Hva skal utføres</label>
					</dt>
					<dd>
						<textarea id="what_to_do" rows="5" cols="60"></textarea>
					</dd>
					<dt>
						<label for="how_to_do">Utførelsesbeskrivelse</label>
					</dt>
					<dd>
						<textarea id="how_to_do" rows="5" cols="60"></textarea>
					</dd>
					<dt>
						<label for="control_group">Kontrollgruppe</label>
					</dt>
					<dd>
						<select id="control_group" name="control_group">
							<?php 
								foreach ($control_group_array as $control_group) {
									echo "<option value='" . $control_group->get_id() . "'>" . $control_group->get_group_name() . "</option>";
								}
							?>
						</select>
					</dd>
					<dt>
						<label for="control_area">Kontrolltype</label>
					</dt>
					<dd>
						<select id="control_area" name="control_area">
							<?php 
								foreach ($control_area_array as $control_area) {
									echo "<option value='" . $control_area->get_id() . "'>" . $control_area->get_title() . "</option>";
								}
							?>
						</select>
					</dd>				
				</dl>
				
				<div class="form-buttons">
					<?php
						echo '<input type="submit" name="save_control" value="' . lang('save') . '"/>';
					?>
				</div>
				
			</form>
						
		</div>
</div>
