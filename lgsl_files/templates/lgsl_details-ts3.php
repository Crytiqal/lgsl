<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ORIGINAL_INCLUDED', TRUE);

$lgsl_config['title'][0] = '';

//------------------------------------------------------------------------------------------------------------+
// SHOW THE STANDARD INFO
/*
    echo '
        <br>
        <div class="card">
            <div class="card-header">
                '.$server['s']['name'].'
            </div>
            <div class="card-body">
                <a href="'.$misc['software_link'].'"><h5 class="card-title">Join</h5></a>
                <p class="card-text">
                    <table class="table table-sm">
                        <tbody>
                            <tr><th scope="row">'.$lgsl_config['text']['sts'].'</th><td>'.$misc['text_status'].'  </td></tr>
                            <tr><th scope="row">'.$lgsl_config['text']['adr'].'</th><td>'.$server['b']['ip'].'    </td></tr>
                            <tr><th scope="row">'.$lgsl_config['text']['cpt'].'</th><td>'.$server['b']['c_port'].'</td></tr>
                            <tr><th scope="row">'.$lgsl_config['text']['qpt'].'</th><td>'.$server['b']['q_port'].'</td></tr>
                        </tbody>
                    </table>
                </p>
            </div>
        </div>
        <br>';
*/
/*
    $output .= "
    <table cellpadding='2' cellspacing='2' style='margin:auto'>
        <tr>
        <td colspan='3' style='text-align:center'>
            <b> {$server['s']['name']} </b><br /><br />
        </td>
        </tr>
    </table>
    <table cellpadding='2' cellspacing='2' style='margin:auto'>
        <tr>
        <td colspan='2' style='text-align:center'>
            <table cellpadding='4' cellspacing='2' style='width:100%; margin:auto'>
            <tr><td style='".lgsl_bg(TRUE)."; text-align:center'><a href='{$misc['software_link']}'>{$lgsl_config['text']['slk']}</a></td></tr>
            </table>
        </td>
        <td rowspan='2' style='text-align:center; vertical-align:top'>
            <div style='width:{$lgsl_config['zone']['width']}px; padding:2px; position:relative; margin:auto'>
            <img alt='' src='{$misc['image_map']}'                                            style='vertical-align:middle' />
            <img alt='' src='{$misc['image_map_password']}'                                   style='position:absolute; z-index:2; top:0px; left:0px;' />
            <!-- <img alt='' src='{$misc['icon_game']}'          title='{$misc['text_type_game']}' style='position:absolute; z-index:2; top:6px; left:6px;' /> -->
            <!-- <img alt='' src='{$misc['icon_location']}'      title='{$misc['text_location']}'  style='position:absolute; z-index:2; top:6px; right:6px;' /> -->
            </div>
        </td>
        </tr>
        <tr>
        <td style='text-align:center'>
            <table cellpadding='4' cellspacing='2' style='margin:auto'>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['sts']} </b></td><td style='white-space:nowrap'> {$misc['text_status']}                                   </td></tr>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['adr']} </b></td><td style='white-space:nowrap'> {$server['b']['ip']}                                     </td></tr>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['cpt']} </b></td><td style='white-space:nowrap'> {$server['b']['c_port']}                                 </td></tr>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['qpt']} </b></td><td style='white-space:nowrap'> {$server['b']['q_port']}                                 </td></tr>
            </table>
        </td>
        <td style='text-align:center'>
            <table cellpadding='4' cellspacing='2' style='margin:auto'>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['typ']} </b></td><td style='white-space:nowrap'> {$server['b']['type']}                                   </td></tr>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['gme']} </b></td><td style='white-space:nowrap'> {$server['s']['game']}                                   </td></tr>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['map']} </b></td><td style='white-space:nowrap'> {$server['s']['map']}                                    </td></tr>
            <tr style='".lgsl_bg().";white-space:nowrap'><td> <b> {$lgsl_config['text']['plr']} </b></td><td style='white-space:nowrap'> {$server['s']['players']} / {$server['s']['playersmax']} </td></tr>
            </table>
        </td>
        </tr>
    </table>";
*/

//------------------------------------------------------------------------------------------------------------+
// SHOW THE PLAYERS

echo '
    <br>
    <div class="container-fluid" id="teamspeak_menu">
        <div class="card">
            <div class="card-header text-dark">'.$server['s']['name'].'</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">';
                        require_once("/srv/htdocs/team-aero.org/www/e107_plugins/teamspeak_menu/teamspeak_menu.php");
echo '              </div>
                    <div class="col-md-5 col-md-offset-2">
                        <div class="card">
                            <div class="card-body" style="background-color:rgba(0, 0, 0, 0.5);">
                                <p class="card-text">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr><th scope="row">'.$lgsl_config['text']['sts'].'</th><td>'.$misc['text_status'].'  <a href="'.$misc['software_link'].'">Join</a></td></tr>
                                            <tr><th scope="row">'.$lgsl_config['text']['adr'].'</th><td>'.$server['b']['ip'].'    </td></tr>
                                            <tr><th scope="row">'.$lgsl_config['text']['cpt'].'</th><td>'.$server['b']['c_port'].'</td></tr>
                                            <tr><th scope="row">'.$lgsl_config['text']['qpt'].'</th><td>'.$server['b']['q_port'].'</td></tr>
                                        </tbody>
                                    </table>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';

?>