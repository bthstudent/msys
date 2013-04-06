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
session_start();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="style/main.css" type="text/css">
    <link rel="stylesheet" href="style/login.css" type="text/css">
    <script type="text/javascript" src="script/main.js"></script>
    <?php
        require_once "functions.php";
        handlesession();
        handlepost();
        handlestyle();
        handlejavascript();
    ?>
    <title>Medlem - Blekinge studentkår</title>
</head>
<body>
    <div id="wrapper">
<?php
if ($_SESSION["page"] == "admin") {
    adminpage();
} elseif ($_SESSION["page"] == "student") {
    studentpage();
} else {
    loginpage();
}
?>
    </div>
</body>
</html>
