<?php

 /*----------------------------------------------------------------------------------------------------------\
 |                                                                                                            |
 |                      [ LIVE GAME SERVER LIST ] [ � RICHARD PERRY FROM GREYCUBE.COM ]                       |
 |                                                                                                            |
 |    Released under the terms and conditions of the GNU General Public License Version 3 (http://gnu.org)    |
 |                                                                                                            |
 \-----------------------------------------------------------------------------------------------------------*/
 
//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  header('Content-Type: text/html; charset=utf-8');
  header('X-Content-Type-Options: nosniff');
  header('Cache-Control: no-cache');

//------------------------------------------------------------------------------------------------------------+

  require("../lgsl_files/lgsl_class.php");
  require("../lgsl_files/lgsl_config.php");

//------------------------------------------------------------------------------------------------------------+
// GET THE SERVER DETAILS AND PREPARE IT FOR DISPLAY

  $r = isset($_GET['r']) ? $_GET['r'] : "1";
  $l = isset($_GET['l']) ? $_GET['l'] : "0";
  $lgsl_cmd = isset($_GET['cmd']) ? $_GET['cmd'] : "";

//---------------------------------------------------------+
//  GET DATA 

  if(is_numeric($r)) {

    $lgsl_server_id = $r;
    $request = "sp";   // default = sep
    $server = lgsl_query_cached("", "", "", "", "", $request, $lgsl_server_id);   

    if (!$server) { $server = $lgsl_config['text']['mid']; return; }

    $lgsl_protocol_list = lgsl_protocol_list();

//---------------------------------------------------------+
//  GET LOG

    if($l == "1") {
      
      $bytes = isset($_GET['bytes']) ? $_GET['bytes'] : "";
      $logfile = glob("/srv/games/{$server['b']['type']}/homepath/{$server['o']['comment']}/{$server['s']['game']}/*.log")[0];
      $filesize = exec("wc -c < {$logfile}");
      $server['filesize'] = $filesize;
      
      if($bytes == "") {
        $cmd = "tail -n 20 {$logfile}";
      } else {
        $diff = $filesize - $_GET['bytes'];
        $cmd = "tail -c {$diff} {$logfile}";
      }

      exec($cmd, $output);
      
      foreach($output as $outputline) {
        $server['log'][] = mb_convert_encoding(trim($outputline), 'UTF-8', 'UTF-8');
      }
    }

//---------------------------------------------------------+
//  RCON CMD

    if($lgsl_cmd) {

      $server['b']['rconpassword'] = isset($_GET['rconpassword']) ? $_GET['rconpassword'] : "";

      $lgsl_function = "lgsl_rcon_{$lgsl_protocol_list[$server['b']['type']]}";

      require("lgsl_rconprotocol.php");

      if ($lgsl_function == "lgsl_rcon_01") // TEST RETURNS DIRECT
      {
        $lgsl_fp = "";
        $response = call_user_func_array($lgsl_function, array(&$server, &$lgsl_cmd, &$lgsl_fp));
        return $server;
      }
      elseif ($lgsl_function == "lgsl_rcon_30" || $lgsl_function == "lgsl_rcon_33")
      {
        $scheme = "tcp";
        $lgsl_fp = @fsockopen("{$scheme}://{$server['b']['ip']}", $server['b']['q_port'], $errno, $errstr, 1);
        $response = call_user_func_array($lgsl_function, array(&$server, &$lgsl_cmd, &$lgsl_fp));
        @fclose($lgsl_fp);
      }
      else
      {
        $scheme = "udp";
        $lgsl_fp = @fsockopen("{$scheme}://{$server['b']['ip']}", $server['b']['q_port'], $errno, $errstr, 1);
        stream_set_timeout($lgsl_fp, 1); // Set a low timeout
        $response = call_user_func_array($lgsl_function, array(&$server, &$lgsl_cmd, &$lgsl_fp));
        @fclose($lgsl_fp);
      }

    }

//---------------------------------------------------------+
//  STATUS QUERY

    $lgsl_function = "lgsl_query_{$lgsl_protocol_list[$server['b']['type']]}";

    if ($lgsl_function == "lgsl_query_01") // TEST RETURNS DIRECT
    {
      $lgsl_fp = "";
      $response = call_user_func_array($lgsl_function, array(&$server, &$lgsl_cmd, &$lgsl_fp));
      return $server;
    }
    elseif ($lgsl_function == "lgsl_query_30" || $lgsl_function == "lgsl_query_33")
    {
      $scheme = "tcp";
      $lgsl_fp = @fsockopen("{$scheme}://{$server['b']['ip']}", $server['b']['q_port'], $errno, $errstr, 1);
      $response = call_user_func_array($lgsl_function, array(&$server, &$lgsl_cmd, &$lgsl_fp));
      @fclose($lgsl_fp);
    }
    else
    {
      $scheme = "udp";
      $lgsl_fp = @fsockopen("{$scheme}://{$server['b']['ip']}", $server['b']['q_port'], $errno, $errstr, 1);
      stream_set_timeout($lgsl_fp, 1); // Set a low timeout
      $response = call_user_func_array($lgsl_function, array(&$server, &$lgsl_cmd, &$lgsl_fp));
      @fclose($lgsl_fp);
    }
      
    $fields = lgsl_sort_fields($server, $fields_show, $fields_hide, $fields_other);
    $server = lgsl_sort_players($server);
    $server = lgsl_sort_extras($server);
    $misc   = lgsl_server_misc($server);
    $server = lgsl_server_html($server);

//---------------------------------------------------------+

  } else { $server['status'] = "404"; }

//------------------------------------------------------------------------------------------------------------+


  //unset($server['b']);  // No need to send this back
  //unset($server['o']);  // No need to send this back
  echo( json_encode($server, JSON_UNESCAPED_UNICODE) );

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+