<?

class Conf {

    //Database Connection Settings
    public $db_host = "127.0.0.1";
    public $db_name = "quakealyzer";
    public $db_user = "root";
    public $db_password = "";

    public $decode_player_name_colors = True;

    //show this sections on sidebar
    public $show_sidebar_maps = True;
    public $show_sidebar_items = True;
    public $show_sidebar_downloads = True;

    //show this game information data
    public $show_game_duration = True;
    public $show_game_finish = True;
    public $show_game_mod = True;

    //loginsettings
    public $use_login = True;
    //Nickname oder global Username as Login Username?
    public $use_nick_as_username = True;
    public $global_username = 'quake';
    //Serverpassword, or individuell?
    /*priority:
        1) no_login_password (if False -> next)
        2) use_server_password (if False -> next)
        3) global_password (if empty -> deny access)
    */
    public $no_login_password = True;
    public $use_server_password = True; //(configure server_config_file !)
    public $global_password = 'changemypassword';


    //check if new packages are in dedicated server listed
    public $auto_discovery_new_packages = True;
    public $get_levelshots = True;
    public $get_model_images = True;
    public $get_item_icons = True;
    public $package_for_download = True;

    //refresh_interval: last check is minimum this seconds before
    public $sync_intervall = 60; //don't set below 30


    //rootpath of the dedicated server
    public $server_path = "/srv/quake3";
    public $server_config_file = "/srv/quake3/baseq3/q3ded.cfg";

    public $tmp_path = "/tmp";


    //relativ paths on the Webfrontend
    public $download_path = "downloads";
    public $map_picture_path = "images/maps";
    public $model_picture_path = "images/players";
    public $item_icon_picture_path = "images/icons";
}


class Path {

    //paths to this binaries
    public $unzip = "/usr/bin/unzip";
    public $zip = "/usr/bin/zip";
}


class Message {

    public $true = "Yes";
    public $false = "No";

    public $game_not_correct_finished = "(not finished)";

    //Error Messages
    public $error_cannot_connection = "Cannot connect to Host";
    public $error_db_not_found = "Database not found";
    public $error_no_known_site = "No known Page found";
    public $error_no_valid_value = "No valid value!";
    public $error_game_details_id_not_found = "Game ID not found";
    public $error_user_details_id_not_found = "User ID not found";


    //Login
    public $login_title = "Quakealyzer Login";
    public $login_username = "Quake3-Username";
    public $login_password = "Password";
    public $login_button = "Login";


    //Website title
    public $title_text = "Quakealyzer";

    //Sidebar title
    public $sidebar_title_games = "Games";
    public $sidebar_title_user = "User";
    public $sidebar_title_items = "Items";
    public $sidebar_title_maps = "Maps";
    public $sidebar_title_downloads = "Downloads";
    public $sidebar_title_logout = "Logout";

    //Game list
	public $games_list_title = "Games";
	public $games_list_game_id = "ID";
	public $games_list_game_start = "Started";
	public $games_list_game_finish = "Finished";
	public $games_list_game_duration = "Duration";
	public $games_list_game_type = "Game Type";
	public $games_list_game_map = "Map";
	public $games_list_game_end_cause = "End Reason";
	public $games_list_game_mod = "Mod";

    //User list
	public $users_list_title = "Users";
    public $users_list_player = "Player";
    public $users_list_bot = "Bots";
	public $users_list_user_id = "ID";
	public $users_list_user_username = "Username";

    //Map list
	public $maps_list_title = "Maps";
	public $maps_list_map_id = "ID";
	public $maps_list_map_name = "Mapname";

    //Item list
	public $items_list_title = "Items";
	public $items_list_item_id = "ID";
	public $items_list_item_item = "Item";
    public $items_list_items = "tooked Items";
    public $items_list_used_weapons = "used Weapons and Effects";

    //Download list
    public $downloads_list_title = "Downloads";
    public $downloads_list_maps = "Maps";
    public $downloads_list_filename = "Filename";
    public $downloads_list_filesize = "Size";

    //Game Details
    public $games_details_title = "Game Details";
    public $games_details_id = "Game ID";
    public $games_details_start = "Started";
    public $games_details_finish = "Finished";
    public $games_details_duration = "Duration";
    public $games_details_endcause = "Finished because";
    public $games_details_map_name = "Map";
    public $games_details_game_type = "Gametype";
    public $games_details_game_timelimit = "Timelimit";
    public $games_details_game_fraglimit = "Fraglimit";
    public $games_details_game_capturelimit = "Capturelimit";
    public $games_details_game_bot_skill = "Bot Skill";
    public $games_details_user_name = "User";
    public $games_details_user_score = "Score";
    public $games_details_user_bot = "Bot";
    public $games_details_user_team_leader = "Teamleader";
    public $games_details_team_color = "Team";
    public $games_details_team_score = "Score";

    //User Details
    public $users_details_title = "User Details";
    public $users_details_id = "ID";
    public $users_details_username = "Username";
    public $users_details_bot = "Bot";
    public $users_details_model = "Model";
    public $users_details_hmodel = "Head Model";
    public $users_details_played_games = "played Games";
    public $users_details_game_id = "ID";
    public $users_details_map_name = "Map";
    public $users_details_game_type = "Gametype";
    public $users_details_game_start = "Started";
    public $users_details_game_finish = "Finished";
    public $users_details_duration = "Duration";
    public $users_details_user_score = "Score";
    public $users_details_user_kills = "kills";
    public $users_details_user_killed = "killed";
    public $users_details_user_suicide = "suicide";
}


class Translator {

    private $conf = '';

    public function __construct() {
        $this->conf = new Conf();
    }

    public function game_type($type) {
        switch ($type) {
        case 0:             return "Deathmatch";
        case 1:             return "Tourney";
        case 2:             return "Deathmatch";
        case 3:             return "Team Deathmatch";
        case 4:             return "Capture the Flag";
        default:            return "unknown Gametype '" . $type . "'";
        }
    }

    public function game_limit($type) {
        //which limits are set for which game_type
        // timelimit, fraglimit, capturelimit ?
        switch ($type) {
        case 0:             return array('timelimit','fraglimit');
        case 1:             return array('timelimit','fraglimit');
        case 2:             return array('timelimit','fraglimit');
        case 3:             return array('timelimit','fraglimit');
        case 4:             return array('timelimit','capturelimit');
        default:            return array('timelimit','fraglimit');
        }
    }

    public function bot_skill($skill) {
        switch ($skill) {
        case 0:             return "i can win ($skill)";
        case 1:             return "Bring it on ($skill)";
        case 2:             return "Hurt me plenty ($skill)";
        case 3:             return "Hardcore ($skill)";
        case 4:             return "Nightmare ($skill)";
        default:            return "unknown Skill '" . $skill . "'";
        }
    }

    public function team($team_id) {
        switch ($team_id) {
        case 0:             return "none";
        case 1:             return "red";
        case 2:             return "blue";
        case 3:             return "spectator";
        default:            return "unknown Team '" . $team_id . "'";
        }
    }

    public function team_task($task) {
        switch ($task) {
        case 0:             return "none";
        case 1:             return "offence";
        case 2:             return "defence";
        case 3:             return "patrol";
        case 4:             return "follow";
        case 5:             return "retrieve";
        case 6:             return "escort";
        case 7:             return "camp";
        default:            return "unknown Task'" . $task . "'";
        }
    }

    public function item($item) {
        switch ($item) {
        # kills with weapon
        case 'MOD_BFG':             	return "BFG";
        case 'MOD_BFG_SPLASH':      	return "BFG Splash";
        case 'MOD_CRUSH':           	return "crushed";
        case 'MOD_FALLING':         	return "falling";
        case 'MOD_GAUNTLET':        	return "gauntlet";
        case 'MOD_GRENADE':         	return "Grenade";
        case 'MOD_GRENADE_SPLASH':  	return "Grenade Splash";
        case 'MOD_LAVA':            	return "Lava";
        case 'MOD_LIGHTNING':       	return "Lightninggun";
        case 'MOD_MACHINEGUN':      	return "Machinegun";
        case 'MOD_PLASMA':          	return "Plasmagun";
        case 'MOD_PLASMA_SPLASH':   	return "Plasma Splash";
        case 'MOD_RAILGUN':         	return "Railgun";
        case 'MOD_ROCKET':          	return "Rocketlauncher";
        case 'MOD_ROCKET_SPLASH':   	return "Rocket Splash";
        case 'MOD_SHOTGUN':         	return "Shotgun";
        case 'MOD_TELEFRAG':        	return "Telefrag";
        case 'MOD_TRIGGER_HURT':    	return "Trigger hurt";
        case 'MOD_WATER':           	return "Water";
        case 'MOD_SLIME':           	return "Slime";
        case 'MOD_SUICIDE':         	return "suicide";
        # holdable items
        case 'holdable_medkit':     	return "holdable Medikit";
        case 'holdable_teleporter': 	return "holdable Teleporter";
        # ammo
        case 'ammo_bfg':            	return "Ammo BFG";
        case 'ammo_bullets':        	return "Ammo Machinegun";
        case 'ammo_cells':          	return "Ammo Plasmagun";
        case 'ammo_grenades':       	return "Ammo Grenadelauncher";
        case 'ammo_lightning':      	return "Ammo Lightning Gun";
        case 'ammo_rockets':        	return "Ammo Rocketlauncher";
        case 'ammo_shells':         	return "Ammo Shotgun";
        case 'ammo_slugs':          	return "Ammo Railgun";
        # team items
        case 'team_CTF_redflag':    	return "Red Flag";
        case 'team_CTF_blueflag':   	return "Blue Flag";
        # items
        case 'item_armor_body':     	return "Bodyarmor";
        case 'item_armor_combat':   	return "Combatarmor";
        case 'item_armor_shard':   	    return "Armor shard";
        case 'item_enviro':         	return "Environment Suite";
        case 'item_flight':         	return "Flight";
        case 'item_haste':          	return "Haste";
        case 'item_health':         	return "Health";
        case 'item_health_large':   	return "Health large";
        case 'item_health_mega':    	return "Health mega";
        case 'item_health_small':   	return "Health small";
        case 'item_invis':          	return "Invisibility";
        case 'item_quad':           	return "Quad Damage";
        case 'item_regen':          	return "Regeneration";
        # weapons
        case 'weapon_bfg':              return "BFG Gun";
        case 'weapon_grenadelauncher':  return "Grenadelauncher";
        case 'weapon_lightning':        return "Lightning Gun";
        case 'weapon_plasmagun':        return "Plasmagun";
        case 'weapon_railgun':          return "Railgun";
        case 'weapon_rocketlauncher':   return "Rocketlauncher";
        case 'weapon_shotgun':          return "Shotgun";

        default:                        return "unknown Item '" . $item . "'";
        }
    }

    public function item_icon($item) {
        switch ($item) {

        # holdable items
        case 'holdable_medkit':     	return "medkit.gif";
        case 'holdable_teleporter': 	return "teleporter.gif";
        # ammo
        case 'ammo_bfg':            	return "icona_bfg.gif";
        case 'ammo_bullets':        	return "icona_machinegun.gif";
        case 'ammo_cells':          	return "icona_plasma.gif";
        case 'ammo_grenades':       	return "icona_grenade.gif";
        case 'ammo_lightning':      	return "icona_lightning.gif";
        case 'ammo_rockets':        	return "icona_rocket.gif";
        case 'ammo_shells':         	return "icona_shotgun.gif";
        case 'ammo_slugs':          	return "icona_railgun.gif";
        # team items
        case 'team_CTF_redflag':    	return "iconf_blu.gif";
        case 'team_CTF_blueflag':   	return "iconf_red.gif";
        # items
        case 'item_armor_body':     	return "iconr_yellow.gif";
        case 'item_armor_combat':   	return "iconr_red.gif";
        case 'item_armor_shard':   	    return "iconr_shard.gif";
        case 'item_enviro':         	return "envirosuit.gif";
        case 'item_flight':         	return "flight.gif";
        case 'item_haste':          	return "haste.gif";
        case 'item_health':         	return "iconh_yellow.gif";
        case 'item_health_large':   	return "iconh_red.gif";
        case 'item_health_mega':    	return "iconh_mega.gif";
        case 'item_health_small':   	return "iconh_green.gif";
        case 'item_invis':          	return "invis.gif";
        case 'item_quad':           	return "quad.gif";
        case 'item_regen':          	return "regen.gif";
        # weapons
        case 'weapon_bfg':              return "iconw_bfg.gif";
        case 'weapon_grenadelauncher':  return "iconw_grenade.gif";
        case 'weapon_lightning':        return "iconw_lightning.gif";
        case 'weapon_plasmagun':        return "iconw_plasma.gif";
        case 'weapon_railgun':          return "iconw_railgun.gif";
        case 'weapon_rocketlauncher':   return "iconw_rocket.gif";
        case 'weapon_shotgun':          return "iconw_shotgun.gif";
/*
        case '':                        return "iconw_gauntlet.gif";
        case '':                        return "iconw_grapple.gif";
        case '':                        return "iconw_machinegun.gif";
        case '':                        return "noammo.gif";
*/

        default:                        return "";
        }
    }

    public function username($username) {
        /***********************************
        ruleset for renaming a Username
        remove color-signings or something like this
        ***********************************/

        switch ($username) {
        default:        $new_name = $username;
        }

        /*********
        0 black
        1 red
        2 green
        3 yellow
        4 blue
        5 cyan
        6 magenta
        7 white
        *********/
        if ($this->conf->decode_player_name_colors) {
            $new_name = "<font>" . $new_name;
            $new_name = preg_replace('(\^0)', "<font color='black'>", $new_name);
            $new_name = preg_replace('(\^1)', "<font color='red'>", $new_name);
            $new_name = preg_replace('(\^2)', "<font color='green'>", $new_name);
            $new_name = preg_replace('(\^3)', "<font color='yellow'>", $new_name);
            $new_name = preg_replace('(\^4)', "<font color='blue'>", $new_name);
            $new_name = preg_replace('(\^5)', "<font color='cyan'>", $new_name);
            $new_name = preg_replace('(\^6)', "<font color='magenta'>", $new_name);
            $new_name = preg_replace('(\^7)', "<font color='white'>", $new_name);
            $new_name .= "</font>";
        }

        return $new_name;
    }

    public function cleanup_username($username) {
        //# remove Colorsettings in username:
        //# ^1A^2n^3a^4r^5k^6i  ->  Anarki
        return preg_replace('(\^\d)', "", $username);
    }
}

?>
