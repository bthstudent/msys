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
	<h2>Lägg till APIanvändare</h2>
	<form name="AddAPIUser" class="info" method="post">
		<input type="hidden" readonly="readonly" value="AddAPIUser" name="handler" />
		<table>
			<tr>
				<td>Användarnamn:</td>
				<td><input type="text" name="USR" /></td>
				<td>API-nyckel:</td>
				<td><input type="text" name="KEY" /></td>
			</tr>
			<tr>
				<td>Rättigheter</td>
				<td>
					<input type="checkbox" name="getPerson" value=1/> Hämta Personinformation <br />
					<input type="checkbox" name="setPerson" value=2/> Editera Personinformation <br />
					<input type="checkbox" name="regPayment" value=4 /> Registera Betalningar <br />
					<input type="checkbox" name="regPerson" value=8 /> Registera Personer <br />
					<input type="checkbox" name="isMember" value=16 /> Kontrollera medlemskap <br />
				</td>
			</tr>
		</table>
		<div style="text-align:right">
		<input type="submit" value="Lägg till API-användare">
		</div>
	</form>
</div>

<div class="outerdivs">
	<h2>Lista över API-konton</h2>
<?php
$users = getAPIUsers();

foreach ($users as $row) {
    echo "<hr />";
    echo "<div style=\"text-align:left;float:left\">";
    echo "<p style=\"font-size:18px\">Användarnamn: " . $row->username . "<br>Permissions: " . $row->permissions . "<br>API-Nyckel: ";
    echo "<input type=\"text\" value=\"". $row->apikey . "\" readonly float:right/>" . "</p>";
    echo "</div>";
    echo "<form name=\"RemoveAPIUser\" class=\"info\" method=\"post\">
        <input type=\"hidden\" readonly=\"readonly\" value=\"RemoveAPIUser\" name=\"handler\" />
        <input type=\"hidden\" readonly=\"readonly\" value=\"" . $row->username . "\" name=\"USR\"/>
        <div style=\"text-align:right;float:right\">
            <input type=\"submit\" value=\"Ta Bort API-Användare\">
        </div>
    </form>";
    echo "<br /><br /><br /><br /><br /><br />";
}
?>
</div>
