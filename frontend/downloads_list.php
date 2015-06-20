<?

include_once('conf.php');

$conf = new Conf();

$file_list = get_directory_list('./' . $conf->download_path );
asort($file_list);

$head = array(
        array($msg->downloads_list_filename, 'text-align:left'),
        array($msg->downloads_list_filesize, 'text-align:left')
);
$body = array();
foreach ($file_list as $file) {
    $filename = $view->get_download_echo($file[0], './' . $conf->download_path);
    $filesize = $file[1];
    $body[] = array(
        array($filename, 'text-align:left'),
        array($filesize, 'text-align:right')
    );
}

echo $view->get_detail_title_echo($msg->downloads_list_title);

?><h2><?=$msg->downloads_list_maps?></h2><?
echo $view->build_table($head, $body);
?>


