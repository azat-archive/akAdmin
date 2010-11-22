<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=<?=charset?>" />
		<title>Admin System<?=(isset($headTitle) && $headTitle ? ' | ' . $headTitle : (isset($title) && $title ? ' | ' . $title : null))?></title>

		<link rel="shortcut icon" href="/images/favicon.ico" /> 

		<link rel="stylesheet" type="text/css" href="<?=dirCss?>main.css" />
		<link rel="stylesheet" type="text/css" href="<?=dirCss?>jquery.alerts.css" />
		<link rel="stylesheet" type="text/css" href="<?=dirCss?>jquery.autocomplete.css" />
		<link rel="stylesheet" type="text/css" href="<?=dirCss?>jquery.fancybox.css" />
		<link rel="stylesheet" type="text/css" href="<?=dirCss?>jquery.paginator.css" />
		<link rel="stylesheet" type="text/css" href="<?=dirCss?>jquery.wysiwyg.css" />

		<script type="text/javascript" src="<?=dirJs?>jquery.js"></script>
		<script type="text/javascript" src="<?=dirJs?>jquery.autocomplete.js"></script>
		<script type="text/javascript" src="<?=dirJs?>jquery.alerts.js"></script>
		<script type="text/javascript" src="<?=dirJs?>jquery.paginator.js"></script>
		<script type="text/javascript" src="<?=dirJs?>jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?=dirJs?>jquery.wysiwyg.js"></script>

		<script type="text/javascript" src="<?=dirJs?>functions.js"></script>
		<script type="text/javascript" src="<?=dirJs?>main.js"></script>
	</head>
	<body>
		<div id="mainWrapper">
			<?if (isset($error) && $error && is_scalar($error)) {?>
			<div id="mainError"><?=$error?><span class="close">x</span></div>
			<?}?>

			<?if (isset($currentSection) && $currentSection['tableName']) {?>
			<div id="searchPanel">
				<form name="search" method="get" action="/section/details/<?=$currentSection['id']?>/search/">
					<input type="text" name="q" value="<?=(isset($searchQuery) ? $searchQuery : null)?>" />
				</form>
			</div>
			<?}?>

			<?if (isset($user) && $user) {?>
			<div class="userInfo">
				Hi, <a href="/user/details/<?=$user['id']?>"><?=$user['login']?></a>
				<?if (isset($user['lastTime']) && $user['lastTime']) {?>, last visit <?=dh('d.m', $user['lastTime'])?><?}?>
				<a class="actionConfirm" href="/user/logout">logout</a>
			</div>
			<ul class="menu">
				<li><a href="/users">Users</a></li>
				<?if (isset($user) && $user['isAdmin']) {?><li><a href="/user/add">Add user</a></li><?}?>
				<li><a href="/sections">Sections</a></li>
				<li><a href="/section/add">Add section</a></li>
				
				<?if (isset($currentSection) && $currentSection['tableName']) {?><a href="/section/items/<?=$currentSection['id']?>/add">Add item</a><?}?>
			</ul>
			<?}?>

			<?if (isset($title) && $title) {?><h1><?=$title?></h1><?}?>

