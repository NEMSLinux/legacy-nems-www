<?php
  $cloudauth = intval(shell_exec('/usr/local/bin/nems-info cloudauthcache'));
  if ($cloudauth != 1) {
    exit('This NEMS Server is not authorized to access NEMS Cloud Services.' . PHP_EOL . 'Please sign up and configure your connection in NEMS SST.');
  }

  // Get the HWID
  $hwid = trim(shell_exec('/usr/local/bin/nems-info hwid'));

  // Load the NEMS Cloud Services License Key
  $nemsconffile = '/usr/local/share/nems/nems.conf'; // www-admin must have access to read/write
  $conf = file($nemsconffile);
  if (is_array($conf)) { // Load the existing conf data
        foreach ($conf as $line) {
                $tmp = explode('=',$line);
                if (is_array($tmp) && count($tmp) == 2 && trim($tmp[0]) == 'osbkey') $osbkey = trim($tmp[1]);
        }
  }

echo '<html>
<body onload="document.forms[\'redirect\'].submit()">
<form action="https://cloud.nemslinux.com/dashboard/login.php" method="post" name="redirect">
<input type="hidden" name="hwid" value="' . $hwid . '" />
<input type="hidden" name="osbkey" value="' . $osbkey . '" />
</form>
</body>
</html>';
