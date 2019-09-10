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
  error_reporting(E_ALL ^ E_NOTICE);

  include('/var/www/html/inc/functions.php');
  if (!initialized()) {
    include('../init.php');
    exit();
  }
  if (ver('nems') < 1.3) {
    exit('Requires NEMS 1.3+');
  }
  include('/var/www/html/inc/header.php');

  // Add the color chooser
  echo "<script src='/js/spectrum.js'></script><link rel='stylesheet' href='/css/spectrum.css' />";

  $platform = ver('platform');

  $uploaddir = '/var/www/html/userfiles/';

  // define variables
  $v=1;
  while ($v<=32) {
    ${'USER' . $v} = '';
    $v++;
  }

// Nagios config
if (ver('nems') < 1.4) {
  // LEGACY VERSION
  $resourcefile = '/etc/nagios3/resource.cfg'; // www-admin must have access to read/write
  $pluginfolder = '/usr/lib/nagios/plugins';
} else {
  // MODERN VERSION
  $resourcefile = '/usr/local/nagios/etc/resource.cfg';
  $pluginfolder = '/usr/lib/nagios/plugins';  // /usr/local/nagios/libexec is a symlink to that
}

if (isset($_POST) && isset($_POST['email'])) {

  if (isset($_FILES) && strlen($_FILES['file']['tmp_name']) > 0) {
    $verifyimg = getimagesize($_FILES['file']['tmp_name']);

    if ( // only allow png and jpg
      $verifyimg['mime'] == 'image/png' ||
      $verifyimg['mime'] == 'image/jpeg'
    ) {

// Re-size large images
$maxDim = 1900;
$file_name = $_FILES['file']['tmp_name'];
list($width, $height, $type, $attr) = getimagesize( $file_name );
if ( $width > $maxDim || $height > $maxDim ) {
    $target_filename = $file_name;
    $ratio = $width/$height;
    if( $ratio > 1) {
        $new_width = $maxDim;
        $new_height = $maxDim/$ratio;
    } else {
        $new_width = $maxDim*$ratio;
        $new_height = $maxDim;
    }
    $src = imagecreatefromstring( file_get_contents( $file_name ) );
    $dst = imagecreatetruecolor( $new_width, $new_height );
    imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
    imagedestroy( $src );
    if ($verifyimg['mime'] == 'image/png') {
      imagepng( $dst, $target_filename, 9 );
    } elseif ($verifyimg['mime'] == 'image/jpeg') {
      imagejpeg( $dst, $target_filename, 60 );
    }
    imagedestroy( $dst );
}

      $uploadfile = $uploaddir . basename($_FILES['file']['name']);

      if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
        $imguploadresp = "Image succesfully uploaded.";
        $bgfileNEW = basename($_FILES['file']['name']);
        // create a hex color that is the most prominent color in the photo
        $image=imagecreatefromjpeg($uploadfile);
        $thumb=imagecreatetruecolor(1,1); imagecopyresampled($thumb,$image,0,0,0,0,1,1,imagesx($image),imagesy($image));
        $mainColor='hsv(' . rgb2hsv(hex2rgb(strtoupper(dechex(imagecolorat($thumb,0,0))))) . ')';
      } else {
        $imguploadresp = "Image uploading failed.";
      }
    }
  }

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

  # TLS for SMTP enabled (1) or not (2), default 1
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
        $nemsconf['perfdata_cutoff'] = intval($_POST['perfdata_cutoff']) ?: 0;
	$nemsconf['tv_require_notify'] = sanitize($_POST['tv_require_notify']);
	$nemsconf['tv_24h'] = sanitize($_POST['tv_24h']);
	$nemsconf['osbpass'] = sanitize($_POST['osbpass']);
	$nemsconf['osbkey'] = sanitize($_POST['osbkey']);
	$nemsconf['webhook'] = sanitize($_POST['webhook']);
	$nemsconf['alias'] = preg_replace("/&#?[a-z0-9]{2,8};/i","",sanitize($_POST['alias']));
        $nemsconf['allowupdate'] = intval($_POST['allowupdate']) ?: 5;
        $nemsconf['background'] = intval($_POST['background']) ?: 5;
        if (isset($bgfileNEW) && strlen($bgfileNEW) > 0) {
          // delete the previous image and replace with the new
          if (isset($nemsconf['backgroundImage']) && file_exists($uploaddir . $nemsconf['backgroundImage'])) {
            // only unlink if the filename differs - re-uploading the same filename replaces so no need to unlink
            if ($nemsconf['backgroundImage'] != $bgfileNEW) unlink ($uploaddir . $nemsconf['backgroundImage']);
          }
          $nemsconf['backgroundImage'] = $bgfileNEW;
        }
        $nemsconf['backgroundBlur'] = intval($_POST['backgroundBlur']) ?: 1;
        if (isset($mainColor)) {
          $nemsconf['backgroundColor'] = $mainColor;
        } else {
          $nemsconf['backgroundColor'] = sanitize($_POST['backgroundColor']) ?: '0,100%,100%';
        }
        $nemsconf['checkin.enabled'] = intval($_POST['checkin_enabled']) ?: 0;
        $nemsconf['checkin.email'] = filter_var(trim($_POST['checkin_email']), FILTER_VALIDATE_EMAIL) ?: '';
        $nemsconf['checkin.interval'] = intval($_POST['checkin_interval']) ?: 8; // how many 15 minute cycles before notifying. Default 8 (2 hours).

        $nemsconf['speedtestserver'] = intval($_POST['speedtestserver']);
        $nemsconf['speedtestwhich'] = intval($_POST['speedtestwhich']) ?: 0;

	$nemsconfoutput = '';
	foreach ($nemsconf as $key=>$value) {
		$nemsconfoutput .= $key . '=' . $value . PHP_EOL;
	}
        file_put_contents($nemsconffile,$nemsconfoutput); // overwrite the existing config
}
if (!isset($nemsconf['alias']) || strlen($nemsconf['alias']) == 0) $nemsconf['alias'] = 'NEMS';

function sanitize($string) {
  return filter_var(trim($string),FILTER_SANITIZE_STRING);
}

// File storage devices
  $drivestmp = shell_exec('/usr/local/share/nems/nems-scripts/info.sh drives');
  $drivestmp = json_decode($drivestmp, true);
  if (is_array($drivestmp['blockdevices']) && count($drivestmp['blockdevices']) > 0) {
    foreach ($drivestmp['blockdevices'] as $blockdevice) {
      if (isset($blockdevice['children']) && is_array($blockdevice['children']) && count($blockdevice['children']) > 0) {
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

// do this AFTER storing changes
$cloudauth = shell_exec('/usr/local/bin/nems-info cloudauth');

?>

<div class="container" style="margin-top: 100px; padding-bottom: 100px;">
  <h2><b>NEMS</b> <b>S</b>ystem <b>S</b>ettings <b>T</b>ool</h2>

<?php
  if (strlen($response) > 0) echo '<div class="col-md-12 alert alert-danger fade in">' . $response . '</div>';
?>
<script src="/js/jquery.are-you-sure.js"></script>
<script src="/js/ays-beforeunload-shim.js"></script>
<script>
  $(function() {
    $('#sst').areYouSure(
      {
        message: 'If you do not save your changes you will lose them. '
               + 'Please cancel and save if you want to keep them.'
      }
    );
  });
</script>
<form method="post" id="sst" class="sky-form" style="border:none;" enctype="multipart/form-data">


					<div class="tab-v1">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#general" data-toggle="tab">General</a></li>
              <li><a href="#cloud" data-toggle="tab">NEMS Cloud Services</a></li>
		<li style="display:none;"><a href="#networking" data-toggle="tab">Networking</a></li>
              <li><a href="#notifications" data-toggle="tab">Notifications</a></li>
              <li><a href="#tv" data-toggle="tab">TV Dashboard</a></li>
              <li><a href="#options" data-toggle="tab">Optional Services</a></li>
						</ul>
						<div class="tab-content">

							<div class="tab-pane fade in active" id="general">
								<div class="row">
									<div class="col-md-12">
										<div class="row">


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
        <?php if (ver('nems') >= 1.5) { ?>
          <section>
            <label class="label">Background Image</label>
            <label class="select">
              <select name="background" id="background">
		<option value="6"<?php if (!isset($nemsconf['background']) || $nemsconf['background'] == 6) echo ' SELECTED'; ?>>Daily Image (Default)</option>
		<option value="9"<?php if (isset($nemsconf['background']) && $nemsconf['background'] == 9) echo ' SELECTED'; ?>>Daily Color</option>
		<option value="5"<?php if (isset($nemsconf['background']) && $nemsconf['background'] == 5) echo ' SELECTED'; ?>>NEMS Legacy</option>
		<option value="7"<?php if (isset($nemsconf['background']) && $nemsconf['background'] == 7) echo ' SELECTED'; ?>>Custom Color</option>
		<option value="8"<?php if (isset($nemsconf['background']) && $nemsconf['background'] == 8) echo ' SELECTED'; ?>>Upload Image</option>
              </select>
              <i></i>
            </label>
          </section>
          <script>
            $(function () {
              $("#background").change(function() {
                var val = $(this).val();
                if(val === "7") {
                  $("#colorpicker").slideDown();
                } else {
                  $("#colorpicker").slideUp();
                }
                if(val === "8") {
                  $("#fileupload").slideDown();
                } else {
                  $("#fileupload").slideUp();
                }
              });
            });
          </script>
          <section id="colorpicker" <?php if (!isset($nemsconf['background']) || $nemsconf['background'] != 7) echo 'style="display:none;"'; ?>>
            <label class="label">Custom Background Color</label>
            <label class="input">
              <input type="text" id="bgcolor" name="backgroundColor" value="<?= $nemsconf['backgroundColor'] ?>">
            </label>
            <script>
              $("#bgcolor").spectrum({
                color: "<?= $nemsconf['backgroundColor'] ?>",
                chooseText: "Ok"
              });
            </script>
          </section>

          <section id="fileupload" <?php if (!isset($nemsconf['background']) || $nemsconf['background'] != 8) echo 'style="display:none;"'; ?>>
            <?php if (strlen($nemsconf['backgroundImage']) > 0 && file_exists($uploaddir . $nemsconf['backgroundImage'])) { echo 'Current: <a href="' . str_replace('/var/www/html/', '/', $uploaddir) . $nemsconf['backgroundImage'] . '" target="_blank">' . $nemsconf['backgroundImage'] . '</a>'; } ?>
            <label class="label">Upload Your Image</label>
            <label for="file" class="input input-file">
              <div class="button"><input type="file" id="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">Browse</div><input type="text" readonly>
            </label>
          </section>



          <section>
            <label class="label">Blur Background</label>
            <label class="select">
              <select name="backgroundBlur">
		<option value="1"<?php if (!isset($nemsconf['backgroundBlur']) || $nemsconf['backgroundBlur'] == 1) echo ' SELECTED'; ?>>Disabled</option>
		<option value="2"<?php if (isset($nemsconf['backgroundBlur']) && $nemsconf['backgroundBlur'] == 2) echo ' SELECTED'; ?>>Enabled, Slight Blur</option>
		<option value="3"<?php if (isset($nemsconf['backgroundBlur']) && $nemsconf['backgroundBlur'] == 3) echo ' SELECTED'; ?>>Enabled, Medium Blur</option>
		<option value="4"<?php if (isset($nemsconf['backgroundBlur']) && $nemsconf['backgroundBlur'] == 4) echo ' SELECTED'; ?>>Enabled, Heavy Blur</option>
              </select>
              <i></i>
            </label>
          </section>
        <?php } ?>
        <?php
          if (ver('nems') >= 1.4 && $disabled == 1) {
            $wifi = json_decode(trim(shell_exec('/usr/local/bin/nems-info wifi')));
            if (is_object($wifi) && count((array)$wifi) > 0) {
              echo '<section><label class="label"><b>Wireless Connection</b><br />SSID:</label><label class="select"><select name="wifi">' . PHP_EOL;
              foreach ($wifi as $ssid=>$wifidata) {
                echo '<option value="' . $ssid . '">' . $ssid . '</option>' . PHP_EOL;
              }
            }
        ?>
              </select>
              <i></i>
            </label>
          </section>
        <?php } ?>
    </fieldset>
</div>


<div class="row" style="background: #fff; margin: 0;">

  <div class="col-md-4">
    <header>Maintenance</header>
    <fieldset>
          <section>
            <label class="label">Performance Data Retention (ie., for NagiosGraphs)</label>
            <label class="select">
              <select name="perfdata_cutoff">
		<option value="0"<?php if (!isset($nemsconf['perfdata_cutoff']) || $nemsconf['perfdata_cutoff'] == 0) echo ' SELECTED'; ?>>Keep Indefinitely</option>
		<option value="365"<?php if (isset($nemsconf['perfdata_cutoff']) && $nemsconf['perfdata_cutoff'] == 365) echo ' SELECTED'; ?>>365 Days</option>
		<option value="180"<?php if (isset($nemsconf['perfdata_cutoff']) && $nemsconf['perfdata_cutoff'] == 180) echo ' SELECTED'; ?>>180 Days</option>
		<option value="90"<?php if (isset($nemsconf['perfdata_cutoff']) && $nemsconf['perfdata_cutoff'] == 90) echo ' SELECTED'; ?>>90 Days</option>
		<option value="30"<?php if (isset($nemsconf['perfdata_cutoff']) && $nemsconf['perfdata_cutoff'] == 30) echo ' SELECTED'; ?>>30 Days</option>
		<option value="7"<?php if (isset($nemsconf['perfdata_cutoff']) && $nemsconf['perfdata_cutoff'] == 7) echo ' SELECTED'; ?>>7 Days</option>
              </select>
              <i></i>
            </label>
          </section>
    </fieldset>

  </div>
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
                <input type="password" name="domainpassword" placeholder="Password" id="domainpassword" value="<?= $USER4 ?>">
                <b class="tooltip tooltip-bottom-right">Administrator password</b>
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

  <div class="col-md-4" style="display: none;">
    <header>Internet Speedtest</header>
    <fieldset>
        <section>
            <label class="label">Automatically Chosen Server</label>
            <label class="input">
                <i class="icon-append fa fa-server"></i>
                <input type="hidden" name="speedtestserver" value="<?= $nemsconf['speedtestserver'] ?>" />
                <input type="text" disabled="disabled" value="<?= $nemsconf['speedtestserver'] ?>" />
            </label>
        </section>
        <section>
           <label class="label">Which To Use</label>
           <label class="select">
             <select name="speedtestwhich">
               <option value="0"<?php if (!isset($nemsconf['speedtestwhich']) || $nemsconf['speedtestwhich'] == 0) echo ' SELECTED'; ?>>Recommended Nearest Server (Dynamic)</option>
               <option value="1"<?php if (isset($nemsconf['speedtestwhich']) && $nemsconf['speedtestwhich'] == 1) echo ' SELECTED'; ?>>Hard-set in NEMS NConf service (Static)</option>
             </select>
             <i></i>
           </label>
        </section>
    </fieldset>
  </div>

<?php
}
?>

</div>





</div>
</div>
</div>
</div>


							<div class="tab-pane fade in" id="cloud">
								<div class="row">
									<div class="col-md-12">
										<div class="row">

                      <div>
                         <header>NEMS Cloud Services<?php if ($cloudauth != 1) echo ' <a class="btn-u btn-u-xs" href="https://www.patreon.com/bePatron?c=1348071&rid=2163022" target="_blank">Sign Up</a>'; ?> <a class="btn-u btn-u-dark-green btn-u-xs" href="https://docs.nemslinux.com/features/cloud" target="_blank">Learn More</a></header>
                         <fieldset>
                              <section>
                                  <label class="label">NEMS Cloud Services Key (If Registered)</label>
                                  <label class="input">
                                      <i class="icon-append fa fa-key"></i>
                                      <input type="text" name="osbkey" value="<?= $nemsconf['osbkey'] ?>">
                                      <b class="tooltip tooltip-bottom-right">Your NEMS Cloud Services Key</b>
                                      <?php
                                        if (isset($nemsconf['osbkey']) && strlen($nemsconf['osbkey'])) {
                                          echo '<span style="font-size: 0.8em;">';
                                          if ($cloudauth == 1) {
                                            echo '<span class="nems-green">Connected</span>';
                                          } else {
                                            echo '<span class="color-red">Authorization Failed</span>';
                                            if (!isset($nemsconf['osbpass']) || $nemsconf['osbpass'] == '') echo ' (NEMS Cloud Services requires encryption, but you haven\'t set a Personal Encryption/Decryption Password on the <em>General</em> tab.)';
                                          }
                                          echo '</span>';
                                        }
                                      ?>
                                  </label>
                              </section>
                              <p><b>Please Note:</b> Your off-site backup will be encrypted using the personal encryption/decryption key you entered on the General tab. If you do not enter an encryption key, your backup will not be sent to NEMS Cloud Services.</p>
                          </fieldset>
                      </div>

                      <div>
                         <header>NEMS Migrator<?php if ($cloudauth != 1) echo ' <a class="btn-u btn-u-xs" href="https://www.patreon.com/bePatron?c=1348071&rid=2163022" target="_blank">Sign Up for Off-Site</a>'; ?> <a class="btn-u btn-u-dark-green btn-u-xs" href="https://docs.nemslinux.com/features/nems-migrator" target="_blank">Documentation</a></header>
                         <fieldset>
                              <section>
                                  <p>By adding a personal encryption/decryption password, your NEMS server will backup its configuration to NEMS Cloud Services. If you manage multiple NEMS servers, use the same encryption/decryption password to allow you to see multiple NEMS servers on a single NEMS Cloud TV Dashboard.</p>
                                  <p>This password will never be stored on NEMS Cloud Services. It is strictly used for encryption/decryption operations. You may use it to view your NEMS Server status on the NEMS Cloud Services web site.</p>
                                  <label class="label">Personal Encryption/Decryption Password</label>
                                  <label class="input">
                                      <i class="icon-append fa fa-lock"></i>
                                      <input type="password" name="osbpass" value="<?= $nemsconf['osbpass'] ?>">
                                      <b class="tooltip tooltip-bottom-right">Your private password which will encrypt/decrypt your backup set</b>
                                  </label>
                              </section>
                          </fieldset>
                      </div>


                                            <?php if (ver('nems') >= 1.5) { ?>
                                                <header>NEMS CheckIn Notifications</header>
                                                <fieldset>
                                                  <section>
                                                    <p>Receive an email if your NEMS server goes offline or crashes.</p>
                                                    <label class="label">State</label>
                                                    <label class="select">
                                                      <select name="checkin.enabled">
                                                <option value="0"<?php if (!isset($nemsconf['checkin.enabled']) || $nemsconf['checkin.enabled'] == 0) echo ' SELECTED'; ?>>Disabled</option>
                                                <option value="1"<?php if (isset($nemsconf['checkin.enabled']) && $nemsconf['checkin.enabled'] == 1) echo ' SELECTED'; ?>>Enabled</option>
                                                      </select>
                                                      <i></i>
                                                    </label>
                                                  </section>
                                                  <section>
                                                    <label class="label">Email Address for CheckIn Alerts</label>
                                                    <label class="input">
                                                        <i class="icon-append fa fa-envelope"></i>
                                                        <input type="email" name="checkin.email" placeholder="CheckIn email address" value="<?= $nemsconf['checkin.email'] ?>">
                                                        <b class="tooltip tooltip-bottom-right">Email Address for CheckIn Alerts</b>
                                                    </label>
                                                  </section>
                                                  <section>
                                                    <label class="label">When to Notify</label>
                                                    <label class="select">
                                                      <select name="checkin.interval">
                                                <option value="96"<?php if (isset($nemsconf['checkin.interval']) && $nemsconf['checkin.interval'] == 96) echo ' SELECTED'; ?>>After 1 Day</option>
                                                <option value="24"<?php if (isset($nemsconf['checkin.interval']) && $nemsconf['checkin.interval'] == 24) echo ' SELECTED'; ?>>After 6 Hours</option>
                                                <option value="8"<?php if (!isset($nemsconf['checkin.interval']) || $nemsconf['checkin.interval'] == 8) echo ' SELECTED'; ?>>After 2 Hours</option>
                                                <option value="4"<?php if (isset($nemsconf['checkin.interval']) && $nemsconf['checkin.interval'] == 4) echo ' SELECTED'; ?>>After 1 Hour</option>
                                                <option value="2"<?php if (isset($nemsconf['checkin.interval']) && $nemsconf['checkin.interval'] == 2) echo ' SELECTED'; ?>>After 30 Minutes</option>
                                                <option value="1"<?php if (isset($nemsconf['checkin.interval']) && $nemsconf['checkin.interval'] == 1) echo ' SELECTED'; ?>>After 15 Minutes</option>
                                                      </select>
                                                      <i></i>
                                                    </label>
                                                  </section>
                                                </fieldset>
                                            <?php } ?>


</div></div></div></div>

							<div class="tab-pane fade in" id="networking">
								<div class="row">
									<div class="col-md-12">
										<div class="row">
<p>Coming Soon...</p>
</div></div></div></div>

							<div class="tab-pane fade in" id="notifications">
								<div class="row">
									<div class="col-md-12">
										<div class="row">

<div>


    <div class="col-md-12">

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
                    <select name="smtp_tls">
                      <option value="1"<?php if (!isset($USER15) || $USER15 == 1) echo ' SELECTED'; ?>>Use TLS Secure Authentication</option>
                      <option value="2"<?php if (isset($USER15) && $USER15 == 2) echo ' SELECTED'; ?>>Do not use TLS</option>
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
                      <input type="text" name="smtpuser" placeholder="Username" value="<?= $USER9 ?>">
                      <b class="tooltip tooltip-bottom-right">SMTP Username</b>
                  </label>
              </section>
              <section>
                  <label class="label">SMTP Password</label>
                  <label class="input">
                      <i class="icon-append fa fa-lock"></i>
                      <input type="password" name="smtppassword" placeholder="Password" id="smtppassword" value="<?= $USER10 ?>">
                      <b class="tooltip tooltip-bottom-right">SMTP Password</b>
                  </label>
              </section>
          </fieldset>

          <div class="row" style="background: #fff; margin: 0;">

            <div class="col-md-4">
              <header>Telegram Account Info <a href="https://docs.nemslinux.com/usage/notify-host-by-telegram" target="_blank"><i class="fa fa-question-circle" style="font-size: 0.8em; color: #1b4a90; text-decoratoin:none;"></i></a></header>
              <fieldset>
                  <section>
                      <label class="label">Bot API Token</label>
                      <label class="input">
                          <i class="icon-append fa fa-user"></i>
                          <input type="text" name="telegram_bot" placeholder="XXXXXXXX:YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY" value="<?= $USER11 ?>">
                          <b class="tooltip tooltip-bottom-right">Enter the name of your bot as configured in the Telegram interface</b>
                      </label>
                  </section>
                  <section>
                      <label class="label">Telegram Chat ID</label>
                      <label class="input">
                          <i class="icon-append fa fa-lock"></i>
                          <input type="text" name="telegram_chatid" placeholder="gXXXXXXXXX" value="<?= $USER12 ?>">
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
        <header>Webhook Notifications</header>
        <fieldset>
            <section>
              <p>Send notifications to a Webhook.</p>
                <label class="label">Webhook URL</label>
                <label class="input">
                    <i class="icon-append fa fa-globe"></i>
                    <input type="text" name="webhook" placeholder="" value="<?= $nemsconf['webhook'] ?>">
                    <b class="tooltip tooltip-bottom-right">Enter your webhook URL</b>
                </label>
            </section>
        </fieldset>
    </div>
    <?php
    }
    ?>
</div>


</div>

</div></div></div></div>
</div>




<!-- tv dashboard -->
<?php if (ver('nems') >= 1.4) { ?>

							<div class="tab-pane fade in" id="tv">
								<div class="row">
									<div class="col-md-12">
										<div class="row">

<div>


    <div class="col-md-12">

          <header>NEMS TV Dashboard Configuration</header>
          <fieldset>

<?php
      		    if (checkConfEnabled('tvpw') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
            		    echo '<div class="row" style="margin-bottom: 20px;"><label class="toggle col-md-4"><input ' . $checked . ' name="tvpw" type="checkbox" class="services"><i></i>Allow TV Dashboard Without Password</label></div>';
?>

                <section>
                  <label class="label">When should issues appear on NEMS TV Dashboard?</label>
                  <label class="select">
                    <select name="tv_require_notify">
                      <option value="1"<?php if (!isset($nemsconf['tv_require_notify']) || $nemsconf['tv_require_notify'] == 1) echo ' SELECTED'; ?>>Once they enter their individual notification period (Default)</option>
                      <option value="2"<?php if ($nemsconf['tv_require_notify'] == 2) echo ' SELECTED'; ?>>Immediately</option>
                    </select>
                    <i></i>
                  </label>
                </section>

                <section>
                  <label class="label">Clock Format</label>
                  <label class="select">
                    <select name="tv_24h">
                      <option value="3"<?php if (!isset($nemsconf['tv_24h']) || $nemsconf['tv_24h'] == 3) echo ' SELECTED'; ?>>3:25</option>
                      <option value="2"<?php if ($nemsconf['tv_24h'] == 2) echo ' SELECTED'; ?>>3:25 PM</option>
                      <option value="1"<?php if ($nemsconf['tv_24h'] == 1) echo ' SELECTED'; ?>>15:25</option>
                    </select>
                    <i></i>
                  </label>
                </section>

          </fieldset>


</div>

</div></div></div></div>
</div>
<?php } ?>

<!-- / tv dashboard -->

							<div class="tab-pane fade in" id="options">
								<div class="row">
									<div class="col-md-12">
										<div class="row">

                          <header>Optional Services</header>
                          <fieldset>
                              <section class="col-md-6">
                      		<?php

                      		  // Only for Raspberry Pi
                      		  if ($platform->num < 10) {
                      			if (checkConfEnabled('rpi-monitor') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
                      			echo '<label class="toggle text-right"><input ' . $checked . ' name="rpi-monitor" type="checkbox" class="services reboot"><i></i>RPi-Monitor</label>';
                      		  }

                      		  if (checkConfEnabled('nagios-api') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
                      		  echo '<label class="toggle text-right"><input ' . $checked . ' name="nagios-api" type="checkbox" class="services reboot"><i></i>Nagios API</label>';

                                  if (ver('nems') <= 1.4) {
                      		    if (checkConfEnabled('webmin') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
                      		    echo '<label class="toggle text-right"><input ' . $checked . ' name="webmin" type="checkbox" class="services reboot"><i></i>Webmin</label>';
                                  }

                      		  if (checkConfEnabled('monitorix') == true) $checked = 'CHECKED="CHECKED"'; else $checked = '';
                      		  echo '<label class="toggle text-right"><input ' . $checked . ' name="monitorix" type="checkbox" class="services reboot"><i></i>Monitorix</label>';

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

</div></div></div></div>

<!-- close tabs-->
</div>
</div>
    <footer>
        <button type="submit" class="btn-u">Save All Settings (All Pages)</button>
    </footer>
</form>

</div>
<?php
  include('/var/www/html/inc/footer.php');
?>
