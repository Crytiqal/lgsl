<?php

    $output .= "
    <div class='container-fluid'>";

//------------------------------------------------------------------------------------------------------------+
// SHOW THE STANDARD INFO

    $output .= "
    <div class='row'>
        <div class='col-md-12 col-sm-12 text-center'>
            <h3>{$server['s']['name']}</h3>
        </div>
    </div>
    <br>
    <div class='row'>
        <div class='col-xs-12 col-md-7 col-md-offset-1'>
            <div class='row' style='margin-bottom:1em'>
                <div class='col-xs-12'>
                    <table class='table hidden-xs'>
                        <thead>
                            <tr>
                                <th scope='col' colspan='5' class='text-center'><a href='{$misc['software_link']}'>{$lgsl_config['text']['slk']}</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{$lgsl_config['text']['sts']}       </td>
                                <td>{$misc['text_status']}              </td>
                                <td>{$lgsl_config['text']['typ']}       </td>
                                <td id='type'>{$server['b']['type']}    </td>
                                <td rowspan='4'>
                                    <div style='position: relative;width: 160px;margin: auto;'>
                                        <div style='position:absolute;top:0;left:0;'>
                                            <img class='img-responsive' id='image_map'             alt='' src='{$misc['image_map']}' />
                                        </div>
                                        <div style='position:absolute;top:0;left:0;'>
                                            <img class='img-responsive' id='image_map_password'    alt='' src='{$misc['image_map_password']}' />
                                        </div>
                                        <div style='position:absolute;top:0.2em;left:0.2em;'>
                                            <img class='img-responsive' id='icon_game'             alt='' src='{$misc['icon_game']}' title='{$misc['text_type_game']}' />
                                        </div>
                                        <div style='position:absolute;top:0.2em;right:0.2em;'>
                                            <img class='img-responsive' id='icon_location'         alt='' src='{$misc['icon_location']}' title='{$misc['text_location']}' />  
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr><td>{$lgsl_config['text']['adr']}</td><td id='ip'>{$server['b']['ip']}          </td><td>{$lgsl_config['text']['gme']}</td><td id='game'>{$server['s']['game']}   </td></tr>
                            <tr><td>{$lgsl_config['text']['cpt']}</td><td id='c_port'>{$server['b']['c_port']}  </td><td>{$lgsl_config['text']['map']}</td><td id='map' >{$server['s']['map']}    </td></tr>
                            <tr><td>{$lgsl_config['text']['qpt']}</td><td id='q_port'>{$server['b']['q_port']}  </td><td>{$lgsl_config['text']['plr']}</td><td><span id='players'>{$server['s']['players']}</span>/<span id='playersmax'>{$server['s']['playersmax']}</span></td></tr>
                        </tbody>
                    </table>
                    <table class='table hidden-sm hidden-md hidden-lg'>
                    <thead>
                        <tr>
                            <th scope='col' colspan='4' class='text-center'><a href='{$misc['software_link']}'>{$lgsl_config['text']['slk']}</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{$lgsl_config['text']['sts']}       </td>
                            <td>{$misc['text_status']}              </td>
                            <td>{$lgsl_config['text']['typ']}       </td>
                            <td id='type'>{$server['b']['type']}    </td>
                        </tr>
                        <tr><td>{$lgsl_config['text']['adr']}</td><td id='ip'>{$server['b']['ip']}          </td><td>{$lgsl_config['text']['gme']}</td><td id='game'>{$server['s']['game']}   </td></tr>
                        <tr><td>{$lgsl_config['text']['cpt']}</td><td id='c_port'>{$server['b']['c_port']}  </td><td>{$lgsl_config['text']['map']}</td><td id='map' >{$server['s']['map']}    </td></tr>
                        <tr><td>{$lgsl_config['text']['qpt']}</td><td id='q_port'>{$server['b']['q_port']}  </td><td>{$lgsl_config['text']['plr']}</td><td><span id='players'>{$server['s']['players']}</span>/<span id='playersmax'>{$server['s']['playersmax']}</span></td></tr>
                    </tbody>
                </table>
                </div>
            </div>
            <div class='row' style='margin-bottom:1em'>
                <div class='col-xs-12'>
                    <div class='alert alert-warning alert-dismissible fade in' role='alert' style='position: absolute; right: 17px; left: 0px; margin-top: 0; margin-right:15px; margin-left: 15px; border-top-left-radius: 0px; border-top-right-radius: 0px; z-index: 2'>
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                        <strong>Live log is disabled!</strong> Enable live logging by clicking the logbutton below.
                    </div>
                    <div class='fade out' role='alert' style='position: absolute; right: 0px; left: 0px; padding-left: 0.1em; margin-top: 0; margin-right:15px; margin-bottom: 0; margin-left: 15px; z-index: 1; height:420px; overflow-y: scroll; border-radius: 0;color: #fff; background-color:#0037DA;'>
                        <span id='response'></span>
                    </div>
                    <div id='log' class='logbox' style='padding-left: 0.1em; white-space: nowrap; overflow:scroll; color: #fff; background-color: #000; width: 100%; height: 420px;'>
                    </div>
                    <div id='controls' class='controls'>
                        <form class='input-group' onsubmit='return false;'>
                            <span class='input-group-btn'>
                                <button type='button' class='btn btn-primary'
                                    style='border-top-left-radius:0px;'
                                    onclick='streamLog(this);return false' value='1'>
                                    <i class='glyphicon glyphicon-align-left'></i></button>
                            </span>
                            <div class='input-group' style='width:100%;'>
                                <input type='text' class='form-control' aria-label='Text input with segmented button dropdown' placeholder='set rconpassword' style='border-radius:0px'>
                                <div class='input-group-btn dropup'>
                                    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style='border-radius:0px'>
                                        <span class='caret'></span>
                                        <span class='sr-only'>Toggle Dropdown</span>
                                    </button>
                                    <ul class='dropdown-menu dropdown-menu-right'>
                                        <li><a href='javascript:void(0);'>Action</a></li>
                                        <li><a href='javascript:void(0);'>Another action</a></li>
                                        <li><a href='javascript:void(0);'>Something else here</a></li>
                                        <li role='separator' class='divider'></li>
                                        <li><a href='javascript:void(0);'>Separated link</a></li>
                                    </ul>
                                    <button type='button' class='btn btn-default'
                                        style='border-radius: 0px;'
                                        onclick='sendCmd(this);return false'>Submit</button>
                                </div>
                            </div>

                            <span class='input-group-btn'>
                                <!--
                                <button type='button' class='btn btn-default'
                                    onclick='sendCmd(this);return false'>Submit</button>
                                -->
                                <button type='button' class='btn btn-primary'
                                    onclick='pinResponse(this);return false'>
                                    <i class='glyphicon glyphicon-pushpin'></i></button>
                                <button type='button' class='btn btn-danger'
                                    style='border-top-right-radius:0px;'
                                    onclick='clearRconPasswd();return false'>
                                    <i class='glyphicon glyphicon-remove'></i></button>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-xs-12 col-md-3' style='margin-bottom:1em'>";
         
//------------------------------------------------------------------------------------------------------------+
// SHOW THE PLAYERS
        
if (empty($server['p']) || !is_array($server['p']))
{
    $output .= "    
    <table class='table' id='playerinfo'>
        <thead>
            <tr style='text-transform:capitalize'><th class='text-center'>{$lgsl_config['text']['npi']}</th></tr>
        </thead>
        <tbody></tbody>
    </table>";
} else {
    $output .= "
    <table class='table' id='playerinfo'>
        <thead>
            <tr style='text-transform:capitalize'>";

    foreach ($fields as $field)
    {
        $field = ucfirst($field);
        $output .= "<th>{$field}</th>";
    }

    $output .= "
            </tr>
        </thead>
        <tbody>";

        foreach ($server['p'] as $player_key => $player)
        {
            $output .= "
                <tr>";
            foreach ($fields as $field)
            {
                $output .= "<td> {$player[$field]} </td>";
            }
        
            $output .= "
                </tr>";
        }

    $output .= "
        </tbody>
    </table>";            

}
    $output .= "   
        </div>
    </div>
    <div class='row'>
        <div class='col-xs-12'>";

//------------------------------------------------------------------------------------------------------------+
// SHOW THE SETTINGS

    if (empty($server['e']) || !is_array($server['e']))
    {
    $output .= "
    <table cellpadding='4' cellspacing='2' style='margin:auto'>
        <tr style='".lgsl_bg(FALSE)."'>
        <td> {$lgsl_config['text']['nei']} </td>
        </tr>
    </table>";
    }
    else
    {
    $output .= "
    <table cellpadding='4' cellspacing='2' style='margin:auto; display:none;'>
        <tr style='".lgsl_bg(FALSE)."'>
        <td> <b>{$lgsl_config['text']['ehs']}</b> </td>
        <td> <b>{$lgsl_config['text']['ehv']}</b> </td>
        </tr>";

    foreach ($server['e'] as $field => $value)
    {
        $color = lgsl_bg();

        $output .= "
        <tr>
        <td style='{$color}'> {$field} </td>
        <td style='{$color}' id='{$field}'> {$value} </td>
        </tr>";
    }

    $output .= "
    </table>";
    }

//------------------------------------------------------------------------------------------------------------+

    $output .= "
            </div>
        </div>
    </div>
    <script src='lgsl_rcon/js/lgsl_rcon.js'></script>";    

?>