from sqlalchemy import Table, Column, Integer, ForeignKey, Unicode, Boolean, UniqueConstraint
from sqlalchemy.orm import relationship, backref
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.ext.associationproxy import association_proxy


class DatabaseConnector(object):

    def create_session(self, engine):
        return sessionmaker(bind=engine)



class DBUpdate(object):

    @property
    def max_version(self):
        return 1



class DBBase(object):
    __abstract__ = True

    id = Column(Integer, primary_key=True)


DBBase = declarative_base(cls=DBBase)

class Game(DBBase):
    __tablename__ = 'game'

    starttime = Column(Integer, nullable=False)
    endtime = Column(Integer)
    game_type_id = Column(Integer, ForeignKey('game_type.id'))
    game_map_id = Column(Integer, ForeignKey('game_map.id'))
    end_cause_id = Column(Integer, ForeignKey('end_cause.id'))
    game_mod_id = Column(Integer, ForeignKey('game_mod.id'))
    team_stats = relationship("TeamStats", backref="game")
    kills = relationship("GameKill", backref="game")
    scores = relationship("Score", backref="game")
    say = relationship("Say", backref="game")
    tell = relationship("Tell", backref="game")
    sayteam = relationship("SayTeam", backref="game")
    bot_skill = Column(Integer)


class GameParameter(DBBase):
    __tablename__ = 'game_parameter'

    game_id = Column(Integer, ForeignKey('game.id'))
    game_key = Column(Unicode(256))
    game_value = Column(Unicode(256))

    __tableargs__ = (UniqueConstraint('game_id', 'key', name='game_id_key_uc'),
                     )

class TeamStats(DBBase):
    __tablename__ = 'team_stats'

    score = Column(Integer)
    team_id = Column(Integer, ForeignKey('team.id'))
    game_id = Column(Integer, ForeignKey('game.id'))


class GameKill(DBBase):
    __tablename__ = 'game_kill'

    time = Column(Integer, nullable=False)
    game_id = Column(Integer, ForeignKey('game.id'))
    weapon_id = Column(Integer, ForeignKey('item.id'))
    weapon = relationship("Item", backref="game_kill")
    killer_id = Column(Integer, ForeignKey('user.id'))
    killer = relationship("User", foreign_keys=[killer_id])
    killee_id = Column(Integer, ForeignKey('user.id'))
    killee = relationship("User", foreign_keys=[killee_id])


class Say(DBBase):
    __tablename__ = 'game_say'

    time = Column(Integer, nullable=False)
    game_id = Column(Integer, ForeignKey('game.id'))
    talker_id = Column(Integer, ForeignKey('user.id'))
    talker = relationship("User", foreign_keys=[talker_id])
    message = Column(Unicode(256))


class Tell(DBBase):
    __tablename__ = 'game_tell'

    time = Column(Integer, nullable=False)
    game_id = Column(Integer, ForeignKey('game.id'))
    talker_id = Column(Integer, ForeignKey('user.id'))
    talker = relationship("User", foreign_keys=[talker_id])
    message = Column(Unicode(256))
    listener_id = Column(Integer, ForeignKey('user.id'))
    listener = relationship("User", foreign_keys=[listener_id])


class SayTeam(DBBase):
    __tablename__ = 'game_sayteam'

    time = Column(Integer, nullable=False)
    game_id = Column(Integer, ForeignKey('game.id'))
    talker_id = Column(Integer, ForeignKey('user.id'))
    talker = relationship("User", foreign_keys=[talker_id])
    team_id = Column(Integer, ForeignKey('team.id'))
    message = Column(Unicode(256))


class Score(DBBase):
    __tablename__ = 'score'

    count = Column(Integer)
    user_id = Column(Integer, ForeignKey('user.id'))
    user = relationship("User")
    game_id = Column(Integer, ForeignKey('game.id'))
    team_id = Column(Integer, ForeignKey('team.id'))
    team_leader = Column(Boolean, default=False)


class User(DBBase):
    __tablename__ = 'user'

    username = Column(Unicode(64), unique=True)
    bot = Column(Boolean, default=False)
    model_id = Column(Integer, ForeignKey('model.id'))
    model = relationship("Model", backref="user")
    hmodel_id = Column(Integer, ForeignKey('hmodel.id'))
    hmodel = relationship("HModel", backref="user")


class Model(DBBase):
    __tablename__ = 'model'

    name = Column(Unicode(64), unique=True)


class HModel(DBBase):
    __tablename__ = 'hmodel'

    name = Column(Unicode(64), unique=True)


class Team(DBBase):
    __tablename__ = 'team'

    name = Column(Unicode(64), unique=True)


class GameMod(DBBase):
    __tablename__ = 'game_mod'

    name = Column(Unicode(256), unique=True)


class Item(DBBase):
    __tablename__ = 'item'

    item = Column(Unicode(64), unique=True)
    weapon = Column(Boolean, default=False)


class GameMap(DBBase):
    __tablename__ = 'game_map'

    name = Column(Unicode(256), unique=True)
    games = relationship("Game", backref="game_map")


class GameType(DBBase):
    __tablename__ = 'game_type'

    type = Column(Unicode(64), unique=True)
    games = relationship("Game", backref="game_type")


class EndCause(DBBase):
    __tablename__ = 'end_cause'

    reason = Column(Unicode(256), unique=True)
    games = relationship("Game", backref="end_cause")


class Pk3Files(DBBase):
    __tablename__ = 'pk3_files'

    filename = Column(Unicode(256), unique=True)


class Config(DBBase):
    __tablename__ = 'config'

    parameter = Column(Unicode(256), unique=True)
    value = Column(Unicode(256))

