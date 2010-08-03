<?=$this->content('header.php');?>
<?=(isset($fullBetaTree) && $fullBetaTree ? $fullBetaTree : null)?>

<form name="grants" method="post">
	<ul class="form">
		<li><input type="submit" value="Submit" /></li> 
	</ul>
</form>
<?=$this->content('footer.php');?>