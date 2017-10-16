<?php
  //  Nagios supports up to 32 $USERx$ macros ($USER1$ through $USER32$)

  include('/var/www/html/inc/functions.php');
  if (!file_exists('/var/www/htpasswd')) {
    include('../init.php');
    exit();
  }
  if (ver('nems') < 1.3) {
    exit('Requires NEMS 1.3+');
  }
  include('/var/www/html/inc/header.php');

// Nagios config
$resourcefile = '/etc/nagios3/resource.cfg'; // www-admin must have access to read/write

if (isset($_POST) && isset($_POST['email'])) {
  if ($_POST['port'] == '') $_POST['port'] = 25;
  $output  = '###########################################################################' . PHP_EOL . '#' . PHP_EOL . '# RESOURCE.CFG - Resource File for Nagios' . PHP_EOL . '#' . PHP_EOL . '# This file is configured using the NEMS System Settings Tool ' . PHP_EOL . '# Please do not edit it directly.' . PHP_EOL . '#' . PHP_EOL . '###########################################################################' . PHP_EOL;
  $output .= '$USER1$=/usr/lib/nagios/plugins' . PHP_EOL; // A default setting, not user-configurable: the path to the plugins
  $output .= '$USER3$=' . sanitize($_POST['domainuser']) . PHP_EOL;
  $output .= '$USER4$=' . sanitize($_POST['domainpassword']) . PHP_EOL;
  $output .= '$USER5$=' . sanitize($_POST['email']) . PHP_EOL; // The "from address" for notifications
  $output .= '$USER7$=' . sanitize($_POST['smtp']) . ':' . sanitize($_POST['port']) . PHP_EOL; // The SMTP server:port
  $output .= '$USER9$=' . sanitize($_POST['smtpuser']) . PHP_EOL; // The SMTP authentication username
  $output .= '$USER10$=' . sanitize($_POST['smtppassword']) . PHP_EOL; // The SMTP authentication username
  file_put_contents($resourcefile,$output); // overwrite the existing config
}

$resource = file($resourcefile);
if (is_array($resource)) {
  foreach ($resource as $line) {
    if (strstr($line,'$=')) {
      $tmp = explode('$=',$line);
      if (substr(trim($tmp[0]),0,1) == '$') { // omit comments (eg., starts with # instead of $)
        $variable_name = str_replace('$','',trim($tmp[0]));
        $$variable_name = trim($tmp[1]);
      }
    }
  }
}

function sanitize($string) {
  return filter_var(trim($string),FILTER_SANITIZE_STRING);
}


// File storage devices
  $drivestmp = shell_exec('/home/pi/nems-scripts/info.sh drives');
  $drivestmp = json_decode($drivestmp, true);
  if (is_array($drivestmp['blockdevices']) && count($drivestmp['blockdevices']) > 0) {
    foreach ($drivestmp['blockdevices'] as $blockdevice) {
      if (is_array($blockdevice['children']) && count($blockdevice['children']) > 0) {
        foreach ($blockdevice['children'] as $partition) {
          if ($partition['fstype'] != 'swap' && $partition['mountpoint'] != '/boot') {
            $partitions['usable'][$partition['uuid']] = $partition;
            if ($partition['mountpoint'] == '/') $partitions['default'] = $partition['uuid'];
          }
        }
      }
    }
  }
// print_r($drivestmp);

?>

<div class="container" style="margin-top: 100px; padding-bottom: 100px;">
  <h2>NEMS System Settings Tool</h2>
  <p><b>Your NEMS Hardware ID:</b> <?= shell_exec('/usr/bin/nems-info hwid'); ?></p>

<form method="post" id="sky-form4" class="sky-form">

    <header>NEMS Configuration Options</header>
    <fieldset>
        <section>
            <label class="label">Realtime Data Storage</label>
            <label class="select">
              <select name="budget">
                
                <?php
                  echo '<option value="' . $partitions['usable'][$partitions['default']]['name'] . '">Default - ' . $partitions['usable'][$partitions['default']]['name'] . ' | ' . $partitions['usable'][$partitions['default']]['size'] . ' | ' . $partitions['usable'][$partitions['default']]['fstype'] . ' | Mounted on ' . $partitions['usable'][$partitions['default']]['mountpoint'] . '</option>';
                  foreach ($partitions['usable'] as $uuid=>$partition) {
                    if ($uuid != $partitions['default']) echo '<option value="' . $partition['name'] . '">' . $partition['name'] . ' | ' . $partition['size'] . ' | ' . $partition['fstype'] . ' | Mounted on ' . $partition['mountpoint'] . '</option>';
                  }
                ?>
              </select>
              <i></i>
            </label>
        </section>
    </fieldset>

    <header>Windows Domain Access (Hidden from CGIs)</header>
    <fieldset>
        <section>
            <label class="label">Administrator domain/username. If not on a domain, use username only.</label>
            <label class="input">
                <i class="icon-append fa fa-user"></i>
                <input type="text" name="domainuser" placeholder="mydomain/Administrator" value="<?= $USER3 ?>">
                <b class="tooltip tooltip-bottom-right">Administrator username for Windows Domain Machines</b>
            </label>
        </section>
        <section>
            <label class="label">Administrator Password</label>
            <label class="input">
                <i class="icon-append fa fa-lock"></i>
                <input type="password" name="domainpassword" placeholder="Password" id="password" value="<?= $USER4 ?>">
                <b class="tooltip tooltip-bottom-right">Administrator password</b>
            </label>
        </section>
    </fieldset>

    <header>SMTP Email Configuration</header>
    <fieldset>
	<?php
		// figure out the server and port from config or use default port
		$smtptmp = explode(':',$USER7);
		if (count($smtptmp) > 1) $port = intval($smtptmp[1]); else $port = 25;
		$smtp = trim($smtptmp[0]);
	?>
        <section>
            <label class="label">SMTP Server Address</label>
            <label class="input">
                <input type="text" name="smtp" placeholder="smtp.gmail.com" value="<?= $smtp ?>">
            </label>
        </section>
        <section>
            <label class="label">SMTP Server Port</label>
            <label class="input">
                <input type="text" name="port" placeholder="25" value="<?= $port ?>">
            </label>
        </section>
        <section>
            <label class="label">"From" Sender Email Address</label>
            <label class="input">
                <i class="icon-append fa fa-envelope"></i>
                <input type="email" name="email" placeholder="Email address" value="<?= $USER5 ?>">
                <b class="tooltip tooltip-bottom-right">Sender Email Address</b>
            </label>
        </section>
        <section>
            <label class="label">SMTP Authentication Username (Typically an email address)</label>
            <label class="input">
                <i class="icon-append fa fa-envelope"></i>
                <input type="email" name="smtpuser" placeholder="Email address" value="<?= $USER9 ?>">
                <b class="tooltip tooltip-bottom-right">SMTP Username</b>
            </label>
        </section>
        <section>
            <label class="label">SMTP Password</label>
            <label class="input">
                <i class="icon-append fa fa-lock"></i>
                <input type="password" name="smtppassword" placeholder="Password" id="password" value="<?= $USER10 ?>">
                <b class="tooltip tooltip-bottom-right">SMTP Password</b>
            </label>
        </section>
    </fieldset>
    <footer>
        <button type="submit" class="btn-u">Save All Settings</button>
    </footer>
</form>

</div>
<?php
  include('/var/www/html/inc/footer.php');
?>
