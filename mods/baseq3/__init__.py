


class Baseq3(object):

    mod_name = "baseq3"

    def __init__(self):
        pass

    @property
    def get_mod_name(self):
        return self.mod_name

    def key_value(self, arguments):
        if arguments.startswith("\\"):
            arguments = arguments[1:]
        argument_list = arguments.split('\\')

        args = {}
        run_me = True
        while run_me:
            v = argument_list.pop()
            k = argument_list.pop()
            args[k] = v
            if not len(argument_list):
                run_me = False
        return args

    @property
    def server_start(self):
        return "  0:00 ------------------------------------------------------------\n"

    @property
    def translate(self):
        dict = {
            'gamename': 'mod_name',
            'g_gametype': 'game_type',
            'timelimit': 'timelimit',
            'fraglimit': 'fraglimit',
            'capturelimit': 'capturelimit',
            'mapname': 'map_name',
            'bot_minplayer': 'bot_min_players',
            'tt': 'team_task',
            'tl': 'team_leader',
            't': 'team',
            'c1': 'color1',
            'c2': 'color2',
            'hc': 'health_count',
            'w': 'wins',
            'l': 'looses',
        }
        return dict

    @property
    def log_structure(self):
        """
        list of dictionaries
        with the following structure:
        {'call':'<Action_to_run_1>','search':'<Action_to_search>','parser':'{<matching-parse-syntax1>}'}
        {'call':'<Action_to_run_2>','search':'<Action_to_search>','parser':'{<matching-parse-syntax2>}'}
        """
        log_entries = [

        #  0:00 InitGame: \sv_punkbuster\0\sv_maxPing\0\sv_minPing\0\dmflags\0\fraglimit\20\timelimit\10\sv_hostname\Wotans Daddelbude\sv_maxclients\8\sv_maxRate\0\sv_floodProtect\1\g_maxGameClients\0\capturelimit\8\version\Q3 1.32c linux-i386 May  8 2006\g_gametype\3\protocol\68\mapname\q3dm1\sv_privateClients\0\sv_allowDownload\1\.Admin\Mr.T\g_needpass\1\gamename\baseq3
        # 755:50 InitGame: \sv_punkbuster\0\g_needpass\1\bot_minplayers\4\sv_allowDownload\1\g_gametype\3\capturelimit\6     \g_maxGameClients\0\sv_floodProtect\1\sv_maxRate\0\sv_maxclients\8\sv_hostname\Wotans Daddelbude\timelimit\10\fraglimit\2     0\dmflags\0\sv_minPing\0\sv_maxPing\0\version\Q3 1.32c linux-i386 May  8 2006\protocol\68\mapname\q3dm18\sv_privateClient     s\0\.Admin\Mr.T\gamename\baseq3
        {'call':'init_game','search':'InitGame:','parser':'{time}InitGame: {arguments}','custom':'dict(arguments=self.mod.key_value)'},
        {'call':'init_game','search':'InitGame:','parser':'{time}InitGame: {arguments}','custom':True},
        # 11163:1Exit: Timelimit hit.
        # 587:44 Exit: Capturelimit hit.
        # 5:47 Exit: Fraglimit hit.
        {'call':'exit','search':'Exit:','parser':'{time}Exit: {endcause} hit.'},
        # 5:53 ShutdownGame:
        {'call':'shutdown_game','search':'ShutdownGame:','parser':'{time}ShutdownGame:'},


        # 5:47 score: 20  ping: 0  client: 2 Xaero
        # 5:47 score: 17  ping: 0  client: 3 Wrack
        # 5:47 score: 15  ping: 0  client: 1 Sarge
        # 5:47 score: 10  ping: 0  client: 4 Mr.T
        {'call':'score','search':'score:','parser':'{time}score: {score}  ping: {ping}  client: {id} {username}'},
        # 9:31 red:1  blue:20
        {'call':'red','search':'red:','parser':'{time}red:{red_score}  blue:{blue_score}'},
        {'call':'blue','search':'blue:','parser':'{time}blue:{blue_score}  red:{red_score}'},


        # 0:10 ClientConnect: 0
        {'call':'client_connect','search':'ClientConnect:','parser':'{time}ClientConnect: {id}'},
        # 712:26 ClientUserinfoChanged: 6 n\Mr.T\t\2\model\bones/bones\hmodel\bones/bones\g_redteam\\g_blueteam\\c1\4\c2     \5\hc\100\w\0\l\0\tt\0\tl\1
        # 0:10 ClientUserinfoChanged: 0 n\Hossman\t\2\model\biker/hossman\hmodel\biker/hossman\c1\4\c2\5\hc\70\w\0\l\0\skill\    2.00\tt\0\tl\0
        {'call':'client_userinfo_changed','search':'ClientUserinfoChanged:','parser':'{time}ClientUserinfoChanged: {id} {arguments}','custom':True},
        # 0:10 ClientBegin: 0
        {'call':'client_begin','search':'ClientBegin:','parser':'{time}ClientBegin: {id}'},
        # 3:40 ClientDisconnect: 0
        {'call':'client_disconnect','search':'ClientDisconnect:','parser':'{time}ClientDisconnect: {id}'},
        # 0:11 Item: 1 item_armor_shard
        {'call':'item','search':'Item:','parser':'{time}Item: {id} {item}'},

        # 1867:48Kill: 1022 5 22: <world> killed Major by MOD_TRIGGER_HURT
        # 9:06 Kill: 1 2 1: Orbb killed Lucy by MOD_SHOTGUN
        {'call':'Kill','search':'Kill:','parser':'{time}Kill: {id} {id_1} {id_2}: {killer} killed {killee} by {weapon}'},


        # 593:05 say: Mr.T: kommt vor :D
        {'call':'say','search':'say:','parser':'{time}say: {user}: {message}'},
        # 0:47 tell: Bitterman to Hossman: Precisely right. I'll follow you around Hossman.
        {'call':'tell','search':'tell:','parser':'{time}tell: {talker} to {listener}: {message}'},
        # 0:27 sayteam: Biker: who's the team leader
        {'call':'sayteam','search':'sayteam:','parser':'{time}sayteam: {user}: {message}'},


        # 0:00 ------------------------------------------------------------
        {'call':'empty','search':'------------------------------------------------------------','parser':'{time}------------------------------------------------------------'},
        ]
        return log_entries


def init():
    return Baseq3

