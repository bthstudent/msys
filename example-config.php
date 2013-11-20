<?php
/**
    The membership tracker system.
    Copyright © 2012-2013 Blekinge studentkår <sis@bthstudent.se>
    Copyright © 2013 Martin Bagge <brother@bsnet.se>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/* copy to local-config.php and replace values with your things. */
$db["host"] = "sqlhost.example.com";
$db["user"] = "db_user";
$db["pass"] = "db_password";
$db["db"] = "database_name";

/* Default Salt for passwords.
 * Change this to a random string. Your choice!
 * */
$globalsalt = "3f46781d4ad88ad67885122d25a8e47c";

/* Style config, if no other specified default style will be used
 * If multiple stylesheets are used, include these in one master stylesheet
 * eg. master.css
 * */
$customize["style"]["stylesheet"] = "default.css";
$customize["style"]["logo"] = "misc/logga_uggla.png";
$customize["text"]["title"] = "Medlemmar - Blekinge studentkår";
?>
