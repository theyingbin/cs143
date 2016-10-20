-- Primary Key: Movie id should be unique --
-- A Movie with id = 3 already exists --
-- ERROR 1062 (23000): Duplicate entry '3' for key 'PRIMARY' --
INSERT INTO Movie VALUES(3, 'MovieName', 2000, 'G', 'ABC');

-- Primary Key: Actor id should be unique --
-- An Actor with id = 1001 already exists --
-- ERROR 1062 (23000): Duplicate entry '1001' for key 'PRIMARY' --
INSERT INTO Actor VALUES(1001, 'First', 'Last', 'Male', 2000-01-01, 2100-01-01);

-- Primary Key: Director id should be unique --
-- A Director with id = 16 already exists --
-- ERROR 1062 (23000): Duplicate entry '16' for key 'PRIMARY' --
INSERT INTO Director VALUES(16, 'First', 'Last', 2000-01-01, 2100-01-01);

-- Foreign Key: MovieGenre mid corresponds to Movie id --
-- A Movie with id = 1 does not exist --
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`)) --
INSERT INTO MovieGenre VALUES (1, 'Genre');

-- Foreign Key: MovieDirector mid corresponds to Movie id --
-- A Movie with id = 7 does not exist --
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`)) --
INSERT INTO MovieDirector VALUES (7, 1001);

-- Foreign Key: MovieDirector did corresponds to Director id --
-- A Director with id = 1 does not exist --
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) REFERENCES `Director` (`id`)) --
INSERT INTO MovieDirector VALUES (3, 1);

-- Foreign Key: MovieActor mid corresponds to Movie id --
-- A Movie with id = 10 does not exist --
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`)) --
INSERT INTO MovieActor VALUES (10, 1001, 'Role');

-- Foreign Key: MovieActor aid corresponds to Actor id --
-- An Actor with id = 2 does not exist --
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`)) --
INSERT INTO MovieActor VALUES (3, 2, 'Role');

-- Foreign Key: Review mid corresponds to Movie id --
-- A Movie with id = 11 does not exist --
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`)) --
INSERT INTO Review VALUES ('Name', '01-01-2000 00:00:00', 11, 3, 'Comment');

-- Check: Review rating should be x / 5 --
-- A rating greater than 5 is not allowed --
-- Checks are not tested in MySQL, so there is no error --
INSERT INTO Review VALUES ('Name', '01-01-2000 00:00:00', 3, 10000000, 'Comment');

-- Check: Movie year should be between 1000 and 3000 --
-- A year of greater than 3000 is not allowed --
-- Checks are not tested in MySQL, so there is no error --
INSERT INTO Movie VALUES(1, 'MovieName', 10000000, 'G', 'ABC');

-- Check: Movie rating should be 'G', 'PG', 'PG-13', 'R', or 'NC-17' --
-- A rating of anything other than above is not allowed, so 'Invalid' is not allowed --
-- Checks are not tested in MySQL, so there is no error --
INSERT INTO Movie VALUES(1, 'MovieName', 2000, 'Invalid', 'ABC');








