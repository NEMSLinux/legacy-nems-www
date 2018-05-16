<?php 
// include.inc.php 
// main include file for all global variables, constants, and script includes
// @author Mike Guthrie
// @author Hirose Masaaki


//////////////////FILE LOCATIONS:  EDIT THESE TO MATCH YOUR NAGIOS INSTALLATION//////
////////////////////////////////////////////////////////////////////////////////////
$STATUS_FILE  = "/var/cache/nagios/status.dat";
$COMMAND_FILE = "/usr/local/nagios/var/rw/nagios.cmd";
$CGI_FILE = "/usr/local/nagios/etc/cgi.cfg";
$OBJECTS_FILE = "/var/lib/nagios/objects.precache"; 
$BASE_URL = '/nagiosmobile';
$TTL = 30; //Time to live for cached data parsed from status and objects file 
$USER = ''; //you can hard-code the 'nagiosadmin' if you don't want any authentication: NOT RECOMMENDED!! 
///////////////////////////////////////////////////////////////////////////////////
////////////////////DO NOT MAKE CHANGES BELOW THIS LINE/////////////////////////////


// Nagios Mobile 1.0
// Copyright (c) 2011 Nagios Enterprises, LLC
// Web: http://www.nagios.com/products/nagiosmobile
// Developed by Mike Guthrie and Wesley Zhao.  
// Based on Teeny Nagios by HIROSE Masaaki. 

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


//////////INCLUDES
require_once('includes/functions.inc.php'); 
require_once('includes/displayfunctions.inc.php');
require_once('includes/parse_cgi_file.inc.php'); 
require_once('includes/parse_objects_file.inc.php');
require_once('includes/parse_status_file.inc.php');
require_once('class/NagiosUser.php');
require_once('class/NagiosData.php');

///////////CONSTANTS
define("TN_VERSION", "1.03");
define("HOST_UP",          0);
define("HOST_DOWN",        1);
define("HOST_UNREACHABLE", 2);
define("STATE_OK",       0);
define("STATE_WARNING",  1);
define("STATE_CRITICAL", 2);
define("STATE_UNKNOWN",  3);
define("CHILD_PROBLEM", 99); 

$HOST_STATUS_BY = array(
                        HOST_UP          => "Up",
                        HOST_DOWN        => "Down",
                        HOST_UNREACHABLE => "Unreachable",
                        );
$SERVICE_STATUS_BY = array(
                           STATE_OK       => "OK",
                           STATE_WARNING  => "Warning",
                           STATE_CRITICAL => "Critical",
                           STATE_UNKNOWN  => "Unknown",
                           );

//////APC
//use apc if we've got it 
$APC = (function_exists('apc_add') && function_exists('apc_fetch') && function_exists('apc_exists')) ? true : false; 
 


?>
