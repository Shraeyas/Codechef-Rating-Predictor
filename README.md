# Codechef Rating Predictor
---
##### Read The Discussion on CodeChef Here : https://discuss.codechef.com/t/codechef-rating-predictor/18427
---

Clone the Repository in your Local Dev Environment **(generally /var/www/html)**

Modify the **connect.php** file with appropriate credentials for your database

Run **cron.php**, it will automatically create a table livecontests which contains a list of ongoing contests and respective tables for each running contest. The table livecontests in the database will only contain the list of ongoing "Rated" Contests.
