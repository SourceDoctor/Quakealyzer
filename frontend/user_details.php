<?

$score_list = $db->get_score_list('user_id', $id, 'id');
if ( ! $db->get_user_list('id', $id)) {
    echo "<b>$msg->error_user_details_id_not_found</b>";
    return 1;
}
$user_bot = get_name_from_id($user_list, $id, 'bot') ? $msg->true : $msg->false;
$user_username = get_name_from_id($user_list, $id, 'username');
$user_model_id = get_name_from_id($user_list, $id, 'model_id');
$user_hmodel_id = get_name_from_id($user_list, $id, 'hmodel_id');
$user_model = get_name_from_id($model_list, $user_model_id, 'name');
$user_hmodel = get_name_from_id($hmodel_list, $user_hmodel_id, 'name');
$user_games_count = count($score_list);


$head = array();
$head[] = $msg->users_details_game_id;
$head[] = $msg->users_details_map_name;
$head[] = $msg->users_details_game_type;
$head[] = $msg->users_details_game_start;
if ($conf->show_game_finish) {
    $head[] = $msg->users_details_game_finish;
}
if ($conf->show_game_duration) {
    $head[] = $msg->users_details_duration;
}
$head[] = $msg->users_details_user_score;
$head[] = $msg->users_details_user_kills;
$head[] = $msg->users_details_user_killed;
$head[] = $msg->users_details_user_suicide;

$body = array();
foreach ($score_list as $score) {
    $game_details = $db->get_game_list($score['game_id'])[0];
    $game_id = $view->get_game_echo($score['game_id']);
    $game_map_name = get_name_from_id($map_list, $game_details['game_map_id'], 'name');
    $game_map_type = $translate->game_type(get_name_from_id($game_type_list, $game_details['game_type_id'], 'type'));
    $game_map_start = get_datetime($game_details['starttime']);
    $game_map_finish = get_datetime($game_details['endtime']);
    $game_map_duration = get_duration($game_details['starttime'], $game_details['endtime']);
    $game_map_endcause = get_name_from_id($end_cause_list, $game_details['end_cause_id'], 'reason');
    $user_score = $score['count'];
    $game_kills = count($db->get_kills($score['game_id'], $id));
    $game_killed = count($db->get_killed($score['game_id'], $id));
    $game_suicide = count($db->get_suicide($score['game_id'], $id));

    $body_game = array();
    $body_game[] = $game_id;
    $body_game[] = array($game_map_name, 'text-align:left');
    $body_game[] = array($game_map_type, 'text-align:left');
    $body_game[] = array($game_map_start, 'text-align:left');
    if ($conf->show_game_finish) {
        $body_game[] = array($game_map_finish, 'text-align:left');
    }
    if ($conf->show_game_duration) {
        $body_game[] = array($game_map_duration, 'text-align:right');
    }
    $body_game[] = array($user_score, 'text-align:right');
    $body_game[] = array($game_kills, 'text-align:right');
    $body_game[] = array($game_killed, 'text-align:right');
    $body_game[] = array($game_suicide, 'text-align:right');

    $body[] = $body_game;
}

echo $view->get_detail_title_echo($msg->users_details_title);

?>

<table>
<tr>
<td class='table_details_content'>
<?echo $view->get_model_picture_echo($conf->model_picture_path, $user_model)?>
</td>
<td class='table_details_content'>
<table>
<!---
<tr>
<td class='table_details_content_internal'><b><?=$msg->users_details_id?></b></td>
<td class='table_details_content_internal'><?=$id?></td>
</tr>
-->
<tr>
<td class='table_details_content_internal'><b><?=$msg->users_details_username?></b></td>
<td class='table_details_content_internal'><?=$view->get_user_echo($id, $user_username)?></td>
</tr>
<tr>
<td class='table_details_content_internal'><b><?=$msg->users_details_bot?></b></td>
<td class='table_details_content_internal'><?=$user_bot?></td>
</tr>
<tr>
<td class='table_details_content_internal'><b><?=$msg->users_details_model?></b></td>
<td class='table_details_content_internal'><?=$user_model?></td>
</tr>
<!---
<tr>
<td class='table_details_content_internal'><b><?=$msg->users_details_hmodel?></b></td>
<td class='table_details_content_internal'><?=$user_hmodel?></td>
</tr>
-->
<tr>
<td class='table_details_content_internal'><b><?=$msg->users_details_played_games?></b></td>
<td class='table_details_content_internal'><?=$user_games_count?></td>
</tr>
</table>
</td>
</tr>
</table>

<br>
<?
echo $view->build_table($head, $body);
?>
