-- noinspection SqlNoDataSourceInspectionForFile

CREATE SCHEMA lbaw23115;
SET search_path TO lbaw23115;

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS game_developer CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS event CASCADE;
DROP TABLE IF EXISTS participates CASCADE;
DROP TABLE IF EXISTS tag CASCADE;
DROP TABLE IF EXISTS event_tag CASCADE;
DROP TABLE IF EXISTS poll CASCADE;
DROP TABLE IF EXISTS option CASCADE;
DROP TABLE IF EXISTS developer_option CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS comment_like CASCADE;
DROP TABLE IF EXISTS faq CASCADE;
DROP TABLE IF EXISTS votes CASCADE;
DROP TABLE IF EXISTS likes;
DROP TYPE IF EXISTS event_type;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE event_type AS ENUM ('public', 'private', 'protected');
CREATE TYPE comment_notification_type AS ENUM ('reply', 'like');
CREATE TYPE event_notification_type AS ENUM ('start', 'results', 'invitation');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE users(
    id SERIAL PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(20) NOT NULL,
    email VARCHAR(50) UNIQUE
);

CREATE TABLE game_developer(
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    FOREIGN KEY(id_user) REFERENCES users(id) ON UPDATE CASCADE
);

CREATE TABLE admin(
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    FOREIGN KEY(id_user) REFERENCES users(id) ON UPDATE CASCADE
);

CREATE TABLE event(
    id SERIAL PRIMARY KEY,
    id_host INT NOT NULL,
    start TIMESTAMP NOT NULL,
    end_ TIMESTAMP NOT NULL CHECK (end_ > start),
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    types event_type NOT NULL,
    FOREIGN KEY(id_host) REFERENCES game_developer(id) ON UPDATE CASCADE
);

CREATE TABLE participates(
    id_participant INT NOT NULL,
    id_event INT NOT NULL,
    FOREIGN KEY(id_participant) REFERENCES game_developer(id) ON UPDATE CASCADE,
    FOREIGN KEY(id_event) REFERENCES event(id) ON UPDATE CASCADE
);

CREATE TABLE tag(
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE event_tag(
    id_event INT,
    id_tag INT,
    FOREIGN KEY(id_event) REFERENCES event(id) ON UPDATE CASCADE,
    FOREIGN KEY(id_tag) REFERENCES tag(id) ON UPDATE CASCADE
);

CREATE TABLE poll(
    id SERIAL PRIMARY KEY,
    id_event INT NOT NULL,
    FOREIGN KEY(id_event) REFERENCES event(id) ON UPDATE CASCADE
);

CREATE TABLE option(
    id SERIAL PRIMARY KEY,
    id_poll INT NOT NULL,
    name VARCHAR(20) NOT NULL,
    FOREIGN KEY(id_poll) REFERENCES poll(id) ON UPDATE CASCADE
);

CREATE TABLE votes(
    id_developer INT,
    id_option INT,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id) ON UPDATE CASCADE,
    FOREIGN KEY(id_option) REFERENCES option(id) ON UPDATE CASCADE
);

CREATE TABLE comment(
    id SERIAL PRIMARY KEY,
    id_writer INT,
    id_event INT,
    content TEXT NOT NULL,
    time TIMESTAMP NOT NULL,
    FOREIGN KEY(id_writer) REFERENCES game_developer(id) ON UPDATE CASCADE,
    FOREIGN KEY(id_event) REFERENCES event(id) ON UPDATE CASCADE
);

CREATE TABLE likes(
    id_comment INT,
    id_developer INT,
    likes BOOLEAN NOT NULL,
    PRIMARY KEY (id_comment, id_developer),
    FOREIGN KEY(id_comment) REFERENCES comment(id) ON UPDATE CASCADE,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id) ON UPDATE CASCADE
);

CREATE TABLE faq(
    id SERIAL PRIMARY KEY,
    id_admin INT NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    FOREIGN KEY(id_admin) REFERENCES admin(id) ON UPDATE CASCADE
);

CREATE TABLE notification(
    id SERIAL PRIMARY KEY,
    id_developer INT NOT NULL,
    content TEXT NOT NULL,
    time TIMESTAMP NOT NULL,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id) ON UPDATE CASCADE
);

CREATE TABLE comment_notification(
    id SERIAL PRIMARY KEY,
    id_notification INT NOT NULL,
    id_comment INT NOT NULL,
    type comment_notification_type NOT NULL,
    FOREIGN KEY(id_notification) REFERENCES notification(id) ON UPDATE CASCADE,
    FOREIGN KEY(id_comment) REFERENCES comment(id) ON UPDATE CASCADE
);

CREATE TABLE event_notification(
    id SERIAL PRIMARY KEY,
    id_notification INT NOT NULL,
    id_event INT NOT NULL,
    type event_notification_type NOT NULL,
    FOREIGN KEY(id_notification) REFERENCES notification(id) ON UPDATE CASCADE,
    FOREIGN KEY(id_event) REFERENCES event(id) ON UPDATE CASCADE
);

-----------------------------------------
-- INDEXES
-----------------------------------------

CREATE INDEX username ON users USING hash(username);

CREATE INDEX event_participants ON participates USING hash(id_event);

CREATE INDEX user_notifications ON notification USING btree(id_developer);
CLUSTER notification USING user_notifications;

-- Add column 'tsvectors' to 'event' to store computed tsvectors
ALTER TABLE event
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update tsvectors
CREATE FUNCTION event_update_FTS() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('simple', NEW.name), 'A') ||
            setweight(to_tsvector('simple', NEW.description), 'B')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('simple', NEW.name), 'A') ||
                setweight(to_tsvector('simple', NEW.description), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on 'events'
CREATE TRIGGER event_update_FTS
BEFORE INSERT OR UPDATE ON event
EXECUTE PROCEDURE event_update_FTS();

-- Create a GIN index for ts_vectors
CREATE INDEX event_update_FTS ON event USING GIN(tsvectors);

-----------------------------------------
-- TRIGGERS
-----------------------------------------


