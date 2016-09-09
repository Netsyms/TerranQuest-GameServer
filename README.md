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
6. Add the correct database settings to settings.php.
7. Add API keys for third-party services to settings.php.  You'll probably have to disable Munzee
  in the code2item.php file, as they don't give everyone capture permissions 
  for their API.  The Geocaching key isn't used for anything right now, leave it blank.
8. Package the TerranQuest app with the URL of your server (see https://github.com/Netsyms/TerranQuest/issues/3).

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0e51d378f78242b78f60c5aaa7187259)](https://www.codacy.com/app/netsyms/TerranQuest-GameServer?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Netsyms/TerranQuest-GameServer&amp;utm_campaign=Badge_Grade)