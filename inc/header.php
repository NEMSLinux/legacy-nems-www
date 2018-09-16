<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
	<title>NEMS Linux</title>

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
<body id="body" data-spy="scroll" data-target=".one-page-header" class="demo-lightbox-gallery dark">

	<!--=== Header ===-->
	<nav class="one-page-header one-page-header-style-2 navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="menu-container page-scroll">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<a class="navbar-brand" href="/">
					<!--<span>N</span>EMS Linux-->
                                        <img src="/img/nems_logo.png" id="logo-header" alt="NEMS Linux" />
				</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<div class="menu-container">
					<ul class="nav navbar-nav">

						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
							  Configuration
							</a>
							<ul class="dropdown-menu">
							  <?php if (ver('nems') >= 1.3) echo '<li><a href="https://' . $self->host . '/config/">NEMS System Settings Tool</a></li>'; ?>
							  <li><a href="/nconf/" target="_blank">NEMS Configurator (NConf)</a></li>
							</ul>
						</li>

						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
							  Reporting
							</a>
							<ul class="dropdown-menu">
							  <?php
							    if (ver('nems') >= 1.4) {
							      echo '<li><a href="/nagios/" target="_blank">Nagios Core</a></li>';
                                                            } else {
							      echo '<li><a href="/nagios3/" target="_blank">Nagios Core</a></li>';
							    }
							  ?>
							  <li><a href="/nagvis/" target="_blank">NagVis</a></li>
							  <?php if (ver('nems') < 1.4) echo '<li><a href="/check_mk/" target="_blank">Check_MK Multisite</a></li>'; ?>
							  <?php if (ver('nems') >= 1.4) echo '<li><a href="/adagios/" target="_blank">Adagios</a></li>'; ?>
							  <?php if (ver('nems') >= 1.4) echo '<li><a href="/mobile/" target="_blank">NEMS Mobile UI</a></li>'; ?>
							  <?php if (ver('nems') >= 1.4) echo '<li><a href="/tv/" target="_blank">NEMS TV Dashboard</a></li>'; ?>
							</ul>
						</li>

						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
							  System
							</a>

							<ul class="dropdown-menu">
 							  <?php if (checkConfEnabled('monitorix')) { echo '<li><a href="/monitorix/">Monitorix</a></li>'; } ?>
							  <?php if (ver('nems') >= 1.4 && checkConfEnabled('cockpit')) echo '<li><a href="https://' . $self->host . ':9090" target="_blank">Cockpit</a></li>'; ?>
							  <?php if ((ver('platform')->num < 10) && checkConfEnabled('rpi-monitor')) { echo '<li><a href="http://' . $self->host . ':8888" target="_blank">RPi-Monitor</a></li>'; } ?>
							  <?php if (checkConfEnabled('webmin')) { echo '<li><a href="https://' . $self->host . ':10000" target="_blank">Webmin</a></li>'; } ?>
							  <?php if (ver('nems') >= 1.3) echo '<li><a href="https://' . $self->host . ':2812" target="_blank"><em>monit</em> Service Monitor</a></li>'; ?>
							  <?php if (ver('nems') >= 1.4 && file_exists('/var/log/nems/phoronix/index.php')) echo '<li><a href="/phoronix/" target="_blank">Server Benchmarks</a></li>'; ?>
							</ul>
						</li>

						<li class="dropdown">
							<a href="/backup/nems-migrator/">Migrator</a>
						</li>

						<li class="dropdown">
							<a href="https://cat5.tv/pi" target="_blank">Buy a Pi</a>
						</li>
						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
								Support Us
							</a>
							<ul class="dropdown-menu">
                						<li><a href="https://patreon.com/nems" target="_blank"><i class="fa fa-fw fa-user"></i> Patreon</a></li>
								<li><a href="https://teespring.com/stores/nems" target="_blank"><i class="fa fa-fw fa-shopping-cart"></i> NEMS Merch</a></li>
								<li><a href="http://www.amazon.com/?tag=cat5tv-20" target="_blank"><i class="fa fa-fw fa-amazon"></i> Shop on Amazon.com</a></li>
								<li><a href="http://www.amazon.ca/?tag=cattectv06-20" target="_blank"><i class="fa fa-fw fa-amazon"></i> Shop on Amazon.ca</a></li>
								<li><a href="http://www.amazon.co.uk/?tag=cattectv-21" target="_blank"><i class="fa fa-fw fa-amazon"></i> Shop on Amazon.co.uk</a></li>
               							<li><a href="https://donate.category5.tv/" target="_blank"><i class="fa fa-fw fa-credit-card"></i> Donate</a></li>
            						</ul>
						</li>

						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
								Get Help
							</a>
							<ul class="dropdown-menu">
						                <li><a href="https://docs.nemslinux.com/" target="_blank">NEMS Documentation</a></li>
								<li><a href="https://www.patreon.com/bePatron?c=1348071&rid=2163018" target="_blank">Priority Support</a></li>
						                <li><a href="https://forum.category5.tv/forum-8.html" target="_blank">Community Forum</a></li>
                						<li><a href="https://nemslinux.com/" target="_blank">Official NEMS Web Site</a></li>
					                </ul>
						</li>

					</ul>
				</div>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container -->
	</nav>
	<!--=== End Header ===-->

