--TRANSACTIONS
--INSERT NEW USER
CREATE OR REPLACE FUNCTION insert_user(
    username VARCHAR(20),
    email VARCHAR(50),
    password VARCHAR(20)
) RETURNS INT AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DECLARE
    user_id INT;
BEGIN TRANSACTION;
    INSERT INTO users (username, email, password)
    VALUES (username, email, password)
    RETURNING id INTO user_id;

    INSERT INTO game_developer (id_user)
    VALUES (user_id);

    COMMIT;

    RETURN user_id;

EXCEPTION
    WHEN others THEN
        ROLLBACK;
        raise(ignore);

END TRANSACTION;

--GET PARTICIPANTES FROM AN EVENT
CREATE OR REPLACE FUNCTION get_participants(
    id_event INT
) RETURNS TABLE (
    id INT,
    username VARCHAR(20),
    email VARCHAR(50)
) AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

BEGIN TRANSACTION;
    SELECT game_developer.id, users.username, users.email
    FROM game_developer
    INNER JOIN users ON game_developer.id_user = users.id
    INNER JOIN participants ON game_developer.id = participants.id_participant
    WHERE participants.id_event = id_event;
END TRANSACTION;

--GET TAGS FROM AN EVENT
CREATE OR REPLACE FUNCTION get_tags(
    id_event INT
) RETURNS TABLE (
    id INT,
    name VARCHAR(20)
) AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

BEGIN TRANSACTION;
    SELECT tag.id, tag.name
    FROM tag
    INNER JOIN event_tag ON tag.id = event_tag.id_tag
    WHERE event_tag.id_event = id_event;
END TRANSACTION;

--GET LIKES FROM A COMMENT
CREATE OR REPLACE FUNCTION get_likes(
    id_comment INT
) RETURNS TABLE (
    id_developer INT,
    likes BOOLEAN
) AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

BEGIN TRANSACTION;
    SELECT likes.id_developer, likes.likes
    FROM likes
    WHERE likes.id_comment = id_comment;
END TRANSACTION;

--GET COMMENTS FROM AN EVENT
CREATE OR REPLACE FUNCTION get_comments(
    id_event INT
) RETURNS TABLE (
    id INT,
    id_writer INT,
    username VARCHAR(20),
    content TEXT,
    time TIMESTAMP
) AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

BEGIN TRANSACTION;
    SELECT comment.id, comment.id_writer, users.username, comment.content, comment.time
    FROM comment
    INNER JOIN users ON comment.id_writer = users.id
    WHERE comment.id_event = id_event;
END TRANSACTION;

--GET NUMBER OF VOTES FOR A POLL OPTION
CREATE OR REPLACE FUNCTION get_votes(
    id_option INT
) RETURNS INT AS $$

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

BEGIN TRANSACTION;
    SELECT COUNT(*)
    FROM votes
    WHERE votes.id_option = id_option;
END TRANSACTION;

$$ LANGUAGE plpgsql;
