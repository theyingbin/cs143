-- Return firstname lastname for all actors in the movie 'Die Another Day' --
SELECT CONCAT(a.first, ' ', a.last) Name
FROM Actor a, MovieActor ma, Movie m
WHERE m.title = 'Die Another Day' AND a.id = ma.aid AND m.id = ma.mid;

-- Return the number of actors who have starred in multiple movies --
SELECT COUNT(DISTINCT ma1.aid) NumberOfActors
FROM MovieActor ma1, MovieActor ma2
WHERE ma1.aid = ma2.aid AND ma1.mid <> ma2.mid;

-- Return firstname lastname for all people who are both directors and actors and no longer alive --
SELECT CONCAT(a.first, ' ', a.last) Name
FROM Actor a, Director d
WHERE a.id = d.id AND a.dod IS NOT NULL;

