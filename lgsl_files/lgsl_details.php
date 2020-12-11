<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 /*----------------------------------------------------------------------------------------------------------\
 |                                                                                                            |
 |                      [ LIVE GAME SERVER LIST ] [ � RICHARD PERRY FROM GREYCUBE.COM ]                       |
 |                                                                                                            |
 |    Released under the terms and conditions of the GNU General Public License Version 3 (http://gnu.org)    |
 |                                                                                                            |
 \-----------------------------------------------------------------------------------------------------------*/
 
//------------------------------------------------------------------------------------------------------------+

  require "lgsl_class.php";

//------------------------------------------------------------------------------------------------------------+
// THIS CONTROLS HOW THE PLAYER FIELDS ARE DISPLAYED

  $fields_show  = array("name", "score", "kills", "deaths", "team", "ping", "bot", "time"); // ORDERED FIRST
  $fields_hide  = array("teamindex", "pid", "pbguid"); // REMOVED
  $fields_other = TRUE; // FALSE TO ONLY SHOW FIELDS IN $fields_show

//------------------------------------------------------------------------------------------------------------+
// GET THE SERVER DETAILS AND PREPARE IT FOR DISPLAY

  global $lgsl_server_id;

  $server = lgsl_query_cached("", "", "", "", "", "sep", $lgsl_server_id);

  if (!$server) { $output .= "<div style='margin:auto; text-align:center'> {$lgsl_config['text']['mid']} </div>"; return; }

  $fields = lgsl_sort_fields($server, $fields_show, $fields_hide, $fields_other);
  $server = lgsl_sort_players($server);
  $server = lgsl_sort_extras($server);
  $misc   = lgsl_server_misc($server);
  $server = lgsl_server_html($server);

//------------------------------------------------------------------------------------------------------------+

  $filename = $_SERVER['DOCUMENT_ROOT']."/e107_plugins/lgsl/templates/lgsl_details_{$server['b']['type']}.php";
  
  if(file_exists($filename)) {
    require $_SERVER['DOCUMENT_ROOT']."/e107_plugins/lgsl/templates/lgsl_details_{$server['b']['type']}.php";
  } else {
    require $_SERVER['DOCUMENT_ROOT']."/e107_plugins/lgsl/templates/lgsl_details_default.php";
  }
  
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//------ PLEASE MAKE A DONATION OR SIGN THE GUESTBOOK AT GREYCUBE.COM IF YOU REMOVE THIS CREDIT ----------------------------------------------------------------------------------------------------+
  $output .= "<div style='text-align:center; font-family:tahoma; font-size:9px'><br /><br /><br /><a href='http://www.greycube.com' style='text-decoration:none'>".lgsl_version()."</a><br /></div>";
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+