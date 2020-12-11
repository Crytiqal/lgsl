<?php

  global $output, $lgsl_zone_number, $lgsl_config;

  $output = "";  
  $lgsl_zone_number = 4;
  require e_PLUGIN."lgsl/lgsl_files/lgsl_zone.php";
  $ns -> tablerender($lgsl_config['title'][$lgsl_zone_number], $output);
  unset($output);
