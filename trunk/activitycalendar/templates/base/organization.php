<?php
	//include common logic for all templates
	include("common.php");
?>
<div class="identifier-header">
	<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('organization') ?></h1>
</div>
<div class="yui-content">
	<div id="details">
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($organization->get_id()){ echo $organization->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<dt><label for="orgname">Organisasjonsnavn</label></dt>
				<dd><?php echo $organization->get_name();?></dd>
				<dt><label for="orgno">Organisasjonsnummer</label></dt>
				<dd>
					<?php 
					if($editable){?>
						<input type="text" name="orgno" value="<?php echo $organization->get_organization_number();?>"/><br/>
					<?php 
					}else{?>
						<?php echo $organization->get_organization_number();?>
					<?php }?>
				</dd>
				<dt><label for="district">Bydel</label></dt>
				<dd>
				<?php if($editable){?>
				<?php $curr_district = $organization->get_district();?>
					<select name="org_district">
						<option value="0">Ingen bydel valgt</option>
					<?php 
						foreach($districts as $d){?>
							<option value="<?php echo $d['part_of_town_id']?>" <?php echo ($curr_district == $d['part_of_town_id'])? 'selected' : '' ?>><?php echo $d['name']?></option>
						<?php }?>
					</select>
				<?php }else{?>
					<?php echo activitycalendar_soactivity::get_instance()->get_district_from_id($organization->get_district());?>
				<?php }?>
				</dd>
				<dt><label for="homepage">Hjemmeside</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="homepage" value="<?php echo $organization->get_homepage();?>"/><br/>
				<?php }else{?>
					<?php echo $organization->get_homepage();?>
				<?php }?>
				</dd>
				<dt><label for="email">E-post</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="email" value="<?php echo $organization->get_email();?>"/>
				<?php }else{?>
					<?php echo $organization->get_email();?>
				<?php }?>
				</dd>
				<dt><label for="phone">Telefon</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="phone" value="<?php echo $organization->get_phone();?>"/>
				<?php }else{?>
					<?php echo $organization->get_phone();?>
				<?php }?>
				</dd>
				<dt><label for="street">Adresse</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="address" value="<?php echo $organization->get_address();?>"/>
				<?php }else{?>
					<?php echo $organization->get_address();?>
				<?php }?>
				</dd>
				<dt><label for="org_description">Beskrivelse</label></dt>
				<dd>
				<?php if($editable){?>
					<textarea rows="10" cols="100" name="org_description"><?php echo $organization->get_description();?></textarea>
				<?php }else{?>
					<?php echo $organization->get_description();?>
				<?php }?>
				</dd>
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_organization" value="' . lang('save') . '"/>';
						echo '<input type="submit" name="store_organization" value="' . lang('store') . '"/>';
					}
				?>
			</div>
		</form>
	</div>
</div>
				