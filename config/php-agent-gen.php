<?php
$php_agent_key = '';
$nemsconffile = '/usr/local/share/nems/nems.conf'; // www-admin must have access to read/write
$conf = file($nemsconffile);
if (is_array($conf)) { // Load the existing conf data
        foreach ($conf as $line) {
                $tmp = explode('=',$line);
                if (trim($tmp[0]) == 'php_agent_key') $php_agent_key = trim($tmp[1]);
        }
}

if ($php_agent_key == '') die('Missing passphrase. Did you set one in NEMS SST?');

$nemsver = shell_exec('/usr/local/bin/nems-info nemsver');
$nemsagentver = '1.3';

$data = '<' . '?php
  // This is the NEMS PHP Server Agent v' . $nemsagentver . '
  // Used by NEMS Server to safely monitor things like disk space and memory usage on a PHP server
  // NEMS Linux is a product of The Category5 TV Network and is developed by Robbie Ferguson
  // https://nemslinux.com/

  // If you change your NEMS PHP Server Agent Encryption/Decryption Key in NEMS System Settings Tool, this will no longer match
  // You can either edit it here base64 encoded, or just download the new agent (recommended) and re-upload to your server
  $encryptionkey = \'' . openssl_encrypt($php_agent_key,"AES-128-ECB",base64_encode(':' . $php_agent_key . ':')) . '\';

  if (isset($_POST[\'check\'])) {
    $check = filter_var($_POST[\'check\'], FILTER_SANITIZE_STRING);
  } else {
    $check = "";
  }

  $data = array();

  // CPU Data
  if ($check == \'load\') {
    $cores = shell_exec("/usr/bin/nproc --all");
    $prevVal = shell_exec("cat /proc/stat");
    $prevArr = explode(\' \',trim($prevVal));
    $prevTotal = $prevArr[2] + $prevArr[3] + $prevArr[4] + $prevArr[5];
    $prevIdle = $prevArr[5];
    usleep(intval(floor(0.15 * 1000000)));
    $val = shell_exec("cat /proc/stat");
    $arr = explode(\' \', trim($val));
    $total = $arr[2] + $arr[3] + $arr[4] + $arr[5];
    $idle = $arr[5];
    $intervalTotal = intval($total - $prevTotal);
    $data[\'cpu\'][\'usage\'] =  intval(100 * (($intervalTotal - ($idle - $prevIdle)) / $intervalTotal));
    $cpu_result = shell_exec("cat /proc/cpuinfo | grep model\ name");
    $data[\'cpu\'][\'model\'] = strstr($cpu_result, "\n", true);
    $data[\'cpu\'][\'model\'] = trim(str_replace("model name\t:", "", $data[\'cpu\'][\'model\']));
    $tmp = sys_getloadavg();
    $data[\'cpu\'][\'loadaverage\'][1] = $tmp[0];
    $data[\'cpu\'][\'loadaverage\'][5] = $tmp[1];
    $data[\'cpu\'][\'loadaverage\'][15] = $tmp[2];
    $data[\'cpu\'][\'cores\'] = $cores;
  }

  //memory stat
  if ($check == \'mem\') {
    $data[\'mem\'][\'percent\'] = round(shell_exec("free | grep Mem | awk \'{print $3/$2 * 100.0}\'"), 2);
    $mem_result = shell_exec("cat /proc/meminfo | grep MemTotal");
    $data[\'mem\'][\'total\'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
    $mem_result = shell_exec("cat /proc/meminfo | grep MemFree");
    $data[\'mem\'][\'free\'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
    $data[\'mem\'][\'used\'] = $data[\'mem\'][\'total\'] - $data[\'mem\'][\'free\'];
  }

  //hdd stat
  if ($check == \'.\') {
    $mountpoint = \'.\';
    $df = disk_free_space($mountpoint);
    $dt = disk_total_space($mountpoint);
    if ($df == \'\') {
      $data[\'storage\'][$mountpoint][\'locked\'] = 1;
    } else {
      $data[\'storage\'][$mountpoint][\'path\'] = getcwd();
      $data[\'storage\'][$mountpoint][\'free\'] = round($df / 1024 / 1024 / 1024, 2);
      $data[\'storage\'][$mountpoint][\'total\'] = round($dt / 1024 / 1024/ 1024, 2);
      $data[\'storage\'][$mountpoint][\'used\'] = $data[\'storage\'][$mountpoint][\'total\'] - $data[\'storage\'][$mountpoint][\'free\'];
      $data[\'storage\'][$mountpoint][\'percent\'] = round(sprintf(\'%.2f\',($data[\'storage\'][$mountpoint][\'used\'] / $data[\'storage\'][$mountpoint][\'total\']) * 100), 2);
    }
  }

  if ($check == \'/\') {
    $mountpoint = \'/\';
    $df = disk_free_space($mountpoint);
    $dt = disk_total_space($mountpoint);
    if ($df == \'\') {
      $data[\'storage\'][$mountpoint][\'locked\'] = 1;
    } else {
      $data[\'storage\'][$mountpoint][\'free\'] = round($df / 1024 / 1024 / 1024, 2);
      $data[\'storage\'][$mountpoint][\'total\'] = round($dt / 1024 / 1024/ 1024, 2);
      $data[\'storage\'][$mountpoint][\'used\'] = $data[\'storage\'][$mountpoint][\'total\'] - $data[\'storage\'][$mountpoint][\'free\'];
      $data[\'storage\'][$mountpoint][\'percent\'] = round(sprintf(\'%.2f\',($data[\'storage\'][$mountpoint][\'used\'] / $data[\'storage\'][$mountpoint][\'total\']) * 100), 2);
    }
  }

  if ($check == \'var\') {
    $mountpoint = \'/var\';
    $df = disk_free_space($mountpoint);
    $dt = disk_total_space($mountpoint);
    if ($df == \'\') {
      $data[\'storage\'][$mountpoint][\'locked\'] = 1;
    } else {
      $data[\'storage\'][$mountpoint][\'free\'] = round($df / 1024 / 1024 / 1024, 2);
      $data[\'storage\'][$mountpoint][\'total\'] = round($dt / 1024 / 1024/ 1024, 2);
      $data[\'storage\'][$mountpoint][\'used\'] = $data[\'storage\'][$mountpoint][\'total\'] - $data[\'storage\'][$mountpoint][\'free\'];
      $data[\'storage\'][$mountpoint][\'percent\'] = round(sprintf(\'%.2f\',($data[\'storage\'][$mountpoint][\'used\'] / $data[\'storage\'][$mountpoint][\'total\']) * 100), 2);
    }
  }

  //network stat
  if ($check == \'net\' || $check == \'netrx\' || $check == \'nettx\') {
    $ifstat = trim(shell_exec(\'which ifstat\'));
    if (strlen($ifstat) > 0) {
      // Obtain output from ifstat
      $seconds = 5; // how many seconds to average (affects how long the check takes)
      $network = shell_exec("$ifstat -T $seconds 1");
      // Convert ifstat output to an array
      $networkARR = array_values(array_filter(explode(PHP_EOL,$network)));
      // Trim the array values
      foreach ($networkARR as $key => $line) {
        $networkARR[$key] = trim($line);
      }
      // Parse it into a clean array
      // array_values resets the key to sequential after array_filter removes empty elements
      $interfaces = array_values(array_filter(explode(\'  \',$networkARR[0])));
      $headings = array_values(array_filter(explode(\'  \',$networkARR[1])));
      $values = array_values(array_filter(explode(\'  \',$networkARR[2])));
      $counter=0;
      // 0.008 multiplier to convert the KB/s output to Mb/s
      foreach ($interfaces as $key => $intname) {
        $data[\'network\'][strtolower(trim($intname))][\'rx\'] = round((trim($values[$counter++])*0.008),2);
        $data[\'network\'][strtolower(trim($intname))][\'tx\'] = round((trim($values[$counter++])*0.008),2);
      }
    } else {
      $data[\'network\'][\'none\'][\'rx\'] = 0;
      $data[\'network\'][\'none\'][\'tx\'] = 0;
    }
  }

  //output headers
    header(\'Content-type: text/json\');
    header(\'Content-type: application/json\');

  // Convert data to json for ingestion to NEMS Server
    $dataJSON = json_encode($data);

  // Create the output
    $output[\'ver\'][\'nems\'] = \'' . $nemsver . '\';
    $output[\'ver\'][\'nemsagent\'] = \'' . $nemsagentver . '\';
    $output[\'data\'] = openssl_encrypt($dataJSON,"AES-128-ECB",$encryptionkey);
    $output[\'auth\'] = hash(\'sha256\', $encryptionkey); // Used to test to make sure the decryption key is correct

  // Output the output
    echo json_encode($output);
';

header('Content-Disposition: attachment; filename="nems-agent.php"');
header('Content-Type: text/plain');
header('Content-Length: ' . strlen($data));
header('Connection: close');
echo $data;

// Could iterate through the /sys/class/net folder if ifstat isn't installed... does not seem as accurate though.
//    $data[\'network\'][\'rx\'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/rx_bytes")) / 1024/ 1024/ 1024, 2);
//    $data[\'network\'][\'tx\'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/tx_bytes")) / 1024/ 1024/ 1024, 2);

?>
