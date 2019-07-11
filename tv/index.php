<?php
  require_once('/var/www/html/inc/bgcolor.php');
# The nagios-dashboard was written by Morten Bekkelund & Jonas G. Drange in 2010
#
# Patched, modified and added to by various people, see README
# Maintained as merlin-dashboard by Mattias Bergsten <mattias.bergsten@op5.com>
#
# Parts copyright (C) 2010 Morten Bekkelund & Jonas G. Drange
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# See: http://www.gnu.org/copyleft/gpl.html

    $refreshvalue = 30; //value in seconds to refresh data
    $pagetitle = "NEMS TV Dashboard";

    $nemsalias = trim(shell_exec('/usr/local/bin/nems-info alias'));
    if (isset($nemsalias) && strtoupper($nemsalias) != 'NEMS') {
       $pagetitle = 'NEMS TV: ' . $nemsalias;
    }


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<link type="image/ico" rel="icon" href="favicon.ico" />
        <title><?php echo($pagetitle); ?></title>
        <script src="/js/jquery.min.js"></script>
        <script src="/js/loadingoverlay.min.js"></script>

        </script>
		<link rel="stylesheet" type="text/css" href="nagios.css" />
        <style>
          .nagios_statusbar {
            background: rgba(<?= $bgcolorDarkRGB[0] ?>,<?= $bgcolorDarkRGB[1] ?>,<?= $bgcolorDarkRGB[2] ?>,.8) !important;
          }
        </style>
    </head>
    <body>
	
        <script type="text/javascript">

            var placeHolder,
            refreshValue = <?php print $refreshvalue; ?>;
            
            $().ready(function(){
                placeHolder = $("#nagios_placeholder");
                updateNagiosData(placeHolder);
                window.setInterval(updateCountDown, 1000);
            });
            
            
            
            // timestamp stuff
            
            function createTimeStamp() {
                <?php
                  $tv_24h = intval(trim(shell_exec('/usr/local/bin/nems-info tv_24h')));
                ?>
                // create timestamp
                var ts = new Date();
                // see if the user has set 24 hour or 12 hour format
		var options = { <?php
                  if ($tv_24h == 1) echo 'hour12: false';
                ?> };
		ts = ts.toLocaleTimeString(navigator.language, {hour: '2-digit', minute:'2-digit'}, options)<?php if ($tv_24h != 2) echo '.replace(/(:\d{2}| [AP]M)$/, "")'; ?>;
                $("#timestamp_wrap").empty().append("<div class=\"timestamp_drop\"></div><div class=\"timestamp_stamp\">" + ts +"</div>");
            }
            
            function updateNagiosData(block){
                $("#loading").fadeIn(200);
    		block.load("./livestatus.php", function(response){
                    $(this).html(response);
                    $("#loading").fadeOut(200);
                    createTimeStamp();
                });
            }
            
            function updateCountDown(){
                var countdown = $("#refreshing_countdown"); 
                var remaining = parseInt(countdown.text());
                if(remaining == 1){
                    updateNagiosData(placeHolder);
                    countdown.text(remaining - 1);
                }
                else if(remaining == 0){
                    countdown.text(refreshValue);
                }
                else {
                    countdown.text(remaining - 1);
                }
            }

            function refreshAt(hours, minutes, seconds) {
              var now = new Date();
              var then = new Date();

              if ( now.getHours() > hours || (now.getHours() == hours && now.getMinutes() > minutes) || now.getHours() == hours && now.getMinutes() == minutes && now.getSeconds() >= seconds ) {
                then.setDate(now.getDate() + 1);
              }
              then.setHours(hours);
              then.setMinutes(minutes);
              then.setSeconds(seconds);

              var timeout = (then.getTime() - now.getTime());
              setTimeout(function() { window.location.reload(true); }, timeout);
            }

            // force a page reload at 4am every day (for those who leave the TV Dashboard open 24/7 to see the day's background)
            refreshAt(4,0,0);

        </script>
	<div id="nagios_placeholder"></div>
    <div class="nagios_statusbar">
	<div class="nagios_statusbar_logo">
        	<p id="logo_holder"><span id="logo"></span></p>
		</div>
        <div class="nagios_statusbar_item">
            <div id="timestamp_wrap"></div>
        </div>
        <div class="nagios_statusbar_item">
            <div id="loading"></div>
            <p id="refreshing"><span id="refreshing_countdown"><?php print $refreshvalue; ?></span> seconds to next check</p>
        </div>
    </div>

    <script src="/js/jquery.backstretch.min.js"></script>

   <?php
     $backgroundElem = 'body';
     require_once('/var/www/html/inc/wallpaper.php');
   ?>

<script>
  jQuery(document).ready(function() {

    $(function() {
      var timer;
      var fadeInBuffer = false;
      $(document).mousemove(function() {
        if (!fadeInBuffer) {
            if (timer) {
                clearTimeout(timer);
                timer = 0;
            }

            $('html').css({
                cursor: '',
                overflow: 'auto'
            });
        } else {
            $('body').css({
                cursor: 'default',
                overflow: 'auto'
            });
            fadeInBuffer = false;
        }


        timer = setTimeout(function() {
            $('body').css({
                cursor: 'none',
                overflow: 'hidden'
            });
            $('html').css({
                overflow: 'hidden'
            });

            fadeInBuffer = true;
        }, 5000)
      });
      $('body').css({
        cursor: 'default',
        overflow: 'auto'
      });
      $('html').css({
        overflow: 'auto'
      });
    });


    check_connect();

  });

    function check_connect() {
    $.ajax({
      type: 'GET',
      url: '/tv/',
      timeout: 5000,  // allow this many milisecs for network connect to succeed
      success: function(data) {
        // we have a connection
        $.LoadingOverlay("hide");
        window.setTimeout(check_connect, 15000)  // try again after 15 minutes
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        // no connection, refresh every 15 seconds until resolved
        $.LoadingOverlay("show");
        window.setTimeout(check_connect, 15000) 
      }
      });
    };

</script>

    </body>
</html>
