<?php
  require_once('./inc/functions.php');
?><h1>Not Initialized</h1>
You have not initialized NEMS yet. Please SSH to your NEMS server and run the <em>nems-init</em> command as follows:

<br /><br />
<pre>
ssh pi@<?= $self->host ?>

Password: raspberry

sudo nems-init
</pre>
