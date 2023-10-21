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

CREATE TABLE users(
    id SERIAL PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(20) NOT NULL,
    email VARCHAR(50) UNIQUE,
);

CREATE TABLE game_developer(
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    FOREIGN KEY(id_user) REFERENCES users(id)
);

CREATE TABLE admin(
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    FOREIGN KEY(id_user) REFERENCES users(id)
);

CREATE TABLE event(
    id SERIAL PRIMARY KEY,
    id_host INT NOT NULL,
    start DATE NOT NULL,
    end_ DATE NOT NULL CHECK (end_ > start),
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    type CHECK (type IN ('public', 'private', 'protected')) NOT NULL,
    FOREIGN KEY(id_host) REFERENCES game_developer(id)
);

CREATE TABLE participates(
    id_participant INT NOT NULL,
    id_event INT NOT NULL,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id),
    FOREIGN KEY(id_event) REFERENCES event(id)
);

CREATE TABLE tag(
    id SERIAL PRIMARY KEY;
    name VARCHAR(20) NOT NULL,
);

CREATE TABLE event_tag(
    id_event INT,
    id_tag INT,
    FOREIGN KEY(id_event) REFERENCES event(id),
    FOREIGN KEY(id_tag) REFERENCES tag(id)
);

CREATE TABLE poll(
    id SERIAL PRIMARY KEY,
    id_event INT NOT NULL,
    FOREIGN KEY(id_event) REFERENCES event(id)
);

CREATE TABLE option(
    id SERIAL PRIMARY KEY,
    id_poll INT NOT NULL,
    name VARCHAR(20) NOT NULL,
    FOREIGN KEY(id_poll) REFERENCES poll(id)
);

CREATE TABLE votes(
    id_developer INT,
    id_option INT,
    FOREIGN KEY(id_developer) REFERENCES game_developer(id),
    FOREIGN KEY(id_option) REFERENCES option(id)
);

CREATE TABLE comment(
    id SERIAL PRIMARY KEY,
    id_writer INT,
    id_event INT,
    content TEXT NOT NULL,
    time TIMESTAMP NOT NULL,
    FOREIGN KEY(id_writer) REFERENCES game_developer(id),
    FOREIGN KEY(id_event) REFERENCES event(id)
);

CREATE TABLE likes(
    id_comment INT,
    id_developer INT,
    like BOOLEAN NOT NULL,
    FOREIGN KEY(id_comment) REFERENCES comment(id),
    FOREIGN KEY(id_developer) REFERENCES game_developer(id)
);

CREATE TABLE faq(
    id SERIAL PRIMARY KEY,
    id_admin INT NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    FOREIGN KEY(id_admin) REFERENCES admin(id)
);
