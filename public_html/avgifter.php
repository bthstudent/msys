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
<?php
$avgifter = getAvgifter();
$perioder = getPeriods();
$medlemstyper = getMedlemstyper();
$periodid = -1;
$medlemstypid = -1;
$visibility = "style=\"visibility:hidden\"";
$medlemstypvisibility = "style=\"visibility:hidden\"";
$avgiftid = -1;
$avgift = 0;
if (isset($_GET["periodid"]) && $_GET["periodid"] > 0) {
    getConnection();

    $periodid = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_GET["periodid"]);
    $medlemstypvisibility = "";
    if (isset($_GET["medlemstypid"]) && $_GET["medlemstypid"] > 0) {
        $medlemstypid = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_GET["medlemstypid"]);
        $visibility = "";

        $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, avgift FROM avgift
                          WHERE perioder_id=".$periodid." AND
                          medlemstyp_id=".$medlemstypid."");
        if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) > 0) {
            $a = mysqli_fetch_assoc($r);
            $avgiftid = $a["id"];
            $avgift = $a["avgift"];
        }
    }
}

echo "<form name=\"avgift\" method=\"post\">\n";
echo "    <input type=\"hidden\" readonly=\"readonly\" value=\"ChangeAvgift\" name=\"handler\" />
    <table>
        <tr class=\"toptr\">
            <td><p>Period</p></td>
            <td><p>Medlemstyp</p></td>
            <td><p>Avgift</p></td>
            <td><p $visibility>Spara ändring</p></td>
        </tr>
        <tr>
            <td>
                <select name=\"period_id\" onchange=\"window.location = '?page=avgifter&amp;periodid='+(document.forms.avgift.period_id[document.forms.avgift.period_id.selectedIndex].value);\">
                    <option value=\"-1\">Ange period</option>\n";
foreach ($perioder as $period) {
    if (strtotime($period->forst) > date("U")) {
        $selected = "";
        if ($period->id == $periodid) {
            $selected = "SELECTED";
        }
        echo "                    <option value=\"".$period->id."\" $selected>".$period->period."</option>\n";
    }
}
echo"                </select>
            </td>
            <td>
                <select $medlemstypvisibility name=\"medlemstyp_id\" onchange=\"window.location = '?page=avgifter&amp;periodid=$periodid&amp;medlemstypid='+(document.forms.avgift.medlemstyp_id[document.forms.avgift.medlemstyp_id.selectedIndex].value);\">
                    <option value=\"-1\">Ange medlemstyp</option>\n";
foreach ($medlemstyper as $id => $medlemstyp) {
    $selected = "";
    if ($id == $medlemstypid) {
        $selected = "SELECTED";
    }
    echo "                    <option value=\"".$id."\" $selected>".$medlemstyp."</option>\n";
}


echo "                </select>
            </td>
            <td>
                <input type=\"hidden\" name=\"avgiftid\" value=\"$avgiftid\" />
                <input $visibility type=\"text\" name=\"avgiften\" size=\"10\" value=\"$avgift\" />
            </td>
            <td>
                <img src=\"misc/save.png\" $visibility class=\"cursor\" onclick=\"document.forms['avgift'].submit();\" />
            </td>
        </tr>
    </table>
</form>";
putBoxEnd();
?>
