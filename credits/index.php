<?php
  if (file_exists('/var/www/html/inc/functions.php')) {
    include('/var/www/html/inc/functions.php');
    if (!initialized()) {
      include('../init.php');
      exit();
    }
    $platform = ver('platform');
  }

  if (file_exists('/var/log/nems/')) {
    include('/var/www/html/inc/header.php');
  } else {
    include('/var/www/nemslinux.com/html/inc/functions.php');
    include('/var/www/nemslinux.com/html/inc/header.php');
  }

?>
<script>
window.findKeyframesRule = function (rule) {
	// gather all stylesheets into an array
	var i, j, ss = document.styleSheets;
	// loop through the stylesheets
	for (i = 0; i < ss.length; ++i) {
	// loop through all the rules
		for (j = 0; j < ss[i].cssRules.length; ++j) {
			// find the keyframes rule whose name matches our passed over parameter and return that rule
			if (ss[i].cssRules[j].type == window.CSSRule.KEYFRAMES_RULE && ss[i].cssRules[j].name == rule) {
				return ss[i].cssRules[j];
			}
		}
	}
	// rule not found
	return null;
};
</script>
  <style>
    .scroller {
	position: absolute;
	top: 100%;
        width: 100%;
	margin: 0 auto;
	font: 300 30px/1 'Open Sans Condensed', sans-serif;
	font-family: 'Open Sans Condensed', sans-serif;
	font-size: 30px;
	line-height: 1;
	text-align: center;
	color: #fff;
	animation: 170s credits linear; /* Adjust length to control speed. Ideally about 26 seconds longer than the song. */
    }
    .scroller img {
      margin: 0 auto;
    }

    .fullscreen-static-image:after {
        content: '\A';
        position: absolute;
        width: 100%;
        height:100%;
        top:0;
        left:0;
        box-shadow: inset 0px 0px 600px black;
	animation: 140s fadeout forwards; /* Match seconds to the timing of the scroller and match to logo below. */
    }

    @keyframes credits {
	0% {
          top: 100%;
        }
	100% {
          top: -8000px; /* Ensure this is larger than the tallest possible screen */
        }
    }

    @keyframes fadeout { /* Do some fading with the background to really give that cinematic look */
	0% {
          background:rgba(0,0,0,0.5);
          opacity: .5;
        }
	20% {
          background:rgba(0,0,0,0.8);
          opacity: .8;
        }
	60% {
          background:rgba(0,0,0,0.8);
          opacity: .8;
        }
	100% {
          background:rgba(0,0,0,1);
          opacity: 1;
        }
    }
    #footsie {
      width: 100%;
    }
    #logo {
      position: absolute;
      top: 40%;
      width: 100%;
      animation: 140s logo forwards; /* Match to .fullscreen-static-image:after */
    }
    #logo img {
      margin: 0 auto;
      width: 300px;
    }
    @keyframes logo {
	0% {
          display: none;
          opacity: 0;
        }
        75% {
          display: block;
          opacity: 0;
        }
	80% {
          opacity: 1;
        }
    }

    body {
      overflow:hidden;
    }
    h1 {
      margin-top: 50px;
      font-size: 0.6em;
      color: #aaa !important;
    }
    h2 {
      font-size: 1em;
      color: #eee !important;
    }
    h4 {
      color: #FFFF00 !important;
      margin-top: 10px;
    }

  </style>

  <!-- Please note: This music is licensed to Robbie Ferguson for royalty free use within this project. -->
  <!-- The audio is streaming from Robbie's server and does not reside on your NEMS Server. -->
  <!-- While licensed for this project, you may not use the music elsewhere. -->
  <audio autoplay="autoplay" id="music">
    <source src="https://cdn.zecheriah.com/nems/audio/Movie-Score-Jeffrey-Peterson.ogg" type="audio/ogg">
    <source src="https://cdn.zecheriah.com/nems/audio/Movie-Score-Jeffrey-Peterson.mp3" type="audio/mpeg">
  </audio>
  <script>
    function aud_play(){
      music = document.getElementById("music");
      music.play();
      console.log('Initialized music playback.');
    }
    window.onload = aud_play();
    function aud_fade(){
      var timer,
      music = document.getElementById("music");
      if (music.volume > 0.0005) {
        music.volume -= 0.0005;
        timer = setTimeout(aud_fade,5);
        /* console.log(music.volume); */
      }
    }
    setTimeout(aud_fade, 105000); /* match to logo, - 35 seconds, * 1000 */
  </script>
  <div class="fullscreen-static-image fullheight">  </div>


    <div id="container">

      <div id="logo">
        <img src="/img/nems_logo.png" class="img-responsive" />
      </div>
      <div class="scroller">



            <img src="/img/nems_logo.png" class="img-responsive" style="max-height: 80px;" />
            <h1>Created By</h1><h2>Robbie Ferguson</h2><h3><a href="https://Category5.TV/" target="_blank">Category5.TV</a></h3>

            <div style="height: 400px;"></div>

            <h1><b>NEMS Linux could not exist without the open source efforts of countless developers.</b><br />The following projects are directly responsible for notable features of the NEMS architecture.</h1>

            <h1></h1><h2>Nagios Core</h2><h3><a href="https://nagios.org" target="_blank">nagios.org</a></h3>

            <h1></h1><h2>Debian Linux</h2><h3><a href="https://debian.org" target="_blank">debian.org</a></h3>

            <h1>Adagios</h1><h2>By Opin Kerfi</h2><h3><a href="https://adagios.org" target="_blank">adagios.org</a></h3>

            <h1>NConf</h1><h2>By Sunrise Communications AG</h2><h3><a href="https://nconf.org" target="_blank">nconf.org</a></h3>

            <h1>Monitorix</h1><h2>By Jordi Sanfeliu</h2><h3><a href="https://monitorix.org" target="_blank">monitorix.org</a></h3>

            <h1>Merlin Dashboard</h1><h2>By Mattias Bergsten</h2><h3><a href="https://github.com/fnordpojk" target="_blank">github.com/fnordpojk</a></h3>

            <h1>NagiosTV</h1><h2>By Chris Carey</h2><h3><a href="https://github.com/chriscareycode" target="_blank">github.com/chriscareycode</a></h3>

            <h1>Sponsored By</h1>
            <a href="https://www.rnitsolutions.com/" target="_blank"><img src="assets/rnit_logo_full_dark.png" class="img-responsive" style="max-height: 80px;" /></a></h1>

            <h1><b>I could not develop NEMS Linux without the financial support of its users.</b><br />The following <a href="https://patreon.com/nems" target="_blank">Patrons</a> have opted to have their name listed in the credits.</h1>
<?php
$list = explode(PHP_EOL, '
Patrick Kersten
Marc D&ouml;rseln
Dave Harman
Bill Marshall
Aaron Tringle
Steve Hudnall
IT Cyber Solutions
Natacha Norman
David Klindt
Wolfgang Friedl
Jeff Conaway
Don Jenkins
Marco Antonini
Jessica K. Litwin
Matthew Mattox
Premium | Fischer-ICT
Steve Thompson
Jiffy
Larry Getz
Coquille Indian Tribe
Jarrod Andrews
Dennis Bailey
Brian Darnell
SystemOfADL
Tony Browne
Steven Beukes
Rob Thomas
Heiko Gerstung
');
foreach ($list as $name) {
  if (trim(strlen($name)) > 0) {
    echo '<h4>' . trim($name) . '</h4>' . PHP_EOL;
  }
}
?>

            <h2 style="margin-top: 50px;">Platform Support</h2>
            <h3>Big thanks to the following for their contributions to open source, which<br /> helped me greatly in my quest to port NEMS Linux to many platforms.</h3>

            <h1>Raspberry Pi</h1><h2>Raspbian</h2>
            <h1>PINE64</h1><h2>Ayufan</h2>
            <h1>ODROID</h1><h2>Meveric, mad_ady</h2>
            <h1>ODROID, FriendlyElec, ASUS, OrangePi</h1><h2>Armbian</h2>

            <h2 style="margin-top: 50px;">Community Contributors</h2><h1 style="margin-top: 0;">Thank you to the following community members for going above and beyond.</h1>

            <h2 style="margin-top: 20px;">UltimateBugHunter-NitPicker</h2>
            <h2 style="margin-top: 20px;">bhammy187</h2>
            <h2 style="margin-top: 20px;">geek-dom</h2>
            <h2 style="margin-top: 20px;">mydogboris</h2>
            <h2 style="margin-top: 20px;">rkadmin</h2>
            <h2 style="margin-top: 20px;">Zerant</h2>
            <h2 style="margin-top: 20px;">JonBackhaus</h2>
            <h2 style="margin-top: 20px;">baggins</h2>
            <h2 style="margin-top: 20px;">Vincenzo Di Iorio</h2>

            <h1 style="margin-top: 20px;font-size: 0.5em;line-height: 1.1em;"><b>Note:</b> I only just recently started keeping a list.<br />If you don't see your name yet, it does not mean you are not appreciated.<br />Please message me on Discord.</h1>


            <h1>"Movie Score"</h1><h2>By Jeffrey Peterson</h2>
            <h1>"Epic Inspiration"</h1><h2>By Veaceslav Draganov</h2>
            <h3 style="margin-top:20px;">Licensed By Storyblocks<br /><span style="font-size: 0.6em;">All Rights Reserved. Not licensed for use outside this project.</span></h3>

            <h1>Nagios, the Nagios logo, and Nagios graphics<br />
                are the servicemarks, trademarks, or registered<br />
                trademarks owned by Nagios Enterprises. All<br />
                other servicemarks and trademarks are the<br />
                property of their respective owner.</h1>

            <h1 style="font-weight: bold; color: #fff;">Thank you for supporting NEMS Linux.</h1>

        </div>

    </div>

<?php
  if (file_exists('/var/log/nems/')) {
    include('/var/www/html/inc/footer.php');
  } else {
    include('/var/www/nemslinux.com/html/inc/footer.php');
  }
?>


