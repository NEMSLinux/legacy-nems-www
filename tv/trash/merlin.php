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

$con = mysql_connect("localhost", "merlin", "merlin") or die("<h3><font color=red>Could not connect to the Merlin database!</font></h3>");
$db = mysql_select_db("merlin", $con);

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

?>
<div class="dash_unhandled_hosts hosts dash">
    <h2>Unhandled host problems</h2>
    <div class="dash_wrapper">
        <table class="dash_table">
            <?php 
            $query = "select host_name, alias, count(host_name) from host where last_hard_state = 1 and problem_has_been_acknowledged = 0 and host_name not in (select distinct host_name from scheduled_downtime where start_time < unix_timestamp() and end_time > unix_timestamp() and downtime_type=2) group by host_name;";
            $result = mysql_query($query);
            $save = "";
            $output = "";
            while ($row = mysql_fetch_array($result)) {
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
            # number of hosts down
            $query = "select count(1) as count from host where last_hard_state = 1";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $hosts_down = $row[0];

            # number of hosts unreachable
            $query = "select count(1) as count from host where last_hard_state = 2";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $hosts_unreach = $row[0];
            
            # total number of hosts
            $query = "select count(1) as count from host";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $total_hosts = $row[0];
            
            $hosts_down_pct = round($hosts_down / $total_hosts * 100, 2);
            $hosts_unreach_pct = round($hosts_unreach / $total_hosts * 100, 2);
            $hosts_up = $total_hosts - ($hosts_down + $hosts_unreach);
            $hosts_up_pct = round($hosts_up / $total_hosts * 100, 2);
            
            #### SERVICES
            #
            # critical
            $query = "select count(1) as count from service where last_hard_state = 2";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $services_critical = $row[0];

			# warning
            $query = "select count(1) as count from service where last_hard_state = 1";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $services_warning = $row[0];
            
            # total number of hosts
            $query = "select count(1) as count from service";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $total_services = $row[0];
            
            $services_critical_pct = round($services_critical / $total_services * 100, 2);
            $services_warning_pct = round($services_warning / $total_services * 100, 2);
            $services_ok = $total_services - ($services_critical + $services_warning);
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
        </table>
    </div>
</div>
<div class="clear"></div>
<div class="dash_unhandled_service_problems hosts dash">
    <h2>Unhandled service problems</h2>
    <div class="dash_wrapper">
        <table class="dash_table">
            <?php 
            $query = "select service.host_name,service.service_description,service.last_hard_state,service.output,service.last_hard_state_change,service.last_check from service,host where host.host_name = service.host_name and service.last_hard_state in (1,2,3) and service.problem_has_been_acknowledged = 0 and service.host_name not in (select distinct host_name from scheduled_downtime where start_time < unix_timestamp() and end_time > unix_timestamp() and downtime_type=2) and service.service_description not in (select distinct service_description from scheduled_downtime where host_name = service.host_name and start_time < unix_timestamp() and end_time > unix_timestamp() and downtime_type=1) order by service.last_hard_state;";

            $result = mysql_query($query);

            $save = "";
            $output = "";

            while ($row = mysql_fetch_array($result)) {
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
