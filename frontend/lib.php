<?


function check_login() {

    if(! isset($_SESSION['username'])) {
        $view = new View();
        //no Sessiondata -> Login
        $view->login_area();
        return False;
    }
    return True;
}

function logout() {
    session_destroy();
    header("location: index.php");
}


function get_server_password() {
    $conf = new Conf();
    $password = exec("/bin/grep g_password " . $conf->server_config_file . " |/usr/bin/cut -d'\"' -f2 ");
    return $password;
}

function get_name_from_id($search_list, $search_id, $search_column) {

    foreach ($search_list as $entry) {
        if ($entry['id'] == $search_id) {
            return $entry[$search_column];
        }
    }
}


function get_datetime($timestamp) {
    if ( ! $timestamp) {
        return "----------------------";
    }
    return date("Y.m.d - H:i:s",$timestamp);
}

function get_duration($start_timestamp, $end_timestamp) {

    if ( ! $end_timestamp) {
        return "--:--";
    }

    $duration = $end_timestamp - $start_timestamp;
    $minutes = round($duration/60, 0);
    $seconds = $duration % 60;

    if ($seconds < 10) {
        return $minutes . ':0' . $seconds;
    }
    return $minutes . ':' . $seconds;
}


function get_directory_list($directory, $extension='', $type='file') {

    $unit = array('', 'k', 'M', 'G', 'T');
    $results = array();

    if ( ! is_dir($directory)) {
        return array();
    }

    $handler = opendir($directory);

    while ($file = readdir($handler)) {
        if (($type == 'file') && (is_file($directory . '/' . $file) )) {
            if ($extension) {
                $filedata = pathinfo($file);
                if ($filedata['extension'] != $extension) {
                    continue;
                }
            }
            $u = 0;
            $size = filesize($directory . '/' . $file);
            while ( ($size >= 1024) || ( $u < count($u) ) ) {
                $size = $size / 1024;
                $u++;
            }


            // if file isn't this directory or its parent, add it to the results
            if ($file != "." && $file != "..") {
                $results[] = array($file, round($size, 2) . ' ' . $unit[$u] . 'B');
            }
        }
        elseif (($type == 'dir') && (is_dir($directory . '/' . $file) )) {
            $results[] = $file;
        }
    }

    // tidy up: close the handler
    closedir($handler);

    return $results;
}


function check_property($class, $type) {
    /************************************************
    proof settings for correctness
    (files for existence ...)
    ************************************************/
    $view = new View();

    $properties = get_object_vars($class);

    if ($type == 'exist') {
        foreach ($properties as $property) {
            if ( ! file_exists($property)) {
                $view->print_error('MISSING: ' . $property);
            }
        }
    }
}


function convert_tga_pic($tga_file, $picture_type) {

    if ($picture_type == 'jpg') {
        $picture_format = 'jpeg';
    }
    else {
        $picture_format = $picture_type;
    }

    $converter = new Imagick();

    $file = pathinfo($tga_file);
    $file_no_ext = str_replace('.' . $file['extension'], '', $file['basename']);

    $converter->readImage($tga_file);
    $converter->setImageFormat($picture_format);
    $converter->writeImage($file['dirname'] . '/' . $file_no_ext . '.' . $picture_type);

}


class Package {

    private $conf = '';
    private $path = '';

    public function __construct() {
        $this->conf = new Conf();
        $this->path = new Path();
    }

    public function extract_package($package) {


        //extract package to temp-dir
        $source = $this->conf->server_path . '/baseq3/' . $package;

        $extract_dir = $this->conf->tmp_path . '/' . $package;
        exec("mkdir " . $extract_dir);
        $unzip_param = "-oq -d " . $extract_dir;
        exec ($this->path->unzip . ' ' . $unzip_param . ' ' . $source);

    }


    public function remove_extracted_package($package) {
        exec("cd " . $this->conf->tmp_path . " && rm -rf " . $package);
    }


    public function zip_package_to_download_section($package) {

        $server_path = $this->conf->server_path;
        $download_path = realpath($this->conf->download_path);
        $zip_file = $download_path . '/' . $package . '.zip';

        exec("cd " . $server_path . '/baseq3/ && ' . $this->path->zip . ' ' . $zip_file . ' ' . $package);
    }




    private function convert_path_picture($src_path, $dest_path, $filter='', $picture_type='jpg') {

        if ( ! is_dir($src_path)) {
            return;
        }

        //now convert every tga into jpg
        $tga_shots = get_directory_list($src_path, 'tga');
        foreach ($tga_shots as $tga) {
            if ($filter && ( ! preg_match($filter, $tga[0]))) {
                continue;
            }
            convert_tga_pic($src_path . '/' . $tga[0], $picture_type);
        }

        //and copy all jpg into the destination directory
        $pic_shots = get_directory_list($src_path, $picture_type);
        foreach ($pic_shots as $pic) {
            copy($src_path . '/' . $pic[0], $dest_path . '/' . $pic[0]);
        }
    }


    public function get_item_icons($package) {
        $extract_dir = $this->conf->tmp_path . '/' . $package;
        $extract_dir .= '/icons/';

        $icon_pic_target_path = realpath($this->conf->item_icon_picture_path);

        $this->convert_path_picture($extract_dir, $icon_pic_target_path, '', 'gif');
    }


    public function get_levelshots($package) {
        $extract_dir = $this->conf->tmp_path . '/' . $package;
        $extract_dir .= '/levelshots/';

        $map_pic_target_path = realpath($this->conf->map_picture_path);

        $this->convert_path_picture($extract_dir, $map_pic_target_path);
    }


    public function get_model_images($package) {
        $extract_dir = $this->conf->tmp_path . '/' . $package;
        $extract_dir .= '/models/players/';

        $model_pic_path = realpath($this->conf->model_picture_path);
        //get a list of playermodels in package
        $players = get_directory_list($extract_dir, '', 'dir');
        //now let's do for every model
        foreach ($players as $player) {
            //create player directory
            exec('mkdir -p ' . $model_pic_path . '/' . $player);

            $this->convert_path_picture($extract_dir . '/' . $player, $model_pic_path . '/' . $player, '/^icon_.*\.tga$/');
        }
    }




    public function remove_package_from_download_section($package) {
        $download_path = realpath($this->conf->download_path);
        exec("rm -f " . $download_path . '/' . $package . ".zip");
    }


    public function check_packages($db) {
        /********************************************
        checks for new pk3 Package in Serverdir and
        automatically handle all publishing jobs
        ********************************************/

        $package_path = $this->conf->server_path . '/baseq3/';

        $_package_dir_list = get_directory_list($package_path, 'pk3');
        $package_dir_list = array();
        foreach ($_package_dir_list as $p) {
            $package_dir_list[] = $p[0];
        }

        $package_database_list = $db->get_packages();


        $new_packages = array_diff($package_dir_list, $package_database_list);
        $removed_packages = array_diff($package_database_list, $package_dir_list);

        foreach ($removed_packages as $removed_package) {
            $this->remove_package_from_download_section($removed_package);
            //remove package name from database
            $db->remove_package($removed_package);
        }
        foreach ($new_packages as $new_package) {

            //add packagename to database
            $db->add_package($new_package);

            $this->extract_package($new_package);

            //get levelshots from package?
            if ($this->conf->get_levelshots) {
                $this->get_levelshots($new_package);
            }
            //get item icons from package?
            if ($this->conf->get_item_icons) {
                $this->get_item_icons($new_package);
            }
            //get model images from package?
            if ($this->conf->get_model_images) {
                $this->get_model_images($new_package);
            }

            $this->remove_extracted_package($new_package);

            //build zip File from package for downloading?
            if ($this->conf->package_for_download) {
                //don't publish native pak-Files for downloading
                if (preg_match('/^pak[0-9].pk3$/', $new_package)) {
                    continue;
                }
                $this->zip_package_to_download_section($new_package);
            }
        }
    }
}



class View {

    private $translate = '';
    private $msg = '';

    public function __construct() {
        $this->translate = new Translator();
        $this->msg = new Message();
        $this->conf = new Conf();
    }

    function login_area() {
    ?>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <title>
    <?=$this->msg->title_text?>
    </title>
    <table width=100% height=100% border="0" align="center">
    <tr align=center><td>
    <h1><?=$this->msg->login_title?></h1>

    <table class=table_login_window align="center">
    <tr>
        <form name="loginwindow" method="post" action="check_login.php">
            <td>
            <table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#F0F0F0">
            <tr>
                <td colspan="3">&nbsp;</strong></td>
            </tr>
            <tr>
                <td width="78"><?=$this->msg->login_username?></td>
                <td width="6">:</td>
                <td width="294"><input name="username" type="text" id="username"></td>
            </tr>
            <?
            if ( ! $this->conf->no_login_password) {
            ?>
                <tr>
                    <td><?=$this->msg->login_password?></td>
                    <td>:</td>
                    <td><input name="password" type="password" id="password"></td>
                </tr>
            <?
            }
            ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><input type="submit" name="Submit" value="<?=$this->msg->login_button?>"></td>
            </tr>
            </table>
            </td>
        </form>
    </tr>
    </table>
    </td></tr>
    </table>
    <script language=javascript>
    document.forms[0].username.focus();
    </script>
    <?
    }


    function print_error($message) {
        echo "<h1><font color=#dd2222>" . $message . "</font></h1>";
    }

    function build_table($t_head=array(), $t_body=array(), $t_foot=array()) {
    ?>
        <table class=table_list>

        <thead class=table_list_head>
        <?
        foreach ($t_head as $head) {
            if (is_array($head)) {
                ?><td style='<?=$head[1]?>'><?=$head[0]?></td><?
            }
            else {
                ?><td><?=$head?></td><?
            }
        }
        ?>
        </thead>
        <tbody>
        <?
        foreach ($t_body as $row) {
            ?><tr><?
            foreach ($row as $element) {
                if (is_array($element)) {
                ?><td  style='<?=$element[1]?>' class=table_list_content><?=$element[0]?></td><?
                }
                else {
                    ?><td class=table_list_content><?=$element?></td><?
                }
            }
            ?></tr><?
        }
        ?>
        </tbody>
        <tfoot>
        <?
        foreach ($t_foot as $foot) {
            if (is_array($foot)) {
                ?><td style='<?=$foot[1]?>'><?=$foot[0]?></td><?
            }
            else {
                ?><td><?=$foot?></td><?
            }
        }
        ?>
        </tfoot>
        </table>
    <?
    }

    public function get_detail_title_echo ($text) {
        return "<h2>" . $text . "</h2>";
    }

    public function get_user_echo($user_id, $username) {
        return "<a align style='link' href='index.php?details=user&id=" . $user_id . "'>" . $this->translate->username($username) . "</a>";
    }

    public function get_game_echo($game_id) {
        return "<a align style='link' href='index.php?details=game&id=" . $game_id . "'>" . $game_id . "</a>";
    }

    public function get_download_echo($file, $path) {
        return "<a align style='link' href='" . $path . '/' . $file . "'>" . $file . "</a>";
    }

    public function get_item_icon_echo($picture_path, $icon) {

        if ( ! $icon) {
            return "";
        }
        return "<img class='item_icon_picture' src=" . $picture_path. '/' . $icon . "></img>";
    }

    public function get_model_picture_echo($picture_path, $player) {
        $default_pic = 'default';

        $_parts = explode('/', $player);
        if ( count($_parts) == 1 ) {
            $pic = $default_pic;
        }
        else {
            $pic = $_parts[1];
        }
        $player_name = $_parts[0];


        if ( ! file_exists(realpath('./'. $picture_path. '/' . $player_name . '/icon_' . $pic . '.jpg'))) {
            $pic = $default_pic;
        }

        return "<img class='model_picture' src=" . $picture_path. '/' . $player_name . '/icon_' . $pic . ".jpg></img>";
    }

    public function get_map_picture_echo($picture_path, $picture_name) {

        $picture_name = strtolower($picture_name);

        //get list of map pictures
        $picture_list = get_directory_list($picture_path);

        //let's search if there is a map_picture with the map name,
        foreach ($picture_list as $pic) {
            $_pic = explode('.', $pic[0]);
            $compare_picture = strtolower($_pic[0]);

            if ( (strcmp($picture_name, $compare_picture) == 0 ) && 
                (strlen($picture_name) == strlen($compare_picture) ) ) {
                return "<img class='map_picture' src=" . $picture_path. '/' . $pic[0] . "></img>";
            }
        }
        //still searching? so filter less restrictive ...
        //search if there is a file which name is completly a part of the picture_name
        foreach ($picture_list as $pic) {
            $_pic = explode('.', $pic[0]);
            $compare_picture = strtolower($_pic[0]);

            if (strpos($picture_name, $compare_picture) !== false ) {
                return "<img class='map_picture' src=" . $picture_path. '/' . $pic[0] . "></img>";
            }
        }
    }
}
?>
