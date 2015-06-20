
import time
import importlib
import re
from parse import parse
from dbhandler import DBHandler


class Parser(object):

    game_start_time = 0
    game_start_offset_time = "0:00"
    game_init_found = False

    def __init__(self):
        self.db = DBHandler()

    @property
    def get_server_start(self):
        return self.mod.server_start

    def renew_db_connection(self):
        return self.db.renew_db_connection()

    def init_game(self, data):
        timestamp_sec, subsec = str(time.time()).split('.')
        self.game_start_time = timestamp_sec

        self.game_start_offset_time = data['time']
        # enable writing analyzis
        self.game_init_found = True

        # create Session for collecting data and close maybe other Sessions
        self.db.close_session()
        self.db.create_session()

        # reset runtime to zero,
        # otherwise init_game will have runtime of game before
        # and only first action in this round will have actuall runtime
        data['runtime'] = 0
        # overwrite the timestamp with the round starting timestamp
        data['timestamp'] = timestamp_sec

        self.db.init_game(data)


    def exit(self, data):
        self.db.exit(data)


    def shutdown_game(self, data):
        self.db.shutdown_game(data)

        # disable writing analyzis
        self.game_init_found = False
        #commit now write data to Database
        self.db.commit_session()
        #and close the session
        self.db.close_session()


    def red(self, data):
        self.db.red(data)

    def blue(self, data):
        self.db.blue(data)

    def score(self, data):
        self.db.score(data)

    def client_connect(self, data):
        self.db.client_connect(data)

    def client_userinfo_changed(self, data):
        self.db.client_userinfo_changed(data)

    def client_begin(self, data):
        self.db.client_begin(data)

    def client_disconnect(self, data):
        self.db.client_disconnect(data)

    def item(self, data):
        self.db.item(data)

    def kill(self, data):
        self.db.kill(data)

    def say(self, data):
        self.db.say(data)

    def tell(self, data):
        self.db.tell(data)

    def sayteam(self, data):
        self.db.sayteam(data)

    def empty(self, data):
        self.db.empty(data)

    def set_engine(self, engine):
        self.engine = engine
        self.db.set_engine(engine)


    def load_mod(self, mod):
        module = importlib.import_module('mods.' + mod)

        if not hasattr(module, 'init'):
            log.error("cannot find 'init' in possible mod")
            return False

        try:
            _pre_init = module.init()
            # loading module for handling
            self.mod =_pre_init()
            return True
        except:
            log.error("loading mod failed")
            return False


    @property
    def get_log_structure(self):
        return self.mod.log_structure


    @property
    def translater(self):
        return self.mod.translate


    def build_key_value(self, data):
        return self.mod.key_value(data)


    def call_action_db_handler(self, action, data):
        """
        forwards the data-dictionary to the method
        which is mapped to the action Paramater
        """

        if action == 'init_game':
            self.init_game(data)
        elif not self.game_init_found:
            # don't start logging of games in the middle
            # otherwise map, game_type and other things will be unknown
            log.debug("waiting for new game to start analyzing: " + action + " -> " + str(data))
        elif action == 'exit':
            self.exit(data)
        elif action == 'shutdown_game':
            self.shutdown_game(data)
        elif action == 'score':
            self.score(data)
        elif action == 'red':
            self.red(data)
        elif action == 'blue':
            self.blue(data)
        elif action == 'client_connect':
            self.client_connect(data)
        elif action == 'client_userinfo_changed':
            self.client_userinfo_changed(data)
        elif action == 'client_begin':
            self.client_begin(data)
        elif action == 'client_disconnect':
            self.client_disconnect(data)
        elif action == 'item':
            self.item(data)
        elif action == 'Kill':
            self.kill(data)
        elif action == 'say':
            self.say(data)
        elif action == 'tell':
            self.tell(data)
        elif action == 'sayteam':
            self.sayteam(data)
        elif action == 'empty':
            self.empty(data)
        else:
            log.debug("unknown Action: '" + action + "'")

        # needed because of foreign keys and searches ...
        if self.game_init_found:
            self.db.commit_session()


    def calc_real_timestamp(self, happening_offset_time):
        start_minute, start_second = str(self.game_start_offset_time).split(':')
        if len(str(start_second)) == 1:
            start_second = int(start_second) * 10
        game_start_offset = int(start_minute) * 60 + int(start_second)

        happening_minute, happening_second = str(happening_offset_time).split(':')
        if len(str(start_second)) == 1:
            happening_second = int(happening_second) * 10
        game_happening_offset = int(happening_minute) * 60 + int(happening_second)

        game_runtime = game_happening_offset - game_start_offset
        real_timestamp = int(self.game_start_time) + game_runtime

        return real_timestamp, game_runtime


    def analyze(self, line):
        """
        analyzes the log line with the
        log_entries dictionary-list from the loaded mod
        and redirect the parsed values to
        the methods which handle them in the correct way
        into the database
        """

        for structure in self.get_log_structure:
            if line.endswith(self.mod.server_start):
                # don't handle serverstart lines, 
                # especially if it contains in partially written loglines
                break
            elif re.search(structure['search'], line):
                try:
                    data = parse(structure['parser'], line).named
                except:
                    log.error("PARSING ERROR")
                    log.debug("Parser Rule: " + structure['parser'])
                    log.debug("Line to parse: " + line)
                    break
                try:
                    if structure['custom']:
                        for k, v in self.build_key_value(data['arguments']).items():
                           # translate keys
                            if self.translater.has_key(k):
                                key = self.translater[k]
                            else:
                                key = k
                            # concat dictionaries
                            data[key] = v
                        del data['arguments']
                except:
                    pass

                if not data:
                    log.warning("\n" +
                                "Mod: " + self.mod.mod_name + "\n" + \
                                "Action '" + structure['search'] + "' found, but Parser not matching!" + "\n" + \
                                "Parser Rule: " + structure['parser'] + "\n" + \
                                "Log Entry: " + line + "\n"
                    )
                    break
                else:
                    log.debug("\n" +
                                "Mod: " + self.mod.mod_name + "\n" + \
                                "Action: '" + structure['search'] + "'" + "\n" + \
                                "Data: '" + str(data)  + "\n"
                    )

                #clean up time
                data['time'] = data['time'].strip()
                #add timestamp and runtime to dictionary
                time_format_check = parse("{:d}:{:d}",data['time'])
                if not time_format_check:
                    log.error("found strange time format")
                    break
                data['timestamp'], data['runtime'] = self.calc_real_timestamp(data['time'])

                try:
                    self.call_action_db_handler(structure['call'], data)
                except:
                    log.debug("call_action_db_handler crashed on: '%s' for '%s'" % (str(structure['call']), str(data)))
                    pass

                break
        else:
            log.debug("NO MATCH:\n" + line)


