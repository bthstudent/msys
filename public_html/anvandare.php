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
?>
<div class="outerdivs">
	<h2>Lägg till Användarkonto</h2>
	<form name="AddUser" class="info" method="post">
		<input type="hidden" readonly="readonly" value="AddUser" name="handler" />
		<table>
			<tr>
				<td>Användarnamn:</td>
				<td><input type="text" name="USR" /></td>
				<td>Lösenord:</td>
				<td><input type="password" name="PAS" /></td>
			</tr>
		</table>
		<div style="text-align:right">
		<input type="submit" value="Lägg till användare">
		</div>
	</form>
</div>

<div class="outerdivs">
	<h2>Lista över användarkonton</h2>
<?php
$users = getUsers();
foreach ($users as $row) {
    echo "<hr />";
    echo "<div style=\"text-align:right;float:left\">";
    echo "<p style=\"font-size:20px\">Användarnamn: " . $row->username . "</p>";
    echo "</div>";
    echo "<form name=\"RemoveUser\" class=\"info\" method=\"post\">
    <input type=\"hidden\" readonly=\"readonly\" value=\"RemoveUser\" name=\"handler\" />
    <input type=\"hidden\" readonly=\"readonly\" value=\"" . $row->id . "\" name=\"id\"/>
    <div style=\"text-align:right;float:right\">
    <input type=\"submit\" value=\"Ta Bort Användare\">
    </div>
</form>";
    echo "<br /><br /><br />";
}
?>
</div>
