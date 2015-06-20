<?

$head = array(
        array($msg->items_list_item_id, 'text-align:left'),
        array($msg->items_list_item_item, 'text-align:left'),
        ""
);

$items = array();

foreach ($item_list as $item) {
    $item_id = $item['id'];
    $item_item = $translate->item($item['item']);
    $item_icon = $translate->item_icon($item['item']);
    $items[] = array(
        $item_id,
        array($item_item, 'text-align:left'),
        array($view->get_item_icon_echo($conf->item_icon_picture_path, $item_icon), 'text-align:left')
    );
}

$used_weapons = array();

foreach ($used_weapons_list as $weapon) {
    $weapon_id = $weapon['id'];
    $weapon_item = $translate->item($weapon['item']);
    $weapon_icon = $translate->item_icon($weapon['item']);
    $used_weapons[] = array(
        $weapon_id,
        array($weapon_item, 'text-align:left'),
        array($view->get_item_icon_echo($conf->item_icon_picture_path, $weapon_icon), 'text-align:left')
    );
}

echo $view->get_detail_title_echo($msg->items_list_title);
?>

<table style='width:100%'>
<tr>
<td><h2><?=$msg->items_list_items?></h2></td>
<td><h2><?=$msg->items_list_used_weapons?></h2></td>
</tr>
<tr>
<td style='width:50%;vertical-align:top;'>
<?
echo $view->build_table($head, $items);
?>
</td>
<td style='width:50%;vertical-align:top;'>
<?
echo $view->build_table($head, $used_weapons);
?>
</td>
</tr>
</table>

