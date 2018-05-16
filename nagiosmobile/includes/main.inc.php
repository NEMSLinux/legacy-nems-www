<?php 
//main.inc.php  
//home page display template 
// @author Hirose Masaaki
// @author Mike Guthrie
// Modified from Hirose's original 'view_main' function.  
// Broke view_main into several separate functions and split the page load into so all content doesn't
// load into index.php

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



//template CSS class variables 
$h_1 = class_by_state($global_stats['host_down'] > 0 ? HOST_DOWN : HOST_UP); 
$h_2 = class_by_state($global_stats['host_unreachable'] > 0 ? HOST_UNREACHABLE : HOST_UP);
$h_4 = class_by_state(($global_stats['host_unhandled'] > 0 ? HOST_DOWN : STATE_OK) );
$h_5 = class_by_state(($global_stats['host_problems'] > 0 ? HOST_DOWN : STATE_OK) );

$s_1 = class_by_state(($global_stats['service_warning'] > 0 ? STATE_WARNING : STATE_OK), true ) ;
$s_2 = class_by_state(($global_stats['service_critical'] > 0 ? STATE_CRITICAL : STATE_OK), true ) ;
$s_3 = class_by_state(($global_stats['service_unknown'] > 0 ? STATE_CRITICAL : STATE_OK), true ); 
$s_5 = class_by_state(($global_stats['service_unhandled'] > 0 ? STATE_CRITICAL : STATE_OK), true );
$s_6 = class_by_state(($global_stats['service_problems'] > 0 ? STATE_CRITICAL : STATE_OK), true ); 

?>

<!-- home ============================================================ -->
	<div data-role="page" id="home" data-theme="a">
	  <div data-role="header">

		<h1>Nagios Mobile</h1>
		<a href="index.php?page=about" data-icon="info" data-iconpos="notext" data-rel="dialog" class="ui-btn-right">About</a>
	  </div> <!-- end header -->

		<div data-role="listview" data-inset="true" data-theme="b" data-dividertheme="f"> <!-- CLOSING TAG???? -->
		
		<div data-role="list-divider" role="heading">Hosts</div>
		
		<div data-role="navbar"> <!-- remove role of navbar  -->
		<ul>
			<li><a data-ajax="false" href="index.php?page=hosts_0"><?php echo $global_stats['host_up'] ?> Up</a></li>
			<li><a data-ajax="false" href="index.php?page=hosts_1"  class="<?php echo $h_1; ?>"> <?php echo $global_stats['host_down'] ?> Down</a></li>
			<li><a data-ajax="false" href="index.php?page=hosts_2"  class="<?php echo $h_2; ?>"> <?php echo $global_stats['host_unreachable'] ?> Unreachable</a></li>
			<!-- <li><a href="index.php?page=hosts_3"><?php echo $global_stats["host_pending"]?> Pending</a></li>	-->
		</ul>	

		<ul>
			<li><a data-ajax="false" href="index.php?page=hosts_4" class="<?php echo $h_4; ?>"> <?php echo $global_stats["host_unhandled"]?> Unhandled</a></li>
			<li><a data-ajax="false" href="index.php?page=hosts_5" class="<?php echo $h_5; ?>"> <?php echo $global_stats["host_problems"]?> Problems</a></li>
		</ul>
		</div> <!-- end navbar -->

		<div data-role="list-divider" role="heading">Services</div>  <!-- THIS CAN'T BE HERE, CHANGED FROM LI TO DIV -->
		
		<div data-role="navbar">
		<ul>
			<li><a data-ajax="false" href="index.php?page=services_0"><?php echo $global_stats["service_ok"] ?> OK</a></li>
			<li><a data-ajax="false" href="index.php?page=services_1"  class="<?php echo $s_1; ?>"> <?php echo $global_stats["service_warning"] ?> Warning</a></li>
			<li><a data-ajax="false" href="index.php?page=services_2" class="<?php echo $s_2; ?>"> <?php echo $global_stats["service_critical"] ?> Critical</a></li>			
			<li><a data-ajax="false" href="index.php?page=services_3"  class="<?php echo $s_3; ?>"> <?php echo $global_stats["service_unknown"] ?> Unknown</a></li>
		</ul>
			
			<!-- <li><a href="index.php?page=services_4"><?php echo $global_stats["service_pending"]?> Pending</a></li> -->
			<!-- <li><a href="index.php?page=services_7"><?php echo $global_stats["service_acknowledged"]?> Acknowledged</a></li> -->	
		<ul>
			<li><a data-ajax="false" href="index.php?page=services_5"  class="<?php echo $s_5; ?>"> <?php echo $global_stats["service_unhandled"]?> Unhandled</a></li>
			<li><a data-ajax="false" href="index.php?page=services_6"  class="<?php echo $s_6; ?>"> <?php echo $global_stats["service_problems"]?> Problems</a></li>
			
		</ul>
		</div> <!-- end navbar -->
	</div> <!-- end page -->
	
	<div data-role="footer" id='footer'>
	  <div data-role="navbar">
		<ul>
		  <li><a data-ajax="false" href="/nagiosxi" data-icon="home">Nagios XI</a></li>
		  <li><a data-ajax="false" href="/nagios" data-icon="star">Nagios Core</a></li>
		  <li><a data-ajax="false" href="http://support.nagios.com" data-icon="info">Nagios Support</a></li>			
		</ul>
	  </div> <!-- end navbar -->
	</div> <!-- end footer -->
	
	</div> 	<!-- WHAT IS THIS FOR?? -->




