CREATE FUNCTION verify_participation_limit() RETURNS TRIGGER AS $BODY$
BEGIN 
    IF EXISTS (SELECT ParticipantCount FROM (SELECT count(*) AS ParticipantCount FROM participants WHERE participants.id_event = NEW.id_event) WHERE ParticipantCount >= 100) THEN
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
    IF NOT EXISTS (SELECT id_writer FROM participants WHERE NEW.id_event = participants.id_event AND comment.id_participant = NEW.id_writer) THEN
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
    DELETE FROM votes WHERE votes.id_developer = NEW.id_developer AND votes.id_option IN (SELECT id_option FROM options WHERE options.id_poll = NEW.id_poll);
END $BODY$
LANGUAGE plpgsql;

CREATE TRIGGER vote_cleaning
    BEFORE INSERT ON votes
    FOR EACH ROW 
    EXECUTE PROCEDURE filter_votes();
