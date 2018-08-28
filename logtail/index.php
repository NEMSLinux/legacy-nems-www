<?php
  include('/var/www/html/inc/functions.php');
  if (!initialized()) {
    include('../init.php');
    exit();
  }
  include('/var/www/html/inc/header.php');
?>
<script type="text/javascript" src="logtail.js"></script>
<div class="container" style="margin-top: 100px; padding-bottom: 100px;">
  <h4>NEMS Log Tail</h4>
    <div class="row">
      <div class="col-md-12">
        <pre id="data">Loading...</pre>
      </div>
    </div>
</div>
<?php
  include('/var/www/html/inc/footer.php');
?>

