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
<h2>Perioduppgifter</h2>
<table>
    <tr class="toptr">
        <td>Period</td>
        <td>Startdatum</td>
        <td>Slutdatum</td>
        <td>Spara ändring</td>
    </tr>
</table>
<form name="addperiod" method="post">
<input type="hidden" readonly="readonly" value="AddPeriod" name="handler" />
<table>
    <tr >
        <td id="lefttd"><input id="periodcaps" type="text" maxlength="50" name="period" onchange="this.value = this.value.toUpperCase();" />
        	<input type="button" value="Lägg till ny" onclick="this.style.display='none';
            												   document.forms['addperiod'].period.style.display='inline';
                                                               document.getElementById('hiddenbutton').style.display='inline';
                                                               document.forms['addperiod'].first.disabled = false;
                                                               document.forms['addperiod'].last.disabled = false;"/></td>
        <td><input type="text" name="first" disabled /></td>
        <td><input type="text" name="last" disabled /></td>
        <td><img src="misc/save.png" class="cursor" id="hiddenbutton"
    	onclick="document.forms['addperiod'].submit();" /></td>
    </tr>
</table>
</form>
<?php
$periods = getPeriods();
$i=$j=$k=0;
$today = strtotime(date("Y-m-d"));
$validcolor = "#c7ffc7";
$pastcolor = "#ffc7c7";
foreach ($periods as $row) {
    echo "<form name=\"changeperiod" . $k . "\" method=\"post\"><table>";
    echo "<input type=\"hidden\" readonly=\"readonly\" value=\"ChangePeriod\" name=\"handler\" />";
    $first = strtotime($row["first"]);
    $last = strtotime($row["last"]);
    if ($first < $today && $today < $last) {
        $i = 1;
    } elseif ($last < $today) {
        $i = -1;
    } else {
        $i = 0;
    }
    if ($i==1) {
        echo "<tr bgcolor=\"" . $validcolor . "\">";
    } elseif ($i==-1) {
        echo "<tr bgcolor=\"" . $pastcolor . "\">";
    } else {
        echo "<tr>";
    }
    echo "<td><input name=\"period\" type=\"text\" readonly=\"readonly\" value=\"" . $row["period"] . "\"</td>
            <td><input name=\"first\" type=\"date\" value=\"" . $row["first"] . "\"/></td>
            <td><input name=\"last\" type=\"date\" value=\"" . $row["last"] . "\"/></td>
            <td><img src=\"misc/save.png\" class=\"cursor\" onclick=\"document.forms['changeperiod" . $k++ . "'].submit();\">
            </td>
        </tr></table></form>";
}
?>
<?php putBoxEnd(); ?>
