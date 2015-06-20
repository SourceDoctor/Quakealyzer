<?

$head = array(
    array($msg->users_list_user_id, 'text-align:left'),
    array($msg->users_list_user_username, 'text-align:left')
);

$body = array();
foreach ($user_list as $user) {
    if ( ! $user['model_id']) {
        //don't print user 'world'
        continue;
    }

    $user_id = $user['id'];
    $user_username = $view->get_user_echo($user_id, $user['username']);

    if ($user['bot']) {
        $body_bot[] = array(
            $user_id,
            array($user_username, 'text-align:left')
        );
    }
    else {
        $body_player[] = array(
            $user_id,
            array($user_username, 'text-align:left')
        );
    }
}
echo $view->get_detail_title_echo($msg->users_list_title);
?>

<table style='width:100%'>
<tr>
<td><h2><?=$msg->users_list_player?></h2></td>
<td><h2><?=$msg->users_list_bot?></h2></td>
</tr>
<tr>
<td style='width:50%;vertical-align:top;'>
<?
echo $view->build_table($head, $body_player);
?>
</td>
<td style='width:50%;vertical-align:top;'>
<?
echo $view->build_table($head, $body_bot);
?>
</td>
</tr>
</table>
