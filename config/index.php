<?php
/*
###########################################################################
#
# RESOURCE.CFG - Resource File for Nagios 
#
# You can define $USERx$ macros in this file, which can in turn be used
# in command definitions in your host config file(s).  $USERx$ macros are
# useful for storing sensitive information such as usernames, passwords, 
# etc.  They are also handy for specifying the path to plugins and 
# event handlers - if you decide to move the plugins or event handlers to
# a different directory in the future, you can just update one or two
# $USERx$ macros, instead of modifying a lot of command definitions.
#
# The CGIs will not attempt to read the contents of resource files, so
# you can set restrictive permissions (600 or 660) on them.
#
# Nagios supports up to 32 $USERx$ macros ($USER1$ through $USER32$)
#
# Resource files may also be used to store configuration directives for
# external data sources like MySQL...
#
###########################################################################
*/

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

// Nagios config
if (ver('nems') < 1.4) {
  # LEGACY VERSION
  $resourcefile = '/etc/nagios3/resource.cfg'; // www-admin must have access to read/write
  $pluginfolder = '/usr/lib/nagios/plugins';
} else {
  # MODERN VERSION
  $resourcefile = '/usr/local/nagios/etc/resource.cfg';
  $pluginfolder = '/usr/local/nagios/libexec';
}

if (isset($_POST) && isset($_POST['email'])) {
  if ($_POST['port'] == '') $_POST['port'] = 25;
  $output  = '###########################################################################' . PHP_EOL . '#' . PHP_EOL . '# RESOURCE.CFG - Resource File for Nagios' . PHP_EOL . '#' . PHP_EOL . '# This file is configured using the NEMS System Settings Tool ' . PHP_EOL . '# Please do not edit it directly.' . PHP_EOL . '#' . PHP_EOL . '###########################################################################' . PHP_EOL;
  $output .= '$USER1$=' . $pluginfolder . PHP_EOL; // A default setting, not user-configurable: the path to the plugins
  $output .= '$USER2$=/usr/share/nagios3/plugins/eventhandlers' . PHP_EOL; // A default setting, not user-configurable: the path to event handlers
  $output .= '$USER3$=' . (sanitize($_POST['domainuser']) ?: 'NULL') . PHP_EOL;
  $output .= '$USER4$=' . (sanitize($_POST['domainpassword']) ?: 'NULL') . PHP_EOL;
  $output .= '$USER5$=' . (sanitize($_POST['email']) ?: 'NULL') . PHP_EOL; // The "from address" for notifications
  $output .= '$USER6$=NULL' . PHP_EOL; // not used at present
  $output .= '$USER7$=' . sanitize($_POST['smtp']) . ':' . sanitize($_POST['port']) . PHP_EOL; // The SMTP server:port
  $output .= '$USER8$=NULL' . PHP_EOL; // not used at present
  $output .= '$USER9$=' . (sanitize($_POST['smtpuser']) ?: 'NULL') . PHP_EOL; // The SMTP authentication username
  $output .= '$USER10$=' . (sanitize($_POST['smtppassword']) ?: 'NULL') . PHP_EOL; // The SMTP authentication username

  # Telegram Account Info
  $output .= '$USER11$=' . (sanitize($_POST['telegram_bot']) ?: 'NULL') . PHP_EOL;
  $output .= '$USER12$=' . (sanitize($_POST['telegram_chatid']) ?: 'NULL') . PHP_EOL;

  # Pushover Account Info
  $output .= '$USER13$=' . (sanitize($_POST['pushover_apikey']) ?: 'NULL') . PHP_EOL;
  $output .= '$USER14$=' . (sanitize($_POST['pushover_userkey']) ?: 'NULL') . PHP_EOL;

  # TLS for SMTP enabled (1) or not (0), default 1
  $output .= '$USER15$=' . (sanitize($_POST['smtp_tls']) ?: '1') . PHP_EOL;

  # IPMI credentials
  $output .= '$USER16$=' . (sanitize($_POST['ipmi_user']) ?: 'NULL') . PHP_EOL;
  $output .= '$USER17$=' . (sanitize($_POST['ipmi_pass']) ?: 'NULL') . PHP_EOL;

  file_put_contents($resourcefile,$output); // overwrite the existing config

  # Restart Nagios
  $response = shell_exec('sudo /bin/systemctl restart nagios');
}

$resource = file($resourcefile);
if (is_array($resource)) {
  foreach ($resource as $line) {
    if (strstr($line,'$=')) {
      $tmp = explode('$=',$line);
      if (substr(trim($tmp[0]),0,1) == '$' && trim($tmp[1]) != 'NULL') { // omit comments (eg., starts with # instead of $)
        $variable_name = str_replace('$','',trim($tmp[0]));
        $$variable_name = trim($tmp[1]);
      }
    }
  }
}


// NEMS Config
$nemsconffile = '/usr/local/share/nems/nems.conf'; // www-admin must have access to read/write
$conf = file($nemsconffile);
if (is_array($conf)) { // Load the existing conf data
	foreach ($conf as $line) {
		$tmp = explode('=',$line);
		if (is_array($tmp) && count($tmp) == 2) $nemsconf[trim($tmp[0])] = trim($tmp[1]);
	}
}
if (is_array($nemsconf) && isset($_POST) && count($_POST) > 0) { // Overwrite the existing conf data
	// only need to include the conf options that are included in NEMS SST. The rest will be re-written from existing values.
	$nemsconf['osbpass'] = sanitize($_POST['osbpass']);
	$nemsconf['osbkey'] = sanitize($_POST['osbkey']);
	$nemsconf['alias'] = preg_replace("/&#?[a-z0-9]{2,8};/i","",sanitize($_POST['alias']));
        $nemsconf['allowupdate'] = intval($_POST['allowupdate']) ?: 5;
	$nemsconfoutput = '';
	foreach ($nemsconf as $key=>$value) {
		$nemsconfoutput .= $key . '=' . $value . PHP_EOL;
	}
        file_put_contents($nemsconffile,$nemsconfoutput); // overwrite the existing config
}
if (!isset($nemsconf['alias'])) $nemsconf['alias'] = 'NEMS';

function sanitize($string) {
  return filter_var(trim($string),FILTER_SANITIZE_STRING);
}


// File storage devices
  $drivestmp = shell_exec('/usr/local/share/nems/nems-scripts/info.sh drives');
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
  <h2><b>NEMS</b> <b>S</b>ystem <b>S</b>ettings <b>T</b>ool</h2>

<?php
  if (strlen($response) > 0) echo '<div class="col-md-12 alert alert-danger fade in">' . $response . '</div>';
?>


<form method="post" id="sky-form4" class="sky-form">

<div class="col-md-12" style="display:none;">
    <header>Storage Options</header>
    <fieldset>
        <section>
            <label class="label">Realtime Data Storage <font color="red">This feature is coming soon but doesn't do anything yet</font></label>
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
</div>

<div>
   <header>This NEMS Server</header>
   <fieldset>
        <section>
            <label class="label">NEMS Server Alias</label>
            <label class="input">
                <i class="icon-append fa fa-key"></i>
                <input type="text" name="alias" value="<?= $nemsconf['alias'] ?>">
                <b class="tooltip tooltip-bottom-right">Give This NEMS Server a Unique Name</b>
            </label>
        </section>
        <?php if (ver('nems') >= 1.5) { ?>
          <section>
            <label class="label">NEMS Update</label>
            <label class="select">
              <select name="allowupdate">
		<option value="5"<?php if (!isset($nemsconf['allowupdate']) || $nemsconf['allowupdate'] == 5) echo ' SELECTED'; ?>>Install Updates As Released</option>
		<option value="4"<?php if (isset($nemsconf['allowupdate']) && $nemsconf['allowupdate'] == 4) echo ' SELECTED'; ?>>Install Updates Every Week</option>
		<option value="3"<?php if (isset($nemsconf['allowupdate']) && $nemsconf['allowupdate'] == 3) echo ' SELECTED'; ?>>Install Updates Every Two Weeks</option>
		<option value="2"<?php if (isset($nemsconf['allowupdate']) && $nemsconf['allowupdate'] == 2) echo ' SELECTED'; ?>>Install Updates Once Per Month</option>
		<option value="1"<?php if (isset($nemsconf['allowupdate']) && $nemsconf['allowupdate'] == 1) echo ' SELECTED'; ?>>Do Not Automatically Install Updates</option>
              </select>
              <i></i>
            </label>
          </section>
        <?php } ?>
    </fieldset>
</div>

<div>
   <header>NEMS Migrator <a class="btn-u btn-u-xs" href="https://www.patreon.com/bePatron?c=1348071&rid=2163022" target="_blank">Sign Up for Off-Site</a> <a class="btn-u btn-u-dark-green btn-u-xs" href="https://docs.nemslinux.com/features/nems-migrator" target="_blank">Documentation</a></header>
   <fieldset>
        <section>
            <label class="label">Personal Encryption/Decryption Password</label>
            <label class="input">
                <i class="icon-append fa fa-lock"></i>
                <input type="password" name="osbpass" value="<?= $nemsconf['osbpass'] ?>">
                <b class="tooltip tooltip-bottom-right">Your private password which will encrypt/decrypt your backup set</b>
            </label>
            <label class="label">NEMS Migrator OSB License Key (If Registered)</label>
            <label class="input">
                <i class="icon-append fa fa-key"></i>
                <input type="text" name="osbkey" value="<?= $nemsconf['osbkey'] ?>">
                <b class="tooltip tooltip-bottom-right">Your Off-Site Backup License Key</b>
            </label>
        </section>
    </fieldset>
</div>

<div class="row" style="background: #fff; margin: 0;">

  <div class="col-md-4">
    <header>Windows Domain Access</header>
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

  </div>

  <div class="col-md-4">
    <header>Telegram Account Info</header>
    <fieldset>
        <section>
            <label class="label">Your Bot</label>
            <label class="input">
                <i class="icon-append fa fa-user"></i>
                <input type="text" name="telegram_bot" placeholder="bot123" value="<?= $USER11 ?>">
                <b class="tooltip tooltip-bottom-right">Enter the name of your bot as provided in the Telegram interface</b>
            </label>
        </section>
        <section>
            <label class="label">Telegram Chat ID</label>
            <label class="input">
                <i class="icon-append fa fa-lock"></i>
                <input type="text" name="telegram_chatid" placeholder="chat123" value="<?= $USER12 ?>">
                <b class="tooltip tooltip-bottom-right">Enter your Telegram Chat ID</b>
            </label>
        </section>
    </fieldset>
  </div>

  <div class="col-md-4">
    <header>Pushover Account Info</header>
    <fieldset>
        <section>
            <label class="label">API Key</label>
            <label class="input">
                <i class="icon-append fa fa-user"></i>
                <input type="text" name="pushover_apikey" placeholder="" value="<?= $USER13 ?>">
                <b class="tooltip tooltip-bottom-right">Enter your Pushover API key</b>
            </label>
        </section>
        <section>
            <label class="label">User Key</label>
            <label class="input">
                <i class="icon-append fa fa-lock"></i>
                <input type="text" name="pushover_userkey" placeholder="" value="<?= $USER14 ?>">
                <b class="tooltip tooltip-bottom-right">Enter your Pushover User key</b>
            </label>
        </section>
    </fieldset>
  </div>

<?php 
  if (ver('nems') >= 1.5) {
?>
  <div class="col-md-4">
    <header>IPMI Credentials</header>
    <fieldset>
        <section>
            <label class="label">IPMI Username</label>
            <label class="input">
                <i class="icon-append fa fa-user"></i>
                <input type="text" name="ipmi_user" placeholder="" value="<?= $USER16 ?>">
                <b class="tooltip tooltip-bottom-right">Enter your IPMI username</b>
            </label>
        </section>
        <section>
            <label class="label">IPMI Password</label>
            <label class="input">
                <i class="icon-append fa fa-lock"></i>
                <input type="text" name="ipmi_pass" placeholder="" value="<?= $USER17 ?>">
                <b class="tooltip tooltip-bottom-right">Enter your IPMI password</b>
            </label>
        </section>
    </fieldset>
  </div>
<?php
}
?>

</div>

    <header>Optional Services</header>
    <fieldset>
        <section class="col-md-4">
		<?php

		  // Only for Raspberry Pi
		  if ($platform->num < 10) {
			if (checkConfEnabled('rpi-monitor') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
			echo '<label class="toggle"><input ' . $checked . ' name="rpi-monitor" type="checkbox" class="services reboot"><i></i>RPi-Monitor</label>';
		  }

		  if (checkConfEnabled('nagios-api') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
		  echo '<label class="toggle"><input ' . $checked . ' name="nagios-api" type="checkbox" class="services reboot"><i></i>Nagios API</label>';

		  if (checkConfEnabled('webmin') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
		  echo '<label class="toggle"><input ' . $checked . ' name="webmin" type="checkbox" class="services reboot"><i></i>Webmin</label>';

		  if (checkConfEnabled('monitorix') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
		  echo '<label class="toggle"><input ' . $checked . ' name="monitorix" type="checkbox" class="services reboot"><i></i>Monitorix</label>';

                  if (ver('nems') >= 1.4) {

		    if (checkConfEnabled('cockpit') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
		    echo '<label class="toggle"><input ' . $checked . ' name="cockpit" type="checkbox" class="services reboot"><i></i>Cockpit</label>';

		    if (checkConfEnabled('tvpw') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
		    echo '<label class="toggle"><input ' . $checked . ' name="tvpw" type="checkbox" class="services"><i></i>Allow TV Dashboard Without Password</label>';

                  }

		?>
		<script>
		window.onload = function() {
		  $(".services.reboot").on('click', function(){
		      var thename = $(this).attr('name');
		      if ( $(this).is( ":checked" ) ) var onoff = 'on'; else var onoff = 'off';
		      $.ajax({
			  url: 'services.php',
			  type: 'post',
			  data: {
			    name: thename,
			    value: onoff
			  },
			  success: function(response) {
			     console.log(thename+' set to '+onoff);
			  }
		      });
		    alert('You have turned ' + onoff + ' ' + thename + '. Please reboot your NEMS server for the change to take effect.');
		  });
		}
		</script>
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
                <input type="text" name="smtp" placeholder="For example: smtp.gmail.com" value="<?= $smtp ?>">
            </label>
        </section>
        <section>
            <label class="label">SMTP Server Port</label>
            <label class="input">
                <input type="text" name="port" placeholder="For example: 25" value="<?= $port ?>">
            </label>
        </section>
        <?php if (ver('nems') >= 1.5) { ?>
          <section>
            <label class="label">SMTP Secure Authentication</label>
            <label class="select">
              <select name="smtptls">
		<option value="1"<?php if (!isset($USER15) || $USER15 == 1) echo ' SELECTED'; ?>>Use TLS Secure Authentication</option>
		<option value="0"<?php if (isset($USER15) && $USER15 == 0) echo ' SELECTED'; ?>>Do not use TLS</option>
              </select>
              <i></i>
            </label>
          </section>
        <?php } ?>
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
