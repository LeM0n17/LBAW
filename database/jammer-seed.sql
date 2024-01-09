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
CREATE TYPE event_notification_type AS ENUM ('start', 'results', 'invitation', 'request', 'cancellation');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  password VARCHAR NOT NULL,
  email VARCHAR UNIQUE NOT NULL,
  image VARCHAR, 
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
    title VARCHAR(20) NOT NULL,
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

CREATE TABLE file(
    id SERIAL PRIMARY KEY,
    id_developer INT,
    id_event INT,
    name TEXT NOT NULL,
    path TEXT NOT NULL,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(id_event) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE likes(
    id_file INT,
    id_developer INT,
    likes BOOLEAN NOT NULL,
    FOREIGN KEY(id_file) REFERENCES file(id) ON UPDATE CASCADE ON DELETE CASCADE,
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

CREATE FUNCTION delete_event_notifications() RETURNS TRIGGER AS $BODY$
BEGIN
    DELETE FROM notifications WHERE notifications.id_event = OLD.id;
    RETURN OLD;
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER delete_event_notifications
    BEFORE DELETE ON events
    FOR EACH ROW
EXECUTE PROCEDURE delete_event_notifications();

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
    IF NOT EXISTS (SELECT 1 FROM participants WHERE participants.id_event = NEW.id_event AND participants.id_participant = NEW.id_writer) THEN
        RAISE EXCEPTION 'User is not a participant in this event';
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
        RAISE EXCEPTION 'PARTICIPANT WAS ALREADY IN EVENT!';
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
    DELETE FROM likes WHERE likes.id_developer = NEW.id_developer AND likes.id_file = NEW.id_file;
    RETURN NEW;
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
    RETURN NEW;
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER vote_cleaning
    BEFORE INSERT ON votes
    FOR EACH ROW
EXECUTE PROCEDURE filter_votes();

CREATE FUNCTION add_game_dev() RETURNS TRIGGER AS $BODY$
BEGIN
    INSERT INTO game_developer VALUES (NEW.id, NEW.id);
    RETURN NEW;
END $BODY$
    LANGUAGE plpgsql;

CREATE TRIGGER add_game_dev
    AFTER INSERT ON users
    FOR EACH ROW
EXECUTE PROCEDURE add_game_dev();

CREATE OR REPLACE FUNCTION delete_notifications() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM notifications
    WHERE (type = 'invitation' OR type = 'request') AND id_developer = NEW.id_participant AND id_event = NEW.id_event;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delete_notifications_trigger
AFTER INSERT ON participants
FOR EACH ROW
EXECUTE FUNCTION delete_notifications();

CREATE FUNCTION owner_to_participant() RETURNS TRIGGER AS $$
BEGIN 
    INSERT INTO participants VALUES (NEW.id_host, NEW.id);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER add_participant_creation
AFTER INSERT ON events
FOR EACH ROW
EXECUTE FUNCTION owner_to_participant();

-----------------------------------------
-- TRANSACTIONS
-----------------------------------------
BEGIN;

CREATE OR REPLACE FUNCTION anonymize_user_parameters() RETURNS TRIGGER AS $$
BEGIN
    UPDATE users
    SET name = 'Anon',
        password = OLD.password,
        email = 'anon' || OLD.id  || '@anon.com'
    WHERE id = OLD.id;
    
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER anonymize_user_parameters_trigger
BEFORE DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION anonymize_user_parameters();

COMMIT;

-----------------------------------------
-- POPULATE
-----------------------------------------

INSERT INTO users VALUES (DEFAULT, 'JohnDoe', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'example@example.com');
INSERT INTO users VALUES (DEFAULT, 'JaneDoe', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'example1@example.com');
INSERT INTO users VALUES (DEFAULT, 'JorgeDoe', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'example2@example.com');
INSERT INTO users VALUES (DEFAULT, 'Joao', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'joao@example.com');
INSERT INTO users VALUES (DEFAULT, 'EltonJohn', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'eltonjohn@example.com');
INSERT INTO users VALUES (DEFAULT, 'FreddyMercury', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'freddymercury@example.com');
INSERT INTO users VALUES (DEFAULT, 'BrianMay', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'brianmay@example.com');
INSERT INTO users VALUES (DEFAULT, 'AlexTurner', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'alexturner@example.com');
INSERT INTO users VALUES (DEFAULT, 'GeorgeHarrison', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'georgeharrison@example.com');
INSERT INTO users VALUES (DEFAULT, 'EricClapton', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'ericclapton@example.com');
INSERT INTO users VALUES (DEFAULT, 'Julian', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'julian@example.com');
INSERT INTO users VALUES (DEFAULT, 'Fab', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'fab@example.com');
INSERT INTO users VALUES (DEFAULT, 'JonBonJovi', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'jonbonjovi@example.com');
INSERT INTO users VALUES (DEFAULT, 'Rob', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'rob@example.com');
INSERT INTO users VALUES (DEFAULT, 'MarioMario', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'mario@example.com');
INSERT INTO users VALUES (DEFAULT, 'LuigiMario', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'luigi@example.com');
INSERT INTO users VALUES (DEFAULT, 'WarioWario', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'wario@example.com');
INSERT INTO users VALUES (DEFAULT, 'WaluigiWario', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'waluigi@example.com');
INSERT INTO users VALUES (DEFAULT, 'Funky', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'funky@example.com');
INSERT INTO users VALUES (DEFAULT, 'CoconutMall', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'coconutmall@example.com');

INSERT INTO tag VALUES (DEFAULT, 'Indie');
INSERT INTO tag VALUES (DEFAULT, 'Professional');
INSERT INTO tag VALUES (DEFAULT, 'Halloween');
INSERT INTO tag VALUES (DEFAULT, 'Christmas');
INSERT INTO tag VALUES (DEFAULT, 'Beginner');
INSERT INTO tag VALUES (DEFAULT, 'Easter');
INSERT INTO tag VALUES (DEFAULT, 'English');
INSERT INTO tag VALUES (DEFAULT, 'Mandarin');
INSERT INTO tag VALUES (DEFAULT, 'Spanish');
INSERT INTO tag VALUES (DEFAULT, 'Italian');
INSERT INTO tag VALUES (DEFAULT, 'German');
INSERT INTO tag VALUES (DEFAULT, 'Portuguese');
INSERT INTO tag VALUES (DEFAULT, 'Japanese');
INSERT INTO tag VALUES (DEFAULT, 'Russian');

INSERT INTO events VALUES (DEFAULT, 1, '2024-01-15 10:00:00', '2024-01-30 23:59:59', 'Jam Fiesta', 'Join the ultimate game creation celebration!', 'public');
INSERT INTO events VALUES (DEFAULT, 2, '2024-01-20 12:00:00', '2024-02-10 23:59:59', 'Pixel Quest', 'Embark on a journey to create pixel-perfect wonders!', 'public');
INSERT INTO events VALUES (DEFAULT, 6, '2024-02-01 08:00:00', '2024-02-28 23:59:59', 'Infinite Ideas', 'Unleash your creativity in this ongoing game jam!', 'private');
INSERT INTO events VALUES (DEFAULT, 5, '2024-02-15 15:30:00', '2024-03-01 00:00:00', 'VR Vortex', 'Dive into the virtual reality abyss of game development!', 'public');
INSERT INTO events VALUES (DEFAULT, 2, '2023-12-23 18:45:00', '2024-01-15 23:59:59', 'Time Warp Jam', 'Explore the fourth dimension in your game creations!', 'protected');
INSERT INTO events VALUES (DEFAULT, 1, '2024-02-24 09:15:00', '2024-03-15 23:59:59', 'Dream Weaver', 'Craft your dreams into playable realities!', 'public');
INSERT INTO events VALUES (DEFAULT, 18, '2024-01-05 14:00:00', '2024-01-20 23:59:59', 'Epic Saga Jam', 'Create epic tales that unfold in the world of gaming!', 'private');
INSERT INTO events VALUES (DEFAULT, 7, '2024-01-10 16:45:00', '2024-02-01 00:00:00', 'Unity Unleashed', 'Ongoing jam for Unity enthusiasts - Unleash the power of Unity!', 'public');
INSERT INTO events VALUES (DEFAULT, 4, '2024-01-05 20:30:00', '2024-01-15 23:59:59', 'Retro Revival', 'Revive the magic of retro games in a modern twist!', 'public');
INSERT INTO events VALUES (DEFAULT, 16, '2024-01-15 22:00:00', '2024-02-10 23:59:59', 'Aetherial Adventures', 'Craft games that transport players to aetherial realms!', 'public');
INSERT INTO events VALUES (DEFAULT, 14, '2024-02-01 11:30:00', '2024-02-28 23:59:59', 'Code Crafters', 'Crafting code, one game at a time!', 'protected');
INSERT INTO events VALUES (DEFAULT, 13, '2024-01-20 17:00:00', '2024-02-10 23:59:59', 'Future Frontiers', 'Design games that explore uncharted territories of the future!', 'private');
INSERT INTO events VALUES (DEFAULT, 17, '2023-06-15 14:30:00', '2023-06-30 23:59:59', 'Summer Game Fest', 'Celebrate the summer with a game creation extravaganza!', 'public');
INSERT INTO events VALUES (DEFAULT, 8, '2023-09-20 10:00:00', '2023-09-30 23:59:59', 'Code Harvest', 'Harvest your coding skills in this autumn game development event!', 'public');
INSERT INTO events VALUES (DEFAULT, 10, '2023-11-10 12:00:00', '2023-11-25 23:59:59', 'Pixel Fusion', 'Fuse pixels and creativity in this pre-winter game jam!', 'public');
INSERT INTO events VALUES (DEFAULT, 2, '2023-12-05 08:00:00', '2023-12-20 23:59:59', 'Winter Wonderland Game Jam', 'Create games that capture the magic of winter!', 'public');
INSERT INTO events VALUES (DEFAULT, 11, '2023-12-31 14:30:00', '2024-01-30 23:59:59', 'New Year Game Fest', 'Join the ongoing New Year game creation extravaganza!', 'public');
INSERT INTO events VALUES (DEFAULT, 15, '2024-01-05 10:00:00', '2024-02-29 23:59:59', 'Code Harvest', 'Ongoing harvest of coding skills in this autumn game development event!', 'public');
INSERT INTO events VALUES (DEFAULT, 4, '2024-01-02 12:00:00', '2024-01-31 23:59:59', 'Pixel Fusion', 'Ongoing fusion of pixels and creativity in this pre-winter game jam!', 'public');
INSERT INTO events VALUES (DEFAULT, 9, '2024-01-06 08:00:00', '2024-01-15 23:59:59', 'Winter Wonderland Game Jam', 'Ongoing creation of games that capture the magic of winter!', 'public');

INSERT INTO event_tag (id_event, id_tag) VALUES
(1, 2),
(1, 9),
(2, 13),
(3, 4),
(3, 8),
(4, 1),
(4, 12),
(5, 10),
(6, 14),
(7, 7),
(7, 1),
(8, 11),
(9, 9),
(9, 3),
(10, 13),
(11, 10),
(11, 1),
(12, 7),
(12, 6),
(13, 14),
(13, 1),
(14, 8),
(14, 3),
(15, 12),
(15, 1),
(16, 11),
(16, 4),
(17, 9),
(17, 6),
(18, 13),
(18, 1),
(19, 7),
(19, 3),
(20, 8),
(20, 2);

INSERT INTO admin VALUES (DEFAULT, 3);
