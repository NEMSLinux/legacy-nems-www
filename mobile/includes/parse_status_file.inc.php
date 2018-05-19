<?php //parse_status_file.inc.php
// modified from Hirose Masaaki's original function
// @author Hirose Masaaki
// @author Mike Guthrie

// Nagios Mobile 1.0
// Copyright (c) 2011 Nagios Enterprises, LLC
// Web: http://www.nagios.com/products/nagiosmobile
// Developed by Mike Guthrie and Wesley Zhao.  
// Based on Teeny Nagios by HIROSE Masaaki. 
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

/**
*	checks for APC data, if not available parses status.dat and adds authorized objects to array
*	@author Hirose Masaaki
*	@author Mike Guthrie
*	@param string $status_file the full file location of status.dat
*	@return mixed $status returns a structured array of hosts and services based on authorized objects 
*/ 
function parse_status_file($status_file) 
{
	global $APC;
	global $TTL; 
	global $username;
	
	//use cached APC array if it's available
	if($APC) {
		$status = apc_fetch($username.'_status');
		if($status && is_array($status)) 
			return $status; 
		//else parse status file 
	}
		 		
    //sentry variables 	
    $status;	
    $in_block = false;
    $block    = array();
    $bad_chars = array(' ','.',':','(',')','#','[',']'); 
    $unique = 0;
    
    //values we want to capture 
    $goodvalues = array("host_name","current_state","service_description","plugin_output","last_check",
								"last_state_change","notifications_enabled","last_update","scheduled_downtime_depth",
								"has_been_checked", "problem_has_been_acknowledged",);
    
    //parse file 
    $fh = fopen($status_file, "r");
    if (!$fh) exit("unable to open file ($STATUS_FILE)");
    
    while (!feof($fh)) 
    {
        $line = fgets($fh, 256);

        if (preg_match("/(\w+)\s+{/", $line, $matches)) //opening bracket 
        {
            //begin block
            //print($matches[1]."\n");
            $in_block = $matches[1]; //boolean with block type   
        } 
        elseif (preg_match("/^\s*}/", $line)) //closing bracket?? 
        {
            //end block
            switch ($in_block) 
            {
	            case "info":
	                $status["info"] = $block;
	            break;
	            
	            case "programstatus":
	                $status["program"] = $block;
	            break;
	            
	            case "hoststatus":
	               $hostname = $block["host_name"]; 
	               if(!is_authorized_for_host($hostname)) break; //only add host if authorized
						 //auth is good 	                
						 //$serviceauth = true; 						                 
	               $block['host_id'] = str_replace($bad_chars,'_',$hostname); //used for DOM 
	               if (isset( $status["hosts"][ $hostname ] ))
	                    $status["hosts"][ $hostname] = array_merge($status["hosts"][ $hostname ], $block);
	               else 
	                    $status["hosts"][ $hostname ] = $block;
	                
	            break;
	                
	            case "servicestatus":
	                # suppose service_description is unique. so don't array_merge.
	                $hostname = $block['host_name'];
	                $service = $block['service_description'];
	                $block['serviceID'] = $unique++; //used for GET navigation 
	                 
					if(!is_authorized_for_service($hostname,$service))  break; //only add service if authorized 					
						 //auth is good 	                
						 //$serviceauth = true; 	
					$block['service_id'] = str_replace($bad_chars,'_',$service);
					$block['host_id'] = str_replace($bad_chars,'_',$hostname); //used for DOM         
					//add host index if it's not there 
	                if(!isset($status["hosts"][ $hostname ])) $status["hosts"][$hostname] = array('host_name' => $hostname);
					//create array if it's not there
	                if(!isset($status["hosts"][ $hostname ]["services"]) )  $status["hosts"][ $hostname ]["services"] = array(); 
	                //add data block for service 	
	                $status["hosts"][ $hostname ]["services"][ $service ] = $block;
	             break;
            }
            $in_block = false;
            $block = array();
        } 
        elseif ($in_block) 
        {
            ### in block
            $line = trim($line);
            @list ($k,$v) = explode("=", $line, 2);
            //echo "KEY IS:$k<br />"; 
			//only grab the items we're interested in 
			if($in_block == 'info' || $in_block == 'programstatus')
				$block[$k] = $v;
			elseif($in_block == 'hoststatus' || $in_block == 'servicestatus')	
				if(in_array($k,$goodvalues) ) $block[$k] = $v;
        } 

    }//end while 
    fclose($fh);

	//dump($status); 
	//die(); 
	if($APC) 
		apc_add($username.'_status',$status,$TTL);
    return $status;
}


?>