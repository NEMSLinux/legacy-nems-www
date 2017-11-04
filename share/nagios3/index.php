<?php
  include('/var/www/html/inc/functions.php');
  include('/var/www/html/inc/header.php');
?>
<div class="container content">
  <div class="row"><div class="margin-bottom-40"></div></div>
  <div class="row">
    <div class="col-md-2">
      <?php include('menu.html'); ?>
      <?php include('sidebar.html'); ?>
    </div>
    <div class="col-md-10">
      <iframe src="cgi-bin/tac.cgi" frameborder="0" style="width: 100%;" scrolling="no"  onload="resizeIframe(this)" name="main"></iframe>
    </div>
  </div>
</div>

<?php
  include('/var/www/html/inc/footer.php');
?>
