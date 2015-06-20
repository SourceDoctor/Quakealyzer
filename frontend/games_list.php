<?
$head= array();
$head[] = array($msg->games_list_game_id, 'text-align:left');
$head[] = $msg->games_list_game_start;
if ($conf->show_game_finish) {
    $head[] = $msg->games_list_game_finish;
}
if ($conf->show_game_duration) {
    $head[] = $msg->games_list_game_duration;
}
$head[] = array($msg->games_list_game_type, 'text-align:left');
$head[] = array($msg->games_list_game_map, 'text-align:left');
$head[] = array($msg->games_list_game_end_cause, 'text-align:left');
if ($conf->show_game_mod) {
    $head[] = $msg->games_list_game_mod;
}


$body = array();
foreach ($game_list as $game) {
    $game_id = $view->get_game_echo($game['id']);
    $game_start = get_datetime($game['starttime']);
    $game_finish = get_datetime($game['endtime']);
    $game_duration = get_duration($game['starttime'], $game['endtime']);
    $game_type = $translate->game_type(get_name_from_id($game_type_list, $game['game_type_id'], 'type'));
    $game_map = get_name_from_id($map_list, $game['game_map_id'], 'name');
    $game_end_cause = get_name_from_id($end_cause_list, $game['end_cause_id'], 'reason');
    $game_mod = get_name_from_id($game_mod_list, $game['game_mod_id'], 'name');

    if ($game['id'] != $db->get_highest_id('game') ) {
        if ( $game_duration == "--:--") {
            $game_end_cause = $msg->game_not_correct_finished;
        }
    }

    $body_game = array();
    $body_game[] = $game_id;
    $body_game[] = array($game_start, 'text-align:left');
    if ($conf->show_game_finish) {
        $body_game[] = array($game_finish, 'text-align:left');
    }
    if ($conf->show_game_duration) {
        $body_game[] = array($game_duration, 'text-align:right');
    }
    $body_game[] = array($game_type, 'text-align:left');
    $body_game[] = array($game_map, 'text-align:left');
    $body_game[] = array($game_end_cause, 'text-align:left');
    if ($conf->show_game_mod) {
        $body_game[] = array($game_mod, 'text-align:left');
    }

    $body[] = $body_game;
}
echo $view->get_detail_title_echo($msg->games_list_title);

echo $view->build_table($head, $body);
