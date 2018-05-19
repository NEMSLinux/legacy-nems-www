<?php 
//header.inc.php  
//html head information
// @author Hirose Masaaki
// @author Mike Guthrie 

// Nagios Mobile 1.0
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


echo '<?xml version="1.0" encoding="UTF-8"?>'; 
echo "\n";
$WEBKIT = (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'webkit')) ? true : false; //detect if we have webkit or not and pass to JS 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="height=device-height,width=device-width" />
<title>NEMS Mobile UI</title>
<link rel="apple-touch-icon" href="nagios.png" />
<link rel="stylesheet" href="nagiosmobile.css" />
<script src="js/jquery-1.5.2.min.js"></script>

<link rel="stylesheet" href="jquery.mobile-1.0/jquery.mobile-1.0.min.css" />
<script type='text/javascript' src="jquery.mobile-1.0/jquery.mobile-1.0.min.js"></script>
<script type='text/javascript' src="js/nagiosmobile.js"></script>
<style>

<?php 
//if webkit exists in browser, use nice gradients, otherwise use flat colors 
echo '/*WEBKIT: '.$WEBKIT.'*/'; 

if($WEBKIT) {
	echo '
	.critical, .down, .hostproblem, .unknown, .unreachable { background: -webkit-gradient(linear, 0% 20%, 0% 100%, from(transparent), to(#f00)) !important;}
	.warning { background: -webkit-gradient(linear, 0% 20%, 0% 100%, from(transparent), to(#ff0)) !important;} 
	';
}
else 
	echo '
	.critical, .down, .hostproblem { background: #993333 !important; color: #FFF !important; text-shadow: 1px 1px 1px #000 !important;}
	.warning { background: #FFCC00 !important; color: #FFF !important; text-shadow: 1px 1px 1px #000 !important;} 		
	.unknown, .unreachable {background: #FF9933 !important; color: #FFF !important; text-shadow: 1px 1px 1px #000 !important;}
	dd.critical {color: #FFF !important;text-shadow: 1px 1px 1px #000 !important;}
	dd.warning {color: #000; text-shadow: 1px 1px 1px #000 !important;}
	'; 

?>


</style>
<script>
	var BASEURL = '<?php echo $BASE_URL; ?>';  
</script>

</head>
<body>	
