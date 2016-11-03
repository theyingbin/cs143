Project 1C

Turning in 1 day late, please use 1 grace day. Thanks!

What we implemented:

We implemented everything required by the specification for this project. We used the demo as our guide and made improvements where we saw fit.

Four input pages:
	Page I1: add_actor_director.php
		This page allows you to add actors or directors to the database.
	Page I2: add_movie_information.php
		This page allows you to add movies to the database using a similar style to the demo.
	Page I3: add_movie_comment.php	
		This page takes in movie, name, rating, and comment similar to the demo.
	Page I4: add_movie_actor_relation.php
		This page has two dropdown menus for movie and actor and allows the user to type the role in.
	Page I5: add_movie_director_relation.php
		This page is similar to the I4, except it is for movies and directors, and leaves out the extra input for role.

Two browsing pages:
	Page B1: show_actor_info.php
		This page initially looks like a search page and is actually linked to search.php. When a real actor is clicked on it gets the actor’s id as part of the link so that it can portray the correct information in very nice tables. This shows the personal information as well as any movies they starred in, with links to those movies on the page.
	Page B2: show_movie_info.php
		This page works similarly to show_actor_info.php except it is for movies. It displays movie information, along with the actors in the movie, the average review, and any user reviews ordered by most recent first. It also has a link to the page that allows you to add a movie comment.

One search page: 
	search.php
		This page initially just has a search bar, but when input is entered, it displays all the actors and then all the movies that match the search using an AND relation between words. These entries all are links to their corresponding show_actor_info.php and show_movie_info.php pages.

Overall, we spent a lot of time on this project to make it functional and useable to the average person. We hit every requirement ask of us by the spec and added some extra functionality to improve user experience. We would definitely have liked to spend more time on the project to perfect the UI, but due to time constraints, were unable to do so.


How we split the work:

We split the work very evenly for this project. Connor wrote the add_movie_info.php,  add_movie_comment.php, add_movie_director_relation.php, search.php. and show_movie_info.php. Ying wrote index.php, navbar.php, show_actor_info.php, and add_actor_director.php. We both worked together to debug our project as we went along.

Ying also did the recording for Selenium, as well as getting the CSS and Bootstrap to cooperate. Connor spent a lot of time searching for bugs and making minor display changes to make the site more appealing.

How we would improve as a team: 

We worked really well together on this project, despite doing most of it remotely. We communicated very well and used Github to store our progress so that both of us had access to the content at all times. If we had to pick something to work on, it would probably be our scheduling because we are turning this project 1 day late. Next time we will focus on being on time.