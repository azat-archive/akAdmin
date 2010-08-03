<?=$this->content('header.php');?>
<?=$this->content('sections/tree.php');?>
<form name="settings" method="post">
	<ul class="form">
		<?foreach ($fields as $fieldName => &$field) {?>
		<li>
			<input type="checkbox" name="<?=$fieldName?>[hidden]" value="<?=$fieldName?>" title="Hide?"<?=($field['hidden'] ? ' checked' : null)?> />
			<label>
				<strong><?=$fieldName?></strong>
				<input type="text" name="<?=$fieldName?>[value]" value="<?=$field['value']?>" />
			</label>
			<input type="text" name="<?=$fieldName?>[sequence]" value="<?=$field['sequence']?>" style="width: 20px;" title="Sequence" />
			<select name="<?=$fieldName?>[type]" title="Type of field">
				<?
				if (isset($DBTypes[$field['DBType']]) && is_array($DBTypes[$field['DBType']])) {
					foreach ($DBTypes[$field['DBType']] as $type) {
				?>
				<option value="<?=$type?>"<?=($field['type'] == $type ? ' selected' : null)?>><?=$type?></option>
				<?
					}
				}
				?>
			</select>
		</li>
		<?}?>
		
		<li><input type="submit" value="Submit" /></li>
	</ul>
</form>
<?=$this->content('footer.php');?>