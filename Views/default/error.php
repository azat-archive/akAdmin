<?=$this->content('header.php');?>
<div class="error">
	<?if ($error) {?>
	
	<?if (is_array($error) && $error && $error['title'] && $error['description']) { // verbose way of errors?>
	<h1><?=$error['title']?></h1>
	<p><?=$error['description']?></p>
	<?} else {?>
	<h1>Error occured</h1>
	<p><?=$error?></p>
	<?}?>
	
	<?} else {?>
	<h1>Error occured</h1>
	<p>Please contact with administrator!</p>
	
	<?}?>
	
	<a href="javascript: history.go(-1);">back</a>
</div>
<?=$this->content('footer.php');?>