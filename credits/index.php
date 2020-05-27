<?php
  include('/var/www/html/inc/functions.php');
  if (!initialized()) {
    include('../init.php');
    exit();
  }
  if (ver('nems') < 1.3) {
    exit('Requires NEMS 1.3+');
  }

  if (file_exists('/var/log/nems/stats.log')) {
    $tmp=file('/var/log/nems/stats.log');
    if (is_array($tmp)) {
      $tmp = array_reverse($tmp);
      foreach ($tmp as $line) {
        if (substr($line,0,2) == 'a:') {
          $statlog = unserialize($line);
          if (isset($statlog['benchmarks']) && substr($statlog['benchmarks'],0,2) == '{"') {
            $tmp2=json_decode($statlog['benchmarks']);
            unset($statlog['benchmarks']);
            $statlog['benchmarks'] = $tmp2;
            unset($tmp2);
          }
          unset($tmp);
          break;
        }
      }
    }
  }

  include('/var/www/html/inc/header.php');

  $platform = ver('platform');

  $speedtestserver = intval(trim(shell_exec('/usr/local/bin/nems-info speedtest')));
  $speedtestwhich = trim(shell_exec('/usr/local/bin/nems-info speedtest which'));
  $speedtestlocation = trim(shell_exec('/usr/local/bin/nems-info speedtest location'));

?>
<script>
if (window.hasOwnProperty("storyFormat")) {
	// Change this to the path where your HTML file is located
	// if you want to run this from inside Twine.
	setup.Path = "C:/Games/YourGame/";  // Running inside Twine application
} else { 
	setup.Path = "";  // Running in a browser
}
setup.ImagePath = setup.Path + "images/";

$(document).on(':passagestart', function (ev) {
	if (passage() == "Credits") {
		$.wiki('<<addclass "#passages" "credits-style">><<addclass "body" "body-credits">>');
	} else {
		$.wiki('<<removeclass "#passages" "credits-style">><<removeclass "body" "body-credits">>');
	}
});

$(document).on(':passagedisplay', function (ev) {
	if (passage() == "Credits") {
		var keyframes = findKeyframesRule("credits");
		keyframes.deleteRule("100%");
		keyframes.appendRule("100% { top: " + ( ( $( ".wrapper" ).height() + 100 ) * -1 ) + "px; }");
	}
});

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
.credits-style {
	padding: 0;
	box-sizing: border-box;
	max-width: 100% !important;
}

.body-credits {
	height: 100vh;
	background: radial-gradient(circle at top center, #333 0%, #000 100%);
	overflow: hidden;
	background-position: 0px 0px;
}

.scroller {
	position: absolute;
	top: 100%;
	left: 50%;
/*	width: 600px;*/
	margin-left: -300px;
	font: 300 30px/1 'Open Sans Condensed', sans-serif;
	font-family: 'Open Sans Condensed', sans-serif;
	font-size: 30px;
	line-height: 1;
	text-align: center;
	color: #fff;
	animation: 40s credits linear;
}

.movie {
	margin-bottom: 50px;
	font-size: 50px;
}

.job {
	margin-bottom: 5px;
	font-size: 18px;
}

.name {
	margin-bottom: 40px;
	font-size: 35px;
}

.name-pic {
	margin-bottom: 70px;
	font-size: 35px;
}

@keyframes credits {
	0% { top: 100%; }
	100% { top: -2069px; }
}
    #container, body {
      overflow:hidden;
      text-align: center;
    }
    #container img {
      margin: 0 auto;
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
      color: yellow;
      margin-top: 10px;
    }
  </style>
  <audio src="bg.mp3" autoplay="autoplay"></audio>

    <div id="container">

      <div class="scroller">

            <img src="/img/nems_logo.png" class="img-responsive" style="max-height: 80px;" />
            <h1>Created By</h1><h2>Robbie Ferguson</h2><h3><a href="https://Category5.TV/" target="_blank">Category5.TV</a></h3>

            <h1><b>NEMS Linux could not exist without the open source efforts of countless developers.</b><br />The following projects are directly responsible for notable features of the NEMS architecture.</h1>

            <h1></h1><h2>Nagios Core</h2><h3><a href="https://nagios.org" target="_blank">nagios.org</a></h3>

            <h1></h1><h2>Debian Linux</h2><h3><a href="https://debian.org" target="_blank">debian.org</a></h3>

            <h1>Adagios</h1><h2>By Opin Kerfi</h2><h3><a href="https://adagios.org" target="_blank">adagios.org</a></h3>

            <h1>Monitorix</h1><h2>By Jordi Sanfeliu</h2><h3><a href="https://monitorix.org" target="_blank">monitorix.org</a></h3>

            <h1>NagiosTV</h1><h2>By Chris Carey</h2><h3><a href="https://github.com/chriscareycode" target="_blank">github.com/chriscareycode</a></h3>

            <h1>Sponsored By</h1>
            <a href="https://www.rnitsolutions.com/" target="_blank"><img src="assets/rnit_logo_full_dark.png" class="img-responsive" style="max-height: 80px;" /></a></h1>

            <h1><b>I could not develop NEMS Linux without the financial support of its users.</b><br />The following people have opted to have their name listed in the credits.</h1>
<table align="center">
<tr>
<?php
$list = explode(PHP_EOL, 'Patrick Kersten
Marc Dörseln
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
Dennis Bailey');
$count = 0;
foreach ($list as $name) {
  echo '<td align="center" style="padding: 4px 8px;"><h4>' . $name . '</h4></td>';
  $count++;
  if ($count == 3) {
    echo '</tr><tr>';
    $count = 0;
  }
}
?>
</table>

            <h1>Platform Support</h1>
            <h2>Big thanks to the following.</h2>
<table align="center">
<tr>
<?php
$list = explode(PHP_EOL, 'Raspbian
Bill');
$count = 0;
foreach ($list as $name) {
  echo '<td align="center" style="padding: 4px 8px;"><h4>' . $name . '</h4></td>';
  $count++;
  if ($count == 3) {
    echo '</tr><tr>';
    $count = 0;
  }
}
?>
</table>

            <h1>Nagios, the Nagios logo, and Nagios graphics<br />
                are the servicemarks, trademarks, or registered<br />
                trademarks owned by Nagios Enterprises. All<br />
                other servicemarks and trademarks are the<br />
                property of their respective owner.</h4>
        </div>

    </div>

<?php
  include('/var/www/html/inc/footer.php');
?>

