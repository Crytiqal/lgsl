<?php

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

  header('Content-Type: application/json');

//------------------------------------------------------------------------------------------------------------+
// VALIDATE REQUEST

    global $output, $lgsl_server_id;
    
    $s = isset($_GET['s']) ? $_GET['s'] : "";
  
    if(is_numeric($s)) {
  
      $lgsl_server_id = $s;
  
  //------------------------------------------------------------------------------------------------------------+
  // THIS CONTROLS HOW THE PLAYER FIELDS ARE DISPLAYED
  
      $fields_show  = array("name", "score", "kills", "deaths", "team", "ping", "bot", "time"); // ORDERED FIRST
      $fields_hide  = array("teamindex", "pid", "pbguid"); // REMOVED
      $fields_other = TRUE; // FALSE TO ONLY SHOW FIELDS IN $fields_show
  
  //------------------------------------------------------------------------------------------------------------+
  
  //------------------------------------------------------------------------------------------------------------+
  // GET THE SERVER DETAILS AND PREPARE IT FOR DISPLAY
      
      $server = lgsl_query_cached("", "", "", "", "", "sep", $lgsl_server_id);
  
      if (!$server) { exit(); }
  
      $fields = lgsl_sort_fields($server, $fields_show, $fields_hide, $fields_other);
      $server = lgsl_sort_players($server);
      $server = lgsl_sort_extras($server);
      $misc   = lgsl_server_misc($server);
      $server = lgsl_server_html($server, 40);
      $jsonArray = array();
      $jsonArray[] = $misc + $server + $fields;
        
      echo json_encode($jsonArray);
  //------------------------------------------------------------------------------------------------------------+
          
    } else {
  
  //------------------------------------------------------------------------------------------------------------+
  // GET THE SERVER DETAILS AND PREPARE IT FOR DISPLAY
  
      $server_list = lgsl_query_group();
      $server_list = lgsl_sort_servers($server_list);
      $jsonArray = array();
  
      foreach ($server_list as $server)
      {
        if($server["b"]["type"] === "ts3" ) { continue; }
        $misc   = lgsl_server_misc($server);
        $server = lgsl_server_html($server, 22);
        $jsonArray[] = $misc + $server;
      }
        
      echo json_encode($jsonArray);
  //------------------------------------------------------------------------------------------------------------+
    }

?>