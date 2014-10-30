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
require_once "functions.php";
putBoxStart();
if (isset($_GET["ssn"])) {
    $result = findPNR($_GET["ssn"]);
} elseif (isset($_GET["fnm"])) {
    if (isset($_GET["lnm"])) {
        $result = findNM($_GET["fnm"], $_GET["lnm"]);
    } else {
        $result = findFNM($_GET["fnm"]);
    }
} elseif (isset($_GET["lnm"])) {
    $result = findLNM($_GET["lnm"]);
} elseif (isset($_GET["email"])) {
    $result = findEMA($_GET["email"]);
}
if ($result) {
    $bgcolor[] = "e7e7e7";
    $bgcolor[] = "ffffff";
    $hovercolor = "#d7d7ff";
    $validcolor = "#d7ffd7";

    echo "<h2>Sökresultat</h2>
    <table>
        <tr class=\"toptr\">
            <td>Personnr</td>
            <td>Förnamn</td>
            <td>Efternamn</td>
            <td>Adress</td>
            <td>Postnr</td>
            <td>Postort</td>
            <td>Epost</td>
        </tr>";
    $i = 0;
    foreach ($result as $value) {
        $color = $bgcolor[$i%2];
        echo "        <tr bgcolor='" . $color . "' onmouseover=\"this.bgColor='" . $hovercolor . "'; this.className='cursor';\" onmouseout=\"this.bgColor='" . $color . "'\" onclick=\" location.href='?page=member&amp;id=" . $value["id"] . "'\">
            <td>" . $value["ssn"] . "</td>
            <td>" . $value["firstname"] . "</td>
            <td>" . $value["lastname"] . "</td>
            <td>" . $value["address"] . "</td>
            <td>" . $value["postalnr"] . "</td>
            <td>" . $value["city"] . "</td>
            <td>" . $value["email"] . "</td>
        </tr>";
        $i++;
    }
    echo "</table>";
} else {
    echo "Inget resultat kunde hittas.";
}
putBoxEnd();
?>
