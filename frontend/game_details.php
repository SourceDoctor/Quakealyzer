<?

$game_details = $db->get_game_list($id)[0];
if ( ! $game_details) {
    echo "<b>$msg->error_game_details_id_not_found</b>";
    return 1;
}
$score_list = $db->get_score_list('game_id', $id);
$team_score_list = $db->get_team_score_list($id);

$game_map_name = get_name_from_id($map_list, $game_details['game_map_id'], 'name');
$game_map_type = $translate->game_type(get_name_from_id($game_type_list, $game_details['game_type_id'], 'type'));
$game_bot_skill = $translate->bot_skill($game_details['bot_skill']);
$game_map_start = get_datetime($game_details['starttime']);
$game_map_finish = get_datetime($game_details['endtime']);
$game_map_duration = get_duration($game_details['starttime'], $game_details['endtime']);
$game_map_endcause = get_name_from_id($end_cause_list, $game_details['end_cause_id'], 'reason');

$game_limits = $translate->game_limit($game_details['game_type_id']);
$game_timelimit = '';
$game_fraglimit = '';
$game_capturelimit = '';
if (in_array('timelimit', $game_limits)) {
    $game_timelimit = $db->get_game_parameter($id, 'timelimit');
}
if (in_array('fraglimit', $game_limits)) {
    $game_fraglimit = $db->get_game_parameter($id, 'fraglimit');
}
if (in_array('capturelimit', $game_limits)) {
    $game_capturelimit = $db->get_game_parameter($id, 'capturelimit');
}


if ( ! $game_map_endcause) {
    $game_map_endcause = $msg->game_not_correct_finished;
}

echo $view->get_detail_title_echo($msg->games_details_title);

?>
<?

$head_users = array();
$head_users[] = $msg->games_details_user_name;
$head_users[] = $msg->games_details_user_bot;
$head_users[] = $msg->games_details_user_score;
if ( count($team_score_list) ) {
    $head_users[] = $msg->games_details_user_team_leader;
}


if ( count($team_score_list) ) {
    $head_team = array($msg->games_details_team_color, $msg->games_details_team_score);
    $body_team = array();
    foreach ($team_score_list as $tscore) {
        $team_id = $db->get_team($tscore['team_id']);
        $team = $translate->team($team_id);
        $body_team[] = array($team, $tscore['score']);
    }
}
?>



<table>
<tr>
<td class='table_details_content'>
    <?echo $view->get_map_picture_echo($conf->map_picture_path, $game_map_name)?>
</td>
<td class='table_details_content'>
    <table>
    <tr><td class='table_details_content_internal'>
    <b><?=$msg->games_details_id?></b>
    </td><td class='table_details_content_internal'>
    <?=$id?>
    </td></tr><tr><td class='table_details_content_internal'>
    <b><?=$msg->games_details_map_name?></b>
    </td><td class='table_details_content_internal'>
    <?=$game_map_name?>
    </td></tr><tr><td class='table_details_content_internal'>
    <b><?=$msg->games_details_game_type?></b>
    </td><td class='table_details_content_internal'>
    <?=$game_map_type?>
    <?
    if ($game_timelimit) {
    ?>
        </td></tr><tr><td class='table_details_content_internal'>
        <b><?=$msg->games_details_game_timelimit?></b>
        </td><td class='table_details_content_internal'>
        <?=$game_timelimit?>
    <?
    }
    if ($game_fraglimit) {
    ?>
        </td></tr><tr><td class='table_details_content_internal'>
        <b><?=$msg->games_details_game_fraglimit?></b>
        </td><td class='table_details_content_internal'>
        <?=$game_fraglimit?>
    <?
    }
    if ($game_capturelimit) {
    ?>
        </td></tr><tr><td class='table_details_content_internal'>
        <b><?=$msg->games_details_game_capturelimit?></b>
        </td><td class='table_details_content_internal'>
        <?=$game_capturelimit?>
    <?
    }
    ?>
    </td></tr><tr><td class='table_details_content_internal'>
    <b><?=$msg->games_details_game_bot_skill?></b>
    </td><td class='table_details_content_internal'>
    <?=$game_bot_skill?>
    </td></tr><tr><td class='table_details_content_internal'>
    <b><?=$msg->games_details_start?></b>
    </td><td class='table_details_content_internal'>
    <?=$game_map_start?>
    <?
    if ($conf->show_game_finish) {
    ?>
        </td></tr><tr><td class='table_details_content_internal'>
        <b><?=$msg->games_details_finish?></b>
        </td><td class='table_details_content_internal'>
        <?=$game_map_finish?>
    <?
    }
    if ($conf->show_game_duration) {
    ?>
        </td></tr><tr><td class='table_details_content_internal'>
        <b><?=$msg->games_details_duration?></b>
        </td><td class='table_details_content_internal'>
        <?=$game_map_duration?>
    <?
    }
    ?>
    </td></tr><tr><td class='table_details_content_internal'>
    <b><?=$msg->games_details_endcause?></b>
    </td><td class='table_details_content_internal'>
    <?=$game_map_endcause?>
    </td></tr>
    </table>
</td>
</tr>
<tr>
<td class='table_details_content'>
<?



$team = True;
# if no team game
# so fake team_score and mark as no team match
if ( ! count($team_score_list)) {
    $team_score_list = array(array('team_id' => ''));
    $team = False;
}

if ( count($score_list) ) {
    foreach ($team_score_list as $tscore) {
        $body_users = array();
        foreach ($score_list as $score) {
            if ($score['team_id'] != $tscore['team_id'] && $team) {
                continue;
            }
            $_body_users = array();
            $user_username = $view->get_user_echo($score['user_id'], get_name_from_id($user_list, $score['user_id'], 'username'));
            $user_bot = get_name_from_id($user_list, $score['user_id'], 'bot') ? $msg->true : $msg->false;
            $user_score = $score['count'];
            $user_team = $db->get_team($score['team_id']);
            $user_team_leader = $score['team_leader'] ? $msg->true : $msg->false;

            $_body_users[] = $user_username;
            $_body_users[] = $user_bot;
            $_body_users[] = $user_score;
            if ( count($team_score_list) && $team ) {
                $_body_users[] = $user_team_leader;
            }

            $body_users[] = $_body_users;
        }
        echo $view->build_table($head_users, $body_users);
    }
}
?>
</td>
<td class='table_details_content'>
<?
if ( count($team_score_list) && $team) {
    echo $view->build_table($head_team, $body_team);
}
?>
</td>
</tr>
</table>
