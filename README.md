
About:
===========================================
This process parses the Quake3 dedicated Server
Logfiles, analyzes the entries and writes them
into the database.

It was written and tested in Python2.7

The Frontend is a fully written Webfrontend to get the
Database Content in a nice form.
For Details read the info.txt in the frontend Directory.


Installation/Configuration:
===========================================

Maybe the Systempackage python-<Databasetype>db (python-mysqldb, ..) needs to be installed.
(For using pip you need to install python-pip)

    'aptitude install python-pip python-mysqldb'


Following python Packages need to be installed (with pip):
    pydaemon
    logger
    pymysql (or the Database you want to use)
    argparse
    sqlalchemy
    parse

    'pip install pydaemon logger pymysql argparse sqlalchemy parse'



- You have to create a Database.

- Now call Quakealyzer and tell him to create the Database Structure in your empty Database:
  For example:
    ./run.py --createdatabase mysql://quakeuser:quakepassword@localhost/quakealyzerDB

- Start Parsing process:
    ./run.py start --database mysql://quakeuser:quakepassword@localhost/quakealyzerDB --file /path/to/quake3_dedicated_server.log

- Stop Parsting process:
    ./run.py stop


- done
  (now you only need to configure the Frontend to get the Statistics viewed)

Database Connecting Syntax Examples:
===========================================
sqlite:////absolute/path/to/sqlite.db
mysql://username:password@hostname/mydatabase
postgresql://username:password@localhost/mydatabase
mssql+pymssql://username:password@hostname:port/dbname
oracle://username:password@hostname:1521/sidname


Todo:
===========================================
Take a look into Frontend info.txt


Contact:
===========================================
For wishes, Bugs, Questions,
write me a Message :-)



License:
===========================================
This Webpage is Open Source and can be used for any non commercial use cases.
Its a Webfrontend for Quakealyzer Backend and uses it's Database.


