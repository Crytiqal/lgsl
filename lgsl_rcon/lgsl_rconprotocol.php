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

  function lgsl_rcon_01(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  PROTOCOL FOR DEVELOPING WITHOUT USING LIVE SERVERS TO HELP ENSURE RETURNED
//  DATA IS SANITIZED AND THAT LONG SERVER AND PLAYER NAMES ARE HANDLED PROPERLY

    $server['s'] = array(
    "game"       => "test_game",
    "name"       => "test_ServerNameThatsOften'Really'LongAndCanHaveSymbols<hr />ThatWill\"Screw\"UpHtmlUnlessEntitied",
    "map"        => "test_map",
    "players"    => rand(0,  16),
    "playersmax" => rand(16, 32),
    "password"   => rand(0,  1));

//---------------------------------------------------------+

    $server['e'] = array(
    "testextra1" => "normal",
    "testextra2" => 123,
    "testextra3" => time(),
    "testextra4" => "",
    "testextra5" => "<b>Setting<hr />WithHtml</b>",
    "testextra6" => "ReallyLongSettingLikeSomeMapCyclesThatHaveNoSpacesAndCauseThePageToGoReallyWideIfNotBrokenUp");

//---------------------------------------------------------+

    $server['p']['0']['name']  = "Normal";
    $server['p']['0']['score'] = "12";
    $server['p']['0']['ping']  = "34";

    $server['p']['1']['name']  = "\xc3\xa9\x63\x68\x6f\x20\xd0\xb8-d0\xb3\xd1\x80\xd0\xbe\xd0\xba"; // UTF PLAYER NAME
    $server['p']['1']['score'] = "56";
    $server['p']['1']['ping']  = "78";

    $server['p']['2']['name']  = "One&<Two>&Three&\"Four\"&'Five'";
    $server['p']['2']['score'] = "90";
    $server['p']['2']['ping']  = "12";

    $server['p']['3']['name']  = "ReallyLongPlayerNameBecauseTheyAreUberCoolAndAreInFiveClans";
    $server['p']['3']['score'] = "90";
    $server['p']['3']['ping']  = "12";

//---------------------------------------------------------+

    if (rand(0, 10) == 5) { $server['p'] = array(); } // RANDOM NO PLAYERS
    if (rand(0, 10) == 5) { return FALSE; }           // RANDOM GOING OFFLINE

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_02(&$server, &$lgsl_cmd, &$lgsl_fp)
  {
//---------------------------------------------------------+

    if     ($server['b']['type'] == "quake2")              { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFstatus");        }
    elseif ($server['b']['type'] == "warsowold")           { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFgetinfo");       }
    elseif (strpos($server['b']['type'], "moh") !== FALSE) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x02getstatus"); } // mohaa_ mohaab_ mohaas_ mohpa_
    else                                                   { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFrcon " . $server['b']['rconpassword'] . " " . $lgsl_cmd . "\n");     }

    //$buffer = fread($lgsl_fp, 4096);

    $buffer = [];
    while ($line = fgets($lgsl_fp)) {
      $line = mb_convert_encoding(trim($line), 'UTF-8', 'UTF-8');
      if($line == "????print") { continue; } else { $buffer[] = $line; }
    }

    $server['r']['response'] = $buffer;
    
    if (!$buffer) { return FALSE; }
    
    //$part = explode("\n", $buffer);  // SPLIT INTO PARTS: HEADER/SETTINGS/PLAYERS/FOOTER
    //$part = array_slice($part, 2);
    //$server['r']['response'] = $part;

//---------------------------------------------------------+
    /*
    $part = explode("\n", $buffer);  // SPLIT INTO PARTS: HEADER/SETTINGS/PLAYERS/FOOTER
    array_pop($part);                // REMOVE FOOTER WHICH IS EITHER NULL OR "\challenge\"
    $item = explode("\\", $part[1]); // SPLIT PART INTO ITEMS

    foreach ($item as $item_key => $data_key)
    {
      if (!($item_key % 2)) { continue; } // SKIP EVEN KEYS

      $data_key               = strtolower(lgsl_parse_color($data_key, "1"));
      $server['e'][$data_key] = lgsl_parse_color($item[$item_key+1], "1");
    }
    */
//---------------------------------------------------------+
    /*
    if (!empty($server['e']['hostname']))    { $server['s']['name'] = $server['e']['hostname']; }
    if (!empty($server['e']['sv_hostname'])) { $server['s']['name'] = $server['e']['sv_hostname']; }

    if (isset($server['e']['gamename'])) { $server['s']['game'] = $server['e']['gamename']; }
    if (isset($server['e']['mapname']))  { $server['s']['map']  = $server['e']['mapname']; }

    $server['s']['players'] = empty($part['2']) ? 0 : count($part) - 2;

    if (isset($server['e']['maxclients']))    { $server['s']['playersmax'] = $server['e']['maxclients']; }    // QUAKE 2
    if (isset($server['e']['sv_maxclients'])) { $server['s']['playersmax'] = $server['e']['sv_maxclients']; }

    if (isset($server['e']['pswrd']))      { $server['s']['password'] = $server['e']['pswrd']; }              // CALL OF DUTY
    if (isset($server['e']['needpass']))   { $server['s']['password'] = $server['e']['needpass']; }           // QUAKE 2
    if (isset($server['e']['g_needpass'])) { $server['s']['password'] = $server['e']['g_needpass']; }

    array_shift($part); // REMOVE HEADER
    array_shift($part); // REMOVE SETTING
    */
//---------------------------------------------------------+
    /*
    if ($server['b']['type'] == "nexuiz") // (SCORE) (PING) (TEAM IF TEAM GAME) "(NAME)"
    {
      $pattern = "/(.*) (.*) (.*)\"(.*)\"/U"; $fields = array(1=>"score", 2=>"ping", 3=>"team", 4=>"name");
    }
    elseif ($server['b']['type'] == "warsow") // (SCORE) (PING) "(NAME)" (TEAM)
    {
      $pattern = "/(.*) (.*) \"(.*)\" (.*)/"; $fields = array(1=>"score", 2=>"ping", 3=>"name", 4=>"team");
    }
    elseif ($server['b']['type'] == "sof2") // (SCORE) (PING) (DEATHS) "(NAME)"
    {
      $pattern = "/(.*) (.*) (.*) \"(.*)\"/"; $fields = array(1=>"score", 2=>"ping", 3=>"deaths", 4=>"name");
    }
    elseif (strpos($server['b']['type'], "mohpa") !== FALSE) // (?) (SCORE) (?) (TIME) (?) "(RANK?)" "(NAME)"
    {
      $pattern = "/(.*) (.*) (.*) (.*) (.*) \"(.*)\" \"(.*)\"/"; $fields = array(2=>"score", 3=>"deaths", 4=>"time", 6=>"rank", 7=>"name");
    }
    elseif (strpos($server['b']['type'], "moh") !== FALSE) // (PING) "(NAME)"
    {
      $pattern = "/(.*) \"(.*)\"/"; $fields = array(1=>"ping", 2=>"name");
    }
    else // (SCORE) (PING) "(NAME)"
    {
      $pattern = "/(.*) (.*) \"(.*)\"/"; $fields = array(1=>"score", 2=>"ping", 3=>"name");
    }
    */
//---------------------------------------------------------+
    /*
    foreach ($part as $player_key => $data)
    {
      if (!$data) { continue; }

      preg_match($pattern, $data, $match);

      foreach ($fields as $match_key => $field_name)
      {
        if (isset($match[$match_key])) { $server['p'][$player_key][$field_name] = trim($match[$match_key]); }
      }

      $server['p'][$player_key]['name'] = lgsl_parse_color($server['p'][$player_key]['name'], "1");

      if (isset($server['p'][$player_key]['time']))
      {
        $server['p'][$player_key]['time'] = lgsl_time($server['p'][$player_key]['time']);
      }
    }
    */
//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_03(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    // BF1942 BUG: RETURNS 'GHOST' NAMES - TO SKIP THESE WE NEED AN [s] REQUEST FOR AN ACCURATE PLAYER COUNT
    if ($server['b']['type'] == "bf1942" && $lgsl_need['p'] && !$lgsl_need['s'] && !isset($lgsl_need['sp'])) { $lgsl_need['s'] = TRUE; $lgsl_need['sp'] = TRUE; }

    if     ($server['b']['type'] == "cncrenegade") { fwrite($lgsl_fp, "\\status\\"); }
    elseif ($lgsl_need['s'] || $lgsl_need['e'])    { fwrite($lgsl_fp, "\\basic\\\\info\\\\rules\\"); $lgsl_need['s'] = FALSE; $lgsl_need['e'] = FALSE; }
    elseif ($lgsl_need['p'])                       { fwrite($lgsl_fp, "\\players\\");                $lgsl_need['p'] = FALSE; }

//---------------------------------------------------------+

    $buffer = "";
    $packet_count = 0;
    $packet_total = 20;

    do
    {
      $packet = fread($lgsl_fp, 4096);

      // QUERY PORT CHECK AS THE CONNECTION PORT WILL ALSO RESPOND
      if (strpos($packet, "\\") === FALSE) { return FALSE; }

      // REMOVE SLASH PREFIX
      if ($packet[0] == "\\") { $packet = substr($packet, 1); }

      while ($packet)
      {
        $key   = strtolower(lgsl_cut_string($packet, 0, "\\"));
        $value =       trim(lgsl_cut_string($packet, 0, "\\"));

        // CHECK IF KEY IS PLAYER DATA
        if (preg_match("/(.*)_([0-9]+)$/", $key, $match))
        {
          // SEPERATE TEAM NAMES
          if ($match[1] == "teamname") { $server['t'][$match[2]]['name'] = $value; continue; }

          // CONVERT TO LGSL STANDARD
          if     ($match[1] == "player")     { $match[1] = "name";  }
          elseif ($match[1] == "playername") { $match[1] = "name";  }
          elseif ($match[1] == "frags")      { $match[1] = "score"; }
          elseif ($match[1] == "ngsecret")   { $match[1] = "stats"; }

          $server['p'][$match[2]][$match[1]] = $value; continue;
        }

        // SEPERATE QUERYID
        if ($key == "queryid") { $queryid = $value; continue; }

        // SERVER SETTING
        $server['e'][$key] = $value;
      }

      // FINAL PACKET NUMBER IS THE TOTAL
      if (isset($server['e']['final']))
      {
        preg_match("/([0-9]+)\.([0-9]+)/", $queryid, $match);
        $packet_total = intval($match[2]);
        unset($server['e']['final']);
      }

      $packet_count ++;
    }
    while ($packet_count < $packet_total);

//---------------------------------------------------------+

    if (isset($server['e']['mapname']))
    {
      $server['s']['map'] = $server['e']['mapname'];

      if (!empty($server['e']['hostname']))    { $server['s']['name'] = $server['e']['hostname']; }
      if (!empty($server['e']['sv_hostname'])) { $server['s']['name'] = $server['e']['sv_hostname']; }

      if (isset($server['e']['password']))   { $server['s']['password']   = $server['e']['password']; }
      if (isset($server['e']['numplayers'])) { $server['s']['players']    = $server['e']['numplayers']; }
      if (isset($server['e']['maxplayers'])) { $server['s']['playersmax'] = $server['e']['maxplayers']; }

      if (!empty($server['e']['gamename']))                                   { $server['s']['game'] = $server['e']['gamename']; }
      if (!empty($server['e']['gameid']) && empty($server['e']['gamename']))  { $server['s']['game'] = $server['e']['gameid']; }
      if (!empty($server['e']['gameid']) && $server['b']['type'] == "bf1942") { $server['s']['game'] = $server['e']['gameid']; }
    }

//---------------------------------------------------------+

    if ($server['p'])
    {
      // BF1942 BUG - REMOVE 'GHOST' PLAYERS
      if ($server['b']['type'] == "bf1942" && $server['s']['players'])
      {
        $server['p'] = array_slice($server['p'], 0, $server['s']['players']);
      }

      // OPERATION FLASHPOINT BUG: 'GHOST' PLAYERS IN UN-USED 'TEAM' FIELD
      if ($server['b']['type'] == "flashpoint")
      {
        foreach ($server['p'] as $key => $value)
        {
          unset($server['p'][$key]['team']);
        }
      }

      // AVP2 BUG: PLAYER NUMBER PREFIXED TO NAMES
      if ($server['b']['type'] == "avp2")
      {
        foreach ($server['p'] as $key => $value)
        {
          $server['p'][$key]['name'] = preg_replace("/[0-9]+~/", "", $server['p'][$key]['name']);
        }
      }

      // IF TEAM NAMES AVAILABLE USED INSTEAD OF TEAM NUMBERS
      if (isset($server['t'][0]['name']))
      {
        foreach ($server['p'] as $key => $value)
        {
          $team_key = $server['p'][$key]['team'] - 1;
          $server['p'][$key]['team'] = $server['t'][$team_key]['name'];
        }
      }

      // RE-INDEX PLAYER KEYS TO REMOVE ANY GAPS
      $server['p'] = array_values($server['p']);
    }

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_04(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp, "REPORT");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $lgsl_ravenshield_key = array(
    "A1" => "playersmax",
    "A2" => "tkpenalty",
    "B1" => "players",
    "B2" => "allowradar",
    "D2" => "version",
    "E1" => "mapname",
    "E2" => "lid",
    "F1" => "maptype",
    "F2" => "gid",
    "G1" => "password",
    "G2" => "hostport",
    "H1" => "dedicated",
    "H2" => "terroristcount",
    "I1" => "hostname",
    "I2" => "aibackup",
    "J1" => "mapcycletypes",
    "J2" => "rotatemaponsuccess",
    "K1" => "mapcycle",
    "K2" => "forcefirstpersonweapons",
    "L1" => "players_name",
    "L2" => "gamename",
    "L3" => "punkbuster",
    "M1" => "players_time",
    "N1" => "players_ping",
    "O1" => "players_score",
    "P1" => "queryport",
    "Q1" => "rounds",
    "R1" => "roundtime",
    "S1" => "bombtimer",
    "T1" => "bomb",
    "W1" => "allowteammatenames",
    "X1" => "iserver",
    "Y1" => "friendlyfire",
    "Z1" => "autobalance");

//---------------------------------------------------------+

    $item = explode("\xB6", $buffer);

    foreach ($item as $data_value)
    {
      $tmp = explode(" ", $data_value, 2);
      $data_key = isset($lgsl_ravenshield_key[$tmp[0]]) ? $lgsl_ravenshield_key[$tmp[0]] : $tmp[0]; // CONVERT TO DESCRIPTIVE KEYS
      $server['e'][$data_key] = trim($tmp[1]); // ALL VALUES NEED TRIMMING
    }

    $server['e']['mapcycle']      = str_replace("/"," ", $server['e']['mapcycle']);      // CONVERT SLASH TO SPACE
    $server['e']['mapcycletypes'] = str_replace("/"," ", $server['e']['mapcycletypes']); // SO LONG LISTS WRAP

//---------------------------------------------------------+

    $server['s']['game']       = $server['e']['gamename'];
    $server['s']['name']       = $server['e']['hostname'];
    $server['s']['map']        = $server['e']['mapname'];
    $server['s']['players']    = $server['e']['players'];
    $server['s']['playersmax'] = $server['e']['playersmax'];
    $server['s']['password']   = $server['e']['password'];

//---------------------------------------------------------+

    $player_name  = isset($server['e']['players_name'])  ? explode("/", substr($server['e']['players_name'],  1)) : array(); unset($server['e']['players_name']);
    $player_time  = isset($server['e']['players_time'])  ? explode("/", substr($server['e']['players_time'],  1)) : array(); unset($server['e']['players_time']);
    $player_ping  = isset($server['e']['players_ping'])  ? explode("/", substr($server['e']['players_ping'],  1)) : array(); unset($server['e']['players_ping']);
    $player_score = isset($server['e']['players_score']) ? explode("/", substr($server['e']['players_score'], 1)) : array(); unset($server['e']['players_score']);

    foreach ($player_name as $key => $name)
    {
      $server['p'][$key]['name']  = $player_name[$key];
      $server['p'][$key]['time']  = $player_time[$key];
      $server['p'][$key]['ping']  = $player_ping[$key];
      $server['p'][$key]['score'] = $player_score[$key];
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_05(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://developer.valvesoftware.com/wiki/Server_Queries

    if ($server['b']['type'] == "halflifewon")
    {
      if     ($lgsl_need['s']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFdetails\x00"); }
      elseif ($lgsl_need['p']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFplayers\x00"); }
      elseif ($lgsl_need['e']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFrules\x00");   }
    }
    else
    {
      $challenge_code = isset($lgsl_need['challenge']) ? $lgsl_need['challenge'] : "\x00\x00\x00\x00";

      if     ($lgsl_need['s']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00"); }
      elseif ($lgsl_need['p']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x55{$challenge_code}");       }
      elseif ($lgsl_need['e']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x56{$challenge_code}");       }
    }

//---------------------------------------------------------+
//  THE STANDARD HEADER POSITION REVEALS THE TYPE BUT IT MAY NOT ARRIVE FIRST
//  ONCE WE KNOW THE TYPE WE CAN FIND THE TOTAL NUMBER OF PACKETS EXPECTED

    $packet_temp  = array();
    $packet_type  = 0;
    $packet_count = 0;
    $packet_total = 4;

    do
    {
      $packet = fread($lgsl_fp, 4096); if (!$packet) { return FALSE; }

      //---------------------------------------------------------------------------------------------------------------------------------+
      // NEWER HL1 SERVERS REPLY TO A2S_INFO WITH 3 PACKETS ( HL1 FORMAT INFO, SOURCE FORMAT INFO, PLAYERS )
      // THIS DISCARDS UN-EXPECTED PACKET FORMATS ON THE GO ( AS READING IN ADVANCE CAUSES TIMEOUT DELAYS FOR OTHER SERVER VERSIONS )
      // ITS NOT PERFECT AS [s] CAN FLIP BETWEEN HL1 AND SOURCE FORMATS DEPENDING ON ARRIVAL ORDER ( MAYBE FIX WITH RETURN ON HL1 APPID )
      if     ($lgsl_need['s']) { if ($packet[4] == "D")                                           { continue; } }
      elseif ($lgsl_need['p']) { if ($packet[4] == "m" || $packet[4] == "I")                      { continue; } }
      elseif ($lgsl_need['e']) { if ($packet[4] == "m" || $packet[4] == "I" || $packet[4] == "D") { continue; } }
      //---------------------------------------------------------------------------------------------------------------------------------+

      if     (substr($packet, 0,  5) == "\xFF\xFF\xFF\xFF\x41") { $lgsl_need['challenge'] = substr($packet, 5,  4); return TRUE; } // REPEAT WITH GIVEN CHALLENGE CODE
      elseif (substr($packet, 0,  4) == "\xFF\xFF\xFF\xFF")     { $packet_total = 1;                     $packet_type = 1;       } // SINGLE PACKET - HL1 OR HL2
      elseif (substr($packet, 9,  4) == "\xFF\xFF\xFF\xFF")     { $packet_total = ord($packet[8]) & 0xF; $packet_type = 2;       } // MULTI PACKET  - HL1 ( TOTAL IS LOWER NIBBLE OF BYTE )
      elseif (substr($packet, 12, 4) == "\xFF\xFF\xFF\xFF")     { $packet_total = ord($packet[8]);       $packet_type = 3;       } // MULTI PACKET  - HL2
      elseif (substr($packet, 18, 2) == "BZ")                   { $packet_total = ord($packet[8]);       $packet_type = 4;       } // BZIP PACKET   - HL2

      $packet_count ++;
      $packet_temp[] = $packet;
    }
    while ($packet && $packet_count < $packet_total);

    if ($packet_type == 0) { return $server['s'] ? TRUE : FALSE; } // UNKNOWN RESPONSE ( SOME SERVERS ONLY SEND [s] )

//---------------------------------------------------------+
//  WITH THE TYPE WE CAN NOW SORT AND JOIN THE PACKETS IN THE CORRECT ORDER
//  REMOVING ANY EXTRA HEADERS IN THE PROCESS

    $buffer = array();

    foreach ($packet_temp as $packet)
    {
      if     ($packet_type == 1) { $packet_order = 0; }
      elseif ($packet_type == 2) { $packet_order = ord($packet[8]) >> 4; $packet = substr($packet, 9);  } // ( INDEX IS UPPER NIBBLE OF BYTE )
      elseif ($packet_type == 3) { $packet_order = ord($packet[9]);      $packet = substr($packet, 12); }
      elseif ($packet_type == 4) { $packet_order = ord($packet[9]);      $packet = substr($packet, 18); }

      $buffer[$packet_order] = $packet;
    }

    ksort($buffer);

    $buffer = implode("", $buffer);

//---------------------------------------------------------+
//  WITH THE PACKETS JOINED WE CAN NOW DECOMPRESS BZIP PACKETS
//  THEN REMOVE THE STANDARD HEADER AND CHECK ITS CORRECT

    if ($packet_type == 4)
    {
      if (!function_exists("bzdecompress")) // REQUIRES http://php.net/bzip2
      {
        $server['e']['bzip2'] = "unavailable"; $lgsl_need['e'] = FALSE;
        return TRUE;
      }

      $buffer = bzdecompress($buffer);
    }

    $header = lgsl_cut_byte($buffer, 4);

    if ($header != "\xFF\xFF\xFF\xFF") { return FALSE; } // SOMETHING WENT WRONG

//---------------------------------------------------------+

    $response_type = lgsl_cut_byte($buffer, 1);

    if ($response_type == "I") // SOURCE INFO ( HALF-LIFE 2 )
    {
      $server['e']['netcode']     = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['name']        = lgsl_cut_string($buffer);
      $server['s']['map']         = lgsl_cut_string($buffer);
      $server['s']['game']        = lgsl_cut_string($buffer);
      $server['e']['description'] = lgsl_cut_string($buffer);
      $server['e']['appid']       = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
      $server['s']['players']     = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['playersmax']  = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['bots']        = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['dedicated']   = lgsl_cut_byte($buffer, 1);
      $server['e']['os']          = lgsl_cut_byte($buffer, 1);
      $server['s']['password']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['anticheat']   = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['version']     = lgsl_cut_string($buffer);
    }

    elseif ($response_type == "m") // HALF-LIFE 1 INFO
    {
      $server_ip                  = lgsl_cut_string($buffer);
      $server['s']['name']        = lgsl_cut_string($buffer);
      $server['s']['map']         = lgsl_cut_string($buffer);
      $server['s']['game']        = lgsl_cut_string($buffer);
      $server['e']['description'] = lgsl_cut_string($buffer);
      $server['s']['players']     = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['playersmax']  = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['netcode']     = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['dedicated']   = lgsl_cut_byte($buffer, 1);
      $server['e']['os']          = lgsl_cut_byte($buffer, 1);
      $server['s']['password']    = ord(lgsl_cut_byte($buffer, 1));

      if (ord(lgsl_cut_byte($buffer, 1))) // MOD FIELDS ( OFF FOR SOME HALFLIFEWON-VALVE SERVERS )
      {
        $server['e']['mod_url_info']     = lgsl_cut_string($buffer);
        $server['e']['mod_url_download'] = lgsl_cut_string($buffer);
        $buffer = substr($buffer, 1);
        $server['e']['mod_version']      = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
        $server['e']['mod_size']         = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
        $server['e']['mod_server_side']  = ord(lgsl_cut_byte($buffer, 1));
        $server['e']['mod_custom_dll']   = ord(lgsl_cut_byte($buffer, 1));
      }

      $server['e']['anticheat'] = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['bots']      = ord(lgsl_cut_byte($buffer, 1));
    }

    elseif ($response_type == "D") // SOURCE AND HALF-LIFE 1 PLAYERS
    {
      $returned = ord(lgsl_cut_byte($buffer, 1));

      $player_key = 0;

      while ($buffer)
      {
        $server['p'][$player_key]['pid']   = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$player_key]['name']  = lgsl_cut_string($buffer);
        $server['p'][$player_key]['score'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
        $server['p'][$player_key]['time']  = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));

        $player_key ++;
      }
    }

    elseif ($response_type == "E") // SOURCE AND HALF-LIFE 1 RULES
    {
      $returned = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");

      while ($buffer)
      {
        $item_key   = strtolower(lgsl_cut_string($buffer));
        $item_value = lgsl_cut_string($buffer);

        $server['e'][$item_key] = $item_value;
      }
    }

//---------------------------------------------------------+

    // IF ONLY [s] WAS REQUESTED THEN REMOVE INCOMPLETE [e]
    if ($lgsl_need['s'] && !$lgsl_need['e']) { $server['e'] = array(); }

    if     ($lgsl_need['s']) { $lgsl_need['s'] = FALSE; }
    elseif ($lgsl_need['p']) { $lgsl_need['p'] = FALSE; }
    elseif ($lgsl_need['e']) { $lgsl_need['e'] = FALSE; }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_06(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  GET A CHALLENGE CODE IF NEEDED

    $challenge_code = "";

    if ($server['b']['type'] != "bf2" && $server['b']['type'] != "graw")
    {
      fwrite($lgsl_fp, "\xFE\xFD\x09\x21\x21\x21\x21\xFF\xFF\xFF\x01");

      $challenge_packet = fread($lgsl_fp, 4096);

      if (!$challenge_packet) { return FALSE; }

      $challenge_code = substr($challenge_packet, 5, -1); // REMOVE HEADER AND TRAILING NULL

      // IF CODE IS RETURNED ( SOME STALKER SERVERS RETURN BLANK WHERE THE CODE IS NOT NEEDED )
      // CONVERT DECIMAL |TO| HEX AS 8 CHARACTER STRING |TO| 4 PAIRS OF HEX |TO| 4 PAIRS OF DECIMAL |TO| 4 PAIRS OF ASCII

      $challenge_code = $challenge_code ? chr($challenge_code >> 24).chr($challenge_code >> 16).chr($challenge_code >> 8).chr($challenge_code >> 0) : "";
    }

    fwrite($lgsl_fp, "\xFE\xFD\x00\x21\x21\x21\x21{$challenge_code}\xFF\xFF\xFF\x01");

//---------------------------------------------------------+
//  GET RAW PACKET DATA

    $buffer = array();
    $packet_count = 0;
    $packet_total = 4;

    do
    {
      $packet_count ++;
      $packet = fread($lgsl_fp, 4096);

      if (!$packet) { return FALSE; }

      $packet       = substr($packet, 14); // REMOVE SPLITNUM HEADER
      $packet_order = ord(lgsl_cut_byte($packet, 1));

      if ($packet_order >= 128) // LAST PACKET - SO ITS ORDER NUMBER IS ALSO THE TOTAL
      {
        $packet_order -= 128;
        $packet_total = $packet_order + 1;
      }

      $buffer[$packet_order] = $packet;
    }
    while ($packet_count < $packet_total);

//---------------------------------------------------------+
//  PROCESS AND SORT PACKETS

    foreach ($buffer as $key => $packet)
    {
      $packet = substr($packet, 0, -1); // REMOVE END NULL FOR JOINING

      if (substr($packet, -1) != "\x00") // LAST VALUE HAS BEEN SPLIT
      {
        $part = explode("\x00", $packet); // REMOVE SPLIT VALUE AS COMPLETE VALUE IS IN NEXT PACKET
        array_pop($part);
        $packet = implode("\x00", $part)."\x00";
      }

      if ($packet[0] != "\x00") // PLAYER OR TEAM DATA THAT MAY BE A CONTINUATION
      {
        $pos = strpos($packet, "\x00") + 1; // WHEN DATA IS SPLIT THE NEXT PACKET STARTS WITH A REPEAT OF THE FIELD NAME

        if (isset($packet[$pos]) && $packet[$pos] != "\x00") // REPEATED FIELD NAMES END WITH \x00\x?? INSTEAD OF \x00\x00
        {
          $packet = substr($packet, $pos + 1); // REMOVE REPEATED FIELD NAME
        }
        else
        {
          $packet = "\x00".$packet; // RE-ADD NULL AS PACKET STARTS WITH A NEW FIELD
        }
      }

      $buffer[$key] = $packet;
    }

    ksort($buffer);

    $buffer = implode("", $buffer);

//---------------------------------------------------------+
//  SERVER SETTINGS

    $buffer = substr($buffer, 1); // REMOVE HEADER \x00

    while ($key = strtolower(lgsl_cut_string($buffer)))
    {
      $server['e'][$key] = lgsl_cut_string($buffer);
    }

    $lgsl_conversion = array("name"=>"hostname", "game"=>"gamename", "map"=>"mapname", "players"=>"numplayers", "playersmax"=>"maxplayers", "password"=>"password");
    foreach ($lgsl_conversion as $s => $e) { if (isset($server['e'][$e])) { $server['s'][$s] = $server['e'][$e]; unset($server['e'][$e]); } } // LGSL STANDARD

    if ($server['b']['type'] == "bf2" || $server['b']['type'] == "bf2142") { $server['s']['map'] = ucwords(str_replace("_", " ", $server['s']['map'])); } // MAP NAME CONSISTENCY

    if ($server['s']['players'] == "0") { return TRUE; } // IF SERVER IS EMPTY SKIP THE PLAYER CODE

//---------------------------------------------------------+
//  PLAYER DETAILS

    $buffer = substr($buffer, 1); // REMOVE HEADER \x01

    while ($buffer)
    {
      if ($buffer[0] == "\x02") { break; }
      if ($buffer[0] == "\x00") { $buffer = substr($buffer, 1); break; }

      $field = lgsl_cut_string($buffer, 0, "\x00\x00");
      $field = strtolower(substr($field, 0, -1));

      if     ($field == "player") { $field = "name"; }
      elseif ($field == "aibot")  { $field = "bot";  }

      if ($buffer[0] == "\x00") { $buffer = substr($buffer, 1); continue; }

      $value_list = lgsl_cut_string($buffer, 0, "\x00\x00");
      $value_list = explode("\x00", $value_list);

      foreach ($value_list as $key => $value)
      {
        $server['p'][$key][$field] = $value;
      }
    }

//---------------------------------------------------------+
//  TEAM DATA

    $buffer = substr($buffer, 1); // REMOVE HEADER \x02

    while ($buffer)
    {
      if ($buffer[0] == "\x00") { break; }

      $field = lgsl_cut_string($buffer, 0, "\x00\x00");
      $field = strtolower($field);

      if     ($field == "team_t")  { $field = "name";  }
      elseif ($field == "score_t") { $field = "score"; }

      $value_list = lgsl_cut_string($buffer, 0, "\x00\x00");
      $value_list = explode("\x00", $value_list);

      foreach ($value_list as $key => $value)
      {
        $server['t'][$key][$field] = $value;
      }
    }

//---------------------------------------------------------+
//  TEAM NAME CONVERSION

    if ($server['p'] && isset($server['t'][0]['name']) && $server['t'][0]['name'] != "Team")
    {
      foreach ($server['p'] as $key => $value)
      {
        if (empty($server['p'][$key]['team'])) { continue; }

        $team_key = $server['p'][$key]['team'] - 1;

        if (!isset($server['t'][$team_key]['name'])) { continue; }

        $server['p'][$key]['team'] = $server['t'][$team_key]['name'];
      }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_07(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFstatus\x00");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = substr($buffer, 6, -2); // REMOVE HEADER AND FOOTER
    $part   = explode("\n", $buffer); // SPLIT INTO SETTINGS/PLAYER/PLAYER/PLAYER

//---------------------------------------------------------+

    $item = explode("\\", $part[0]);

    foreach ($item as $item_key => $data_key)
    {
      if ($item_key % 2) { continue; } // SKIP ODD KEYS

      $data_key               = strtolower($data_key);
      $server['e'][$data_key] = $item[$item_key+1];
    }

//---------------------------------------------------------+

    array_shift($part); // REMOVE SETTINGS

    foreach ($part as $key => $data)
    {
      preg_match("/(.*) (.*) (.*) (.*) \"(.*)\" \"(.*)\" (.*) (.*)/s", $data, $match); // GREEDY MATCH FOR SKINS

      $server['p'][$key]['pid']         = $match[1];
      $server['p'][$key]['score']       = $match[2];
      $server['p'][$key]['time']        = $match[3];
      $server['p'][$key]['ping']        = $match[4];
      $server['p'][$key]['name']        = lgsl_parse_color($match[5], $server['b']['type']);
      $server['p'][$key]['skin']        = $match[6];
      $server['p'][$key]['skin_top']    = $match[7];
      $server['p'][$key]['skin_bottom'] = $match[8];
    }

//---------------------------------------------------------+

    $server['s']['game']       = $server['e']['*gamedir'];
    $server['s']['name']       = $server['e']['hostname'];
    $server['s']['map']        = $server['e']['map'];
    $server['s']['players']    = $server['p'] ? count($server['p']) : 0;
    $server['s']['playersmax'] = $server['e']['maxclients'];
    $server['s']['password']   = isset($server['e']['needpass']) && $server['e']['needpass'] > 0 && $server['e']['needpass'] < 4 ? 1 : 0;

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_08(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp, "s"); // ASE ( ALL SEEING EYE ) PROTOCOL

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = substr($buffer, 4); // REMOVE HEADER

    $server['e']['gamename']   = lgsl_cut_pascal($buffer, 1, -1);
    $server['e']['hostport']   = lgsl_cut_pascal($buffer, 1, -1);
    $server['s']['name']       = lgsl_parse_color(lgsl_cut_pascal($buffer, 1, -1), $server['b']['type']);
    $server['e']['gamemode']   = lgsl_cut_pascal($buffer, 1, -1);
    $server['s']['map']        = lgsl_cut_pascal($buffer, 1, -1);
    $server['e']['version']    = lgsl_cut_pascal($buffer, 1, -1);
    $server['s']['password']   = lgsl_cut_pascal($buffer, 1, -1);
    $server['s']['players']    = lgsl_cut_pascal($buffer, 1, -1);
    $server['s']['playersmax'] = lgsl_cut_pascal($buffer, 1, -1);

    while ($buffer && $buffer[0] != "\x01")
    {
      $item_key   = strtolower(lgsl_cut_pascal($buffer, 1, -1));
      $item_value = lgsl_cut_pascal($buffer, 1, -1);

      $server['e'][$item_key] = $item_value;
    }

    $buffer = substr($buffer, 1); // REMOVE END MARKER

//---------------------------------------------------------+

    $player_key = 0;

    while ($buffer)
    {
      $bit_flags = lgsl_cut_byte($buffer, 1); // FIELDS HARD CODED BELOW BECAUSE GAMES DO NOT USE THEM PROPERLY

      if     ($bit_flags == "\x3D")                 { $field_list = array("name",                  "score", "",     "time"); } // FARCRY PLAYERS CONNECTING
      elseif ($server['b']['type'] == "farcry")     { $field_list = array("name", "team", "",      "score", "ping", "time"); } // FARCRY PLAYERS JOINED
      elseif ($server['b']['type'] == "mta")        { $field_list = array("name", "",      "",     "score", "ping", ""    ); }
      elseif ($server['b']['type'] == "painkiller") { $field_list = array("name", "",     "skin",  "score", "ping", ""    ); }
      elseif ($server['b']['type'] == "soldat")     { $field_list = array("name", "team", "",      "score", "ping", "time"); }

      foreach ($field_list as $item_key)
      {
        $item_value = lgsl_cut_pascal($buffer, 1, -1);

        if (!$item_key) { continue; }

        if ($item_key == "name") { lgsl_parse_color($item_value, $server['b']['type']); }

        $server['p'][$player_key][$item_key] = $item_value;
      }

      $player_key ++;
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_09(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    // SERIOUS SAM 2 RETURNS ALL PLAYER NAMES AS "Unknown Player" SO SKIP OR CONVERT ANY PLAYER REQUESTS
    if ($server['b']['type'] == "serioussam2") { $lgsl_need['p'] = FALSE; if (!$lgsl_need['s'] && !$lgsl_need['e']) { $lgsl_need['s'] = TRUE; } }

//---------------------------------------------------------+

    if ($lgsl_need['s'] || $lgsl_need['e'])
    {
      $lgsl_need['s'] = FALSE; $lgsl_need['e'] = FALSE;

      fwrite($lgsl_fp, "\xFE\xFD\x00\x21\x21\x21\x21\xFF\x00\x00\x00");

      $buffer = fread($lgsl_fp, 4096);

      $buffer = substr($buffer, 5, -2); // REMOVE HEADER AND FOOTER

      if (!$buffer) { return FALSE; }

      $item = explode("\x00", $buffer);

      foreach ($item as $item_key => $data_key)
      {
        if ($item_key % 2) { continue; } // SKIP EVEN KEYS

        $data_key = strtolower($data_key);
        $server['e'][$data_key] = $item[$item_key+1];
      }

      if (isset($server['e']['hostname']))   { $server['s']['name']       = $server['e']['hostname']; }
      if (isset($server['e']['mapname']))    { $server['s']['map']        = $server['e']['mapname']; }
      if (isset($server['e']['numplayers'])) { $server['s']['players']    = $server['e']['numplayers']; }
      if (isset($server['e']['maxplayers'])) { $server['s']['playersmax'] = $server['e']['maxplayers']; }
      if (isset($server['e']['password']))   { $server['s']['password']   = $server['e']['password']; }

      if (!empty($server['e']['gamename']))   { $server['s']['game'] = $server['e']['gamename']; }   // AARMY
      if (!empty($server['e']['gsgamename'])) { $server['s']['game'] = $server['e']['gsgamename']; } // FEAR
      if (!empty($server['e']['game_id']))    { $server['s']['game'] = $server['e']['game_id']; }    // BFVIETNAM

      if ($server['b']['type'] == "arma" || $server['b']['type'] == "arma2")
      {
        $server['s']['map'] = $server['e']['mission'];
      }
      elseif ($server['b']['type'] == "vietcong2")
      {
        $server['e']['extinfo_autobalance'] = ord($server['e']['extinfo'][18]) == 2 ? "off" : "on";
        // [ 13 = Vietnam and RPG Mode 19 1b 99 9b ] [ 22 23 = Mounted MG Limit ]
        // [ 27 = Idle Limit ] [ 18 = Auto Balance ] [ 55 = Chat and Blind Spectator 5a 5c da dc ]
      }
    }

//---------------------------------------------------------+

    elseif ($lgsl_need['p'])
    {
      $lgsl_need['p'] = FALSE;

      fwrite($lgsl_fp, "\xFE\xFD\x00\x21\x21\x21\x21\x00\xFF\x00\x00");

      $buffer = fread($lgsl_fp, 4096);

      $buffer = substr($buffer, 7, -1); // REMOVE HEADER / PLAYER TOTAL / FOOTER

      if (!$buffer) { return FALSE; }

      if (strpos($buffer, "\x00\x00") === FALSE) { return TRUE; } // NO PLAYERS

      $buffer     = explode("\x00\x00",$buffer, 2);            // SPLIT FIELDS FROM ITEMS
      $buffer[0]  = str_replace("_",      "",     $buffer[0]); // REMOVE UNDERSCORES FROM FIELDS
      $buffer[0]  = str_replace("player", "name", $buffer[0]); // LGSL STANDARD
      $field_list = explode("\x00",$buffer[0]);                // SPLIT UP FIELDS
      $item       = explode("\x00",$buffer[1]);                // SPLIT UP ITEMS

      $item_position = 0;
      $item_total    = count($item);
      $player_key    = 0;

      do
      {
        foreach ($field_list as $field)
        {
          $server['p'][$player_key][$field] = $item[$item_position];

          $item_position ++;
        }

        $player_key ++;
      }
      while ($item_position < $item_total);
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_10(&$server, &$lgsl_cmd, &$lgsl_fp)
  {
//---------------------------------------------------------+

    if ($server['b']['type'] == "etqw") { fwrite($lgsl_fp, "\xFF\xFFrcon\xFF".$server['b']['rconpassword']."\xFF".$lgsl_cmd."\x00"); }
    else                                { fwrite($lgsl_fp, "\xFF\xFFrcon\xFF".$server['b']['rconpassword']."\xFF".$lgsl_cmd."\x00"); }

    // $buffer = fread($lgsl_fp, 4096);

    $buffer = [];
    while ($line = fgets($lgsl_fp)) {
      $buffer[] = $line;
    }

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+
    /*
    if     ($server['b']['type'] == "wolf2009")  { $buffer = substr($buffer, 31); }  // REMOVE HEADERS
    elseif ($server['b']['type'] == "etqw") { $buffer = substr($buffer, 33); }
    else                                         { $buffer = substr($buffer, 23); }
    */
    $buffer = lgsl_parse_color($buffer, "2");

    $server['r']['response'] = $buffer;

//---------------------------------------------------------+
    /*
    while ($buffer && $buffer[0] != "\x00")
    {
      $item_key   = strtolower(lgsl_cut_string($buffer));
      $item_value = lgsl_cut_string($buffer);

      $server['e'][$item_key] = $item_value;
    }
    */
//---------------------------------------------------------+
    /*
    $buffer = substr($buffer, 2);

    $player_key = 0;
    */
//---------------------------------------------------------+
    /*
    if ($server['b']['type'] == "wolf2009") // WOLFENSTEIN: (PID)(PING)(NAME)(TAGPOSITION)(TAG)(BOT)
    {
      while ($buffer && $buffer[0] != "\x10") // STOPS AT PID 16
      {
        $server['p'][$player_key]['pid']     = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$player_key]['ping']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $server['p'][$player_key]['rate']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $server['p'][$player_key]['unknown'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $player_name                         = lgsl_cut_string($buffer);
        $player_tag_position                 = ord(lgsl_cut_byte($buffer, 1));
        $player_tag                          = lgsl_cut_string($buffer);
        $server['p'][$player_key]['bot']     = ord(lgsl_cut_byte($buffer, 1));

        if     ($player_tag == "")           { $server['p'][$player_key]['name'] = $player_name; }
        elseif ($player_tag_position == "0") { $server['p'][$player_key]['name'] = $player_tag." ".$player_name; }
        else                                 { $server['p'][$player_key]['name'] = $player_name." ".$player_tag; }

        $player_key ++;
      }
    }
    */
//---------------------------------------------------------+
    /*
    elseif ($server['b']['type'] == "etqw") // QUAKEWARS: (PID)(PING)(NAME)(TAGPOSITION)(TAG)(BOT)
    {
      while ($buffer && $buffer[0] != "\x20") // STOPS AT PID 32
      {
        $server['p'][$player_key]['pid']  = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$player_key]['ping'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $player_name                      = lgsl_cut_string($buffer);
        $player_tag_position              = ord(lgsl_cut_byte($buffer, 1));
        $player_tag                       = lgsl_cut_string($buffer);
        $server['p'][$player_key]['bot']  = ord(lgsl_cut_byte($buffer, 1));

            if ($player_tag_position == "")  { $server['p'][$player_key]['name'] = $player_name; }
        elseif ($player_tag_position == "1") { $server['p'][$player_key]['name'] = $player_name." ".$player_tag; }
        else                                 { $server['p'][$player_key]['name'] = $player_tag." ".$player_name; }

        $player_key ++;
      }

      $buffer                      = substr($buffer, 1);
      $server['e']['si_osmask']    = lgsl_unpack(lgsl_cut_byte($buffer, 4), "I");
      $server['e']['si_ranked']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['si_timeleft']  = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "I") / 1000);
      $server['e']['si_gamestate'] = ord(lgsl_cut_byte($buffer, 1));
      $buffer                      = substr($buffer, 2);

      $player_key = 0;

      while ($buffer && $buffer[0] != "\x20") // QUAKEWARS EXTENDED: (PID)(XP)(TEAM)(KILLS)(DEATHS)
      {
        $server['p'][$player_key]['pid']    = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$player_key]['xp']     = intval(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
        $server['p'][$player_key]['team']   = lgsl_cut_string($buffer);
        $server['p'][$player_key]['score']  = lgsl_unpack(lgsl_cut_byte($buffer, 4), "i");
        $server['p'][$player_key]['deaths'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "i");
        $player_key ++;
      }
    }
    */
//---------------------------------------------------------+
    /*
    elseif ($server['b']['type'] == "quake4") // QUAKE4: (PID)(PING)(RATE)(NULLNULL)(NAME)(TAG)
    {
      while ($buffer && $buffer[0] != "\x20") // STOPS AT PID 32
      {
        $server['p'][$player_key]['pid']  = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$player_key]['ping'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $server['p'][$player_key]['rate'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $buffer                           = substr($buffer, 2);
        $player_name                      = lgsl_cut_string($buffer);
        $player_tag                       = lgsl_cut_string($buffer);
        $server['p'][$player_key]['name'] = $player_tag ? $player_tag." ".$player_name : $player_name;

        $player_key ++;
      }
    }
    */
//---------------------------------------------------------+
    /*
    else // DOOM3 AND PREY: (PID)(PING)(RATE)(NULLNULL)(NAME)
    {
      while ($buffer && $buffer[0] != "\x20") // STOPS AT PID 32
      {
        $server['p'][$player_key]['pid']  = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$player_key]['ping'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $server['p'][$player_key]['rate'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $buffer                           = substr($buffer, 2);
        $server['p'][$player_key]['name'] = lgsl_cut_string($buffer);

        $player_key ++;
      }
    }
    */
//---------------------------------------------------------+
    /*
    $server['s']['game']       = $server['e']['gamename'];
    $server['s']['name']       = $server['e']['si_name'];
    $server['s']['map']        = $server['e']['si_map'];
    $server['s']['players']    = $server['p'] ? count($server['p']) : 0;
    $server['s']['playersmax'] = $server['e']['si_maxplayers'];

    if ($server['b']['type'] == "wolf2009" || $server['b']['type'] == "etqw")
    {
      $server['s']['map']      = str_replace(".entities", "", $server['s']['map']);
      $server['s']['password'] = $server['e']['si_needpass'];
    }
    else
    {
      $server['s']['password'] = $server['e']['si_usepass'];
    }
    */
//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_11(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://wiki.unrealadmin.org/UT3_query_protocol
//  UT3 RESPONSE IS REALLY MESSY SO THIS CLEANS IT UP

    $status = lgsl_rcon_06($server, $lgsl_need, $lgsl_fp);

    if (!$status) { return FALSE; }

//---------------------------------------------------------+

    $server['s']['map'] = $server['e']['p1073741825'];
                    unset($server['e']['p1073741825']);

//---------------------------------------------------------+

    $lgsl_ut3_key = array(
    "s0"          => "bots_skill",
    "s6"          => "pure",
    "s7"          => "password",
    "s8"          => "bots_vs",
    "s10"         => "forcerespawn",
    "p268435703"  => "bots",
    "p268435704"  => "goalscore",
    "p268435705"  => "timelimit",
    "p268435717"  => "mutators_default",
    "p1073741826" => "gamemode",
    "p1073741827" => "description",
    "p1073741828" => "mutators_custom");

    foreach ($lgsl_ut3_key as $old => $new)
    {
      if (!isset($server['e'][$old])) { continue; }
      $server['e'][$new] = $server['e'][$old];
      unset($server['e'][$old]);
    }

//---------------------------------------------------------+

    $part = explode(".", $server['e']['gamemode']);

    if ($part[0] && (stristr($part[0], "UT") === FALSE))
    {
      $server['s']['game'] = $part[0];
    }

//---------------------------------------------------------+

    $tmp = $server['e']['mutators_default'];
           $server['e']['mutators_default'] = "";

    if ($tmp & 1)     { $server['e']['mutators_default'] .= " BigHead";           }
    if ($tmp & 2)     { $server['e']['mutators_default'] .= " FriendlyFire";      }
    if ($tmp & 4)     { $server['e']['mutators_default'] .= " Handicap";          }
    if ($tmp & 8)     { $server['e']['mutators_default'] .= " Instagib";          }
    if ($tmp & 16)    { $server['e']['mutators_default'] .= " LowGrav";           }
    if ($tmp & 64)    { $server['e']['mutators_default'] .= " NoPowerups";        }
    if ($tmp & 128)   { $server['e']['mutators_default'] .= " NoTranslocator";    }
    if ($tmp & 256)   { $server['e']['mutators_default'] .= " Slomo";             }
    if ($tmp & 1024)  { $server['e']['mutators_default'] .= " SpeedFreak";        }
    if ($tmp & 2048)  { $server['e']['mutators_default'] .= " SuperBerserk";      }
    if ($tmp & 8192)  { $server['e']['mutators_default'] .= " WeaponReplacement"; }
    if ($tmp & 16384) { $server['e']['mutators_default'] .= " WeaponsRespawn";    }

    $server['e']['mutators_default'] = str_replace(" ",    " / ", trim($server['e']['mutators_default']));
    $server['e']['mutators_custom']  = str_replace("\x1c", " / ",      $server['e']['mutators_custom']);

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_12(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE:
//  VICE CITY CURRENTLY ONLY SUPPORTS THE 'i' CHALLENGE

    if     ($server['b']['type'] == "samp") { $challenge_packet = "SAMP\x21\x21\x21\x21\x00\x00"; }
    elseif ($server['b']['type'] == "vcmp") { $challenge_packet = "VCMP\x21\x21\x21\x21\x00\x00"; $lgsl_need['e'] = FALSE; $lgsl_need['p'] = FALSE; }

    if     ($lgsl_need['s']) { $challenge_packet .= "i"; }
    elseif ($lgsl_need['e']) { $challenge_packet .= "r"; }
    elseif ($lgsl_need['p']) { $challenge_packet .= "d"; }

    fwrite($lgsl_fp, $challenge_packet);

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = substr($buffer, 10); // REMOVE HEADER

    $response_type = lgsl_cut_byte($buffer, 1);

//---------------------------------------------------------+

    if ($response_type == "i")
    {
      $lgsl_need['s'] = FALSE;

      $server['s']['password']   = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['players']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
      $server['s']['playersmax'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
      $server['s']['name']       = lgsl_cut_pascal($buffer, 4);
      $server['e']['gamemode']   = lgsl_cut_pascal($buffer, 4);
      $server['s']['map']        = lgsl_cut_pascal($buffer, 4);
    }

//---------------------------------------------------------+

    elseif ($response_type == "r")
    {
      $lgsl_need['e'] = FALSE;

      $item_total = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");

      for ($i=0; $i<$item_total; $i++)
      {
        if (!$buffer) { return FALSE; }

        $data_key   = strtolower(lgsl_cut_pascal($buffer));
        $data_value = lgsl_cut_pascal($buffer);

        $server['e'][$data_key] = $data_value;
      }
    }

//---------------------------------------------------------+

    elseif ($response_type == "d")
    {
      $lgsl_need['p'] = FALSE;

      $player_total = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");

      for ($i=0; $i<$player_total; $i++)
      {
        if (!$buffer) { return FALSE; }

        $server['p'][$i]['pid']   = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$i]['name']  = lgsl_cut_pascal($buffer);
        $server['p'][$i]['score'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "S");
        $server['p'][$i]['ping']  = lgsl_unpack(lgsl_cut_byte($buffer, 4), "S");
      }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_13(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    $buffer_s = ""; fwrite($lgsl_fp, "\x21\x21\x21\x21\x00"); // REQUEST [s]
    $buffer_e = ""; fwrite($lgsl_fp, "\x21\x21\x21\x21\x01"); // REQUEST [e]
    $buffer_p = ""; fwrite($lgsl_fp, "\x21\x21\x21\x21\x02"); // REQUEST [p]

//---------------------------------------------------------+

    while ($packet = fread($lgsl_fp, 4096))
    {
      if     ($packet[4] == "\x00") { $buffer_s .= substr($packet, 5); }
      elseif ($packet[4] == "\x01") { $buffer_e .= substr($packet, 5); }
      elseif ($packet[4] == "\x02") { $buffer_p .= substr($packet, 5); }
    }

    if (!$buffer_s) { return FALSE; }

//---------------------------------------------------------+
//  SOME VALUES START WITH A PASCAL LENGTH AND END WITH A NULL BUT THERE IS AN ISSUE WHERE
//  CERTAIN CHARACTERS CAUSE A WRONG PASCAL LENGTH AND NULLS TO APPEAR WITHIN NAMES

    $buffer_s = str_replace("\xa0", "\x20", $buffer_s); // REPLACE SPECIAL SPACE WITH NORMAL SPACE
    $buffer_s = substr($buffer_s, 5);
    $server['e']['hostport']   = lgsl_unpack(lgsl_cut_byte($buffer_s, 4), "S");
    $buffer_s = substr($buffer_s, 4);
    $server['s']['name']       = lgsl_cut_string($buffer_s, 1);
    $server['s']['map']        = lgsl_cut_string($buffer_s, 1);
    $server['e']['gamemode']   = lgsl_cut_string($buffer_s, 1);
    $server['s']['players']    = lgsl_unpack(lgsl_cut_byte($buffer_s, 4), "S");
    $server['s']['playersmax'] = lgsl_unpack(lgsl_cut_byte($buffer_s, 4), "S");

//---------------------------------------------------------+

    while ($buffer_e && $buffer_e[0] != "\x00")
    {
      $item_key   = strtolower(lgsl_cut_string($buffer_e, 1));
      $item_value = lgsl_cut_string($buffer_e, 1);

      $item_key   = str_replace("\x1B\xFF\xFF\x01", "", $item_key);   // REMOVE MOD
      $item_value = str_replace("\x1B\xFF\xFF\x01", "", $item_value); // GARBAGE

      $server['e'][$item_key] = $item_value;
    }

//---------------------------------------------------------+
//  THIS PROTOCOL RETURNS MORE INFO THAN THE ALTERNATIVE BUT IT DOES NOT
//  RETURN THE GAME NAME ! SO WE HAVE MANUALLY DETECT IT USING THE GAME TYPE

    $tmp = strtolower(substr($server['e']['gamemode'], 0, 2));

        if ($tmp == "ro") { $server['s']['game'] = "Red Orchestra"; }
    elseif ($tmp == "kf") { $server['s']['game'] = "Killing Floor"; }

   $server['s']['password'] = empty($server['e']['password']) && empty($server['e']['gamepassword']) ? "0" : "1";

//---------------------------------------------------------+

    $player_key = 0;

    while ($buffer_p && $buffer_p[0] != "\x00")
    {
      $server['p'][$player_key]['pid']   = lgsl_unpack(lgsl_cut_byte($buffer_p, 4), "S");

      $end_marker = ord($buffer_p[0]) > 64 ? "\x00\x00" : "\x00"; // DIRTY WORK-AROUND FOR NAMES WITH PROBLEM CHARACTERS

      $server['p'][$player_key]['name']  = lgsl_cut_string($buffer_p, 1, $end_marker);
      $server['p'][$player_key]['ping']  = lgsl_unpack(lgsl_cut_byte($buffer_p, 4), "S");
      $server['p'][$player_key]['score'] = lgsl_unpack(lgsl_cut_byte($buffer_p, 4), "s");
      $tmp                               = lgsl_cut_byte($buffer_p, 4);

          if ($tmp[3] == "\x20") { $server['p'][$player_key]['team'] = 1; }
      elseif ($tmp[3] == "\x40") { $server['p'][$player_key]['team'] = 2; }

      $player_key ++;
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_14(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://flstat.cryosphere.co.uk/global-list.php

    fwrite($lgsl_fp, "\x00\x02\xf1\x26\x01\x26\xf0\x90\xa6\xf0\x26\x57\x4e\xac\xa0\xec\xf8\x68\xe4\x8d\x21");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = substr($buffer, 4); // HEADER   ( 00 03 F1 26 )
    $buffer = substr($buffer, 4); // NOT USED ( 87 + NAME LENGTH )
    $buffer = substr($buffer, 4); // NOT USED ( NAME END TO BUFFER END LENGTH )
    $buffer = substr($buffer, 4); // UNKNOWN  ( 80 )

    $server['s']['map']        = "freelancer";
    $server['s']['password']   = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l") - 1 ? 1 : 0;
    $server['s']['playersmax'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l") - 1;
    $server['s']['players']    = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l") - 1;
    $buffer                    = substr($buffer, 4);  // UNKNOWN ( 88 )
    $name_length               = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
    $buffer                    = substr($buffer, 56); // UNKNOWN
    $server['s']['name']       = lgsl_cut_byte($buffer, $name_length);

    lgsl_cut_string($buffer, 0, ":");
    lgsl_cut_string($buffer, 0, ":");
    lgsl_cut_string($buffer, 0, ":");
    lgsl_cut_string($buffer, 0, ":");
    lgsl_cut_string($buffer, 0, ":");

    // WHATS LEFT IS THE MOTD
    $server['e']['motd'] = substr($buffer, 0, -1);

    // REMOVE UTF-8 ENCODING NULLS
    $server['s']['name'] = str_replace("\x00", "", $server['s']['name']);
    $server['e']['motd'] = str_replace("\x00", "", $server['e']['motd']);

    // DOES NOT RETURN PLAYER INFORMATION

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_15(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp, "GTR2_Direct_IP_Search\x00");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = str_replace("\xFE", "\xFF", $buffer);
    $buffer = explode("\xFF", $buffer);

    $server['s']['name']       = $buffer[3];
    $server['s']['game']       = $buffer[7];
    $server['e']['version']    = $buffer[11];
    $server['e']['hostport']   = $buffer[15];
    $server['s']['map']        = $buffer[19];
    $server['s']['players']    = $buffer[25];
    $server['s']['playersmax'] = $buffer[27];
    $server['e']['gamemode']   = $buffer[31];

    // DOES NOT RETURN PLAYER INFORMATION

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_16(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE:
//  http://www.planetpointy.co.uk/software/rfactorsspy.shtml
//  http://users.pandora.be/viperius/mUtil/
//  USES FIXED DATA POSITIONS WITH RANDOM CHARACTERS FILLING THE GAPS

    fwrite($lgsl_fp, "rF_S");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

//  $server['e']['gamename']         = lgsl_get_string($buffer);
    $buffer = substr($buffer, 8);
//  $server['e']['fullupdate']       = lgsl_unpack($buffer[0], "C");
    $server['e']['region']           = lgsl_unpack($buffer[1] .$buffer[2],  "S");
//  $server['e']['ip']               = ($buffer[3] .$buffer[4].$buffer[5].$buffer[6]); // UNSIGNED LONG
//  $server['e']['size']             = lgsl_unpack($buffer[7] .$buffer[8],  "S");
    $server['e']['version']          = lgsl_unpack($buffer[9] .$buffer[10], "S");
//  $server['e']['version_racecast'] = lgsl_unpack($buffer[11].$buffer[12], "S");
    $server['e']['hostport']         = lgsl_unpack($buffer[13].$buffer[14], "S");
//  $server['e']['queryport']        = lgsl_unpack($buffer[15].$buffer[16], "S");
    $buffer = substr($buffer, 17);
    $server['s']['game']             = lgsl_get_string($buffer);
    $buffer = substr($buffer, 20);
    $server['s']['name']             = lgsl_get_string($buffer);
    $buffer = substr($buffer, 28);
    $server['s']['map']              = lgsl_get_string($buffer);
    $buffer = substr($buffer, 32);
    $server['e']['motd']             = lgsl_get_string($buffer);
    $buffer = substr($buffer, 96);
    $server['e']['packed_aids']      = lgsl_unpack($buffer[0].$buffer[1], "S");
//  $server['e']['ping']             = lgsl_unpack($buffer[2].$buffer[3], "S");
    $server['e']['packed_flags']     = lgsl_unpack($buffer[4],  "C");
    $server['e']['rate']             = lgsl_unpack($buffer[5],  "C");
    $server['s']['players']          = lgsl_unpack($buffer[6],  "C");
    $server['s']['playersmax']       = lgsl_unpack($buffer[7],  "C");
    $server['e']['bots']             = lgsl_unpack($buffer[8],  "C");
    $server['e']['packed_special']   = lgsl_unpack($buffer[9],  "C");
    $server['e']['damage']           = lgsl_unpack($buffer[10], "C");
    $server['e']['packed_rules']     = lgsl_unpack($buffer[11].$buffer[12], "S");
    $server['e']['credits1']         = lgsl_unpack($buffer[13], "C");
    $server['e']['credits2']         = lgsl_unpack($buffer[14].$buffer[15], "S");
    $server['e']['time']   = lgsl_time(lgsl_unpack($buffer[16].$buffer[17], "S"));
    $server['e']['laps']             = lgsl_unpack($buffer[18].$buffer[19], "s") / 16;
    $buffer = substr($buffer, 23);
    $server['e']['vehicles']         = lgsl_get_string($buffer);

    // DOES NOT RETURN PLAYER INFORMATION

//---------------------------------------------------------+

    $server['s']['password']    = ($server['e']['packed_special'] & 2)  ? 1 : 0;
    $server['e']['racecast']    = ($server['e']['packed_special'] & 4)  ? 1 : 0;
    $server['e']['fixedsetups'] = ($server['e']['packed_special'] & 16) ? 1 : 0;

                                              $server['e']['aids']  = "";
    if ($server['e']['packed_aids'] & 1)    { $server['e']['aids'] .= " TractionControl"; }
    if ($server['e']['packed_aids'] & 2)    { $server['e']['aids'] .= " AntiLockBraking"; }
    if ($server['e']['packed_aids'] & 4)    { $server['e']['aids'] .= " StabilityControl"; }
    if ($server['e']['packed_aids'] & 8)    { $server['e']['aids'] .= " AutoShifting"; }
    if ($server['e']['packed_aids'] & 16)   { $server['e']['aids'] .= " AutoClutch"; }
    if ($server['e']['packed_aids'] & 32)   { $server['e']['aids'] .= " Invulnerability"; }
    if ($server['e']['packed_aids'] & 64)   { $server['e']['aids'] .= " OppositeLock"; }
    if ($server['e']['packed_aids'] & 128)  { $server['e']['aids'] .= " SteeringHelp"; }
    if ($server['e']['packed_aids'] & 256)  { $server['e']['aids'] .= " BrakingHelp"; }
    if ($server['e']['packed_aids'] & 512)  { $server['e']['aids'] .= " SpinRecovery"; }
    if ($server['e']['packed_aids'] & 1024) { $server['e']['aids'] .= " AutoPitstop"; }

    $server['e']['aids']     = str_replace(" ", " / ", trim($server['e']['aids']));
    $server['e']['vehicles'] = str_replace("|", " / ", trim($server['e']['vehicles']));

    unset($server['e']['packed_aids']);
    unset($server['e']['packed_flags']);
    unset($server['e']['packed_special']);
    unset($server['e']['packed_rules']);

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_17(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://masterserver.savage.s2games.com

    fwrite($lgsl_fp, "\x9e\x4c\x23\x00\x00\xce\x21\x21\x21\x21");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = substr($buffer, 12); // REMOVE HEADER

    while ($key = strtolower(lgsl_cut_string($buffer, 0, "\xFE")))
    {
      if ($key == "players") { break; }

      $value = lgsl_cut_string($buffer, 0, "\xFF");
      $value = str_replace("\x00", "", $value);
      $value = lgsl_parse_color($value, $server['b']['type']);

      $server['e'][$key] = $value;
    }

    $server['s']['name']       = $server['e']['name'];  unset($server['e']['name']);
    $server['s']['map']        = $server['e']['world']; unset($server['e']['world']);
    $server['s']['players']    = $server['e']['cnum'];  unset($server['e']['cnum']);
    $server['s']['playersmax'] = $server['e']['cmax'];  unset($server['e']['cnum']);
    $server['s']['password']   = $server['e']['pass'];  unset($server['e']['cnum']);

//---------------------------------------------------------+

    $server['t'][0]['name'] = $server['e']['race1'];
    $server['t'][1]['name'] = $server['e']['race2'];
    $server['t'][2]['name'] = "spectator";

    $team_key   = -1;
    $player_key = 0;

    while ($value = lgsl_cut_string($buffer, 0, "\x0a"))
    {
      if ($value[0] == "\x00") { break; }
      if ($value[0] != "\x20") { $team_key++; continue; }

      $server['p'][$player_key]['name'] = lgsl_parse_color(substr($value, 1), $server['b']['type']);
      $server['p'][$player_key]['team'] = $server['t'][$team_key]['name'];

      $player_key++;
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_18(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://masterserver.savage2.s2games.com

    fwrite($lgsl_fp, "\x01");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = substr($buffer, 12); // REMOVE HEADER

    $server['s']['name']            = lgsl_cut_string($buffer);
    $server['s']['players']         = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax']      = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['time']            = lgsl_cut_string($buffer);
    $server['s']['map']             = lgsl_cut_string($buffer);
    $server['e']['nextmap']         = lgsl_cut_string($buffer);
    $server['e']['location']        = lgsl_cut_string($buffer);
    $server['e']['minimum_players'] = ord(lgsl_cut_string($buffer));
    $server['e']['gamemode']        = lgsl_cut_string($buffer);
    $server['e']['version']         = lgsl_cut_string($buffer);
    $server['e']['minimum_level']   = ord(lgsl_cut_byte($buffer, 1));

    // DOES NOT RETURN PLAYER INFORMATION

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_19(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp, "\xC0\xDE\xF1\x11\x42\x06\x00\xF5\x03\x21\x21\x21\x21");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $buffer = substr($buffer, 25); // REMOVE HEADER

    $server['s']['name']       = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));
    $server['s']['map']        = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));
    $server['e']['nextmap']    = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));
    $server['e']['gametype']   = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));

    $buffer = substr($buffer, 1);

    $server['s']['password']   = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 4));
    $server['s']['players']    = ord(lgsl_cut_byte($buffer, 4));

//---------------------------------------------------------+

    for ($player_key=0; $player_key<$server['s']['players']; $player_key++)
    {
     $server['p'][$player_key]['name'] = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));
    }

//---------------------------------------------------------+

    $buffer = substr($buffer, 17);

    $server['e']['version']    = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));
    $server['e']['mods']       = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));
    $server['e']['dedicated']  = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['time']       = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['status']     = ord(lgsl_cut_byte($buffer, 4));
    $server['e']['gamemode']   = ord(lgsl_cut_byte($buffer, 4));
    $server['e']['motd']       = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));
    $server['e']['respawns']   = ord(lgsl_cut_byte($buffer, 4));
    $server['e']['time_limit'] = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['voting']     = ord(lgsl_cut_byte($buffer, 4));

    $buffer = substr($buffer, 2);

//---------------------------------------------------------+

    for ($player_key=0; $player_key<$server['s']['players']; $player_key++)
    {
     $server['p'][$player_key]['team'] = ord(lgsl_cut_byte($buffer, 4));

     $unknown = ord(lgsl_cut_byte($buffer, 1));
    }

//---------------------------------------------------------+

    $buffer = substr($buffer, 7);

    $server['e']['platoon_1_color']   = ord(lgsl_cut_byte($buffer, 8));
    $server['e']['platoon_2_color']   = ord(lgsl_cut_byte($buffer, 8));
    $server['e']['platoon_3_color']   = ord(lgsl_cut_byte($buffer, 8));
    $server['e']['platoon_4_color']   = ord(lgsl_cut_byte($buffer, 8));
    $server['e']['timer_on']          = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['timer_time']        = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['time_debriefing']   = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['time_respawn_min']  = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['time_respawn_max']  = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['time_respawn_safe'] = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['difficulty']        = ord(lgsl_cut_byte($buffer, 4));
    $server['e']['respawn_total']     = ord(lgsl_cut_byte($buffer, 4));
    $server['e']['random_insertions'] = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['spectators']        = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['arcademode']        = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['ai_backup']         = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['random_teams']      = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['time_starting']     = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
    $server['e']['identify_friends']  = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['identify_threats']  = ord(lgsl_cut_byte($buffer, 1));

    $buffer = substr($buffer, 5);

    $server['e']['restrictions']      = lgsl_get_string(lgsl_cut_pascal($buffer, 4, 3, -3));

//---------------------------------------------------------+

    switch ($server['e']['status'])
    {
      case 3: $server['e']['status'] = "Joining"; break;
      case 4: $server['e']['status'] = "Joining"; break;
      case 5: $server['e']['status'] = "Joining"; break;
    }

    switch ($server['e']['gamemode'])
    {
      case 2: $server['e']['gamemode'] = "Co-Op"; break;
      case 3: $server['e']['gamemode'] = "Solo";  break;
      case 4: $server['e']['gamemode'] = "Team";  break;
    }

    switch ($server['e']['respawns'])
    {
      case 0: $server['e']['respawns'] = "None";       break;
      case 1: $server['e']['respawns'] = "Individual"; break;
      case 2: $server['e']['respawns'] = "Team";       break;
      case 3: $server['e']['respawns'] = "Infinite";   break;
    }

    switch ($server['e']['difficulty'])
    {
      case 0: $server['e']['difficulty'] = "Recruit"; break;
      case 1: $server['e']['difficulty'] = "Veteran"; break;
      case 2: $server['e']['difficulty'] = "Elite";   break;
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_20(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    if ($lgsl_need['s'])
    {
      fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFFLSQ");
    }
    else
    {
      fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x57");

      $challenge_packet = fread($lgsl_fp, 4096);

      if (!$challenge_packet) { return FALSE; }

      $challenge_code = substr($challenge_packet, 5, 4);

      if     ($lgsl_need['e']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x56{$challenge_code}"); }
      elseif ($lgsl_need['p']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x55{$challenge_code}"); }
    }

    $buffer = fread($lgsl_fp, 4096);
    $buffer = substr($buffer, 4); // REMOVE HEADER

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $response_type = lgsl_cut_byte($buffer, 1);

    if ($response_type == "I")
    {
      $server['e']['netcode']     = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['name']        = lgsl_cut_string($buffer);
      $server['s']['map']         = lgsl_cut_string($buffer);
      $server['s']['game']        = lgsl_cut_string($buffer);
      $server['e']['gamemode']    = lgsl_cut_string($buffer);
      $server['e']['description'] = lgsl_cut_string($buffer);
      $server['e']['version']     = lgsl_cut_string($buffer);
      $server['e']['hostport']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "n");
      $server['s']['players']     = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
      $server['s']['playersmax']  = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
      $server['e']['dedicated']   = lgsl_cut_byte($buffer, 1);
      $server['e']['os']          = lgsl_cut_byte($buffer, 1);
      $server['s']['password']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
      $server['e']['anticheat']   = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
      $server['e']['cpu_load']    = round(3.03 * lgsl_unpack(lgsl_cut_byte($buffer, 1), "C"))."%";
      $server['e']['round']       = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
      $server['e']['roundsmax']   = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
      $server['e']['timeleft']    = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 2), "S") / 250);
    }

    elseif ($response_type == "E")
    {
      $returned = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");

      while ($buffer)
      {
        $item_key   = strtolower(lgsl_cut_string($buffer));
        $item_value = lgsl_cut_string($buffer);

        $server['e'][$item_key] = $item_value;
      }
    }

    elseif ($response_type == "D")
    {
      $returned = ord(lgsl_cut_byte($buffer, 1));

      $player_key = 0;

      while ($buffer)
      {
        $server['p'][$player_key]['pid']   = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$player_key]['name']  = lgsl_cut_string($buffer);
        $server['p'][$player_key]['score'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "N");
        $server['p'][$player_key]['time']  = lgsl_time(lgsl_unpack(strrev(lgsl_cut_byte($buffer, 4)), "f"));
        $server['p'][$player_key]['ping']  = lgsl_unpack(lgsl_cut_byte($buffer, 2), "n");
        $server['p'][$player_key]['uid']   = lgsl_unpack(lgsl_cut_byte($buffer, 4), "N");
        $server['p'][$player_key]['team']  = ord(lgsl_cut_byte($buffer, 1));

        $player_key ++;
      }
    }

//---------------------------------------------------------+

    if     ($lgsl_need['s']) { $lgsl_need['s'] = FALSE; }
    elseif ($lgsl_need['e']) { $lgsl_need['e'] = FALSE; }
    elseif ($lgsl_need['p']) { $lgsl_need['p'] = FALSE; }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_21(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp,"\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffgief");

    $buffer = fread($lgsl_fp, 4096);
    $buffer = substr($buffer, 20); // REMOVE HEADER

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $server['s']['name']       = lgsl_cut_string($buffer);
    $server['s']['map']        = lgsl_cut_string($buffer);
    $server['e']['gamemode']   = lgsl_cut_string($buffer);
    $server['s']['password']   = lgsl_cut_string($buffer);
    $server['e']['progress']   = lgsl_cut_string($buffer)."%";
    $server['s']['players']    = lgsl_cut_string($buffer);
    $server['s']['playersmax'] = lgsl_cut_string($buffer);

    switch ($server['e']['gamemode'])
    {
      case 0: $server['e']['gamemode'] = "Deathmatch"; break;
      case 1: $server['e']['gamemode'] = "Team Deathmatch"; break;
      case 2: $server['e']['gamemode'] = "Capture The Flag"; break;
    }

//---------------------------------------------------------+

    $player_key = 0;

    while ($buffer)
    {
      $server['p'][$player_key]['name']  = lgsl_cut_string($buffer);
      $server['p'][$player_key]['score'] = lgsl_cut_string($buffer);

      $player_key ++;
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_22(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp,"\x03\x00\x00");

    $buffer = fread($lgsl_fp, 4096);
    $buffer = substr($buffer, 3); // REMOVE HEADER

    if (!$buffer) { return FALSE; }

    $response_type = ord(lgsl_cut_byte($buffer, 1)); // TYPE SHOULD BE 4

//---------------------------------------------------------+

    $grf_count = ord(lgsl_cut_byte($buffer, 1));

    for ($a=0; $a<$grf_count; $a++)
    {
      $server['e']['grf_'.$a.'_id'] = strtoupper(dechex(lgsl_unpack(lgsl_cut_byte($buffer, 4), "N")));

      for ($b=0; $b<16; $b++)
      {
        $server['e']['grf_'.$a.'_md5'] .= strtoupper(dechex(ord(lgsl_cut_byte($buffer, 1))));
      }
    }

//---------------------------------------------------------+

    $server['e']['date_current']   = lgsl_unpack(lgsl_cut_byte($buffer, 4), "L");
    $server['e']['date_start']     = lgsl_unpack(lgsl_cut_byte($buffer, 4), "L");
    $server['e']['companies_max']  = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['companies']      = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['spectators_max'] = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['name']           = lgsl_cut_string($buffer);
    $server['e']['version']        = lgsl_cut_string($buffer);
    $server['e']['language']       = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['password']       = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax']     = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['players']        = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['spectators']     = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['map']            = lgsl_cut_string($buffer);
    $server['e']['map_width']      = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
    $server['e']['map_height']     = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
    $server['e']['map_set']        = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['dedicated']      = ord(lgsl_cut_byte($buffer, 1));

    // DOES NOT RETURN PLAYER INFORMATION

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_23(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE:
//  http://siteinthe.us
//  http://www.tribesmasterserver.com

    fwrite($lgsl_fp, "b++");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

    $buffer = substr($buffer, 4); // REMOVE HEADER

//---------------------------------------------------------+

    $server['s']['game']       = lgsl_cut_pascal($buffer);
    $server['e']['version']    = lgsl_cut_pascal($buffer);
    $server['s']['name']       = lgsl_cut_pascal($buffer);
    $server['e']['dedicated']  = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['password']   = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['cpu']        = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
    $server['e']['mod']        = lgsl_cut_pascal($buffer);
    $server['e']['type']       = lgsl_cut_pascal($buffer);
    $server['s']['map']        = lgsl_cut_pascal($buffer);
    $server['e']['motd']       = lgsl_cut_pascal($buffer);
    $server['e']['teams']      = ord(lgsl_cut_byte($buffer, 1));

//---------------------------------------------------------+

    $team_field = "?".lgsl_cut_pascal($buffer);
    $team_field = split("\t", $team_field);

    foreach ($team_field as $key => $value)
    {
      $value = substr($value, 1);
      $value = strtolower($value);
      $team_field[$key] = $value;
    }

//---------------------------------------------------------+

    $player_field = "?".lgsl_cut_pascal($buffer);
    $player_field = split("\t", $player_field);

    foreach ($player_field as $key => $value)
    {
      $value = substr($value, 1);
      $value = strtolower($value);

      if ($value == "player name") { $value = "name"; }

      $player_field[$key] = $value;
    }

    $player_field[] = "unknown_1";
    $player_field[] = "unknown_2";

//---------------------------------------------------------+

    for ($i=0; $i<$server['e']['teams']; $i++)
    {
      $team_name = lgsl_cut_pascal($buffer);
      $team_info = lgsl_cut_pascal($buffer);

      if (!$team_info) { continue; }

      $team_info = str_replace("%t", $team_name, $team_info);
      $team_info = split("\t", $team_info);

      foreach ($team_info as $key => $value)
      {
        $field = $team_field[$key];
        $value = trim($value);

        if ($field == "team name") { $field = "name"; }

        $server['t'][$i][$field] = $value;
      }
    }

//---------------------------------------------------------+

    for ($i=0; $i<$server['s']['players']; $i++)
    {
      $player_bits   = array();
      $player_bits[] = ord(lgsl_cut_byte($buffer, 1)) * 4; // %p = PING
      $player_bits[] = ord(lgsl_cut_byte($buffer, 1));     // %l = PACKET LOSS
      $player_bits[] = ord(lgsl_cut_byte($buffer, 1));     // %t = TEAM
      $player_bits[] = lgsl_cut_pascal($buffer);           // %n = PLAYER NAME
      $player_info   = lgsl_cut_pascal($buffer);

      if (!$player_info) { continue; }

      $player_info = str_replace(array("%p","%l","%t","%n"), $player_bits, $player_info);
      $player_info = split("\t", $player_info);

      foreach ($player_info as $key => $value)
      {
        $field = $player_field[$key];
        $value = trim($value);

        if ($field == "team") { $value = $server['t'][$value]['name']; }

        $server['p'][$i][$field] = $value;
      }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_24(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://cubelister.sourceforge.net

    fwrite($lgsl_fp, "\x21\x21");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

    $buffer = substr($buffer, 2); // REMOVE HEADER

//---------------------------------------------------------+

    if ($buffer[0] == "\x1b") // CUBE 1
    {
      // RESPONSE IS XOR ENCODED FOR SOME STRANGE REASON
      for ($i=0; $i<strlen($buffer); $i++) { $buffer[$i] = chr(ord($buffer[$i]) ^ 0x61); }

      $server['s']['game']       = "Cube";
      $server['e']['netcode']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['gamemode']   = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['timeleft']   = lgsl_time(ord(lgsl_cut_byte($buffer, 1)) * 60);
      $server['s']['map']        = lgsl_cut_string($buffer);
      $server['s']['name']       = lgsl_cut_string($buffer);
      $server['s']['playersmax'] = "0"; // NOT PROVIDED

      // DOES NOT RETURN PLAYER INFORMATION

      return TRUE;
    }

    elseif ($buffer[0] == "\x80") // ASSAULT CUBE
    {
      $server['s']['game']       = "AssaultCube";
      $server['e']['netcode']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['version']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
      $server['e']['gamemode']   = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['timeleft']   = lgsl_time(ord(lgsl_cut_byte($buffer, 1)) * 60);
      $server['s']['map']        = lgsl_cut_string($buffer);
      $server['s']['name']       = lgsl_cut_string($buffer);
      $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
    }

    elseif ($buffer[1] == "\x05") // CUBE 2 - SAUERBRATEN
    {
      $server['s']['game']       = "Sauerbraten";
      $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
      $info_returned             = ord(lgsl_cut_byte($buffer, 1)); // CODED FOR 5
      $server['e']['netcode']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['version']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
      $server['e']['gamemode']   = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['timeleft']   = lgsl_time(ord(lgsl_cut_byte($buffer, 1)) * 60);
      $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['password']   = ord(lgsl_cut_byte($buffer, 1)); // BIT FIELD
      $server['s']['password']   = $server['s']['password'] & 4 ? "1" : "0";
      $server['s']['map']        = lgsl_cut_string($buffer);
      $server['s']['name']       = lgsl_cut_string($buffer);
    }

    elseif ($buffer[1] == "\x06") // BLOODFRONTIER
    {
      $server['s']['game']       = "Blood Frontier";
      $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
      $info_returned             = ord(lgsl_cut_byte($buffer, 1)); // CODED FOR 6
      $server['e']['netcode']    = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['version']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
      $server['e']['gamemode']   = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['mutators']   = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['timeleft']   = lgsl_time(ord(lgsl_cut_byte($buffer, 1)) * 60);
      $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['password']   = ord(lgsl_cut_byte($buffer, 1)); // BIT FIELD
      $server['s']['password']   = $server['s']['password'] & 4 ? "1" : "0";
      $server['s']['map']        = lgsl_cut_string($buffer);
      $server['s']['name']       = lgsl_cut_string($buffer);
    }

    else // UNKNOWN
    {
      return FALSE;
    }

//---------------------------------------------------------+
//  CRAZY PROTOCOL - REQUESTS MUST BE MADE FOR EACH PLAYER
//  BOTS ARE RETURNED BUT NOT INCLUDED IN THE PLAYER TOTAL
//  AND THERE CAN BE ID GAPS BETWEEN THE PLAYERS RETURNED

    if ($lgsl_need['p'] && $server['s']['players'])
    {
      $player_key = 0;

      for ($player_id=0; $player_id<32; $player_id++)
      {
        fwrite($lgsl_fp, "\x00\x01".chr($player_id));

        // READ PACKET
        $buffer = fread($lgsl_fp, 4096);
        if (!$buffer) { break; }

        // CHECK IF PLAYER ID IS ACTIVE
        if ($buffer[5] != "\x00")
        {
          if ($player_key < $server['s']['players']) { continue; }
          break;
        }

        // IF PREVIEW PACKET GET THE FULL PACKET THAT FOLLOWS
        if (strlen($buffer) < 15)
        {
          $buffer = fread($lgsl_fp, 4096);
          if (!$buffer) { break; }
        }

        // REMOVE HEADER
        $buffer = substr($buffer, 7);

        // WE CAN NOW GET THE PLAYER DETAILS
        if ($server['s']['game'] == "Blood Frontier")
        {
          $server['p'][$player_key]['pid']       = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['ping']      = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['ping']      = $server['p'][$player_key]['ping'] == 128 ? lgsl_unpack(lgsl_cut_byte($buffer, 2), "S") : $server['p'][$player_key]['ping'];
          $server['p'][$player_key]['name']      = lgsl_cut_string($buffer);
          $server['p'][$player_key]['team']      = lgsl_cut_string($buffer);
          $server['p'][$player_key]['score']     = lgsl_unpack(lgsl_cut_byte($buffer, 1), "c");
          $server['p'][$player_key]['damage']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['deaths']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['teamkills'] = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['accuracy']  = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C")."%";
          $server['p'][$player_key]['health']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "c");
          $server['p'][$player_key]['spree']     = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['weapon']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
        }
        else
        {
          $server['p'][$player_key]['pid']       = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['name']      = lgsl_cut_string($buffer);
          $server['p'][$player_key]['team']      = lgsl_cut_string($buffer);
          $server['p'][$player_key]['score']     = lgsl_unpack(lgsl_cut_byte($buffer, 1), "c");
          $server['p'][$player_key]['deaths']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['teamkills'] = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['accuracy']  = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C")."%";
          $server['p'][$player_key]['health']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "c");
          $server['p'][$player_key]['armour']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
          $server['p'][$player_key]['weapon']    = lgsl_unpack(lgsl_cut_byte($buffer, 1), "C");
        }

        $player_key++;
      }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_25(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://www.tribesnext.com

    fwrite($lgsl_fp,"\x12\x02\x21\x21\x21\x21");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

    $buffer = substr($buffer, 6); // REMOVE HEADER

//---------------------------------------------------------+

    $server['s']['game']       = lgsl_cut_pascal($buffer);
    $server['e']['gamemode']   = lgsl_cut_pascal($buffer);
    $server['s']['map']        = lgsl_cut_pascal($buffer);
    $server['e']['bit_flags']  = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['bots']       = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['cpu']        = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
    $server['e']['motd']       = lgsl_cut_pascal($buffer);
    $server['e']['unknown']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");

    $server['e']['dedicated']  = ($server['e']['bit_flags'] & 1)  ? "1" : "0";
    $server['s']['password']   = ($server['e']['bit_flags'] & 2)  ? "1" : "0";
    $server['e']['os']         = ($server['e']['bit_flags'] & 4)  ? "L" : "W";
    $server['e']['tournament'] = ($server['e']['bit_flags'] & 8)  ? "1" : "0";
    $server['e']['no_alias']   = ($server['e']['bit_flags'] & 16) ? "1" : "0";

    unset($server['e']['bit_flags']);

//---------------------------------------------------------+

    $team_total = lgsl_cut_string($buffer, 0, "\x0A");

    for ($i=0; $i<$team_total; $i++)
    {
      $server['t'][$i]['name']  = lgsl_cut_string($buffer, 0, "\x09");
      $server['t'][$i]['score'] = lgsl_cut_string($buffer, 0, "\x0A");
    }

    $player_total = lgsl_cut_string($buffer, 0, "\x0A");

    for ($i=0; $i<$player_total; $i++)
    {
      lgsl_cut_byte($buffer, 1); // ? 16
      lgsl_cut_byte($buffer, 1); // ? 8 or 14 = BOT / 12 = ALIAS / 11 = NORMAL
      if (ord($buffer[0]) < 32) { lgsl_cut_byte($buffer, 1); } // ? 8 PREFIXES SOME NAMES

      $server['p'][$i]['name']  = lgsl_cut_string($buffer, 0, "\x11");
                                  lgsl_cut_string($buffer, 0, "\x09"); // ALWAYS BLANK
      $server['p'][$i]['team']  = lgsl_cut_string($buffer, 0, "\x09");
      $server['p'][$i]['score'] = lgsl_cut_string($buffer, 0, "\x0A");
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_26(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE:
//  http://hazardaaclan.com/wiki/doku.php?id=aa3_server_query
//  http://aluigi.altervista.org/papers.htm#aa3authdec

    if (!function_exists('gzuncompress')) { return FALSE; } // REQUIRES http://www.php.net/zlib

    $packet = "\x0A\x00playerName\x06\x06\x00query\x00";
    lgsl_gs_crypt($server['b']['type'], $packet, TRUE);
    fwrite($lgsl_fp, "\x4A\x35\xFF\xFF\x02\x00\x02\x00\x01\x00{$packet}");

    $buffer = array();
    $packet_count = 0;
    $packet_total = 4;

    do
    {
      $packet_count ++;
      $packet = fread($lgsl_fp, 4096);

      if (!isset($packet[5])) { return FALSE; }

      if ($packet[5] == "\x03") // MULTI PACKET
      {
        $packet_order = ord($packet[10]);
        $packet_total = ord($packet[12]);
        $packet = substr($packet, 14);
        $buffer[$packet_order] = $packet;
      }
      elseif ($packet[5] == "\x02") // SINGLE PACKET
      {
        $buffer[0] = substr($packet, 10);
        break;
      }
      else
      {
        return FALSE;
      }
    }
    while ($packet_count < $packet_total);

//---------------------------------------------------------+

    ksort($buffer);

    $buffer = implode("", $buffer);

    lgsl_gs_crypt($server['b']['type'], $buffer, FALSE);

    $buffer = @gzuncompress($buffer);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $raw = array();

    do
    {
      $raw_name = lgsl_cut_pascal($buffer, 2);
      $raw_type = lgsl_cut_byte($buffer, 1);

      switch ($raw_type)
      {
        // SINGLE INTEGER
        case "\x02":
        $raw[$raw_name] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "i");
        break;

        // ARRAY OF STRINGS
        case "\x07":
        $raw_total = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");

        for ($i=0; $i<$raw_total;$i++)
        {
          $raw_value = lgsl_cut_pascal($buffer, 2);
          if (substr($raw_value, -1) == "\x00") { $raw_value = substr($raw_value, 0, -1); } // SOME STRINGS HAVE NULLS
          $raw[$raw_name][] = $raw_value;
        }
        break;

        // 01=BOOLEAN|03=SHORT INTEGER|04=DOUBLE
        // 05=CHAR|06=STRING|09=ARRAY OF INTEGERS
        default:
        break 2;
      }
    }
    while ($buffer);

    if (!isset($raw['attributeNames'])  || !is_array($raw['attributeNames']))  { return FALSE; }
    if (!isset($raw['attributeValues']) || !is_array($raw['attributeValues'])) { return FALSE; }

//---------------------------------------------------------+

    foreach ($raw['attributeNames'] as $key => $field)
    {
      $field = strtolower($field);

      preg_match("/^player(.*)(\d+)$/U", $field, $match);

      if (isset($match[1]))
      {
        // IGNORE POINTLESS PLAYER FIELDS
        if ($match[1] == "mapname")         { continue; }
        if ($match[1] == "version")         { continue; }
        if ($match[1] == "servermapname")   { continue; }
        if ($match[1] == "serveripaddress") { continue; }

        // LGSL STANDARD ( SWAP NAME AS ITS ACTUALLY THE ACCOUNT NAME )
        if ($match[1] == "name")        { $match[1] = "username"; }
        if ($match[1] == "soldiername") { $match[1] = "name"; }

        $server['p'][$match[2]][$match[1]] = $raw['attributeValues'][$key];
      }
      else
      {
        if (substr($field, 0, 6) == "server") { $field = substr($field, 6); }
        $server['e'][$field] = $raw['attributeValues'][$key];
      }
    }

    $lgsl_conversion = array("gamename"=>"name","mapname"=>"map","playercount"=>"players","maxplayers"=>"playersmax","flagpassword"=>"password");
    foreach ($lgsl_conversion as $e => $s) { $server['s'][$s] = $server['e'][$e]; unset($server['ea'][$e]); } // LGSL STANDARD
    $server['s']['playersmax'] += intval($server['e']['maxspectators']); // ADD SPECTATOR SLOTS TO MAX PLAYERS

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_27(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE:
//  http://skulltag.com/wiki/Launcher_protocol
//  http://en.wikipedia.org/wiki/Huffman_coding
//  http://www.greycube.com/help/lgsl_other/skulltag_huffman.txt

    $huffman_table = array(
    "010","110111","101110010","00100","10011011","00101","100110101","100001100","100101100","001110100","011001001","11001000","101100001","100100111","001111111","101110000","101110001","001111011",
    "11011011","101111100","100001110","110011111","101100000","001111100","0011000","001111000","10001100","100101011","100010000","101111011","100100110","100110010","0111","1111000","00010001",
    "00011010","00011000","00010101","00010000","00110111","00110110","00011100","01100101","1101001","00110100","10110011","10110100","1111011","10111100","10111010","11001001","11010101","11111110",
    "11111100","10001110","11110011","001101011","10000000","000101101","11010000","001110111","100000010","11100111","001100101","11100110","00111001","10001010","00010011","001110110","10001111",
    "000111110","11000111","11010111","11100011","000101000","001100111","11010100","000111010","10010111","100000111","000100100","001110001","11111010","100100011","11110100","000110111","001111010",
    "100010011","100110001","11101","110001011","101110110","101111110","100100010","100101001","01101","100100100","101100101","110100011","100111100","110110001","100010010","101101101","011001110",
    "011001101","11111101","100010001","100110000","110001000","110110000","0001001010","110001010","101101010","000110110","10110001","110001101","110101101","110001100","000111111","110010101",
    "111000100","11011001","110010110","110011110","000101100","001110101","101111101","1001110","0000","1000010","0001110111","0001100101","1010","11001110","0110011000","0110011001","1000011011",
    "1001100110","0011110011","0011001100","11111001","0110010001","0001010011","1000011010","0001001011","1001101001","101110111","1000001101","1000011111","1100000101","0110000000","1011011101",
    "11110101","0001111011","1101000101","1101000100","1001000010","0110000001","1011001000","100101010","1100110","111100101","1100101111","0001100111","1110000","0011111100","11111011","1100101110",
    "101110011","1001100111","1001111111","1011011100","111110001","101111010","1011010110","1001010000","1001000011","1001111110","0011111011","1000011110","1000101100","01100001","00010111",
    "1000000110","110000101","0001111010","0011001101","0110011110","110010100","111000101","0011001001","0011110010","110000001","101101111","0011111101","110110100","11100100","1011001001",
    "0011001000","0001110110","111111111","110101100","111111110","1000001011","1001011010","110000000","000111100","111110000","011000001","1001111010","111001011","011000111","1001000001",
    "1001111100","1000110111","1001101000","0110001100","1001111011","0011010101","1000101101","0011111010","0001100100","01100010","110000100","101101100","0110011111","1001011011","1000101110",
    "111100100","1000110110","0110001101","1001000000","110110101","1000001000","1000001001","1100000100","110001001","1000000111","1001111101","111001010","0011010100","1000101111","101111111",
    "0001010010","0011100000","0001100110","1000001010","0011100001","11000011","1011010111","1000001100","100011010","0110010000","100100101","1001010001","110000011");

//---------------------------------------------------------+

    fwrite($lgsl_fp, "\x02\xB8\x49\x1A\x9C\x8B\xB5\x3F\x1E\x8F\x07");

    $packet = fread($lgsl_fp, 4096);

    if (!$packet) { return FALSE; }

    $packet = substr($packet, 1); // REMOVE HEADER

//---------------------------------------------------------+

    $packet_binary = "";

    for ($i=0; $i<strlen($packet); $i++)
    {
      $packet_binary .= strrev(sprintf("%08b", ord($packet[$i])));
    }

    $buffer = "";

    while ($packet_binary)
    {
      foreach ($huffman_table as $ascii => $huffman_binary)
      {
        $huffman_length = strlen($huffman_binary);

        if (substr($packet_binary, 0, $huffman_length) === $huffman_binary)
        {
          $packet_binary = substr($packet_binary, $huffman_length);
          $buffer .= chr($ascii);
          continue 2;
        }
      }
      break;
    }

//---------------------------------------------------------+

    $response_status        = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l"); if ($response_status != "5660023") { return FALSE; }
    $response_time          = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
    $server['e']['version'] = lgsl_cut_string($buffer);
    $response_flag          = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");

//---------------------------------------------------------+

    if ($response_flag & 0x00000001) { $server['s']['name']       = lgsl_cut_string($buffer); }
    if ($response_flag & 0x00000002) { $server['e']['wadurl']     = lgsl_cut_string($buffer); }
    if ($response_flag & 0x00000004) { $server['e']['email']      = lgsl_cut_string($buffer); }
    if ($response_flag & 0x00000008) { $server['s']['map']        = lgsl_cut_string($buffer); }
    if ($response_flag & 0x00000010) { $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1)); }
    if ($response_flag & 0x00000020) { $server['e']['playersmax'] = ord(lgsl_cut_byte($buffer, 1)); }
    if ($response_flag & 0x00000040)
    {
      $pwad_total = ord(lgsl_cut_byte($buffer, 1));

      $server['e']['pwads'] = "";

      for ($i=0; $i<$pwad_total; $i++)
	    {
        $server['e']['pwads'] .= lgsl_cut_string($buffer)." ";
      }
    }
    if ($response_flag & 0x00000080)
    {
      $server['e']['gametype'] = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['instagib'] = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['buckshot'] = ord(lgsl_cut_byte($buffer, 1));
    }
    if ($response_flag & 0x00000100) { $server['s']['game']         = lgsl_cut_string($buffer); }
    if ($response_flag & 0x00000200) { $server['e']['iwad']         = lgsl_cut_string($buffer); }
    if ($response_flag & 0x00000400) { $server['s']['password']     = ord(lgsl_cut_byte($buffer, 1)); }
    if ($response_flag & 0x00000800) { $server['e']['playpassword'] = ord(lgsl_cut_byte($buffer, 1)); }
    if ($response_flag & 0x00001000) { $server['e']['skill']        = ord(lgsl_cut_byte($buffer, 1)) + 1; }
    if ($response_flag & 0x00002000) { $server['e']['botskill']     = ord(lgsl_cut_byte($buffer, 1)) + 1; }
    if ($response_flag & 0x00004000)
    {
      $server['e']['dmflags']     = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
      $server['e']['dmflags2']    = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
      $server['e']['compatflags'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
    }
    if ($response_flag & 0x00010000)
    {
      $server['e']['fraglimit'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
      $timelimit                = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");

      if ($timelimit) // FUTURE VERSION MAY ALWAYS RETURN THIS
      {
        $server['e']['timeleft'] = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 2), "S") * 60);
      }

      $server['e']['timelimit']  = lgsl_time($timelimit * 60);
      $server['e']['duellimit']  = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
      $server['e']['pointlimit'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
      $server['e']['winlimit']   = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
    }
    if ($response_flag & 0x00020000) { $server['e']['teamdamage'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"); }
    if ($response_flag & 0x00040000) // DEPRECIATED
    {
      $server['t'][0]['score'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
      $server['t'][1]['score'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
    }
    if ($response_flag & 0x00080000) { $server['s']['players'] = ord(lgsl_cut_byte($buffer, 1)); }
    if ($response_flag & 0x00100000)
    {
      for ($i=0; $i<$server['s']['players']; $i++)
      {
        $server['p'][$i]['name']      = lgsl_parse_color(lgsl_cut_string($buffer), $server['b']['type']);
        $server['p'][$i]['score']     = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
        $server['p'][$i]['ping']      = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
        $server['p'][$i]['spectator'] = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$i]['bot']       = ord(lgsl_cut_byte($buffer, 1));

        if (($response_flag & 0x00200000) && ($response_flag & 0x00400000))
        {
          $server['p'][$i]['team'] = ord(lgsl_cut_byte($buffer, 1));
        }

        $server['p'][$i]['time'] = lgsl_time(ord(lgsl_cut_byte($buffer, 1)) * 60);
      }
    }
    if ($response_flag & 0x00200000)
    {
      $team_total = ord(lgsl_cut_byte($buffer, 1));

      if ($response_flag & 0x00400000)
      {
        for ($i=0; $i<$team_total; $i++) { $server['t'][$i]['name'] = lgsl_cut_string($buffer); }
      }
      if ($response_flag & 0x00800000)
      {
        for ($i=0; $i<$team_total; $i++) { $server['t'][$i]['color'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l"); }
      }
      if ($response_flag & 0x01000000)
      {
        for ($i=0; $i<$team_total; $i++) { $server['t'][$i]['score'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s"); }
      }

      for ($i=0; $i<$server['s']['players']; $i++)
      {
        if ($server['t'][$i]['name']) { $server['p'][$i]['team'] = $server['t'][$i]['name']; }
      }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_28(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://doomutils.ucoz.com

    fwrite($lgsl_fp, "\xA3\xDB\x0B\x00"."\xFC\xFD\xFE\xFF"."\x01\x00\x00\x00"."\x21\x21\x21\x21");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

//---------------------------------------------------------+

    $response_status  = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l"); if ($response_status != "5560022") { return FALSE; }
    $response_version = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
    $response_time    = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");

    $server['e']['invited']    = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['version']    = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
    $server['s']['name']       = lgsl_cut_string($buffer);
    $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['map']        = lgsl_cut_string($buffer);

    $pwad_total = ord(lgsl_cut_byte($buffer, 1));

    for ($i=0; $i<$pwad_total; $i++)
    {
      $server['e']['pwads'] .= lgsl_cut_string($buffer)." ";
      $pwad_optional         = ord(lgsl_cut_byte($buffer, 1));
      $pwad_alternative      = lgsl_cut_string($buffer);
    }

    $server['e']['gametype']   = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['game']       = lgsl_cut_string($buffer);
    $server['e']['iwad']       = lgsl_cut_string($buffer);
    $iwad_altenative           = lgsl_cut_string($buffer);
    $server['e']['skill']      = ord(lgsl_cut_byte($buffer, 1)) + 1;
    $server['e']['wadurl']     = lgsl_cut_string($buffer);
    $server['e']['email']      = lgsl_cut_string($buffer);
    $server['e']['dmflags']    = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
    $server['e']['dmflags2']   = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
    $server['s']['password']   = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['inviteonly'] = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['players']    = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['timelimit']  = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 2), "S") * 60);
    $server['e']['timeleft']   = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 2), "S") * 60);
    $server['e']['fraglimit']  = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
    $server['e']['gravity']    = lgsl_unpack(lgsl_cut_byte($buffer, 4), "f");
    $server['e']['aircontrol'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "f");
    $server['e']['playersmin'] = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['removebots'] = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['voting']     = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['serverinfo'] = lgsl_cut_string($buffer);
    $server['e']['startup']    = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");

    for ($i=0; $i<$server['s']['players']; $i++)
    {
      $server['p'][$i]['name']      = lgsl_cut_string($buffer);
      $server['p'][$i]['score']     = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
      $server['p'][$i]['death']     = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
      $server['p'][$i]['ping']      = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
      $server['p'][$i]['time']      = lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 2), "S") * 60);
      $server['p'][$i]['bot']       = ord(lgsl_cut_byte($buffer, 1));
      $server['p'][$i]['spectator'] = ord(lgsl_cut_byte($buffer, 1));
      $server['p'][$i]['team']      = ord(lgsl_cut_byte($buffer, 1));
      $server['p'][$i]['country']   = lgsl_cut_byte($buffer, 2);
    }

    $team_total                = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['pointlimit'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
    $server['e']['teamdamage'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "f");

    for ($i=0; $i<$team_total; $i++) // RETURNS 4 TEAMS BUT IGNORE THOSE NOT IN USE
    {
      $server['t']['team'][$i]['name']  = lgsl_cut_string($buffer);
      $server['t']['team'][$i]['color'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
      $server['t']['team'][$i]['score'] = lgsl_unpack(lgsl_cut_byte($buffer, 2), "s");
    }

    for ($i=0; $i<$server['s']['players']; $i++)
    {
      if ($server['t'][$i]['name']) { $server['p'][$i]['team'] = $server['t'][$i]['name']; }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_29(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://www.cs2d.com/servers.php

    if ($lgsl_need['s'] || $lgsl_need['e'])
    {
      $lgsl_need['s'] = FALSE;
      $lgsl_need['e'] = FALSE;

      fwrite($lgsl_fp, "\x01\x00\x03\x10\x21\xFB\x01\x75\x00");

      $buffer = fread($lgsl_fp, 4096);

      if (!$buffer) { return FALSE; }

      $buffer = substr($buffer, 4); // REMOVE HEADER

      $server['e']['bit_flags']  = ord(lgsl_cut_byte($buffer, 1)) - 48;
      $server['s']['name']       = lgsl_cut_pascal($buffer);
      $server['s']['map']        = lgsl_cut_pascal($buffer);
      $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
      $server['s']['playersmax'] = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['gamemode']   = ord(lgsl_cut_byte($buffer, 1));
      $server['e']['bots']       = ord(lgsl_cut_byte($buffer, 1));

      $server['s']['password']        = ($server['e']['bit_flags'] & 1) ? "1" : "0";
      $server['e']['registered_only'] = ($server['e']['bit_flags'] & 2) ? "1" : "0";
      $server['e']['fog_of_war']      = ($server['e']['bit_flags'] & 4) ? "1" : "0";
      $server['e']['friendlyfire']    = ($server['e']['bit_flags'] & 8) ? "1" : "0";
    }

    if ($lgsl_need['p'])
    {
      $lgsl_need['p'] = FALSE;

      fwrite($lgsl_fp, "\x01\x00\xFB\x05");

      $buffer = fread($lgsl_fp, 4096);

      if (!$buffer) { return FALSE; }

      $buffer = substr($buffer, 4); // REMOVE HEADER

      $player_total = ord(lgsl_cut_byte($buffer, 1));

      for ($i=0; $i<$player_total; $i++)
      {
        $server['p'][$i]['pid']    = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$i]['name']   = lgsl_cut_pascal($buffer);
        $server['p'][$i]['team']   = ord(lgsl_cut_byte($buffer, 1));
        $server['p'][$i]['score']  = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
        $server['p'][$i]['deaths'] = lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
      }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_30(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  REFERENCE: http://blogs.battlefield.ea.com/battlefield_bad_company/archive/2010/02/05/remote-administration-interface-for-bfbc2-pc.aspx
//  THIS USES TCP COMMUNICATION

    if ($lgsl_need['s'] || $lgsl_need['e'])
    {
      fwrite($lgsl_fp, "\x00\x00\x00\x00\x1B\x00\x00\x00\x01\x00\x00\x00\x0A\x00\x00\x00serverInfo\x00");
    }
    elseif ($lgsl_need['p'])
    {
      fwrite($lgsl_fp, "\x00\x00\x00\x00\x24\x00\x00\x00\x02\x00\x00\x00\x0B\x00\x00\x00listPlayers\x00\x03\x00\x00\x00all\x00");
    }

//---------------------------------------------------------+

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

    $length = lgsl_unpack(substr($buffer, 4, 4), "L");

    while (strlen($buffer) < $length)
    {
      $packet = fread($lgsl_fp, 4096);

      if ($packet) { $buffer .= $packet; } else { break; }
    }

//---------------------------------------------------------+

    $buffer = substr($buffer, 12); // REMOVE HEADER

    $response_type = lgsl_cut_pascal($buffer, 4, 0, 1);

    if ($response_type != "OK") { return FALSE; }

//---------------------------------------------------------+

    if ($lgsl_need['s'] || $lgsl_need['e'])
    {
      $lgsl_need['s'] = FALSE;
      $lgsl_need['e'] = FALSE;

      $server['s']['name']            = lgsl_cut_pascal($buffer, 4, 0, 1);
      $server['s']['players']         = lgsl_cut_pascal($buffer, 4, 0, 1);
      $server['s']['playersmax']      = lgsl_cut_pascal($buffer, 4, 0, 1);
      $server['e']['gamemode']        = lgsl_cut_pascal($buffer, 4, 0, 1);
      $server['s']['map']             = lgsl_cut_pascal($buffer, 4, 0, 1);
      $server['e']['score_attackers'] = lgsl_cut_pascal($buffer, 4, 0, 1);
      $server['e']['score_defenders'] = lgsl_cut_pascal($buffer, 4, 0, 1);

      // CONVERT MAP NUMBER TO DESCRIPTIVE NAME

      $server['e']['level'] = $server['s']['map'];
      $map_check = strtolower($server['s']['map']);

      if     (strpos($map_check, "mp_001") !== FALSE) { $server['s']['map'] = "Panama Canal";   }
      elseif (strpos($map_check, "mp_002") !== FALSE) { $server['s']['map'] = "Valparaiso";     }
      elseif (strpos($map_check, "mp_003") !== FALSE) { $server['s']['map'] = "Laguna Alta";    }
      elseif (strpos($map_check, "mp_004") !== FALSE) { $server['s']['map'] = "Isla Inocentes"; }
      elseif (strpos($map_check, "mp_005") !== FALSE) { $server['s']['map'] = "Atacama Desert"; }
      elseif (strpos($map_check, "mp_006") !== FALSE) { $server['s']['map'] = "Arica Harbor";   }
      elseif (strpos($map_check, "mp_007") !== FALSE) { $server['s']['map'] = "White Pass";     }
      elseif (strpos($map_check, "mp_008") !== FALSE) { $server['s']['map'] = "Nelson Bay";     }
      elseif (strpos($map_check, "mp_009") !== FALSE) { $server['s']['map'] = "Laguna Presa";   }
      elseif (strpos($map_check, "mp_012") !== FALSE) { $server['s']['map'] = "Port Valdez";    }
    }

//---------------------------------------------------------+

    elseif ($lgsl_need['p'])
    {
      $lgsl_need['p'] = FALSE;

      $field_total = lgsl_cut_pascal($buffer, 4, 0, 1);
      $field_list  = array();

      for ($i=0; $i<$field_total; $i++)
      {
        $field_list[] = strtolower(lgsl_cut_pascal($buffer, 4, 0, 1));
      }

      $player_squad = array("","Alpha","Bravo","Charlie","Delta","Echo","Foxtrot","Golf","Hotel");
      $player_team  = array("","Attackers","Defenders");
      $player_total = lgsl_cut_pascal($buffer, 4, 0, 1);

      for ($i=0; $i<$player_total; $i++)
      {
        foreach ($field_list as $field)
        {
          $value = lgsl_cut_pascal($buffer, 4, 0, 1);

          switch ($field)
          {
            case "clantag": $server['p'][$i]['name']  = $value;                                                                             break;
            case "name":    $server['p'][$i]['name']  = empty($server['p'][$i]['name']) ? $value : "[{$server['p'][$i]['name']}] {$value}"; break;
            case "teamid":  $server['p'][$i]['team']  = isset($player_team[$value]) ? $player_team[$value] : $value;                        break;
            case "squadid": $server['p'][$i]['squad'] = isset($player_squad[$value]) ? $player_squad[$value] : $value;                      break;
            default:        $server['p'][$i][$field]  = $value;                                                                             break;
          }
        }
      }
    }

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_31(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+
//  AVP 2010 ONLY ROUGHLY FOLLOWS THE SOURCE QUERY FORMAT
//  SERVER RULES ARE ON THE END OF THE INFO RESPONSE

    fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

    $buffer = substr($buffer, 5); // REMOVE HEADER

    $server['e']['netcode']     = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['name']        = lgsl_cut_string($buffer);
    $server['s']['map']         = lgsl_cut_string($buffer);
    $server['s']['game']        = lgsl_cut_string($buffer);
    $server['e']['description'] = lgsl_cut_string($buffer);
    $server['e']['appid']       = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
    $server['s']['players']     = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax']  = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['bots']        = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['dedicated']   = lgsl_cut_byte($buffer, 1);
    $server['e']['os']          = lgsl_cut_byte($buffer, 1);
    $server['s']['password']    = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['anticheat']   = ord(lgsl_cut_byte($buffer, 1));
    $server['e']['version']     = lgsl_cut_string($buffer);

    $buffer = substr($buffer, 1);
    $server['e']['hostport']     = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
    $server['e']['friendlyfire'] = $buffer[124];

    // DOES NOT RETURN PLAYER INFORMATION

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_32(&$server, &$lgsl_need, &$lgsl_fp)
  {
//---------------------------------------------------------+

    fwrite($lgsl_fp, "\x05\x00\x00\x01\x0A");

    $buffer = fread($lgsl_fp, 4096);

    if (!$buffer) { return FALSE; }

    $buffer = substr($buffer, 5); // REMOVE HEADER

    $server['s']['name']       = lgsl_cut_pascal($buffer);
    $server['s']['map']        = lgsl_cut_pascal($buffer);
    $server['s']['players']    = ord(lgsl_cut_byte($buffer, 1));
    $server['s']['playersmax'] = 0; // HELD ON MASTER

    // DOES NOT RETURN PLAYER INFORMATION

//---------------------------------------------------------+

    return TRUE;
  }

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

  function lgsl_rcon_33(&$server, &$lgsl_need, &$lgsl_fp)
  {

    require_once("/srv/htdocs/team-aero.org/www/libraries/TeamSpeak3/TeamSpeak3.php");
    

    try {
      $uri = "serverquery://serveradmin:FtVMGc8c@127.0.0.1:10011/?server_port=9987";

      // connect to above specified server, authenticate and spawn an object for the virtual server on port 9987
      $ts3_VirtualServer = TeamSpeak3::factory($uri);
      
      $server['s']['name']        = $ts3_VirtualServer->virtualserver_name;
      $server['s']['map']         = "teamspeak";
      $server['s']['players']     = $ts3_VirtualServer->virtualserver_clientsonline;
      $server['s']['playersmax']  = $ts3_VirtualServer->virtualserver_maxclients;
      
      // build and display HTML treeview using custom image paths (remote icons will be embedded using data URI sheme)
      //$server['s']['getViewer'] = $ts3_VirtualServer->getViewer(new TeamSpeak3_Viewer_Html("images/viewericons/", "images/countryflags/", "data:image"));
      
    } catch(TeamSpeak3_Exception $e) {
      // print the error message returned by the server
      echo "Error " . $e->getCode() . ": " . $e->getMessage();
    }
    
    return TRUE;
  }

/*
  function lgsl_rcon_33(&$server, &$lgsl_need, &$lgsl_fp)
  {
    if (strpos(fread($lgsl_fp, 4096), 'TS') === FALSE) { echo "false1"; return FALSE; }
    $ver = $server['b']['type'] == 'ts3' ? 1 : 0;
    $param[0] = array('sel ','si',"\r\n",'pl');
    $param[1] = array('use port=','serverinfo',' ','clientlist -country');
    if ($ver) { fread($lgsl_fp, 4096); }
    fwrite($lgsl_fp, $param[$ver][0].$server['b']['c_port']."\n");	// select virtualserver
    if (strtoupper(substr(fread($lgsl_fp, 4096), -4, -2)) != 'OK') { echo "false2"; return FALSE; }

    fwrite($lgsl_fp, $param[$ver][1]."\n");	// request serverinfo
    $buffer = fread($lgsl_fp, 4096);
    if (!$buffer || substr($buffer, 0, 5) == 'error') { echo "false3"; return FALSE; }
    while (strtoupper(substr($buffer, -4, -2)) != 'OK') {
      $part = fread($lgsl_fp, 4096);
      if ($part && substr($part, 0, 5) != 'error') { $buffer .= $part; } else { break; }
    }

    while ($val = lgsl_cut_string($buffer, 7+7*$ver, $param[$ver][2])) {
      $key = lgsl_cut_string($val, 0, '='); $items[$key] = $val;
    }
    if (!isset($items['name'])) { echo "false4"; return FALSE; }
    $server['s']['name']	= $ver ? lgsl_unescape($items['name']) : $items['name'];
    $server['s']['map']	= "teamspeak";
    $server['s']['players']	= intval($items[$ver ? 'clientsonline' : 'currentusers']) - $ver;
    $server['s']['playersmax']	= intval($items[$ver ? 'maxclients' : 'maxusers']);
    $server['s']['password']	= intval($items[$ver ? 'flag_password' : 'password']);
    $server['e']['platform']	= $items['platform'];
    $server['e']['motd']	= $ver ? lgsl_unescape($items['welcomemessage']) : $items['welcomemessage'];
    $server['e']['uptime']	= lgsl_time($items['uptime']);
    $server['e']['channels']	= $items[$ver ? 'channelsonline' : 'currentchannels'];
    if ($ver) { $server['e']['version'] = lgsl_unescape($items['version']); }
    if (!$lgsl_need['p'] || $server['s']['players'] < 1) { echo "false5"; return TRUE; }

    fwrite($lgsl_fp, $param[$ver][3]."\n");	// request playerlist
    $buffer = fread($lgsl_fp, 4096);
    while (substr($buffer, -4) != "OK\r\n" && substr($buffer, -2) != "\n\r") { 
      $part = fread($lgsl_fp, 4096);
      if ($part && substr($part, 0, 5) != 'error') { $buffer .= $part; } else { break; }
    }

    $i = 0;
    if ($ver) {
      while ($items = lgsl_cut_string($buffer, 0, '|')) {
        lgsl_cut_string($items, 0, 'e='); $name = lgsl_cut_string($items, 0, ' ');
        if (substr($name, 0, 15) == 'Unknown\sfrom\s') { continue; }
        $server['p'][$i]['name'] = lgsl_unescape($name); lgsl_cut_string($items, 0, 'ry');
        $server['p'][$i]['country'] = substr($items, 0, 1) == '=' ? substr($items, 1, 2) : ''; $i++;
      }
    }
    else {
      $buffer = substr($buffer, 89, -4);
      while ($items = lgsl_cut_string($buffer, 0, "\r\n")) {
        $items = explode("\t", $items);
        $server['p'][$i]['name'] = substr($items[14], 1, -1);
        $server['p'][$i]['ping'] = $items[7];
        $server['p'][$i]['time'] = lgsl_time($items[8]); $i++;
      }
    }
    return TRUE;
  }
*/
//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+