<?php
  include('/var/www/html/inc/functions.php');
  if (!initialized()) {
    include('../init.php');
    exit();
  }
  if (ver('nems') < 1.3) {
    exit('Requires NEMS 1.3+');
  }
  include('/var/www/html/inc/header.php');

  $platform = ver('platform');
?>

<div class="container" style="margin-top: 100px; padding-bottom: 100px;">
  <h2><b>NEMS</b> Server Overview</h2>
  <p style="padding:4px 6px; color: #aaa !important;"><b>Your NEMS Hardware ID:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info hwid'); ?></span><br />Your NEMS HWID is a unique, but anonymous identifier for your NEMS Linux server.</p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>NEMS Server IP Address:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info ip'); ?></span><br />If nems.local is not resolving, you may use the IP address to connect to your NEMS server.</p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>NEMS Server Alias:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info alias'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>Running As:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info username'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>NEMS Version Running:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info nemsver'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>NEMS Version Available:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info nemsveravail'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>NEMS Platform:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info platform-name'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>NEMS Network Interface:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info nic'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>Number of Hosts:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info hosts'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>Number of Services:</b> <span class="nems-green"><?= shell_exec('/usr/local/bin/nems-info services'); ?></span></p>
  <p style="padding:4px 6px; color: #aaa !important;"><b>Authorized for Cloud:</b> <span class="nems-green"><?php if (shell_exec('/usr/local/bin/nems-info cloud') == 1) echo 'Yes'; else echo 'No'; ?></span></p>


</div>
<?php
  include('/var/www/html/inc/footer.php');
?>
