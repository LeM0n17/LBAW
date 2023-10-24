--TRANSACTIONS
--INSERT NEW USER
CREATE FUNCTION insert_user(
    username VARCHAR(20),
    email VARCHAR(50),
    password VARCHAR(20)
) RETURNS INT AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DECLARE
    user_id INT;
BEGIN TRANSACTION;

    SET TRANSACTION ISOLATION LEVEL REPEATABLE READ

    INSERT INTO users (username, email, password)
    VALUES (username, email, password)
    RETURN id;

    INSERT INTO game_developer (id_user)
    VALUES (id);

    COMMIT;

    RETURN user_id;

EXCEPTION
    WHEN others THEN
        ROLLBACK;

COMMIT

--REMOVE USER
CREATE FUNCTION remove_user(
    id_user INT;
) RETURNS VOID AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

BEGIN TRANSACTION;

    SET TRANSACTION ISOLATION LEVEL REPEATABLE READ

    DELETE FROM game_developer
    WHERE game_developer.id_user = id_user;

    DELETE FROM users
    WHERE users.id = id_user;

COMMIT;

--GET PARTICIPANTS FROM AN EVENT
CREATE FUNCTION get_participants(
    id_event INT
) RETURNS TABLE (
    id INT,
    username VARCHAR(20),
    email VARCHAR(50)
) AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

BEGIN TRANSACTION;

    SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

    SELECT game_developer.id, users.username, users.email
    FROM game_developer
    INNER JOIN users ON game_developer.id_user = users.id
    INNER JOIN participants ON game_developer.id = participants.id_participant
    WHERE participants.id_event = id_event;
COMMIT

--Create a notification when a comment is likes
CREATE FUNCTION create_comment_like_notification(
    id_comment INT,
    id_developer INT
) RETURNS VOID AS $$

BEGIN TRANSACTION;

    SET TRANSACTION ISOLATION LEVEL REPEATABLE READ

    INSERT INTO notification(id_developer, content, time)
    VALUES (id_developer, 'Your comment was liked!', CURRENT_TIMESTAMP);

    INSERT INTO comment_notification(id_notification, id_comment, type)
    VALUES ((SELECT MAX(id) FROM notification), id_comment, 'like');

COMMIT;
