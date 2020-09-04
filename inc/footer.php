
<?php /*	<section id="graphs" class="about-section">

		<div class="parallax-counter-v4 parallaxBg1" id="facts">
			<div class="container content-sm">
				<div class="row">
					<div class="col-md-3 col-xs-6 md-margin-bottom-50">
						<i class="fa fa-bar-chart"></i>
						<span class="counter"><?php $load = sys_getloadavg(); echo $load[1]; ?></span>
						<h4>5 Minute Load Average</h4>
					</div>
					<div class="col-md-3 col-xs-6 md-margin-bottom-50">
						<i class="fa fa-line-chart"></i>
						<span class="counter"><?php $mem=get_server_memory_usage(); echo round($mem,2); ?></span>
						<h4>% Memory Usage</h4>
					</div>
					<div class="col-md-3 col-xs-6">
						<i class="fa fa-hdd-o"></i>
						<span class="counter"><?php
                $bytes = disk_total_space("/"); 
                $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
                $base = 1024;
                $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
                echo sprintf('%1.2f' , $bytes / pow($base,$class));
              ?></span>
						<h4><?= $si_prefix[$class] ?> Disk Space</h4>
					</div>
					<div class="col-md-3 col-xs-6">
						<i class="fa fa-pie-chart"></i>
						<span class="counter"><?php
                $bytes = disk_free_space("/"); 
                $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
                $base = 1024;
                $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
                echo sprintf('%1.2f' , $bytes / pow($base,$class));
              ?></span>
						<h4><?= $si_prefix[$class] ?> Free Disk Space</h4>
					</div>
				</div><!--/end row-->
			</div><!--/end container-->
		</div>

	</section>
*/ ?>
	<div class="footsie">
	  <div class="container">
		<div class="row">
		  <div class="col-md-4 col-sm-4 col-xs-5">
			<p><a href="/credits/">Credits</a></p>
		  </div>
                  <div class="col-md-4 col-sm-4 col-xs-5 img-center text-center">
                    <p>Version <?= ver() ?></p>
                  </div>
		  <div class="col-md-4 col-sm-8 col-xs-7 text-right pull-right">
			<ul class="social-icons">
				<li style="display:none;"><a target="_blank" class="social_facebook rounded-x" data-original-title="Facebook" href="https://www.facebook.com/Robbie-Ferguson-22827508923/"></a></li>
				<li><a target="_blank" class="social_twitter rounded-x" data-original-title="Twitter" href="https://twitter.com/NEMSLinux"></a></li>
				<li><a target="_blank" class="social_rss rounded-x" data-original-title="Blog" href="http://baldnerd.com"></a></li>
			        <li><a target="_blank" class="social_youtube rounded-x" data-original-title="NEMS Linux on YouTube" href="https://youtube.com/c/NEMSLinux"></a></li>
			</ul>
		  </div>
		</div>
	  </div>
	</div>

	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/jquery/jquery-migrate.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!-- JS Implementing Plugins -->
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/jquery.easing.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/pace/pace.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/jquery.parallax.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/owl-carousel/owl.carousel.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/sky-forms-pro/skyforms/js/jquery.form.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/sky-forms-pro/skyforms/js/jquery.validate.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/cube-portfolio/cubeportfolio/js/jquery.cubeportfolio.min.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/modernizr.js"></script>
	<script src="https://cdn.zecheriah.com/site-assets/1.9.6/One-Pages/Classic/assets/plugins/login-signup-modal-window/js/main.js"></script> <!-- Gem jQuery -->
	<script src="/js/jquery.backstretch.min.js"></script>
        <script src="https://cdn.zecheriah.com/site-assets/1.9.6/assets/plugins/sky-forms-pro/skyforms/js/jquery-ui.min.js"></script>

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
		});
	</script>
        <?php
          $backgroundElem = '.fullscreen-static-image';
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
