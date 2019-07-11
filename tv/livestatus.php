<?php 
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

$socket_path = "/usr/local/nagios/var/rw/live.sock";

$custom_filters = array(
  'host_name ~ ',
);

$in_notification_period = shell_exec('/usr/local/bin/nems-info tv_require_notify');
if ($in_notification_period != 1 && $in_notification_period != 2) $in_notification_period = 1; // use default setting if for some reason nems-info didn't provide the setting

function _print_duration($start_time, $end_time)
{
                $duration = $end_time - $start_time;
                $days = $duration / 86400;
                $hours = ($duration % 86400) / 3600;
                $minutes = ($duration % 3600) / 60;
                $seconds = ($duration % 60);
                $retval = sprintf("%dd %dh %dm %ds", $days, $hours, $minutes, $seconds);
		return($retval);
}

function sort_by_state($a, $b) {
   if ( $a[2] == $b[2] ) {
      if ( $a[0] > $b[0] ) {
         return 1;
      }
      else if ( $a[0] < $b[0] ) {
         return -1;
      }
      else {
         return 0;
      }
   }
   else if ( $a[2] > $b[2] ) {
      return -1;
   }
   else {
      return 1;
   }
}

function readSocket($len) {
    global $sock;
    $offset = 0;
    $socketData = '';
    
    while($offset < $len) {
        if(($data = @socket_read($sock, $len - $offset)) === false)
            return false;
    
        $dataLen = strlen ($data);
        $offset += $dataLen;
        $socketData .= $data;
        
        if($dataLen == 0)
            break;
    }
    
    return $socketData;
}

function queryLivestatus($query) {
    global $sock;
	global $socket_path;
	
    $sock = socket_create(AF_UNIX, SOCK_STREAM, 0);
    socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 10, 'usec' => 0));
    socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 10, 'usec' => 0));
    $result = socket_connect($sock, $socket_path);

    socket_write($sock, $query . "\n\n");

    $read = readSocket(16);

    if($read === false) {
	$init = shell_exec('/usr/local/bin/nems-info init');
	if ($init == 0) {
	  die("NEMS is not yet initilized. Please run: sudo nems-init");
	} else {
          die("Livestatus error: ".socket_strerror(socket_last_error($sock)));
	}
    }

    $status = substr($read, 0, 3);
    $len = intval(trim(substr($read, 4, 11)));

    $read = readSocket($len);
    
    if($read === false)
	die("Livestatus error: ".socket_strerror(socket_last_error($sock)));
    
    if($status != "200")
	die("Livestatus error: ".$read);
    
    if(socket_last_error($sock) == 104)
	die("Livestatus error: ".socket_strerror(socket_last_error($sock)));

    $result = socket_close($sock);
    
    return $read;

}

?>

<div class="dash_unhandled_hosts hosts dash">
    <h2>Unhandled host problems</h2>
    <div class="dash_wrapper">
        <table class="dash_table">

            <?php 

            $hosts = array();
            while ( list(, $filter) = each($custom_filters) ) {

if ($in_notification_period == 1) {
$query = <<<"EOQ"
GET hosts
Columns: host_name alias
Filter: $filter
Filter: scheduled_downtime_depth = 0
Filter: in_notification_period = 1
Filter: acknowledged = 0
Filter: host_acknowledged = 0
Filter: hard_state != 0
OutputFormat: json
ResponseHeader: fixed16
EOQ;
} else {
$query = <<<"EOQ"
GET hosts
Columns: host_name alias
Filter: $filter
Filter: scheduled_downtime_depth = 0
Filter: acknowledged = 0
Filter: host_acknowledged = 0
Filter: hard_state != 0
OutputFormat: json
ResponseHeader: fixed16
EOQ;
}
               $json=queryLivestatus($query);
               $tmp = json_decode($json, true);
               if ( count($tmp) ) {
                  $hosts = array_merge($hosts, $tmp);
               }
            }
            asort($hosts);

            $save = "";
            $output = "";
            while ( list(, $row) = each($hosts) ) {
                $output .=  "<tr class=\"critical\"><td>".$row[0]."</td><td>".$row[1]."</td></tr>";
                $save .= $row[0];
            }
            if($save):
            ?>
            <tr class="dash_table_head">
                <th>Hostname</th>
                <th>Alias</th>
            </tr>
            <?php print $output; ?>
            <?php
            else: 
                print "<tr class=\"ok\"><td>No hosts down or unacknowledged</td></tr>";
            endif;
            ?>
        </table>
    </div>
</div>

<div class="dash_tactical_overview tactical_overview hosts dash">
    <h2>Tactical overview</h2>
    <div class="dash_wrapper">
        <table class="dash_table">
            <tr class="dash_table_head">
                <th>Type</th>
                <th>Totals</th>
                <th>%</th>
            </tr>
            <?php 

            #### HOSTS
            $hosts_down = 0;
            $hosts_unreach = 0;
            $total_hosts = 0;

            reset($custom_filters);
            while ( list(, $filter) = each($custom_filters) ) {
$query = <<<"EOQ"
GET hosts
Filter: $filter
Stats: hard_state = 1
Stats: hard_state = 2
Stats: hard_state = 3
Stats: hard_state != 0
Stats: hard_state >= 0
OutputFormat: json
ResponseHeader: fixed16
EOQ;

               $json=queryLivestatus($query);
               $stats = json_decode($json, true);

               $hosts_down += $stats[0][0];
               $hosts_unreach += $stats[0][1];
               $total_hosts += $stats[0][4];
            }

            $hosts_down_pct = round($hosts_down / $total_hosts * 100, 2);
            $hosts_unreach_pct = round($hosts_unreach / $total_hosts * 100, 2);
            $hosts_up = $total_hosts - ($hosts_down + $hosts_unreach);
            $hosts_up_pct = round($hosts_up / $total_hosts * 100, 2);
            
            #### SERVICES

            $services_ok = 0;
            $services_critical = 0;
            $services_warning = 0;
            $services_unknown = 0;
            $services_not_ok = 0;
            $total_services = 0;

            reset($custom_filters);
            while ( list(, $filter) = each($custom_filters) ) {
$query = <<<"EOQ"
GET services
Filter: $filter
Filter: state_type = 1
Stats: state = 0
Stats: state = 1
Stats: state = 2
Stats: state = 3
Stats: state >= 1
Stats: state >= 0
OutputFormat: json
ResponseHeader: fixed16
EOQ;

               $json=queryLivestatus($query);
               $stats = json_decode($json, true);

               $services_ok += $stats[0][0];
               $services_warning += $stats[0][1];
               $services_critical += $stats[0][2];
               $services_unknown += $stats[0][3];
               $services_not_ok += $stats[0][4];
               $total_services += $stats[0][5];
            }

            $services_critical_pct = round($services_critical / $total_services * 100, 2);
            $services_warning_pct = round($services_warning / $total_services * 100, 2);
            $services_unknown_pct = round($services_unknown / $total_services * 100, 2);
            $services_ok_pct = round($services_ok / $total_services * 100, 2);
            
            ?>
            <tr class="ok total_hosts_up">
                <td>Hosts up</td>
                <td><?php print $hosts_up ?>/<?php print $total_hosts ?></td>
                <td><?php print $hosts_up_pct ?></td>
            </tr>
	    <?php if ($hosts_down > 0) {
		print "<tr class=\"critical total_hosts_down\">";
	       } else {
		print "<tr class=\"ok total_hosts_down\">";
	       };
	    ?>
                <td>Hosts down</td>
                <td><?php print $hosts_down ?>/<?php print $total_hosts ?></td>
                <td><?php print $hosts_down_pct ?></td>
            </tr>
	    <?php if ($hosts_unreach > 0) {
		print "<tr class=\"critical total_hosts_unreach\">";
	       } else {
		print "<tr class=\"ok total_hosts_unreach\">";
	       };
	    ?>
                <td>Hosts unreachable</td>
                <td><?php print $hosts_unreach ?>/<?php print $total_hosts ?></td>
                <td><?php print $hosts_unreach_pct ?></td>
            </tr>
            <tr class="ok total_services_ok">
                <td>Services OK</td>
                <td><?php print $services_ok ?>/<?php print $total_services ?></td>
                <td><?php print $services_ok_pct ?></td>
            </tr>
	    <?php if ($services_critical > 0) {
		print "<tr class=\"critical total_services_critical\">";
	       } else {
		print "<tr class=\"ok total_services_critical\">";
	       };
	    ?>
                <td>Services critical</td>
                <td><?php print $services_critical ?>/<?php print $total_services ?></td>
                <td><?php print $services_critical_pct ?></td>
            </tr>
	    <?php if ($services_warning > 0) {
		print "<tr class=\"warning total_services_warning\">";
	       } else {
		print "<tr class=\"ok total_services_warning\">";
	       };
	    ?>
                <td>Services warning</td>
                <td><?php print $services_warning ?>/<?php print $total_services ?></td>
                <td><?php print $services_warning_pct ?></td>
            </tr>
	    <?php if ($services_unknown > 0) {
		print "<tr class=\"unknown total_services_unknown\">";
	       } else {
		print "<tr class=\"ok total_services_unknown\">";
	       };
	    ?>
                <td>Services unknown</td>
                <td><?php print $services_unknown ?>/<?php print $total_services ?></td>
                <td><?php print $services_unknown_pct ?></td>
            </tr>
        </table>
    </div>
</div>
<div class="clear"></div>
<div class="dash_unhandled_service_problems hosts dash">
    <h2>Unhandled service problems</h2>
    <div class="dash_wrapper">
        <table class="dash_table">
            <?php 

            reset($custom_filters);
            $services = array();
            while ( list(, $filter) = each($custom_filters) ) {

if ($in_notification_period == 1) {
$query = <<<"EOQ"
GET services
Columns: host_name description state plugin_output last_hard_state_change last_check
Filter: $filter
Filter: scheduled_downtime_depth = 0
Filter: host_scheduled_downtime_depth = 0
Filter: service_scheduled_downtime_depth = 0
Filter: in_notification_period = 1
Filter: host_acknowledged = 0
Filter: acknowledged = 0
Filter: state != 0
Filter: state_type = 1
OutputFormat: json
ResponseHeader: fixed16
EOQ;
} else {
$query = <<<"EOQ"
GET services
Columns: host_name description state plugin_output last_hard_state_change last_check
Filter: $filter
Filter: scheduled_downtime_depth = 0
Filter: host_scheduled_downtime_depth = 0
Filter: service_scheduled_downtime_depth = 0
Filter: host_acknowledged = 0
Filter: acknowledged = 0
Filter: state != 0
Filter: state_type = 1
OutputFormat: json
ResponseHeader: fixed16
EOQ;
}
               $json=queryLivestatus($query);
               $tmp = json_decode($json, true);
               if ( count($tmp) ) {
                  $services = array_merge($services, $tmp);
               }
            }
            usort($services, "sort_by_state");

            $save = "";
            $output = "";
            while ( list(, $row) = each($services) ) {
                if ($row[2] == 2) {
                    $class = "critical";
                } elseif ($row[2] == 1) {
                    $class = "warning";
                } elseif ($row[2] == 3) {
                    $class = "unknown";
                }

		$duration = _print_duration($row[4], time());
		$date = date("Y-m-d H:i:s", $row[5]);

		$output .= "<tr class=\"".$class."\"><td>".$row[0]."</td><td>".$row[1]."</td>";
		$output .= "<td>".$row[3]."</td>";
		$output .= "<td class=\"date date_statechange\">".$duration."</td>";
		$output .= "<td class=\"date date_lastcheck\">".$date."</td></tr>\n";
		$save .= $row[0];
	    };

            if ($save):
            ?>
            <tr class="dash_table_head">
                <th>
                    Host
                </th>
                <th>
                    Service
                </th>
                <th>
                    Output
                </th>
                <th>
                    Duration
                </th>
                <th>
                    Last check
                </th>
            </tr>
            <?php print $output; ?>
            <?php
            else:
                print "<tr class=\"ok\"><td>No services in a problem state or unacknowledged</td></tr>";
            endif;
            ?>
        </table>
    </div>
</div>
</body>
</html>
