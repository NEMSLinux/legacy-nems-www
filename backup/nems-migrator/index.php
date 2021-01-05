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

  if (file_exists('/var/log/nems/nems-osb.json')) {
    $osb = json_decode(file_get_contents('/var/log/nems/nems-osb.json'));
    $filesizetotal = 0;
    if (is_array($osb)) foreach ($osb as $data) {
      $filesizetotal=($filesizetotal+$data->rawfilesize);
    } else {
      exit('Error. Either your OSB account is invalid, or our server is not responding.');
    }
  }

  if (file_exists('/var/www/html/backup/snapshot/size.log')) {
    $currentsize = intval(trim(file_get_contents('/var/www/html/backup/snapshot/size.log')));
  }

  $cloudauth = shell_exec('/usr/local/bin/nems-info cloudauth');

?>

<div class="container" style="margin-top: 100px; padding-bottom: 100px;">
  <h2><b>NEMS Migrator</b> Backup Service</h2>

<div class="row" style="background: #fff; margin: 0; padding: 20px;">

<form method="post" id="sky-form4" class="sky-form">

  <header>NEMS Migrator Local Backup</header>

  <div class="col-md-12 margin-bottom-40">
    <p style="color: #333 !important;">Your local backup is always available to you. You can even access it via Windows networking (samba) and automate your backup. See <a href="https://docs.nemslinux.com/en/latest/apps/nems-migrator.html" target="_blank" class="color-blue">the NEMS Migrator documentation</a> for more details.</p>
    <a href="/backup" target="_blank" class="btn-u btn-u-lg btn-brd-hover btn-u-green btn-u-block"><i class="fa fa-download"></i> DOWNLOAD<?php if (isset($currentsize) && $currentsize > 0) echo ' (' . formatBytes($currentsize) . ')'; ?></a>
  </div>

</form>

<?php
  if ( isset($osb) && is_array($osb) && count($osb) > 0 ) {
?>

<form method="post" id="sky-form4" class="sky-form">

<header>NEMS Migrator Off-Site Backup</header>

  <div class="col-md-4">
    <header>Most Recent Off-Site Backup</header>
    <fieldset>
        <section>
            <label class="label">Your most recent successful OSB:</label>
            <label class="input">
                <i class="icon-append fa fa-calendar"></i>
                <input disabled="disabled" type="text" name="domainuser" placeholder="<?= date('F j, Y',strtotime($osb[0]->filelocaltime)) ?>">
            </label>
        </section>
    </fieldset>

  </div>

  <div class="col-md-4">
    <header>Number Of Off-Site Backups</header>
    <fieldset>
        <section>
            <label class="label">Number of OSBs Currently Available:</label>
            <label class="input">
                <i class="icon-append fa fa-calculator"></i>
                <input disabled="disabled" type="text" name="domainuser" placeholder="<?= count($osb) ?>">
            </label>
        </section>
    </fieldset>

  </div>

  <div class="col-md-4">
    <header>Size Of OSB Account</header>
    <fieldset>
        <section>
            <label class="label">Total Cumulative Size of Your OSBs:</label>
            <label class="input">
                <i class="icon-append fa fa-hdd-o"></i>
                <input disabled="disabled" type="text" name="telegram_bot" placeholder="<?= formatBytes($filesizetotal) ?>">
            </label>
        </section>
    </fieldset>
  </div>

</form>



  <div id="calendar" class="col-md-12"></div>

        <!-- Fullcalendar -->
        <script src="/js/fullcalendar.min.js"></script>

<script>

  $(document).ready(function() {

    $('#calendar').fullCalendar({
      header: {
        left: 'title',
        center: '',
        right: 'prev,next today'
      },
      defaultDate: '<?= date('Y-m-d') ?>',
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      eventLimit: true, // allow "more" link when too many events
      events: [
        <?php
          if (is_array($osb)) {
            foreach ($osb as $backup) {
              echo "{title:'Backup: $backup->filesize',start:'$backup->filelocaltime'},";
            }
          }
        ?>
      ]
    });

  });

</script>

    <div class="call-action-v1 call-action-v1-boxed bg-color-primary margin-bottom-40">
      <div class="call-action-v1-box">
        <div class="call-action-v1-in">
          <p class="color-light" style="color: #eee !important;">To restore your NEMS Linux server to one of your OSBs, simply SSH in and run the command:<br /><em>sudo nems-restore osb</em></p>
        </div>
        <div class="call-action-v1-in inner-btn page-scroll">
          <a href="https://docs.nemslinux.com/en/latest/commands/nems-restore.html#how-to-restore-a-nems-migrator-backup" target="_blank" class="btn-u btn-u-lg btn-brd btn-brd-width-2 btn-brd-hover btn-u-light btn-u-block">DOCUMENTATION</a>
        </div>
      </div>
    </div>


<?php
  } elseif ($cloudauth == 1) {
?>

    <div class="call-action-v1 call-action-v1-boxed bg-color-blue margin-bottom-40">
      <div class="call-action-v1-box">
        <div class="call-action-v1-in">
          <h3 class="color-light" style="color: #fff;">NEMS Migrator Off-Site Backup Service</h3>
          <p class="color-light" style="color: #eee !important;">Your NEMS Cloud Services account is connected, however an OSB is not yet available. OSBs are created automatically. Please check back in 24 hours.</p>
        </div>
      </div>
    </div>

<?php
  } else {
?>

    <div class="call-action-v1 call-action-v1-boxed bg-color-blue margin-bottom-40">
      <div class="call-action-v1-box">
        <div class="call-action-v1-in">
          <h3 class="color-light" style="color: #fff;">Optional Off-Site Backup Service</h3>
          <p class="color-light" style="color: #eee !important;">NEMS Linux also offers an encrypted off-site backup service to those who choose to support the project on Patreon. It'll save your NEMS configuration every day and store it safely on our cloud server. It'll then show up here, and you can restore your NEMS Linux server to your off-site backup at any time (eg., in event of a failed SD card or upgrade). To sign up, you need to become a patron at a level that includes OSB.</p>
        </div>
        <div class="call-action-v1-in inner-btn page-scroll">
          <a href="https://www.patreon.com/bePatron?c=1348071&rid=2163022" target="_blank" class="btn-u btn-u-lg btn-brd btn-brd-width-2 btn-brd-hover btn-u-light btn-u-block">SIGN UP</a>
        </div>
      </div>
    </div>

    <div class="call-action-v1 call-action-v1-boxed bg-color-primary margin-bottom-40">
      <div class="call-action-v1-box">
        <div class="call-action-v1-in">
          <p class="color-light" style="color: #eee !important;">It's easy to restore your NEMS Linux server backup to replace a defective SD card or upgrade your NEMS server.</p>
        </div>
        <div class="call-action-v1-in inner-btn page-scroll">
          <a href="https://docs.nemslinux.com/en/latest/commands/nems-restore.html#how-to-restore-a-nems-migrator-backup" target="_blank" class="btn-u btn-u-lg btn-brd btn-brd-width-2 btn-brd-hover btn-u-light btn-u-block">DOCUMENTATION</a>
        </div>
      </div>
    </div>

<?php
  }
?>
</div>




</div>
<?php
  include('/var/www/html/inc/footer.php');
?>
