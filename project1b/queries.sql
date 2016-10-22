-- Return firstname lastname for all actors in the movie 'Die Another Day' --
SELECT CONCAT(a.first, ' ', a.last) Name
FROM Actor a, MovieActor ma, Movie m
WHERE m.title = 'Die Another Day' AND a.id = ma.aid AND m.id = ma.mid;

-- Return the number of actors who have starred in multiple movies --
SELECT COUNT(*)
FROM(
    SELECT *
    FROM MovieActor
    GROUP BY aid
    HAVING COUNT(mid) > 1
) AS Subquery;


-- Return firstname lastname for all people who are both directors and actors and no longer alive --
SELECT CONCAT(a.first, ' ', a.last) Name
FROM Actor a, Director d
WHERE a.id = d.id AND a.dod IS NOT NULL;

