<?php  
//displayfunctions.inc.php  
//"view" functions for nagios mobile html display 
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

/*
*	@author Hirose Masaaki
*	@author Mike Guthrie
*	@author Wesley Zhao
*	modified from original page html and put into dynamic PHP function 
*/
function host_details($hostname)
{
	global $status; 
	global $HOST_STATUS_BY;
	global $BASE_URL;

	$content = ''; 
	
	foreach ($status["hosts"] as $host_id => $host_status) 
	{
		if($host_status['host_name']!=$hostname) continue; 	
	
		$host = $host_status["host_name"];
		$ddclass = class_by_state($host_status['current_state']); 		
		$textstate = $HOST_STATUS_BY[ $host_status['current_state'] ]; 
		$duration = stringize_duration(get_time_breakdown($status, $host_status)); 
		$notifs = $host_status["notifications_enabled"] == 1 ? "Enabled" : "Disabled"; 
		$dt = $host_status["scheduled_downtime_depth"] > 0 ? "Yes" : "No"; 
		$ack = $host_status["problem_has_been_acknowledged"] > 0 ? "Yes" : "No"; 
		
		$content .=' 
		<!-- host details ============================================================ -->
		<div data-role="page" id="'.$host_id.'" data-theme="a">
		  <div data-role="header" data-backbtn="true">
		  <!-- back button -->
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a>
		  
			<h1>'.htmlentities($host).'</h1>
			<a href="index.php" alax-load="false" data-icon="home" data-iconpos="notext"  data-direction="reverse" class="ui-btn-right">Home</a>
		  </div>
			<ul data-role="listview" data-inset="false" data-theme="f" data-dividertheme="a">
			<li data-role="list-divider" role="heading">Information
				<div class="information rounded">
				  <dl>
				  	<dt>Host: </dt><dd>'.htmlentities($host).'</dd>
					<dt>Status</dt>
					<dd class="'.$ddclass.'">'.$textstate.' (for '.$duration.')</dd>
					<dt>Status Information</dt>
					<dd>'.$host_status["plugin_output"].'</dd>
					<dt>Last Check Time</dt>
					<dd>'.date("c",$host_status["last_check"]).'</dd>
					<dt>Last Update</dt>
					<dd>'.date("c",$host_status["last_update"]).'</dd>
					<dt>Notifications</dt>
					<dd>'.$notifs.'</dd>
					<dt>Scheduled Downtime</dt>
					<dd>'.$dt.'</dd>
					<dt>Acknowledged</dt>
					<dd>'.$ack.'</dd>
				  </dl>
				</div>
			</li>'; 
			
		if(is_authorized_for_command($host)) {		
			$content.='	
				<li data-role="list-divider" role="heading">Commands</li>
				<li><a ajax-load="false" href="'.$BASE_URL.'?page=downtime&amp;host='.urlencode($host).'">Schedule Downtime</a></li>'; //removed data-transition="flip"
				    
			if(($host_status["current_state"] )> 0 && $host_status["problem_has_been_acknowledged"] ==0)
				  $content .=
				"<li><a ajax-load='false' href='".$BASE_URL."?page=problem&amp;host=".urlencode($host)."' >Acknowledge Problem</a></li>";//
			else if(($host_status["current_state"] )> 0 && $host_status["problem_has_been_acknowledged"] !=0)
				$content .=
				"<li><a ajax-load='false' href='".$BASE_URL."?page=problem&amp;host=".urlencode($host)."' >Remove Acknowledgement</a></li>";
	
			if($host_status["notifications_enabled"] ==1)
				$content.= 
				"<li><a ajax-load='false' href='".$BASE_URL."?page=notification&amp;host=".urlencode($host)."' >Disable Notifications</a></li>";
			else
				$content.= 
				"<li><a ajax-load='false' href='".$BASE_URL."?page=notification&amp;host=".urlencode($host)."' >Enable Notifications</a></li>";
		}	

		$content.= '<li data-role="list-divider" role="heading">Services</li>'; 
		
		if (isset($host_status["services"]) && !empty($host_status["services"])) 
		{
			foreach ($host_status["services"] as $service) 
			{
				$class = class_by_state($service['current_state'],true );
				if ($service["scheduled_downtime_depth"] > 0) 
					$class .= " downtime";		
				
				//change to common list item function 				
				$content .="
				<li class='$class'>
				  <a ajax-load='false' href='index.php?page=servicedetail&service=".$service['serviceID']."'>".$service['service_description']."</a>
				</li>\n";
			}
		}
		$content .=" \t</ul>\n\n</div>\n"; 
	}
	print $content;

}//end function all_host_details()



/*
*	@author Hirose Masaaki
*	@author Mike Guthrie
*	@author Wesley Zhao
*	modified from original page html and put into dynamic PHP function 
*/
function hostservice_details($serviceID)
{
	global $status; 
	global $SERVICE_STATUS_BY;
	global $BASE_URL;

	$content = ''; 

	foreach ($status["hosts"] as $host_id => $host_status) 
	{
		$host = $host_status["host_name"];
		if (!isset($host_status["services"]) || empty($host_status["services"])) continue; 
		
		//loop through all services 
		foreach ($host_status["services"] as $service_id => $service_status) 
		{
			if($service_status['serviceID'] != $serviceID) continue; 	
		
			//determine variables for service details 
			$notclass = class_by_state(($service_status['notifications_enabled'] == 1 ? STATE_OK : STATE_CRITICAL),true); 
			$notifs = ($service_status["notifications_enabled"] == 1) ? "Enabled" : "Disabled"; 
			$dt = $service_status["scheduled_downtime_depth"] > 0 ? "Yes" : "No"; 
			$ack = $service_status["problem_has_been_acknowledged"] > 0 ? "Yes" : "No";
			$duration = stringize_duration(get_time_breakdown($status, $service_status)); 
			$stateclass = class_by_state($service_status["current_state"],true); 
			$textstate = $SERVICE_STATUS_BY[ $service_status["current_state"] ]; 
			$service = $service_status['service_description']; 
			
			$content .='
			<div data-role="page" id="'.$host_id.'_'.$service_id.'" data-theme="a">
			  <div data-role="header">
	
		<!-- back button -->
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a>
				<h1>'.htmlentities($host).' - '.htmlentities($service).'</h1>
				<a href="index.php" alax-load="false" data-icon="home" data-iconpos="notext"  data-direction="reverse" class="ui-btn-right">Home</a>
			  </div> <!-- end header -->

				<ul data-role="listview" data-inset="false" data-theme="f" data-dividertheme="a">
				<li data-role="list-divider" role="heading">Information
					<div class="information rounded">
					  <dl>
					  	<dt>Host: </dt><dd>'.htmlentities($host).'</dd>
					  	<dt>Service: </dt><dd>'.htmlentities($service).'</dd>
						<dt>Status</dt>
						<dd class="'.$stateclass.'">'.$textstate.' (for '.$duration.')</dd> 
						<dt>Status Information</dt>
						<dd>'.$service_status["plugin_output"].'</dd> 
						<dt>Last Check Time</dt>
						<dd>'.date("c",$service_status["last_check"]).'</dd> 
						<dt>Last Update</dt>
						<dd>'.date("c",$service_status["last_update"]).'</dd> 
						<dt>Notifications</dt>
						<dd class="'.$notclass.'">'.$notifs.'</dd> 
						<dt>Scheduled Downtime</dt>
						<dd>'.$dt.'</dd> 
						<dt>Acknowledged</dt>
						<dd>'.$ack.'</dd> 
					  </dl>
					</div>
				</li>';
				
				if(is_authorized_for_command($host,$service)) {	
				$content .='
					<li data-role="list-divider" role="heading">Commands</li>
					<li><a ajax-load="false" href="'.$BASE_URL.'?page=downtime&amp;host='.urlencode($host).'&amp;service='.urlencode($service).'">Schedule downtime</a></li>'; 
	
					if(($service_status["current_state"] )> 0 && $service_status["problem_has_been_acknowledged"] == 0)
						$content.=
					"<li><a ajax-load='false' href='".$BASE_URL."?page=problem&amp;host=".urlencode($host)."&amp;service=".urlencode($service)."' >Acknowledge Problem</a></li>";					
					
					else if(($service_status["current_state"] )> 0 && $service_status["problem_has_been_acknowledged"] != 0)
						$content.=
					"<li><a ajax-load='false' href='".$BASE_URL."?page=problem&amp;host=".urlencode($host)."&amp;service=".urlencode($service)."' >Remove Acknowledgement</a></li>\n";
					
					if($service_status["notifications_enabled"]==1 )
						$content .=
					"<li><a ajax-load='false' href='".$BASE_URL."?page=notification&amp;host=".urlencode($host)."&amp;service=".urlencode($service)."' >Disable Notifications</a></li>\n";
					else 
						$content .=
					"<li><a ajax-load='false' href='".$BASE_URL."?page=notification&amp;host=".urlencode($host)."&amp;service=".urlencode($service)."' >Enable Notifications</a></li>\n";
				}
				$content .="
				</ul>

			</div>\n"; 

		}// end foreach service 
	}// for all hosts 
	print $content; 
}//end function 




/*
*	@author Mike Guthrie
*	@author Hirose Masaaki
*	modified from original page html and put into dynamic PHP function 
*/
function host_summary_listings($i=0) //removed data-direction="reverse" from <a></a>
{
	global $status; 
	$content =''; 	
	
//	for($i=0; $i<7; $i++) 
//	{
	$content .='
		<div data-role="page" id="hosts_'.$i.'" data-theme="a">
		  <div data-role="header">
		  		  <!-- back button -->
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a>
		  
			<h1>Hosts</h1>
			<a href="index.php" alax-load="false" data-icon="home" data-iconpos="notext" class="ui-btn-right">Home</a>
		  </div>
			<ul data-role="listview" data-inset="false" data-theme="f" data-dividertheme="a">
			';
			 
		foreach($status["hosts"] as $host_id => $host_status) 
		{
			$host = $host_status["host_name"];
			//print "<pre>".print_r($host_status,true)."</pre>";  
			if(!isset($host_status['services']) ) //handle unset array 
				$host_status['services'] =array(); 
				
			$class = class_by_state(service_state_of($host_status['services']) );
			$count = count($host_status['services']);
	
			if(!isset($host_status["current_state"])) continue; //skip bad array indeces. XXX TODO: find the source of these. 			
			
			switch($i){
			case 0:
			case 1:
			case 2:
				if($host_status["current_state"]==$i)
					$content .= get_host_list_item($class,$host,$count);
				break;
			case 3:
				if($host_status["has_been_checked"]==0)
					$content .= get_host_list_item($class,$host,$count);
				break;
			case 4:
				if($host_status["current_state"]>0 && $host_status["scheduled_downtime_depth"]==0 
					&& $host_status["problem_has_been_acknowledged"]==0)
					$content .= get_host_list_item($class,$host,$count);
				break;
			case 5:
				if($host_status["current_state"]==1 || $host_status["current_state"] == 2)
					$content .= get_host_list_item($class,$host,$count);
				break;
			case 6:
				if($host_status["current_state"]>0 && 
				  ($host_status["scheduled_downtime_depth"]>1 || $host_status["problem_has_been_acknowledged"]>0))
					$content .= get_host_list_item($class,$host,$count);
				break;	 
			}	
		}
		
		$content.="</ul> \n\n\t </div>"; 
//	}
	
	print $content;
}


/*
*	@author Mike Guthrie
* 	@author Hirose Masaaki
*	modified from original page html and put into dynamic PHP function 
*/
function service_summary_listings($i=0)
{
	global $status;
	print "<!-- service summary totals, 'home' page ====================================================== -->\n\n"; 
		
//	for($i=0; $i<8; $i++) 
//	{
		print '
		<div data-role="page" id="services_'.$i.'" data-theme="a">
		  <div data-role="header">
		  		  <!-- back button -->
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a>
			<h1>Services</h1>
			<a href="index.php" alax-load="false" data-icon="home" data-iconpos="notext" class="ui-btn-right">Home</a>
		  </div>
	  	'; 

		foreach ($status["hosts"] as $host_id => $host_status) 
		{
			$host = $host_status["host_name"];
			print '<ul data-role="listview" data-inset="false" data-theme="f" data-dividertheme="a">'; 
			
			if(isset($host_status["services"]["current_state"]) && $host_status["services"]["current_state"]==0) 
				print '<li data-role="list-divider" role="heading">'.htmlentities($host)."</li>\n"; 
			
			if(isset($host_status["services"]) && is_array($host_status["services"]))
				filtered_services_list_items($i,$host_id,$host,$host_status["services"]); 		
				
			print "</ul>\n\n"; 			
		}
		print "</div> <!-- end services_{$i} div -->\n\n"; 
	
//	}

}


/*
*	@author Mike Guthrie
* 	@author Hirose Masaaki
*	modified from original page html and put into dynamic PHP function 
*/
function print_host_list_items()
{
	global $status; 
	
	foreach ($status["hosts"] as $host_id => $host_status) 
	{
		$host = $host_status["host_name"];
		if(!isset($host_status['services'])) $host_status['services'] = array(); 
		$class = class_by_state( service_state_of($host_status['services']) );
		$count = count($host_status['services']);
		print get_host_list_item($class,$host,$count);
	}

}


//returns a properly formatted host list item element 
/*
*	@author Mike Guthrie
* 	@author Hirose Masaaki
*	modified from original page html and put into dynamic PHP function 
*/
function get_host_list_item($class,$host,$count)
{
	$listing = "<li class='{$class}'><a ajax-load='false' href='index.php?page=hostdetail&host=".urlencode($host)."'>".htmlentities($host)."</a>
								<span class='ui-li-count'>{$count}</span></li>\n"; //changed from #$host_id 
	return $listing; 							
}


//returns a properly formatted service list item element 
/*
*	@author Mike Guthrie
* 	@author Hirose Masaaki
*	modified from original page html and put into dynamic PHP function 
*/
function get_service_list_item($class,$id,$host,$service)
{
	$listing = "<li class='{$class}'><a ajax-load='false' href='index.php?page=servicedetail&service=".$id."'>"    //<a href='#".$host_id."_".$service_id."'>"
						.htmlentities($host).' - '.htmlentities($service)."</a></li>\n";
	return $listing;						
}


/*
*	@author Mike Guthrie
*	modified from original page html and put into dynamic PHP function 
*/
function print_services_list_items($showhost=false)
{
	global $status; 
	
	$content = ''; 
	
	foreach ($status["hosts"] as $host_id => $host_status) 
	{
		$host = $host_status["host_name"];
		//add to content string 		
		
		if($showhost) //if this is listed under a host title 
			$content.= "<h3>".htmlentities($host)."</h3>"; 
							
		$content .="<ul data-role='listview' data-inset='true' data-theme='a'>"; 
		
		if(!isset($host_status["services"])) //handle unset arrays 
			$host_status["services"] = array(); 
			
		//print service listings 	
		foreach ($host_status["services"] as $service_id => $service_status) 
		{
			$class = class_by_state( service_state_of(array($service_id=> $service_status),true) );
			$content.= get_service_list_item($class,$service_status['serviceID'],$host,$service_status['service_description']);
		}
		//close list 
		$content .="\n</ul>\n\n"; //remove unneeded <dl> tags 
	}
	print $content; 
}


/*
*	@author Mike Guthrie
*/
function filtered_services_list_items($i,$host_id,$host,$array)
{
	//$host_status["services"] 
	$content = ''; 
	
	foreach ($array as $service_id => $service_status) 
	{
		$class = class_by_state( service_state_of(array($service_id=>$service_status)),true );

		switch($i)
		{
		case 0:
		case 1:
		case 2:
		case 3:
			if($service_status["current_state"]==$i)
				$content .= get_service_list_item($class,$service_status['serviceID'],$host,$service_status['service_description']);
			break;
		case 4:
			if($service_status["has_been_checked"]==0)
				$content.= $listing;
			break;
		case 5:
			if($service_status["current_state"]>0 && $service_status["scheduled_downtime_depth"]==0 && $service_status["problem_has_been_acknowledged"]==0)
				$content .= get_service_list_item($class,$service_status['serviceID'],$host,$service_status['service_description']);
			break;
		case 6:
			if($service_status["current_state"]==1||$service_status["current_state"]==2||$service_status["current_state"]==3)
				$content .= get_service_list_item($class,$service_status['serviceID'],$host,$service_status['service_description']);
			break;
		case 7:
			if($service_status["current_state"]>0 && ($service_status["scheduled_downtime_depth"]>1 || $service_status["problem_has_been_acknowledged"]>0))
				$content .= get_service_list_item($class,$service_status['serviceID'],$host,$service_status['service_description']);
			break;	 
		}	
	}
	print $content;

}


/*
* 	@author Hirose Masaaki
*	@author Mike Guthrie
*	@author Wesley Zhao
*	modified from original function 
*/
function view_downtime($global_stats, $status) 
{
    global $STATUS_FILE, $COMMAND_FILE;
    global $HOST_STATUS_BY, $SERVICE_STATUS_BY;
    global $BASE_URL;
    $host     = grab_request_var("host",'');
    $hostarray = isset($status['hosts'][$host]) ? $status['hosts'][$host] : die('Unknown Host'); //XXX don't die 
	$service = grab_request_var("service",false);
    if ($service) { //service page 
        //foreach($hostarray['services'] as $service)
        	//if($service['serviceID'])
        $service_status= isset($hostarray['services'][$service]) ? $hostarray['services'][$service] : die('Unknown Service'); 
		$id = $hostarray['host_id'].'_'.$servicearray['service_id']; 
	    $service_desc =  $service_status['service_description'] ;  
	    $serviceInput =  '<input type="hidden" name="service" value='.htmlentities($service_desc).' id="dt_'.$id.'_comment" />';

    } 
    else { //host page         
        $id = $hostarray['host_id'];
        $service_desc = ''; 
        $serviceInput = ''; 
    }
    //$host_status = $status["hosts"][$host_id];
    

    
    $content ='
	<div data-role="page" id="dt_'.$id.'" data-theme="a">

	  <div data-role="header">
	  		  		  <!-- back button -->
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a>
		<h1>Schedule Downtime</h1>
		<a href="'.$BASE_URL.'/index.php" alax-load="false" data-icon="home" data-iconpos="notext" class="ui-btn-right">Home</a>
	  </div>

	  <div data-role="content">
		<h2>Information</h2>
		<div class="information rounded">
		  <dl>
			<dt>Host</dt>
			<dd>'.htmlentities($host).'</dd>'; 

	if($service) 
			$content .= "<dt>Service</dt><dd>".$service_status['service_description']."</dd>\n";
		
	$content .='
		  </dl>
		</div>

		<h2>Command Options</h2>
		<form id="dt_'.$id.'_form" data-dt_host="'.htmlentities($host).'" data-dt_service="'.htmlentities($service_desc).'"> 
		  <div data-role="fieldcontain">
			<label for="dt_'.$id.'_comment">Comment:</label>
			<input type="text" name="dt_'.$id.'_comment" id="dt_'.$id.'_comment" value="In scheduled downtime" />
		  </div>
		  
		  <div class="hiddenInputs">
		  	<input type="hidden" name="host" value='.urlencode($host).' id="dt_'.$id.'_comment" />
		  	'.$serviceInput.'
		  </div>

		  <div data-role="fieldcontain">
			<label for="dt_'.$id.'_end_date">End Time:</label>
			<select id="dt_'.$id.'_end_date">';

	for ($i=0, $t = time(); $i < 7; $t += 86400, $i++) {
		$content .= "<option>".strftime("%Y-%m-%d", $t)."</option>\n";
	}
	$content .='</select> 
			<select id="dt_'.$id.'_end_time">'; 
	$content .= get_dt_end_options(); 
	$content .='
			</select>
		  </div>
		  <a href="#" class="submitDowntimeButton" data-role="button" data-theme="e" data-dt_id="'.$id.'">Commit</a>
		</form>
		<div id="dt_'.$id.'_result" class="result">&nbsp;</div>
	  </div>
	</div>'; 
	
	print $content;
	
}






/*
* 	@author Hirose Masaaki
*	@author Mike Guthrie
*	@author Wesley Zhao
*	modified from original function 
*/
function view_problem($global_stats, $status) 
{
    global $STATUS_FILE, $COMMAND_FILE;
    global $HOST_STATUS_BY, $SERVICE_STATUS_BY;
    global $BASE_URL;
    
    $host  = grab_request_var("host");
    $host_status = $status["hosts"][$host];
    $service = grab_request_var("service",false);
    $service_status;
    if ($service) {   
    	$service_status = $host_status["services"][$service];             
        $id = $host_status['host_id'].'_'.$service_status['serviceID'];         
        $service_desc = $service_status['service_description'];
        $class = ($service_status['problem_has_been_acknowledged'] ==0) ? 'submitAckProblemButton' : 'submitRemoveAckButton';
    } else {
		$id = urlencode($host_id);
		$service_desc = ''; 
		$class = ($host_status['problem_has_been_acknowledged'] ==0) ? 'submitAckProblemButton' : 'submitRemoveAckButton';
    }
    
    $content = '
	<div data-role="page" id="ack_'.$id.'" data-theme="a">

	  <div data-role="header">
	  		  		  <!-- back button -->
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a>
		<h1>Problem</h1>
		<a href="'.$BASE_URL.'/index.php" alax-load="false" data-icon="home" data-iconpos="notext"  data-direction="reverse" class="ui-btn-right">Home</a>
	  </div>

	  <div data-role="content">
		<h2>Information</h2>
		<div class="information rounded">
		  <dl>
			<dt>Host</dt>
			<dd>'.htmlentities($host).'</dd>'; 

	if($service)
		$content .= " <dt>Service</dt><dd>".$service_status['service_description']."</dd>\n";

	$content .='
		  </dl>
		</div>

		<h2>Command Options</h2>
		<form id="ack_'.$id.'_form" data-ack_host="'.urlencode($host).'" data-ack_service="'.urlencode($service_desc).'">';

	if($service && $service_status["problem_has_been_acknowledged"] ==0)
	{
		$content.= "<div data-role='fieldcontain'>
			<label for='ack_".$id."_comment'>Comment:</label>
			<input type='text' name='ack_".$id."_comment' id='ack_".$id."_comment' value='acknowledge problem' />
		  </div>";
	}
	else if($host_status["problem_has_been_acknowledged"] ==0) {
	 $content .= "<div data-role='fieldcontain'>
			<label for='ack_".$id."_comment'>Comment:</label>
			<input type='text' name='ack_".$id."_comment' id='ack_".$id."_comment' value='acknowledge problem' />
		  </div>";
	}
	$content .='
	<a href="#" class="'.$class.'" data-role="button" data-theme="e" data-ack_id="'.$id.'">Commit</a>
		</form>
		<div id="ack_'.$id.'_result" class="result">&nbsp;</div>
	  </div>
	</div>';
	
	print $content;
	
}




/*
* 	@author Hirose Masaaki
*	@author Mike Guthrie
*	@author Wesley Zhao
*	modified from original function 
*/
function view_notification($global_stats, $status) 
{
    global $STATUS_FILE, $COMMAND_FILE;
    global $HOST_STATUS_BY, $SERVICE_STATUS_BY;
    global $BASE_URL;
    
    $host  = grab_request_var("host");
    $host_status = $status["hosts"][$host];
    $service = grab_request_var("service",false);
    $service_status;
    if ($service) {   
    	$service_status = $host_status["services"][$service];             
        $id = $host_status['host_id'].'_'.$service_status['serviceID'];         
		$class =  $service_status['notifications_enabled']== 1 ? 'submitDisnotificationButton' : 'submitEnnotificationButton';
		$button_title = $service_status['notifications_enabled'] == 1 ? 'Disable Notifications' : 'Enable Notifications'; 
		$service = $service_status['service_description'];               
    } else {
        $id = $host_id;
		$class = ($host_status['notifications_enabled']==1) ? 'submitDisnotificationButton': 'submitEnnotificationButton';
		$button_title = $host_status['notifications_enabled'] == 1 ? 'Disable Notifications' : 'Enable Notifications'; 
		//$service= '';        
    }
    
	$content ='
	<div data-role="page" id="nt_'.urlencode($id).'" data-theme="a">

	  <div data-role="header">
	  		  		  <!-- back button -->
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a>
		<h1>Notifications</h1>
		<a href="'.$BASE_URL.'/index.php" alax-load="false" data-icon="home" data-iconpos="notext" class="ui-btn-right">Home</a>
	  </div>

	  <div data-role="content">
		<h2>Information</h2>
		<div class="information rounded">
		  <dl>
			<dt>Host</dt>
			<dd>'.htmlentities($host).'</dd>'; 

	if($service)
		$content.= "<dt>Service</dt><dd>".$service."</dd>\n";			

	$content .='
		  </dl>
		</div>

		<h2>Command Options</h2>
		<form id="nt_'.urlencode($id).'_form" data-nt_host="'.urlencode($host).'" data-nt_service="'.urlencode($service).'">
		<a href="#" class="'.$class.'" data-role="button" data-theme="e" data-nt_id="'.urlencode($id).'">'.$button_title.'</a>
		</form>
		<div id="nt_'.urlencode($id).'_result" class="result">&nbsp;</div>
	  </div>
	</div>'; 
	
	print $content;
	
}


/*
*	@author Mike Guthrie
* 	@author Hirose Masaaki
*	modified from original main page script
*/
function show_about_page() {
?>

	<div data-role="page" id="about" data-theme="a">
	  <div data-role="header">
	  	 <!-- back button 
		  <a data-icon="arrow-l" data-rel="back" class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" href="#" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">
				<span class="ui-btn-text">Back</span>
				<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
			</span>
		  </a> -->
		<h1>About</h1>
		<a href="index.php" alax-load="false" data-icon="home" data-iconpos="notext" class="ui-btn-right">Home</a>
	  </div>

	  <div data-role="content">
		<h2>NEMS Mobile UI for NEMS Linux 1.4+</h2>
		<p>Based on Nagios Mobile v1.03</p>
		<p>This version is a proof of concept only. I plan to completely rewrite it without being based on the <u>old</u> Nagios Mobile. jQuery 1.5? <em>EW!</em></p>
		<p>Copyright (c) 2011 <a rel="external" href="http://www.nagios.com" title="Nagios Enterprises" target="_blank">Nagios Enterprises, LLC</a></p>
		<!-- <p>Web:<a href="http://www.nagios.com/products/nagiosmobile"> http://www.nagios.com/products /nagiosmobile</a></p>	-->	
		<p>Based on <a rel="external" href="http://exchange.nagios.org/directory/Addons/Frontends-%28GUIs-and-CLIs%29/Mobile-Device-Interfaces/Teeny-Nagios/details" target="_blank" title="Teeny Nagios">Teeny Nagios</a> by Hirose Masaaki.</p>
		<p>Copyright (c) 2011 Apache 2.0 License</p>
		<p>Originally developed by Mike Guthrie and Wesley Zhao.<br />Adapted for NEMS Linux by Robbie Ferguson.</p>
	  </div>
	</div>
	
<?php 
}


/*
*	@author Mike Guthrie
* 	@author Hirose Masaaki
*	modified from original main page script
*/
function get_dt_end_options()
{
	$content = ''; 

	$now_h = strftime("%H");
	$now_m = strftime("%M");
	if ($now_m < 30) {
		$now_m = 30;
	} else {
		$now_h = intval( ($now_h+1)%24 );
		$now_m = 0;
	}
	$time_selected = sprintf("%02d:%02d", $now_h, $now_m);
	$minutes = array(0,30);
	for ($h=0; $h<24; $h++) 
	{
		foreach ($minutes as $m) {
			$tm = sprintf("%02d:%02d", $h, $m);
			$sel = ($tm === $time_selected) ? "selected='selected'" : ""; 
			$content.="<option {$sel}>{$tm}</option>\n"; 
		}
	}
	return $content; 
}
?>
