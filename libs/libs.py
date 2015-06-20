#!/usr/bin/env python

import time
import os
import sys

from parser import Parser


class Library(object):

    p = Parser()
    max_no_action_time = 600 # seconds
    last_action_time = 0

    @property
    def mod_list(self):
        """
        parses the mod-dir for Quake3 Mods
        Filename=Modname
        Returnvalue will be a list of possible Mods
        """
        mods = []
        for I in os.listdir(sys.path[0] + '/mods'):
            if not os.path.isdir(sys.path[0] + '/mods/' + I):
                continue
            mods.append(I.split('.')[0])
        return mods

    def set_engine(self, engine):
        self.engine = engine
        self.p.set_engine(engine)

    def set_mod(self, mod):
        log.debug("set running mod to: " + str(mod))
        self.mod = mod
        if not self.is_mod(self.mod):
            log.error("setting mod to %s failed", str(mod))
            return False
        return self.p.load_mod(self.mod)


    def get_mod(self):
        return self.mod


    def is_mod(self, mod):
        if mod in self.mod_list:
            return True
        return False

    def parse_line(self, line):
        """
        moves the log line to analyzer
        """
        log.debug("analyzing Line: " + line)
        self.p.analyze(line)


    def start_import(self, file):
        log.debug("start import")
        with open(file) as fh:
            for line in fh:
                self.parse_line(line)


    def start_parsing(self, file):
        log.debug("start parsing")
        for line in self.tail_f(open(file)):
            self.parse_line(line)


    def tail_f(self, file):
        # continous run of reading file
        interval = 0.1
        linepart = ""
        dedicated_server_start = self.p.get_server_start

        file.seek(0,2)
        while True:
            line = file.readline()

            if not line:
                time.sleep(interval)
                continue

            # renew database connection if no action happens for a specified time
            if self.last_action_time + self.max_no_action_time < time.time():
                self.p.renew_db_connection()
                self.last_action_time = time.time()

            # if Quake had an unclean shutdown and starts again,
            # this here detects it
            if linepart != "" and line == dedicated_server_start and dedicated_server_start:
                linepart =""
                yield line
                self.last_action_time = time.time()
            # return only complete lines ...
            elif line.endswith("\n"):
                yield linepart + line
                linepart = ""
            else:
                linepart = line


