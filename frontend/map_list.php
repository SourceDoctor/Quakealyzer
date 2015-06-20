<?

$head = array(
        array($msg->maps_list_map_id, 'text-align:left'),
        array($msg->maps_list_map_name, 'text-align:left')
);

echo $view->get_detail_title_echo($msg->maps_list_title);

?>
<table style='width:100%'>
<tr style='width:<?= round(100/count($game_type_list))?>%;'>
<?

foreach ($game_type_list as $game_type) {
    $body = array();
    ?><td style='vertical-align:top;'><?
    //get maps played with this game_type
    $maps = $db->get_map_list_by_game_type($game_type['id']);

    ?><h2><?=$translate->game_type($game_type['type'])?></h2><?
    //get the name of every map for this game_type and add to table_array
    foreach ($maps as $_map) {
        $map = $db->get_map_list($_map['game_map_id']);
        $body[] = array(
            $map[0]['id'],
            array($map[0]['name'], 'text-align:left')
        );
    }

    echo $view->build_table($head, $body);
    ?></td><?
}
?>
</tr>
</table>
