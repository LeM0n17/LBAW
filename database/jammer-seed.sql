-- noinspection SqlNoDataSourceInspectionForFile
DROP SCHEMA IF EXISTS lbaw23115 CASCADE;

CREATE SCHEMA lbaw23115;
SET search_path TO lbaw23115;

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS game_developer CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS events CASCADE;
DROP TABLE IF EXISTS participants CASCADE;
DROP TABLE IF EXISTS tag CASCADE;
DROP TABLE IF EXISTS event_tag CASCADE;
DROP TABLE IF EXISTS poll CASCADE;
DROP TABLE IF EXISTS option CASCADE;
DROP TABLE IF EXISTS developer_option CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS comment_like CASCADE;
DROP TABLE IF EXISTS faq CASCADE;
DROP TABLE IF EXISTS votes CASCADE;
DROP TABLE IF EXISTS likes CASCADE;

DROP TYPE IF EXISTS event_type CASCADE;
DROP TYPE IF EXISTS comment_notification_type CASCADE;
DROP TYPE IF EXISTS event_notification_type CASCADE;

DROP FUNCTION IF EXISTS event_manage_FTS CASCADE;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE event_type AS ENUM ('public', 'private', 'protected');
CREATE TYPE comment_notification_type AS ENUM ('reply', 'like');
CREATE TYPE event_notification_type AS ENUM ('start', 'results', 'invitation');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  password VARCHAR NOT NULL,
  email VARCHAR UNIQUE NOT NULL,
  remember_token VARCHAR
);

CREATE TABLE game_developer(
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    FOREIGN KEY(id_user) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE admin(
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    FOREIGN KEY(id_user) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE events(
    id SERIAL PRIMARY KEY,
    id_host INT NOT NULL,
    start TIMESTAMP NOT NULL,
    end_ TIMESTAMP NOT NULL CHECK (end_ > start),
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    types event_type NOT NULL,
    FOREIGN KEY(id_host) REFERENCES game_developer(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE participants
(
    id_participant INT NOT NULL,
    id_event INT NOT NULL,
    FOREIGN KEY(id_participant) REFERENCES game_developer(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(id_event) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE tag(
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE event_tag(
    id_event INT,
    id_tag INT,
    FOREIGN KEY(id_event) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(id_tag) REFERENCES tag(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE poll(
    id SERIAL PRIMARY KEY,
    id_event INT NOT NULL,
    FOREIGN KEY(id_event) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE option(
    id SERIAL PRIMARY KEY,
    id_poll INT NOT NULL,
    name VARCHAR(20) NOT NULL,
    FOREIGN KEY(id_poll) REFERENCES poll(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE votes(
    id_developer INT,
    id_option INT,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(id_option) REFERENCES option(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE comment(
    id SERIAL PRIMARY KEY,
    id_writer INT,
    id_event INT,
    content TEXT NOT NULL,
    time TIMESTAMP NOT NULL,
    FOREIGN KEY(id_writer) REFERENCES game_developer(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(id_event) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE likes(
    id_comment INT,
    id_developer INT,
    likes BOOLEAN NOT NULL,
    PRIMARY KEY (id_comment, id_developer),
    FOREIGN KEY(id_comment) REFERENCES comment(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE faq(
    id SERIAL PRIMARY KEY,
    id_admin INT NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    FOREIGN KEY(id_admin) REFERENCES admin(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE notifications(
    id SERIAL PRIMARY KEY,
    id_developer INT NOT NULL,
    id_event INT NOT NULL,
    type event_notification_type NOT NULL,
    content TEXT NOT NULL,
    time TIMESTAMP NOT NULL,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-----------------------------------------
-- INDEXES
-----------------------------------------

CREATE INDEX event_participants ON participants USING hash(id_event);

CREATE INDEX user_notifications ON notifications USING btree(id_developer);
CLUSTER notifications USING user_notifications;

-- Add column 'tsvectors' to 'event' to store computed tsvectors
ALTER TABLE events
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update tsvectors
CREATE FUNCTION event_manage_FTS() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('english', NEW.name), 'A') ||
            setweight(to_tsvector('english', NEW.description), 'B')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('english', NEW.name), 'A') ||
                setweight(to_tsvector('english', NEW.description), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on 'events'
CREATE TRIGGER event_update_FTS
BEFORE INSERT OR UPDATE ON events
FOR EACH ROW
EXECUTE PROCEDURE event_manage_FTS();

-- Create a GIN index for ts_vectors
CREATE INDEX event_FTS_index ON events USING GIN(tsvectors);

-----------------------------------------
-- TRIGGERS
-----------------------------------------

CREATE FUNCTION verify_participation_limit() RETURNS TRIGGER AS $BODY$
BEGIN
    IF EXISTS (SELECT ParticipantCount FROM (SELECT count(*) AS ParticipantCount FROM participants WHERE participants.id_event = NEW.id_event) as pPC WHERE ParticipantCount >= 100) THEN
        RAISE EXCEPTION 'PARTICIPATION LIMIT REACHED!';
    END IF;
    RETURN NEW;
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER participation_limiter
    BEFORE INSERT ON participants
    FOR EACH ROW
EXECUTE PROCEDURE verify_participation_limit();

CREATE FUNCTION comment_from_participant() RETURNS TRIGGER AS $BODY$
BEGIN
    IF NOT EXISTS (SELECT id_writer FROM participants WHERE NEW.id_event = participants.id_event AND comment.id_writer = NEW.id_writer) THEN
        RAISE EXCEPTION 'COMMENT FROM NOT PARTICIPANT!';
    END IF;
    RETURN NEW;
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER comment_restricter
    BEFORE INSERT ON comment
    FOR EACH ROW
EXECUTE PROCEDURE comment_from_participant();

CREATE FUNCTION verify_participation_presence() RETURNS TRIGGER AS $BODY$
BEGIN
    IF EXISTS (SELECT * FROM participants WHERE participants.id_event = NEW.id_event AND participants.id_participant = NEW.id_participant) THEN
        RAISE EXCEPTION 'PARTICIPATION WAS ALREADY IN EVENT!';
    END IF;
    RETURN NEW;
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER participation_presence
    BEFORE INSERT ON participants
    FOR EACH ROW
EXECUTE PROCEDURE verify_participation_presence();

CREATE FUNCTION delete_likes() RETURNS TRIGGER AS $BODY$
BEGIN
    DELETE FROM likes WHERE likes.id_developer = NEW.id_developer AND likes.id_comment = NEW.id_developer;
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER like_cleaning
    BEFORE INSERT ON likes
    FOR EACH ROW
EXECUTE PROCEDURE delete_likes();

CREATE FUNCTION filter_votes() RETURNS TRIGGER AS $BODY$
BEGIN
    DELETE FROM votes WHERE votes.id_developer = NEW.id_developer AND votes.id_option IN
            (SELECT id_option FROM option WHERE option.id_poll in
            (SELECT id_poll FROM option WHERE option.id = NEW.id_option));
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER vote_cleaning
    BEFORE INSERT ON votes
    FOR EACH ROW
EXECUTE PROCEDURE filter_votes();

-----------------------------------------
-- TRANSACTIONS
-----------------------------------------

-----------------------------------------
-- POPULATE
-----------------------------------------

INSERT INTO users VALUES (DEFAULT, 'JohnDoe', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'example@example.com');
INSERT INTO users VALUES (DEFAULT, 'JaneDoe', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'example1@example.com');
INSERT INTO users VALUES (DEFAULT, 'JorgeDoe', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'example2@example.com');

INSERT INTO game_developer VALUES (1, 1);
INSERT INTO game_developer VALUES (2, 2);
INSERT INTO game_developer VALUES (3, 3);

INSERT INTO events VALUES (DEFAULT, 1, '2020-01-01 00:00:00', '2024-01-01 00:00:00', 'Event', 'Description', 'public');
INSERT INTO events VALUES (DEFAULT, 2, '2020-01-01 00:00:00', '2024-01-01 00:00:00', 'Event2', 'Description2', 'public');

INSERT INTO notifications VALUES (DEFAULT, 2, 2, 'invitation', 'join please', '2020-01-01 00:00:00');
INSERT INTO notifications VALUES (DEFAULT, 2, 1, 'invitation', 'join please', '2020-01-01 00:00:00');

INSERT INTO admin VALUES (DEFAULT, 3);
