<?php
  require_once('./inc/functions.php');
?><h1>Not Initialized</h1>
You have not initialized NEMS yet. Please SSH to your NEMS server and run the <em>nems-init</em> command as follows:

<br /><br />
<pre>
<?php
  if (ver('nems') < 1.3) {
    echo 'ssh pi@' . $self->host . PHP_EOL . 'Password: raspberry';
  } else {
    echo 'ssh nemsadmin@nems.local' . PHP_EOL . 'Password: nemsadmin';
  }
  echo PHP_EOL . PHP_EOL . 'sudo nems-init';
?>
</pre>
