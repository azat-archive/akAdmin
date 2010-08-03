<?=$this->content('header.php');?>
<?=$this->content('sections/tree.php');?>
<form method="post">
	<ul class="form">
		<?foreach ($item as $key => &$field) {?>
			<li><?=$field?></li>
		<?}?>
		
		<li><input type="submit" value="Submit" /></li>
	</ul>
</form>
<?=$this->content('footer.php');?>