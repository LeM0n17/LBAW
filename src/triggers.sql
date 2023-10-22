pragma foreign_keys = ON;
.mode columns
.header on
.nullvalue null

Create Trigger if not exists ParticipationLimit
Before Insert on participants
For Each Row
When exists (select ParticipantCount from (select count(*) as ParticipantCount from participants where participants.id_event = New.id_event) where ParticipantCount >= 100)
Begin
    select raise(ignore);
End;

Create Trigger if not exists CommentFilter
Before Insert on comment
For Each Row
When not exists (select id_writer from participants where New.id_event = participants.id_event and comment.id_participant = New.id_writer)
Begin
    select raise(ignore);
End;

Create Trigger if not exists ParticipationFilter
Before Insert on participants
For Each Row
When exists (select * from participants where participants.id_event = New.id_event and participants.id_participant = New.id_participant)
Begin
    select raise(ignore);
End;

Create Trigger if not exists LikeFilter
Before Insert on likes
For Each Row
Begin
    delete from likes where likes.id_developer = New.id_developer and likes.id_comment = New.id_developer;
End;

Create Trigger if not exists VoteFilter
Before Insert on votes
For Each Row
Begin
    -- REMOVER OS VOTOS EM OPTIONS DO MESMO POLL QUE O INSERIDO
End;
