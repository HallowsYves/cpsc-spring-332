use Event_DB;

SET SQL_SAFE_UPDATES = 0;
DELETE FROM Sponsored_events;
DELETE FROM Speaks_on;
DELETE FROM Presents_on;
DELETE FROM Enrolled_in;
DELETE FROM Sponsor;
DELETE FROM keynote_speaker;
DELETE FROM Presenter;
DELETE FROM _User;
DELETE FROM _Event;
DELETE FROM Venue;
DELETE FROM University;
SET SQL_SAFE_UPDATES = 1;