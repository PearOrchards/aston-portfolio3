# AProject ![AProject Favicon](favicon.ico "AProject Icon")
_Simple website to support CRUD operations on a list of projects._

This work somehow achieved a mark of 97%, with the following being the only reported issues:
- Searching should search for exact text, not loosely like I had assumed.
- Any registered user can modify any project. 

... And that was it! Considering this was my first time using PHP, I'm happy I did so well!

## Deployment
This website is currently not being deployed on the Aston servers. It has been replaced by MIRЯOR, and you can go to that repo [by clicking here!](https://github.com/MIRROR-Team18/mvp "MIRЯOR (MVP)")

## Running Locally
Here I used the XAMPP stack, so you will achieve the best results if you do the same. Simply:
1. Get XAMPP [here](https://www.apachefriends.org/ "Apache Friends"). Just be careful not to download unnecessary extras.
2. Place this folder inside of htdocs, which is located in the XAMPP folder. By default this will be `C:/xampp/htdocs/`
3. Run Apache and MySQL from the XAMPP Control Panel.
4. Click on the `Admin` button next to MySQL. This will take you to phpMyAdmin.
5. Import the database file that's also in this repo, [this one](u_220134662_db.sql "u_220134662_db.sql").
6. You should now be able to use the website!

_Please remember to import the database, the website will not do this by itself!_
