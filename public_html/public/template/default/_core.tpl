<!DOCTYPE HTML>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="description" content="<?=$m->description?>" />
	<meta name="keywords" content="<?=$m->keywords?>" />
	<meta name="author" content="<?=$m->author?>">

	<title><?=$m->title?></title>

	<link rel="stylesheet" type="text/css" href="/public/template/default/css/base.css" />

	<link rel="stylesheet" type="text/css" href="/public/template/default/css/grid.css" />

	<link rel="stylesheet" type="text/css" href="/public/template/default/css/style.css" />
	<link rel="stylesheet" type="text/css" href="/public/template/default/css/form.css" />
	<link rel="stylesheet" type="text/css" href="/public/template/default/css/button.css" />

	<?/*<link rel="shortcut icon" href="images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">*/?>

	<? include $drakoon->_head(); ?>
</head>

<body>
	<?=$drakoon->ScrollTo()?>

	<div id="wrapper">
		<div id="headerContainer">
			<div id="header">
				<h1>header</h1>
			</div><!-- #header -->
		</div><!-- #headerContainer -->

		<div id="contentContainer">
			<div id="notify-wrapper">
				<?=$drakoon->_snippet( 'info_messages' )?>
			</div><!-- #notify-wrapper -->

			<div id="content">

				<? include $drakoon->_view(); ?>
			</div><!-- #content -->
		</div><!-- #contentContainer -->

		<div id="footerContainer">
			<div id="footer">
				<div id="copyright">
					&copy; 2013-<?=date('Y')?> <?=$drakoon->setDomain?>&nbsp;|&nbsp;Version: <?=$drakoon->setSiteVersion?>&nbsp;|&nbsp;<?=$drakoon->LoadTimerEnd()?>;<?=DB::queryCount()?>
				</div>
			</div><!-- #footer -->
		</div><!-- #footerContainer -->
	</div><!-- #wrapper -->
</body>
</html>