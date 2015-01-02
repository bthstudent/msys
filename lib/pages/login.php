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

$DBH = new DB();
$DBH->query("SHOW TABLES LIKE 'settings'");
$DBH->execute();
$dbversion = -1;
if ($DBH->rowCount()!=1) {
    $dbversion = 0;
    // initial upgrade of database if needed. Will add the dbversion 0 setting.
    include "../db/upgrade/$dbversion.db";
}

$DBH->query("SELECT option_value FROM settings WHERE option_name = 'db-version'");
$res = $DBH->single();
if ($DBH->rowCount()==1) {
    $dbversion = $res["option_value"];

    $upgrades = glob("../db/upgrade/*.db");
    require "../db/functions.php";
    foreach ($upgrades as $file) {
        if (!preg_match("/^\.\.\/db\/upgrade\/([0-9]+)\.db$/", $file, $availableupgrade)) {
            continue;
        }
        if ($availableupgrade[1] <= $dbversion) {
            // Not applicable, this is already applied.
            continue;
        } else {
            include $file;
            $DBH = new DB();
            $DBH->query("UPDATE settings SET option_value=:version WHERE option_name='db-version'");
            $DBH->bind(":version", $availableupgrade[1]);
            $DBH->execute();
        }
    }
} else {
    exit("<h1>ERROR: Database version missing in settings. Contact a database administrator for assistance.</h1>");
}
?>
<div class="logindivs">
    <form name="admin" method="post">
    	<input type="hidden" readonly value="AdminLogin" name="handler" />
        <h2>Administratörinlogg</h2>
        <div class="field_holder">
            <label>Användarnamn:</label>
            <input name="username" type="text" tabindex="3"><br>
            <label>Lösenord:</label>
            <input name="pass" type="password" tabindex="4">
        </div>
        <div class="right_align">
	        <input type="submit" value="Logga in">
        </div>
	</form>
</div>
