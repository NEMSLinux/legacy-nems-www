<?php 
// functions.inc.php  
// utility and logic functions for nagios mobile
// @author Hirose Masaaki
// @author Mike Guthrie
// @author Wesley Zhao


// Nagios Mobile 1.0
// Copyright (c) 2011 Nagios Enterprises, LLC
// Web: http://www.nagios.com/products/nagiosmobile
// Developed by Mike Guthrie and Wesley Zhao.  
// Based on Teeny Nagios by HIROSE Masaaki. 
// Teeny Nagios is published under the apache license:
// http://www.apache.org/licenses/LICENSE-2.0.html 
// ////////////////////Apache License//////////////////// 
/*
  Copyright [2011] [HIROSE Masaaki]

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/ 


// LICENSE:
//
// This work is made available to you under the terms of Version 2 of
// the GNU General Public License. A copy of that license should have
// been provided with this software, but in any event can be obtained
// from http://www.fsf.org.
// 
// This work is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
// 02110-1301 or visit their web page on the internet at
// http://www.fsf.org.
//
//
// CONTRIBUTION POLICY:
//
// (The following paragraph is not intended to limit the rights granted
// to you to modify and distribute this software under the terms of
// licenses that may apply to the software.)
//
// Contributions to this software are subject to your understanding and acceptance of
// the terms and conditions of the Nagios Contributor Agreement, which can be found 
// online at:
//
// http://www.nagios.com/legal/contributoragreement/
//
//
// DISCLAIMER:
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
// INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
// PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
// HOLDERS BE LIABLE FOR ANY CLAIM FOR DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
// OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
// GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, STRICT LIABILITY, TORT (INCLUDING 
// NEGLIGENCE OR OTHERWISE) OR OTHER ACTION, ARISING FROM, OUT OF OR IN CONNECTION 
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


//////////////////COMMAND SUBMISSIONS ////////////////////
/*
*	@author Mike Guthrie
*/
function submit_command($cmd)
{
	global $COMMAND_FILE;
	global $username;
	
	$service = grab_request_var('service',''); 
	$host = grab_request_var('host',false); 
	
	if(!$host) die("Illegal operation. No host defined!"); 
	//auth check 
	if(!is_authorized_for_command($host,$service)) die("You are not authorized to access commands for this object.");

	switch($cmd)
	{	
		case "schedule_downtime":
			$comment = grab_request_var('comment','In scheduled downtime'); 
			$end_time_str = grab_request_var("end_date")." ".grab_request_var("end_time");
			
			if ($service=="" ) 
		        $command_elm = array("SCHEDULE_HOST_DOWNTIME", $host );
		    else
		        $command_elm = array("SCHEDULE_SVC_DOWNTIME", $host, $service );
				    
		    $end_time = strtotime($end_time_str);		
		    $command_elm = array_merge($command_elm, array(time(), $end_time, 1, 0, 0, $username, $comment)  );				
		break;
		
		case "acknowledge_problem":		
			$comment = grab_request_var('comment','In scheduled downtime'); 			
		    if ($service=="" ) 
		        $command_elm = array("ACKNOWLEDGE_HOST_PROBLEM", urldecode($host));
		    else 
		        $command_elm = array("ACKNOWLEDGE_SVC_PROBLEM", urldecode($host), urldecode($service));
		
		    $command_elm = array_merge($command_elm, array( 1, 1, 1, $username, urldecode($comment) ) );		
		break;
		
		case "disable_notification":
			if ($service=="" ) 
		        $command_elm = array("DISABLE_HOST_NOTIFICATIONS", urldecode($host));
		    else 
		        $command_elm = array("DISABLE_SVC_NOTIFICATIONS", urldecode($host), urldecode($service));				
		break;
		
		case "enable_notification": 
			if ($service=="" ) 
		        $command_elm = array("ENABLE_HOST_NOTIFICATIONS", urldecode($host));
		    else 
		        $command_elm = array("ENABLE_SVC_NOTIFICATIONS", urldecode($host), urldecode($service));		
		break; 
		
		case "remove_acknowledgement":
		  	if ($service=="" )
        		$command_elm = array("REMOVE_HOST_ACKNOWLEDGEMENT", urldecode($host) );
    		else
        		$command_elm = array("REMOVE_SVC_ACKNOWLEDGEMENT",  urldecode($host), urldecode($service) );			
		break;	
		
		default:
			die("Unknown command!");
	
	}
	
    $command = sprintf("[%lu] ",time()).implode(";", $command_elm);
    
    $fh = @fopen($COMMAND_FILE, "w");
    if (!$fh) {
        trigger_error("unable to open file ($COMMAND_FILE):", E_USER_WARNING);
        header("HTTP/1.0 500 Internal Server Error");
        print "{\"result\":false}\n";
        return;
    }
    header("Content-Type: application/json; charset=UTF-8");
    fwrite($fh, $command."\n");
    fclose($fh);

    print "{\"result\":true}\n";	

}


/*
* 	@author Hirose Masaaki
*/
function calc_global_stats($status) 
{
    $global_stats = array(
        "host_up"          => 0,
        "host_down"        => 0,
        "host_unreachable" => 0,
		"host_pending"	   => 0,
		"host_unhandled"   => 0,
		"host_problems"    => 0,
		"host_acknowledged"=> 0,
        "service_ok"       => 0,
        "service_warning"  => 0,
        "service_critical" => 0,
        "service_unknown"  => 0,
		"service_pending"     => 0,
		"service_unhandled"   => 0,
		"service_problems"    => 0,
		"service_acknowledged"=> 0,
        );

    while (list($host,$host_status) = each($status["hosts"])) 
    {
		//handle fix for users who are auth to see services but not the host 
		if(isset($host_status['current_state']) ) 
		{
		
			switch ($host_status["current_state"]) 
			{
				case HOST_UP:
					$global_stats["host_up"]++;
					break;
				case HOST_DOWN:
					$global_stats["host_down"]++;
					break;
				case HOST_UNREACHABLE:
					$global_stats["host_unreachable"]++;
					break;
			}
			$global_stats["host_pending"]+=(1-$host_status["has_been_checked"]);
			if($host_status["current_state"]>0 && $host_status["scheduled_downtime_depth"]==0 
			   && $host_status["problem_has_been_acknowledged"]==0) 
				$global_stats["host_unhandled"]++;
				
			$global_stats["host_problems"] = $global_stats["host_down"]+$global_stats["host_unreachable"];
			
			if($host_status["current_state"] > 0 && ($host_status["scheduled_downtime_depth"] > 1 || $host_status["problem_has_been_acknowledged"]>0))
				$global_stats["host_acknowledged"]++;
		}
		
		//handle services under host 
        if (isset($host_status["services"])) {
            while (list($service_desc,$service_status) = each($host_status["services"])) 
			{
                switch ($service_status["current_state"]) 
				{
					case STATE_OK:
						$global_stats["service_ok"]++;
						break;
					case STATE_WARNING:
						$global_stats["service_warning"]++;
						break;
					case STATE_CRITICAL:
						$global_stats["service_critical"]++;
						break;
					case STATE_UNKNOWN:
						$global_stats["service_unknown"]++;
						break;
                }
				$global_stats["service_pending"]+= (1-$service_status["has_been_checked"]);
				if($service_status["current_state"]>0 && $service_status["scheduled_downtime_depth"]==0 && $service_status["problem_has_been_acknowledged"]==0)
					$global_stats["service_unhandled"]++;
					
				$global_stats["service_problems"] = $global_stats["service_warning"] + 
													$global_stats["service_critical"] + 
													$global_stats["service_unknown"];
													
				if($service_status["current_state"] > 0 && ($service_status["scheduled_downtime_depth"] > 1 || $service_status["problem_has_been_acknowledged"]>1)) 
					$global_stats["service_acknowledged"] ++;
            }
        }


    }

    return $global_stats;
}


/*
* 	@author Hirose Masaaki
*/
function get_time_breakdown($status, $a_status) 
{
    $duration = array(
        'days'    => 0,
        'hours'   => 0,
        'minutes' => 0,
        'seconds' => 0,
        );
    $time = time();
    
    
	//dump($status);
	//print "PRINTR: "; 
	//dump($a_status);     
    
    if ($a_status["last_state_change"] == 0) {
        $time -= $status["program"]["program_start"];
    } else {
        $time -= $a_status["last_state_change"];
    }

    $duration["days"] = intval($time/86400);
    $time -= $duration["days"]*86400;
    $duration["hours"] = intval($time/3600);
    $time -= $duration["hours"]*3600;
    $duration["minutes"] = intval($time/60);
    $time -= $duration["minutes"]*60;
    $duration["seconds"] = intval($time);

    return $duration;
}


/*
* 	@author Hirose Masaaki
*/
function stringize_duration($duration) 
{
    return sprintf("%dd %dh %dm %ds", $duration["days"], $duration["hours"], $duration["minutes"], $duration["seconds"]);
}


/*
* 	@author Hirose Masaaki
*/
function class_by_state($value,$service=false) 
{

	if($value==CHILD_PROBLEM) return 'hostproblem';

	if($service)
	{
	    switch ($value) 
	    {
		    case STATE_WARNING:
		        return "warning";

		    case STATE_CRITICAL:
		        return "critical";

		    case STATE_UNKNOWN:
		        return "unknown";

		    default:
		    case STATE_OK:
		        return "";
		}
    }
    else {	
    	switch($value) {
		    case HOST_DOWN:
		        return "down";
		
		    case HOST_UNREACHABLE:
		        return "unreachable";
		
		    default:
		    case HOST_UP:
		        return "";   
	        }
    }
}


/*
* 	@author Hirose Masaaki
*/
function service_state_of($services) 
{
    $state = STATE_OK;
    foreach ($services as $service) {
    	if($service['current_state'] > 1) return CHILD_PROBLEM;
    	if($service['current_state'] == 1) $state = STATE_WARNING;  
    }
    return $state;
}

/* 	
*	@author Ethan Galstad
*	function modified from Ethan Galstad's original 'grab_request_var' function Nagios XI project
*	basic cleaner function for request variables 
*/
function grab_request_var($varname,$default="")
{	
	$v=$default;
	if(isset($_REQUEST[$varname])) 
	{
		if(is_array($_REQUEST[$varname]))
		{
			@array_walk($_REQUEST[$varname],'htmlentities',ENT_QUOTES); 
			$v=$request[$varname];
		}
		else
			$v=htmlentities($_REQUEST[$varname],ENT_QUOTES);


	}
	//echo "VAR $varname = $v<BR>";
	return $v;
	}

	
/*
*	@author Mike Guthrie
*/	
function dump($array)
{

	print "<pre>".print_r($array,true)."</pre>"; 

}


//authorized for host??
/*
*	@author Mike Guthrie
*/
function is_authorized_for_host($host)
{
	global $NagiosUser;
	$bool = $NagiosUser->is_authorized_for_host($host);
	return $bool; 
}

//authorized for service?
/*
*	@author Mike Guthrie
*/
function is_authorized_for_service($host,$service)
{
	global $NagiosUser; 
	$bool = $NagiosUser->is_authorized_for_service($host,$service);
	return $bool;		
}



//authorized for host/service commands? 
/*
*	@author Mike Guthrie
*/
function is_authorized_for_command($host,$service=false)
{
	global $NagiosUser;
	
	if($service) {
		if(is_authorized_for_service($host,$service) && $NagiosUser->if_has_authKey('authorized_for_read_only')!=true)
				return true;								
	}
	else {
		if(is_authorized_for_host($host) && $NagiosUser->if_has_authKey('authorized_for_read_only')!=true)
				return true;		
	}	
	
	return false;
					
}	
	



?>
