<?php 
// parse_objects_file.inc.php  
// fetches object info if APC data is not available 
// @author Mike Guthrie
// @author Dave Worth 

// Nagios Mobile 1.0
// Copyright (c) 2011 Nagios Enterprises, LLC
// Web: http://www.nagios.com/products/nagiosmobile
// Developed by Mike Guthrie and Wesley Zhao.  
// Based on Teeny Nagios by HIROSE Masaaki. 
// http://www.apache.org/licenses/LICENSE-2.0.html 

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
*	checks for APC data, parses objects.cache if it's not available and returns objects array
*	@author Mike Guthrie
*	@author Dave Worth
*	@param string $objfile the file location of the objects.cache file
*	@return mixed $objects array of all relevant object configuration data to determine permissions
*/ 

function parse_objects_file($objfile)
{
	global $APC;
	global $TTL; 
	//global $username;
	
	//use cached APC array if it's available
	if($APC) {
		$object_collector = apc_fetch('objects');
		if($object_collector && is_array($object_collector)) { 
			//echo '<!-- USING APC -->'; 	
			return $object_collector; 
		}	
		//else parse status file 
	}

	$objs_file = fopen($objfile, "r") or die("Unable to open '$objfile' file!");
	
	$defmatches = array();
	$curdeftype = NULL;
	$kvp = array();
	$object_collector = array();
	$gooddefs = array('host','service','contact','contactgroup','hostescalation','serviceescalation'); 
	
	while(!feof($objs_file)) //read through the file and read object definitions
	{
		$line = fgets($objs_file); //Gets a line from file pointer.
	
		if (preg_match('/^\s*define\s+(\w+)\s*{\s*$/', $line, $defmatches)) {
			// Beginning of a new definition;
			$curdeftype = $defmatches[1];
			if(!in_array($curdeftype,$gooddefs)) $curdeftype=NULL; //ditch objects we don't need 
		
		} elseif (preg_match('/^\s*}\s*$/', $line)) {
			// End of a definition.  Assign key-value pairs and reset variables
			switch($curdeftype)
			{
				case 'host':
				//case 'servicegroup':
				$object_collector[typemap($curdeftype)][$kvp[$curdeftype.'_name']] = $kvp;
				break;
	
				case 'service':
				$object_collector[typemap('host')][$kvp['host_name']]['services'][] = $kvp;
				$object_collector[typemap($curdeftype)][] = $kvp;
				break;
				
				case 'contact': 
				case 'contactgroup': 
				case 'hostescalation': 
				case 'serviceescalation':
					$object_collector[typemap($curdeftype)][] = $kvp;
				break; 		
	
				default:
				//$object_collector[typemap($curdeftype)][] = $kvp;
				break;
			}
	
			$curdeftype = NULL;
			$kvp = array();
		
		} elseif($curdeftype != NULL) {
			// Collect the key-value pairs for the definition			
			@list($key, $value) = explode("\t", trim($line), 2);
			$kvp[trim($key)] = trim($value);		
		} else {
			// outside of definitions? Comments and whitespace should be caught
		}
	      
	} //end of while
	
	fclose($objs_file);	
	
	if($APC) apc_add('objects',$object_collector,$TTL);

	return $object_collector;
}

/*
*	@author Dave Worth
*/
function typemap($type)
{
	//necessary object types to determine auth info 
	$others =  array('contact', 'contactgroup', 'hostescalation','serviceescalation'); 	
			
	$retval = NULL;
	if ( $type == 'host' || $type=='service' ) {  //removed hostgroup,servicegroup
		$retval = $type.'s_objs';
	} elseif (in_array($type,$others )) {
		$retval = $type.'s';
	} else { // TODO other types?  
  }

  return $retval;
}




?>