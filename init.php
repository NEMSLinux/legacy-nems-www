<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
<title>NEMS Linux - Error: Not Initialized</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<script src="/cdn-cgi/apps/head/RaaquJrqbl8-_c9y07kyPLpnMm8.js"></script><script type="text/javascript">
//<![CDATA[
window.__cfRocketOptions = {byc:0,p:0,petok:"a95554d929ae933c89ce32e02e06c022fd9e2289-1515978314-1800"};
//]]>
</script>
<script type="text/javascript" src="https://ajax.cloudflare.com/cdn-cgi/scripts/9014afdb/cloudflare-static/rocket.min.js"></script>
<link rel="shortcut icon" href="favicon.ico">

<link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600&amp;subset=cyrillic,latin'>

<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/style.css">

<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/headers/header-default.css">
<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/footers/footer-v1.css">

<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/animate.css">
<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/line-icons/line-icons.css">
<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/font-awesome/css/font-awesome.min.css">

<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/pages/page_error4_404.css">

<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/theme-colors/default.css" id="style_color">
<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/theme-skins/dark.css">

<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/custom.css">
</head>
<body>

<div class="container content valign__middle">

<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="error-v4">

<div class="call-action-v1 call-action-v1-boxed margin-bottom-40 rounded-4x">

<div style="margin: 0 auto; width: 60%; padding-top: 40px;"><img src="/img/nems_logo.png" class="img-responsive" /></div>

<div class="call-action-v1-box">
<div class="call-action-v1-in">
<h3 class="color-light" style="font-weight: bold;">Your NEMS server is not yet initialized.</h3>
<?php
  if (ver('nems') < 1.4) {
    echo '<p class="color-light">SSH to your NEMS server and run:<br /><em>sudo nems-init</em></p>';
  } else {
    echo '<p class="color-light">SSH or <a href="https://192.168.123.113:9090/system/terminal">open a terminal session</a> to your NEMS server and run:<br /><em>sudo nems-init</em></p>';
  }
?>
</div>
<div class="call-action-v1-in inner-btn page-scroll">
<a href="https://docs.nemslinux.com/commands/nems-init" class="btn-u btn-u-lg btn-brd btn-brd-width-2 btn-brd-hover btn-u-light btn-u-block">DOCUMENTATION</a>
</div>
</div>
</div>

</div>
</div>
</div>
</div>


<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/jquery/jquery.min.js"></script>
<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/jquery/jquery-migrate.min.js"></script>
<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/back-to-top.js"></script>
<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/backstretch/jquery.backstretch.min.js"></script>

<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/js/custom.js"></script>

<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/js/app.js"></script>
<script type="text/rocketscript" data-rocketsrc="https://cdn.zecheriah.com/site-assets/1.9.6/assets/js/plugins/style-switcher.js"></script>
<script type="text/rocketscript">
		jQuery(document).ready(function() {
			App.init();
			StyleSwitcher.initStyleSwitcher();
		});
	</script>
<script type="text/rocketscript">
		$.backstretch([
			"/img/wallpaper/server_room_dark.jpg"
			])
	</script>
<!--[if lt IE 9]>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/respond.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/html5shiv.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/placeholder-IE-fixes.js"></script>
	<![endif]-->
</body>
</html>

