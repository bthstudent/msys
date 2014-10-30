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
$fees = getFees();
$periods = getPeriods();
$membershiptypes = getMembershiptypes();
$periodid = -1;
$membershiptype_id = -1;
$visibility = "style=\"visibility:hidden\"";
$membertypevisibility = "style=\"visibility:hidden\"";
$feeid = -1;
$fee = 0;
if (isset($_GET["periodid"]) && $_GET["periodid"] > 0) {
    $DBH = new DB();
    $periodid = $_GET["periodid"];
    $membertypevisibility = "";
    if (isset($_GET["membershiptype_id"]) && $_GET["membershiptype_id"] > 0) {
        $membershiptype_id = $_GET["membershiptype_id"];
        $visibility = "";

        $DBH->query("SELECT id, fee FROM fee
                    WHERE period_id=:pid AND
                    membershiptype_id=:mtid");
        $DBH->bind(":pid", $periodid);
        $DBH->bind(":mtid", $membershiptype_id);
        $r = $DBH->resultset();
        if($DBH->rowCount() > 0){
            $feeid = $r[0]['id'];
            $fee = $r[0]['fee'];
        }
    }
}

echo "<form name=\"fee\" method=\"post\">\n";
echo "    <input type=\"hidden\" readonly=\"readonly\" value=\"ChangeFee\" name=\"handler\" />
    <table>
        <tr class=\"toptr\">
            <td><p>Period</p></td>
            <td><p>Medlemstyp</p></td>
            <td><p>Avgift</p></td>
            <td><p $visibility>Spara ändring</p></td>
        </tr>
        <tr>
            <td>
                <select name=\"period_id\" onchange=\"window.location = '?page=fees&amp;periodid='+(document.forms.fee.period_id[document.forms.fee.period_id.selectedIndex].value);\">
                    <option value=\"-1\">Ange period</option>\n";
foreach ($periods as $period) {
    if (strtotime($period["first"]) > date("U")) {
        $selected = "";
        if ($period["id"] == $periodid) {
            $selected = "SELECTED";
        }
        echo "                    <option value=\"".$period["id"]."\" $selected>".$period["period"]."</option>\n";
    }
}
echo"                </select>
            </td>
            <td>
                <select $membertypevisibility name=\"membershiptype_id\" onchange=\"window.location = '?page=fees&amp;periodid=$periodid&amp;membershiptype_id='+(document.forms.fee.membershiptype_id[document.forms.fee.membershiptype_id.selectedIndex].value);\">
                    <option value=\"-1\">Ange medlemstyp</option>\n";
foreach ($membershiptypes as $id => $membershiptype) {
    $selected = "";
    if ($membershiptype["id"] == $membershiptype_id) {
        $selected = "SELECTED";
    }
    echo "                    <option value=\"".$membershiptype["id"]."\" $selected>".$membershiptype["naming"]."</option>\n";
}


echo "                </select>
            </td>
            <td>
                <input type=\"hidden\" name=\"feeid\" value=\"$feeid\" />
                <input $visibility type=\"text\" name=\"fee\" size=\"10\" value=\"$fee\" />
            </td>
            <td>
                <img src=\"misc/save.png\" $visibility class=\"cursor\" onclick=\"document.forms['fee'].submit();\" />
            </td>
        </tr>
    </table>
</form>";
putBoxEnd();
?>
