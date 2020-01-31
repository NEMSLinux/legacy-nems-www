<?php
  // This will simply output whether the NEMS Server has been initialized
  // Is used to redirect the initialization notification to the main dashboard
  echo trim(shell_exec('/usr/local/bin/nems-info init'));
?>
