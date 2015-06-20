<?

class Database {

    private $db = '';
    private $msg = '';
    private $conf = '';
    private $translate = '';


    public function __construct($db_host, $db_name, $db_user, $db_password) {

        $this->msg = new Message();
        $this->conf = new Conf();
        $this->translate = new Translator();

        $db_connection = mysql_connect ($db_host, $db_user, $db_password) or die ($this->msg->error_cannot_connection);
        mysql_select_db($db_name) or die ($this->msg->error_db_not_found);
    }


    private function get_list($table, $order_column='', $order_direction='ASC', $filter_column='', $filter_value='', $filter_column2='', $filter_value2='') {

        $res = array();
        $order_rule = '';
        $filter= '';
        $filter2= '';

        if ($order_column) {
            $order_rule = "order by " . $order_column . ' ' . $order_direction;
        }

        if ($filter_column) {
            $filter = "where $filter_column='$filter_value'";
        }
        if ($filter_column2) {
            $filter2 = "and $filter_column2='$filter_value2'";
        }

        $list = mysql_query("select * from $table $filter $filter2 $order_rule");
        while ($entry = mysql_fetch_assoc($list)) {
            $res[] = $entry;
        }
        return $res;
    }


    public function get_team($team_id) {
        $team_data = $this->get_list('team', '', '', 'id', $team_id);
        return $team_data[0]['name'];
    }

    public function get_game_list($game_id='') {
        if ($game_id) {
            return $this->get_list('game', '', '', 'id', $game_id);
        }
        return $this->get_list('game', 'starttime', 'DESC');
    }

    public function get_game_parameter($game_id='', $game_key='') {
        $parameter = $this->get_list('game_parameter', '', '', 'game_id', $game_id, 'game_key', $game_key);
        if ($game_key) {
            return $parameter[0]['game_value'];
        }
        return $parameter[0];
    }

    public function get_user_list($filter_column='', $filter_value='') {
        if ($filter_column) {
            return $this->get_list('user', 'username', '', $filter_column, $filter_value);
        }
        return $this->get_list('user', 'username', '', '');
    }

    public function get_game_type_list() {
        return $this->get_list('game_type', 'type');
    }

    public function get_map_list($id='') {
        if ($id) {
            return $this->get_list('game_map', '', '', 'id', $id);
        }
        return $this->get_list('game_map', 'name');
    }

    public function get_map_list_by_game_type($game_type_id) {
        $res = array();
        $list = mysql_query("select * from game where game_type_id='$game_type_id' group by(game_map_id)");
        while ($entry = mysql_fetch_assoc($list)) {
            $res[] = $entry;
        }
        return $res;
    }

    public function get_item_list($include_weapon='0') {
        return $this->get_list('item', 'item', 'ASC', 'weapon', $include_weapon);
    }

    public function get_kill_weapon_list() {
        return $this->get_item_list('1');
    }

    public function get_end_cause_list() {
        return $this->get_list('end_cause');
    }

    public function get_mod_list() {
        return $this->get_list('game_mod');
    }

    public function get_model_list() {
        return $this->get_list('model');
    }

    public function get_hmodel_list() {
        return $this->get_list('hmodel');
    }

    public function get_team_score_list($game_id='') {
        if ($game_id) {
            return $this->get_list('team_stats', 'score', 'DESC', 'game_id', $game_id);
        }
        return $this->get_list('team_stats');
    }



    public function get_config($parameter) {
        $table = 'config';
        $list = mysql_query("select value from $table where parameter='$parameter'");
        while ($entry = mysql_fetch_assoc($list)) {
            return $entry['value'];
        }
    }

    public function set_config($parameter, $value) {
        $table = 'config';
        $this->remove_config($parameter);
        mysql_query("insert into $table (parameter, value) VALUES ('$parameter', '$value')");
    }

    public function remove_config($parameter) {
        $table = 'config';
        mysql_query("delete from $table where parameter='$parameter'");
    }



    public function get_highest_id($table) {
        $list = mysql_query("select id from $table order by id desc limit 1");
        while ($entry = mysql_fetch_assoc($list)) {
            return $entry['id'];
        }
    }


    public function get_packages() {
        $res = array();
        $table = 'pk3_files';
        $list = mysql_query("select filename from $table");
        while ($entry = mysql_fetch_assoc($list)) {
            $res[] = $entry['filename'];
        }
        return $res;
    }

    public function add_package($filename) {
        $res = array();
        $table = 'pk3_files';
        mysql_query("insert into $table (filename) VALUES ('$filename')");
    }

    public function remove_package($filename) {
        $res = array();
        $table = 'pk3_files';
        mysql_query("delete from $table where filename=$filename");
    }




    public function get_kills($game_id, $user_id) {
        $res = array();
        $table = 'game_kill';
        $list = mysql_query("select * from $table where game_id=$game_id and killer_id=$user_id and killee_id!=$user_id");
        while ($entry = mysql_fetch_assoc($list)) {
            $res[] = $entry;
        }
        return $res;
    }

    public function get_killed($game_id, $user_id) {
        $res = array();
        $table = 'game_kill';
        $list = mysql_query("select * from $table where game_id=$game_id and killee_id=$user_id and killer_id!=$user_id");
        while ($entry = mysql_fetch_assoc($list)) {
            $res[] = $entry;
        }
        return $res;
    }

    public function get_suicide($game_id, $user_id) {
        $res = array();
        $table = 'game_kill';
        $list = mysql_query("select * from $table where game_id=$game_id and killee_id=$user_id and killer_id=$user_id");
        while ($entry = mysql_fetch_assoc($list)) {
            $res[] = $entry;
        }
        return $res;
    }

    public function get_score_list($column='', $id='', $order_column='count') {
        if ($column) {
            return $this->get_list('score', $order_column, 'DESC', $column, $id);
        }
        return $this->get_list('score');
    }

    public function check_username($username) {
        if ($this->conf->use_nick_as_username) {
            //Nickname is username
            $users = array();
            $_user_list = $this->get_user_list('bot', '0');
            foreach ($_user_list as $_user) {
                $user = $this->translate->cleanup_username($_user['username']);
                if ( ! $user) {
                    continue;
                }
                $users[] = $user;
            }

            if ( ! in_array($username, $users)) {
                return False;
            }
        }
        elseif ($this->conf->global_username) {
            //one central username for everyone
            if ($this->translate->cleanup_username($username) != $this->conf->global_username) {
                return False;
            }
        }
        return True;
    }

    public function check_password($password) {
        if ($this->conf->no_login_password) {
            //do nothing, just return true
        }
        elseif ($this->conf->use_server_password) {
            include ('lib.php');
            //password equal with Quake Server login password
            if ($password != get_server_password()) {
                return False;
            }
        }
        elseif ($this->conf->global_password) {
            //local set global password
            if ($password != $this->conf->global_password) {
                return False;
            }
        }
        else {
            //no password handler found, deny access
            return False;
        }
        return True;
    }

}

?>
