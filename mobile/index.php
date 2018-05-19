<?php  

  # Will only work on NEMS 1.4+, so do not allow it to run on anything older
  $nemsver = shell_exec('/usr/local/bin/nems-info nemsver');
  if (floatval($nemsver) < 1.4) {
    exit('Requires NEMS Linux 1.4+');
  }

// index.php 
// main page routing and controller
// @author Mike Guthrie
// Index page has been rewritten from original to control page routing, original 
// content and functions are now split into sub-scripts and sub-functions. 

// Nagios Mobile 1.03
// Copyright (c) 2011 Nagios Enterprises, LLC
// Web: http://www.nagios.com/products/nagiosmobile
// Developed by Mike Guthrie and Wesley Zhao.  
// Based on Teeny Nagios by HIROSE Masaaki
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
/////////////////////////////////////////////////////////
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





ini_set("display_errors", "Off");
//ini_set("track_errors", TRUE);
require_once('include.inc.php'); 

 
//GLOBAL VARIABLES
$status = array();  
$username = $USER;
$NagiosData = new NagiosData(); 
$NagiosUser = new NagiosUser(); 

//main page controller 
route_request(); 


/**
*	processes $_REQUEST variables and routes page accordingly, central page controller for all navigation
*	@author Mike Guthrie
*/ 
function route_request()
{
	global $STATUS_FILE;
	global $COMMAND_FILE;
	global $status;
	global $BASE_URL;
	
	$page = grab_request_var('page',''); 
	$cmd = array("schedule_downtime","acknowledge_problem","remove_acknowledgement","disable_notification","enable_notification"); 
	
	if(in_array($page,$cmd)) { 
		submit_command($page);
		return;
	}	

	//else proceed with regular html page 
	$status = parse_status_file($STATUS_FILE);	
	$global_stats = calc_global_stats($status);
	
	//html head template 
	include('header.inc.php');

	switch ($page) 
	{		
		case "downtime":
			view_downtime($global_stats, $status);
		break;

		case "problem":
			view_problem($global_stats, $status);
		break;

		case "notification":
			view_notification($global_stats, $status);
		break;
		
		case 'hostdetail':
			$host = grab_request_var('host',''); 			
			host_details($host);		
		break;
		case 'servicedetail':
			$serviceID = grab_request_var('service',''); //needs the numeric serviceID 
			hostservice_details($serviceID);
		break;
		
		case 'hosts_0': 
		case 'hosts_1': 
		case 'hosts_2': 
		case 'hosts_3': 
		case 'hosts_4':
			$int = substr($page,6);
			//echo "PAGE: $int<br />";
			host_summary_listings($int);
		break; 
		
		case 'services_0':
		case 'services_1':
		case 'services_2':
		case 'services_3':
		case 'services_4':
		case 'services_5':
		case 'services_6':
		case 'services_7':
			$int = substr($page,9);
			//echo "PAGE: $int<br />";
			service_summary_listings($int);
		break; 
		
		case 'about':
			show_about_page();
		break;
								
		default:
			include('includes/main.inc.php');
		break; 	
	}

	//include footer template 
	include('footer.inc.php');  
}


?>
