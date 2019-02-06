<?php
  if (isset($functions_loaded) && $functions_loaded == 1) {
  $ip = trim(shell_exec('/usr/local/bin/nems-info ip'));
?><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
	<title>NEMS Linux: Not Initialized</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Robbie Ferguson - https://baldnerd.com/">
        <meta name="robots" content="noindex">

	<!-- Favicon -->
        <link rel="shortcut icon" href="/favicon.ico">

	<!-- Web Fonts -->
	<link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600&amp;subset=cyrillic,latin'>

	<!-- CSS Global Compulsory -->
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/css/one.style.css">

	<!-- CSS Footer -->
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/css/footers/footer-v7.css">

	<!-- CSS Implementing Plugins -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.1/animate.min.css">
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/line-icons/line-icons.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.1/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/pace/pace-flash.css">
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/sky-forms-pro/skyforms/css/sky-forms.css">
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/sky-forms-pro/skyforms/custom/custom-sky-forms.css">
	<!--[if lt IE 9]><link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/sky-forms-pro/skyforms/css/sky-forms-ie8.css"><![endif]-->
        <link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/pages/page_error4_404.css">
	
	<!-- CSS Theme -->
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/assets/css/headers/header-v6.css">
	<link rel="stylesheet" href="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/css/theme-skins/one.dark.css">

        <!-- Fullcalendar -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.8.0/fullcalendar.min.css">

	<!-- CSS Customization -->
	<link rel="stylesheet" href="/css/custom.css">

        <!-- JS Global Compulsory -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>

<script language="javascript" type="text/javascript">
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
  }
</script>
</head>

<!--
The #page-top ID is part of the scrolling feature.
The data-spy and data-target are part of the built-in Bootstrap scrollspy function.
-->
<body>

<script>
var top = ($('.navbar').offset() || { "top": NaN }).top;
</script>
<div class="navbar" style="display:none;"></div>

	<div class="container content valign__middle">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="error-v4">

				<div class="call-action-v1 call-action-v1-boxed margin-bottom-40 rounded-4x">

					<div style="margin: 0 auto; width: 60%; padding-top: 40px;"><img src="/img/nems_logo.png" class="img-responsive" />
						<span style="color: #aaa;font-size:1.2em;">For <?php $platform = ver('platform'); echo $platform->name; ?></span>
					</div>

					<div class="call-action-v1-box">
						<div class="call-action-v1-in">
							<h3 class="color-light" style="font-weight: bold;">Your NEMS server is not yet initialized.</h3>
							<?php
							  if (ver('nems') < 1.4) {
							    echo '<p class="color-light">SSH to your NEMS server (' . $ip . ') and run:<br /><em>sudo nems-init</em></p>';
							  } else {
							    echo '<p class="color-light">SSH to your NEMS server (' . $ip . ') or <a href="https://' .  $self->host . ':9090/system/terminal">open a browser-based terminal session</a> and run:<br /><em>sudo nems-init</em></p>';
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


	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/jquery/jquery-migrate.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!-- JS Implementing Plugins -->
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/smoothScroll.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/jquery.easing.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/pace/pace.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/jquery.parallax.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/owl-carousel/owl.carousel.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/sky-forms-pro/skyforms/js/jquery.form.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/sky-forms-pro/skyforms/js/jquery.validate.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/cube-portfolio/cubeportfolio/js/jquery.cubeportfolio.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/modernizr.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/login-signup-modal-window/js/main.js"></script> <!-- Gem jQuery -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-backstretch/2.0.4/jquery.backstretch.min.js"></script>

	<!-- JS Page Level-->
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/js/one.app.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/js/forms/login.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/js/forms/contact.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/js/plugins/pace-loader.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/js/plugins/owl-carousel.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/js/plugins/cube-portfolio/cube-portfolio-lightbox.js"></script>
	<script>
		jQuery(document).ready(function() {
			App.init();
			LoginForm.initLoginForm();
			ContactForm.initContactForm();
			OwlCarousel.initOwlCarousel();
	</script>
        <?php
          $backgroundElem = 'body';
          require_once('/var/www/html/inc/wallpaper.php');
        ?>

	<!--[if lt IE 9]>
		<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/respond.js"></script>
		<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/html5shiv.js"></script>
		<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/js/plugins/placeholder-IE-fixes.js"></script>
		<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/sky-forms-pro/skyforms/js/sky-forms-ie8.js"></script>
	<![endif]-->

	<!--[if lt IE 10]>
		<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/sky-forms-pro/skyforms/js/jquery.placeholder.min.js"></script>
	<![endif]-->
</body>
</html>
<?php
  } else {
    // Someone tried to open init.php directly, which is not included so missing functions
    header('location:/');
    exit();
  }
?>
