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
	<h2>Lägg till person</h2>
	<form name="addPerson" class="info" method="post">
		<input type="hidden" readonly="readonly" value="AddPerson" name="handler" />
		<table>
			<tr>
				<td>Personnr:</td>
				<td><input type="text" name="PNR" /></td>
				<td>C/O:</td>
				<td><input type="text" name="CO" /></td>
				<td class="right_col">Fel Adress:</td>
				<td class="right_col"><input type="checkbox" name="FELADR" value="1" /></td>
			</tr>
			<tr>
				<td>F&ouml;rnamn:</td>
				<td><input type="text" name="FNM" /></td>
				<td>Adress:</td>
				<td><input type="text" name="ADR" /></td>
				<td>Avisera ej:</td>
				<td><input type="checkbox" name="AVISEJ" value="1" /></td>
			</tr>
			<tr>
				<td>Efternamn:</td>
				<td><input type="text" name="ENM" /></td>
				<td>Postnummer:</td>
				<td><input type="text" name="PSTNR" /></td>
			</tr>
			<tr>
				<td>Telefon:</td>
				<td><input type="text" name="TEL" /></td>
				<td>Ort:</td>
				<td><input type="text" name="ORT" /></td>
			</tr>
			<tr>
				<td>Epost:</td>
				<td><input type="text" name="EMAIL" /></td>
				<td>Land:</td>
				<td><input type="text" name="LAND" /></td>
			</tr>
		</table>
		<div style="text-align:right">
		<input type="submit" value="Lägg till person">
		</div>
	</form>
</div>
