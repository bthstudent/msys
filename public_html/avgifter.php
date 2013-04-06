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
putBoxStart();
?>
<h2>Avgifter</h2>
<table>
    <tr class="toptr">
	    <td>Period</td>
        <td>Helavgift</td>
        <td>Halvavgift</td>
        <td>Stödavgift</td>
        <td>Spara ändring</td>
    </tr>
</table>
<?php
$avgifter = getAvgifter();
$i=$j=$k=0;
$today = strtotime(date("Y-m-d"));
$validcolor = "#c7ffc7";
$pastcolor = "#ffc7c7";
foreach ($avgifter as $rad) {
    $forst = strtotime($rad->forst);
    $sist = strtotime($rad->sist);
    if ($forst < $today && $today < $sist) {
        $i=1;
    } elseif ($sist < $today) {
        $i=-1;
    } else {
        $i=0;
    }
    echo "<form name=\"changeavgift" . $k . "\" method=\"post\">";
    echo "<input type=\"hidden\" readonly=\"readonly\" value=\"ChangeAvgift\" name=\"handler\" />";
    echo "<table><tr ";
    if ($i==1) {
        echo "bgcolor=\"" . $validcolor . "\"";
    } elseif ($i==-1) {
        echo "bgcolor=\"" . $pastcolor . "\"";
    }
    echo ">";
    echo "<td><input name=\"period\" value=\"" . $rad->period . "\" type=\"text\" readonly=\"readonly\" /></td>\n";
    echo "<td><input name=\"avg1\" type=\"number\" value=\"" . $rad->avgift[1] . "\" /></td>\n";
    echo "<td><input name=\"avg2\" type=\"number\" value=\"" . $rad->avgift[2] . "\" /></td>\n";
    echo "<td><input name=\"avg3\" type=\"number\" value=\"" . $rad->avgift[3] . "\" /></td>\n";
    echo "<td><img src=\"misc/save.png\" ";
    if ($i==1 || $i==-1) {
        echo "style=\"visibility:hidden\" ";
    }
    echo "class=\"cursor\" onclick=\"document.forms['changeavgift" . $k++ . "'].submit();\" /></td>\n";
    echo "</tr>";
    echo "</table></form>";
}
putBoxEnd();
?>
