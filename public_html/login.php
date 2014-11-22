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

getConnection();

$dbversion = -1;
$nbr = 0;
if ($r=mysqli_query($GLOBALS["___mysqli_ston"], "SHOW TABLES LIKE 'settings'")) {
    $nbr=mysqli_num_rows($r);
}
if ($nbr == 0) {
    $dbversion = 0;
    // initial upgrade of database if needed. Will add the dbversion 0 setting.
    include "../db/upgrade/$dbversion.db";
}

$r = mysqli_query(
    $GLOBALS["___mysqli_ston"],
    "SELECT option_value FROM settings
    WHERE option_name = 'db-version'"
);
if (mysqli_num_rows($r) == 1) {
    $a = mysqli_fetch_assoc($r);

    $dbversion = $a["option_value"];

    $upgrades = glob("../db/upgrade/*.db");
    foreach ($upgrades as $file) {
        if (!preg_match("/^\.\.\/db\/upgrade\/([0-9]+)\.db$/", $file, $availableupgrade)) {
            continue;
        }
        if ($availableupgrade[1] <= $dbversion) {
            // Not applicable, this is already applied.
            continue;
        } else {
            include $file;
            mysqli_query(
                "UPDATE settings SET option_value='".$availableupgrade[1]."'
                WHERE option_name='db-version'"
            );
        }
    }
} else {
    exit("<h1>ERROR: Database version missing in settings. Contact a database administrator for assistance.</h1>");
}
?>
<div class="logindivs" id="logindivleft">
	<form name="student" method="post">
    	<input type="hidden" readonly value="StudentLogin" name="handler" />
        <h2>Studentinlogg</h2>
        <div class="field_holder">
            <label>Akronym:</label>
            <input name="akronym" type="text" value="Student Login Disabled" tabindex="1" disabled ><br>
            <label>Lösenord:</label>
            <input name="pass1" type="password" tabindex="2" disabled >
        </div>
        <div class="right_align">
			<input type="submit" value="Logga in" disabled >
        </div>
    </form>
</div>
<div class="logindivs">
    <form name="admin" method="post">
    	<input type="hidden" readonly value="AdminLogin" name="handler" />
        <h2>Administratörinlogg</h2>
        <div class="field_holder">
            <label>Användarnamn:</label>
            <input name="username" type="text" tabindex="3"><br>
            <label>Lösenord:</label>
            <input name="pass2" type="password" tabindex="4">
        </div>
        <div class="right_align">
	        <input type="submit" value="Logga in">
        </div>
	</form>
</div>
