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

  $link = lgsl_database();

//------------------------------------------------------------------------------------------------------------+
// CRON SETTINGS:

  @set_time_limit(3600);           // MAXIMUM TIME THE CRON IS ALLOWED TO TAKE
  $lgsl_config['cache_time'] = 60; // HOW OLD CACHE MUST BE BEFORE IT NEEDS REFRESHING
  $request = "sep";                // WHAT TO PRE-CACHE: [s] = BASIC INFO [e] = SETTINGS [p] = PLAYERS

//------------------------------------------------------------------------------------------------------------+

  echo "<pre>STARTING [ TIME LIMIT: ".ini_get("max_execution_time")." ] [ CACHE TIME: {$lgsl_config['cache_time']} ]\r\n\r\n";

//------------------------------------------------------------------------------------------------------------+

  $query  = "SELECT `type`,`ip`,`c_port`,`q_port`,`s_port` FROM `{$lgsl_config['db']['prefix']}{$lgsl_config['db']['table']}` WHERE `disabled`=0 ORDER BY `cache_time` ASC";
  $result = mysqli_query($link, $query);

  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
  {
    echo str_pad(lgsl_timer("taken"),  8,  " ").":".
         str_pad($row['type'],   15, " ").":".
         str_pad($row['ip'],     30, " ").":".
         str_pad($row['c_port'], 6,  " ").":".
         str_pad($row['q_port'], 6,  " ").":".
         str_pad($row['s_port'], 12, " ")."\r\n";

    lgsl_query_cached($row['type'], $row['ip'], $row['c_port'], $row['q_port'], $row['s_port'], $request);

    flush();
    ob_flush();
  }

  mysqli_free_result($result);
  
//------------------------------------------------------------------------------------------------------------+

  echo "\r\nFINISHED</pre>";

//------------------------------------------------------------------------------------------------------------+