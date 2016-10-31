TerranQuest Game Server
=======================

This is the server code for TerranQuest.  See the TerranQuest game code for 
examples on how to interact with this server.

The server runs on a standard LAMP server.  MariaDB is recommended.

To work with the database, download MySQL Workbench and open the database.mwb 
file.  You can export the SQL create code from there.

Setup
------

1. Have a standard LAMP server.
2. Clone this repo into the webroot.
3. Run PHP composer to make sure all the dependencies are installed.
4. Create a SQL database.
5. Copy settings.template.php to settings.php.
6. Add the correct settings to settings.php:
  * Plug in your database settings.
  * Contact us for an API key for the GIS API.  The GIS server has a global 
   database of places and terrain.  You could roll your own, but it's a lot to 
   work with (30GB database and counting).
  * Add API keys for third-party services.  You'll probably have 
   to disable Munzee in the code2item.php file, as they don't give everyone 
   capture permissions for their API.  The DarkSky weather API is free for 1000 
   queries a day at darksky.net/dev (no credit card needed).  The MapQuest key 
   is for adding location search on the admin panel, if you don't need that 
   don't use it.  Ignore the Geocache key, it's not doing anything.  Same for 
   the Google Play key, that's only for validating Android in-app purchases.
  * Set an admin username and password, otherwise the admin interface won't work.
7. Go to yourserver.com/admin and login.  If everything works, you should be good
   to go.
8. On the login screen of the app, tap the gear and type your server URL.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0e51d378f78242b78f60c5aaa7187259)](https://www.codacy.com/app/netsyms/TerranQuest-GameServer?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Netsyms/TerranQuest-GameServer&amp;utm_campaign=Badge_Grade)