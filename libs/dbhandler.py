
from sqlalchemy import create_engine, exc
from sqlalchemy.orm import sessionmaker

from database import DBUpdate, DBBase, Game, User, GameKill, Item, GameMod, TeamStats, \
        GameMap, GameType, EndCause, Score, Model, HModel, Say, Tell, SayTeam, \
        Pk3Files, GameParameter, Team, Config


class DBHandler(object):

    engine = None
    game_id = None
    user_dict = {}
    team_dict = {}
    team_leader_dict = {}
    user_team_dict = {}
    item_dict = {}
    game_param_dict = [
        'timelimit',
        'fraglimit',
        'capturelimit',
        'bot_minplayer'
    ]

    const_blue = 2
    const_red = 1

    def __init__(self):
        pass

    def renew_db_connection(self):
        if self.engine:
            # maybe a better soluton exists for this?
            try:
                self.create_session()
                if self.game_id:
                    self.game_now = self.session.query(Game).filter(Game.id==self.game_id).first()
                    self.game_id = self.game_now.id
                return 0
            except exc.SQLAlchemyError as ex:
                log.error("SQLAlchemyError: %s" % ex)
                return 1
        else:
            return 0


    def get_config(self, key):
        config = self.session.query(Config).filter(key=key).first()
        if not config:
            return ""
        return config.value


    def set_config(self, key, value):
        config = Config(parameter=key,
                    value=value
        )
        self.session.add(config)


    def get_db_version(self):
        db_version = self.get_config('database_version')

        if not db_version:
            db_version = 1

        return db_version


    def set_db_version(version):
        set_config('database_version', version)


    def db_update(self):
        db = DBUpdate()
        version = self.get_db_version

        while version < db.max_version:
            new_version = version + 1

            if hasattr(db, version + '_to_' + new_version):
                getattr(db, version + '_to_' + new_version)
            else:
                log.error("missing update Rule from Databaseversion %d to Version %d" % (version, new_version))
                return

            version = self.get_db_version


    def get_team_id(self, teamname):
        #if key not in dictionary,
        #try to load from database,
        #otherwise write in database,
        #now load into dictionary for future use in game
        if teamname not in self.team_dict:
            if not self.session.query(Team).filter(Team.name==teamname).count():
                new_team = Team(name=teamname)
                self.session.add(new_team)
                self.session.commit()
            team = self.session.query(Team).filter(Team.name==teamname).first()
            self.team_dict[teamname] = team.id

        return self.team_dict[teamname]


    def get_user_id(self, username):
        #if key not in dictionary,
        #try to load from database,
        #otherwise write in database,
        #now load into dictionary for future use in game
        if username not in self.user_dict:
            if not self.session.query(User).filter(User.username==username).count():
                new_user = User(username=username)
                self.session.add(new_user)
                self.session.commit()
            user = self.session.query(User).filter(User.username==username).first()
            self.user_dict[username] = user.id

        return self.user_dict[username]


    def get_item_id(self, item, weapon=False):
        #if key not in dictionary,
        #try to load from database,
        #otherwise write in database,
        #now load into dictionary for future use in game
        if item not in self.item_dict:
            if not self.session.query(Item).filter(Item.item==item).count():
                new_item = Item(item=item, weapon=weapon)
                self.session.add(new_item)
                self.session.commit()
            dict_item = self.session.query(Item).filter(Item.item==item).first()
            self.item_dict[item] = [dict_item.id, dict_item.weapon]

        # if killed once with this item an enemy, mark as weapon
        if weapon and self.item_dict[item][1] != weapon:
            item_type_change = self.session.query(Item).filter(Item.item==item).first()
            item_type_change.weapon = weapon
            del self.item_dict[item]
            self.item_dict[item] = [dict_item.id, dict_item.weapon]

        return self.item_dict[item][0]

    def set_team_leader(self, teamid, username):
        self.team_leader_dict[teamid] = username

    def get_team_leader(self, teamid):
        if teamid in self.team_leader_dict:
            return self.team_leader_dict[teamid]
        return ''

    def set_user_teamid(self, username, teamid):
        self.user_team_dict[username] = teamid


    def get_user_teamid(self, username):
        if username not in self.user_team_dict:
            return ''
        return self.user_team_dict[username]


    def connect(self, db, debug=False):
        try:
            self.engine = create_engine(db, echo=debug)
            return True
        except ImportError as ex:
            log.error(ex.message + "\n -> maybe missed pip installation of database game_modul like 'pymysql'?\n maybe need the Package like 'python-mysqldb' from your Distribution also?")
            self.db_help()
            return False


    def db_create_models(self):
        DBBase.metadata.create_all(self.engine)

    def set_engine(self, engine):
        self.engine = engine

    def get_engine(self):
        return self.engine

    def create_session(self):
        session = sessionmaker()
        session.configure(bind=self.engine)
        self.session = session()
        self.db_update()

    def commit_session(self):
        self.session.commit()

    def close_session(self):
        try:
            self.session.close()
        except:
            pass


    def db_help(self):
        print ('sqlite:////absolute/path/to/sqlite.db')
        print ('mysql://username:password@hostname/mydatabase')
        print ('postgresql://username:password@localhost/mydatabase')
        print ('mssql+pymssql://username:password@hostname:port/dbname')
        print ('oracle://username:password@hostname:1521/sidname')


    def init_game(self, data):
        #{'protocol': '68', 'sv_privateClients': '0', 'sv_maxRate': '0', 'bot_minplayers': '4', 'g_maxGameClients': '0', 'version': 'Q3 1.32c linux-i386 May  8 2006', 'game_mod_name': 'baseq3', 'sv_minPing': '0', '.Admin': 'Mr.T', 'timestamp': '1402165340', 'g_needpass': '1', 'sv_punkbuster': '0', 'sv_allowDownload': '1', 'sv_maxclients': '8', 'sv_hostname': 'Wotans Daddelbude', 'fraglimit': '20', 'capturelimit': '6', 'dmflags': '0', 'game_type': '3', 'timelimit': '10', 'time': '2059:48', 'map_name': 'japandm', 'sv_maxPing': '0', 'runtime': 0, 'sv_floodProtect': '1'}

        if not self.session.query(GameMod).filter(GameMod.name==data['mod_name']).count():
            new_game_mod = GameMod(name=data['mod_name'])
            self.session.add(new_game_mod)
            self.session.commit()

        if not self.session.query(GameMap).filter(GameMap.name==data['map_name']).count():
            new_map = GameMap(name=data['map_name'])
            self.session.add(new_map)
            self.session.commit()

        if not self.session.query(GameType).filter(GameType.type==data['game_type']).count():
            new_game_type = GameType(type=data['game_type'])
            self.session.add(new_game_type)
            self.session.commit()

        game_mod = self.session.query(GameMod).filter(GameMod.name==data['mod_name']).first()
        game_map = self.session.query(GameMap).filter(GameMap.name==data['map_name']).first()
        game_type = self.session.query(GameType).filter(GameType.type==data['game_type']).first()

        game = Game(starttime=data['timestamp'],
                            game_mod_id=game_mod.id,
                            game_map_id=game_map.id,
                            game_type_id=game_type.id,
        )
        self.session.add(game)
        self.session.commit()

        #remember for actual game
        self.game_now = self.session.query(Game).filter(Game.starttime==data['timestamp']).first()
        self.game_id = self.game_now.id

        # save additional game parameters
        for key in data.iterkeys():
            if key in self.game_param_dict:
                game_param = GameParameter(game_id=self.game_now.id,
                                        game_key=key,
                                        game_value=data[key],
                )
                self.session.add(game_param)


    def exit(self, data):
        # {'endcause': 'Fraglimit', 'timestamp': 1402184024, 'runtime': 520, 'time': '2055:50'}
        if not self.session.query(EndCause).filter(EndCause.reason==data['endcause']).count():
            new_end_reason = EndCause(reason=data['endcause'])
            self.session.add(new_end_reason)
            self.session.commit()

        reason = self.session.query(EndCause).filter(EndCause.reason==data['endcause']).first()
        self.game_now.endtime = data['timestamp']
        self.game_now.end_cause_id = reason.id


    def shutdown_game(self, data):
        self.close_session()


    def score(self, data):
        # {'username': 'Xaero', 'timestamp': 1402403195, 'ping': '0', 'score': '3', 'time': '2055:50', 'runtime': 520, 'id': '3'}
        team_id = self.get_user_teamid(data['username'])

        if self.get_team_leader(team_id) == data['username']:
            team_leader=True
        else:
            team_leader=False

        user_id = self.get_user_id(data['username'])
        user_score = Score(user_id=user_id,
                            game_id=self.game_now.id,
                            count=data['score'],
                            team_id=team_id,
                            team_leader=team_leader,
        )
        self.session.add(user_score)


    def red(self, data):
        #{'blue_score': '19', 'red_score': '20', 'runtime': 297, 'timestamp': 1402180740, 'time': '1089:59'}

        blue_team_id = self.get_team_id(self.const_blue)
        red_team_id = self.get_team_id(self.const_red)

        team_blue = TeamStats(team_id=blue_team_id,
                            score=data['blue_score'],
                            game_id=self.game_now.id,
        )
        team_red = TeamStats(team_id=red_team_id,
                            score=data['red_score'],
                            game_id=self.game_now.id,
        )
        self.session.add(team_blue)
        self.session.add(team_red)


    def blue(self, data):
        self.red(data)

    def client_connect(self, data):
        pass

    def client_userinfo_changed(self, data):
        #{'hmodel': 'crash', 'skill': '    3.00', 'timestamp': 1402689747, 'team_task': '0', 'looses': '0', 'health_count': '90', 'n': 'Crash', 'team_leader': '0', 'wins': '0', 'time': '14856:4', 'color2': '5', 'model': 'crash', 'runtime': -36, 'color1': '4', 'id': '0', 'team': '2'}

        if not self.session.query(Model).filter(Model.name==data['model']).count():
            new_model = Model(name=data['model'])
            self.session.add(new_model)
            self.session.commit()
        if not self.session.query(HModel).filter(HModel.name==data['hmodel']).count():
            new_model = HModel(name=data['hmodel'])
            self.session.add(new_model)
            self.session.commit()
        model = self.session.query(Model).filter(Model.name==data['model']).first()
        hmodel = self.session.query(HModel).filter(HModel.name==data['hmodel']).first()

        user_id = self.get_user_id(data['n'])
        user = self.session.query(User).filter(User.id==user_id).first()

        if 'skill' in data:
            user.bot = True
            skill_value, sub_value = data['skill'].strip(' \t').split('.')
            self.game_now.bot_skill = int(skill_value)
        else:
            user.bot = False

        user.model_id = model.id
        user.hmodel_id = hmodel.id
        team_id = self.get_team_id(data['team'])
        self.set_user_teamid(data['n'], team_id)

        if data['team_leader']:
            self.set_team_leader(team_id, data['n'])


    def client_begin(self, data):
        pass

    def client_disconnect(self, data):
        pass

    def item(self, data):
        # {'item': 'item_armor_shard', 'runtime': 13, 'id': '0', 'timestamp': 1403357083, 'time': '0:13'}
        item_id = self.get_item_id(data['item'])


    def kill(self, data):
        #{'timestamp': 1402174290, 'weapon': 'MOD_PLASMA', 'killer': 'Visor', 'id_2': '8', 'id_1': '1', 'time': '4965:24', 'runtime': 406, 'killee': 'Major', 'id': '4'}

        killer_id = self.get_user_id(data['killer'])
        killee_id = self.get_user_id(data['killee'])
        weapon_id = self.get_item_id(data['weapon'], weapon=True)

        kill_action = GameKill(time=data['runtime'],
                            killer_id=killer_id,
                            killee_id=killee_id,
                            weapon_id=weapon_id,
                            game_id=self.game_now.id
        )
        self.session.add(kill_action)


    def say(self, data):
        #{'timestamp': 1403358308, 'message': 'team blue', 'runtime': 7061, 'user': 'Eye', 'time': '1085:01'}
        talker_id = self.get_user_id(data['user'])

        say_action = Say(time=data['runtime'],
                            talker_id=talker_id,
                            game_id=self.game_now.id,
                            message=data['message']
        )
        self.session.add(say_action)


    def tell(self, data):
        #{'timestamp': 1403351164, 'listener': 'Uriel', 'talker': 'Razor', 'time': '969:59', 'message': "it's agreed. I'll follow you around uriel.", 'runtime': 159}
        talker_id = self.get_user_id(data['talker'])
        listener_id = self.get_user_id(data['listener'])

        tell_action = Tell(time=data['runtime'],
                            talker_id=talker_id,
                            game_id=self.game_now.id,
                            listener_id=listener_id,
                            message=data['message']
        )
        self.session.add(tell_action)

    def sayteam(self, data):
        #{'timestamp': 1403351037, 'message': 'Who is the team leader', 'runtime': 35, 'user': 'Keel', 'time': '963:35'}

        sayteam_action = SayTeam(time=data['runtime'],
                            talker_id=self.get_user_id(data['user']),
                            game_id=self.game_now.id,
                            team_id=self.get_user_teamid(data['user']),
                            message=data['message']
        )
        self.session.add(sayteam_action)

    def empty(self, data):
        pass


