#!/usr/bin/env python

import __builtin__
import sys
import os.path
import argparse
import logging

__builtin__.log = logging.getLogger(__name__)

from pydaemon import Daemon
from libs.libs import Library
from libs.dbhandler import DBHandler

pidfile = '/var/run/quakealyzer.pid'

class Runner(Daemon):

    lib = Library()
    db_handler = DBHandler()

    engine = None

    def database(self, database):
        self.db_connection = self.db_handler.connect(database)


    def create_database(self, create_database):
        self.database(create_database)
        self.db_handler.db_create_models()


    def database_help(self):
        self.db_handler.db_help()


    @property
    def mod_list(self):
        return self.lib.mod_list


    @property
    def get_db_connection(self):
        return self.db_connection


    def set_mod(self, mod):
        return self.lib.set_mod(mod)


    def set_parsing_file(self, log_file):
        # transfer engine
        self.lib.set_engine(self.db_handler.get_engine())

        self.log_file = log_file


    def run_file_import(self):
        self.lib.start_import(self.log_file)


    def run_parser(self):
        # transfer engine
        self.lib.set_engine(self.db_handler.get_engine())

        # start realtime logfile parsing as Daemon
        self.lib.start_parsing(self.log_file)


    def run(self):
        self.run_parser()



if __name__ == "__main__":

    runner = Runner(pidfile)


    parser = argparse.ArgumentParser()

    group = parser.add_mutually_exclusive_group()
    parser.add_argument("startparam", choices=['start', 'stop'], default='start', help="handles the run of Quakealyzer")
    group.add_argument("--databasehelp", help="gives Information about the structure of a Database-Connect", action="store_true")
    group.add_argument("--listmods", help="lists all avaliable Game Mods", action="store_true")
    group.add_argument("--file", help="the File to Parse")
    group.add_argument("--fileimport", help="(ONLY FOR TESTS, -> incorrect start timestamps) imports a complete File")
    parser.add_argument("--mod", default="baseq3", help="set the Mod which writes the Logfile")
    group.add_argument("--createdatabase", help="creates Databasemodel (empty Database has to exist)")
    parser.add_argument("--database", help="connects to Database (Models have to be created)")
    parser.add_argument("--nodaemon", choices=['yes','no'], default='no', help="force process not to run as daemon")
    parser.add_argument("--logfile", default='/var/log/quakealyzer.log', help="the Logfile for write Processlogs in")
    parser.add_argument("--loglevel", default='ERROR', choices=['CRITICAL', 'ERROR', 'WARNING', 'INFO', 'DEBUG'], help="the Loglevel to write")

    args = parser.parse_args()

    if args.startparam == 'stop':
        runner.stop()
        sys.exit(0)

    loglevel = args.loglevel
    if loglevel == 'DEBUG':
        logformat="%(asctime)s [%(levelname)-8s] - File: %(filename)s - Function: %(funcName)s - %(message)s"
    else:
        logformat="%(asctime)s [%(levelname)-8s] %(message)s"

    # set Logfile and Loglevel
    log.setLevel(loglevel)
    logging.basicConfig(
        filename=args.logfile,
        level=loglevel,
        format=logformat,
        datefmt="%Y.%m.%d %H:%M:%S"
    )

    log.info("Startparameter: " + str(args))



    if args.database:
        runner.database(args.database)

    if args.createdatabase:
        runner.create_database(args.createdatabase)
        sys.exit(0)

    if args.databasehelp:
        runner.database_help()
        sys.exit(0)

    if args.listmods:
        print runner.mod_list
        sys.exit(0)


    # is mod existing? then try to set
    if not args.mod:
        log.error("missing mod Argument")
        sys.exit(1)
    elif not runner.set_mod(args.mod):
        log.error("cannot find mod")
        print "Avaliable Mods:"
        print runner.mod_list
        sys.exit(1)


    if not runner.get_db_connection:
        log.error("Missing Database Connection!")
        sys.exit(1)


    # can logfile open?
    if not args.file or not os.path.isfile(args.file):
        if not args.fileimport or not os.path.isfile(args.fileimport):
            log.error("cannot open File")
            sys.exit(1)
        else:
            parse_file = args.fileimport
    else:
        parse_file = args.file
    runner.set_parsing_file(parse_file)


    # import logfile content instead of running parser
    if args.fileimport:
        runner.run_file_import()
        sys.exit(0)


    # run in nodaemon mode
    if args.nodaemon == 'yes':
        runner.run()
        sys.exit(0)


    #start as daemon
    if args.startparam == 'start':
        runner.start()
        sys.exit(0)

    sys.exit(0)

