<?
$list = filter_input(INPUT_GET, "list");
$details = filter_input(INPUT_GET, "details");
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);


if ( ($details) && (! is_int($id) ) ) {
    # Integer Proofment
    $view->print_error($msg->error_no_valid_value);
}


elseif ($list == 'games') {
    include('games_list.php');
}
elseif ($list == 'user') {
    include('user_list.php');
}
elseif ($list == 'maps') {
    include('map_list.php');
}
elseif ($list == 'downloads') {
    include('downloads_list.php');
}
elseif ($list == 'items') {
    include('item_list.php');
}
elseif ($list == 'logout') {
    logout();
}
elseif ($details == 'game') {
    include('game_details.php');
}
elseif ($details == 'user') {
    include('user_details.php');
}
elseif ($list == '') {
    //nothing, so back to default
    include('games_list.php');
}
else {
    $view->print_error($msg->error_no_known_site);
}

?>
