Connor Kenny and Ying Bin Wu

Project 1B

This project was implementing a movie database and a user interface for that database.

All the code in the project is commented as to explain its purpose, but we will go into specifics here as well.

create.sql - Used to create the tables for the databases (later added the constraints)
load.sql - Used to load the data from the .del files in ~/data/ into the tables created by create.sql
queries.sql - Implements the two queries required by the project along with an extra query of our creation
query.php - User interface that allows the user to input mysql queries and receive the given output for our database
violations.sql - Implements multiple queries that violate the constraints that we came up with
	Constraints:
		3 Primary Key
			Movie id should be unique
			Actor id should be unique
			Director id should be unique
		6 Referential
			MovieGenre mid corresponds to Movie id
			MovieDirector mid corresponds to Movie id
			MovieDirector did corresponds to Director id
			MovieActor mid corresponds to Movie id
			MovieActor aid corresponds to Actor id
			Review mid corresponds to Movie id
		3 Check
			Review rating should be x / 5
			Movie year should be between 1000 and 3000
			Movie rating should be 'G', 'PG', 'PG-13', 'R', or 'NC-17'

We did this project together and each partner did roughly 50% of the work. We utilized pair programming for some portions,
while worked remotely for others. We did create.sql, load.sql, and queries.sql together, and both put in equal work for
the remaining files remotely.

Overall, this project was a very good way to learn about MySQL and PHP. We were able to look into many of the nuances
that MySQL has, and use them to make our project very efficient.

