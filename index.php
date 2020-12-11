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

  require_once "../../class2.php";
  
  require_once HEADERF;

//------------------------------------------------------------------------------------------------------------+

  global $output, $lgsl_server_id;

  $output = "";

  $s = isset($_GET['s']) ? $_GET['s'] : "";

  if     (is_numeric($s)) { $lgsl_server_id = $s; require "lgsl_files/lgsl_details.php"; }
  elseif ($s == "add")    {                       require "lgsl_files/lgsl_add.php";     }
  else                    {                       require "lgsl_files/lgsl_list.php";    }

  $ns->tablerender($lgsl_config['title'][0], $output);

  unset($output);

//------------------------------------------------------------------------------------------------------------+

  require_once FOOTERF;

//------------------------------------------------------------------------------------------------------------+