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
	<h2>Registrera Betalning</h2>
	<form name="addPayment" class="info" method="post">
		<input type="hidden" readonly="readonly" value="addPayment" name="handler" />
		<table>
			<tr>
				<td>Personnr:</td>
				<td><input type="text" name="PNR" value="<?php echo $_GET['pnr']; ?>" readonly /></td>
				<td>Betaldatum:</td>
				<td><input type="text" name="BETDATE" value="<?=date("Y-m-d");?>"/></td>
			</tr>
			<tr>
				<td>Period:</td>
				<td><select name="PERIOD">
<?php
$perioder = getPeriods();
foreach ($perioder as $rad) {
    echo "<option value=\"" . $rad->period . "\">" . $rad->period . "</option>";
}
?>
				</select></td>
				<td>Medlemstyp:</td>
				<td><select name="MEDTYPE">
<?php
$medlemstyp = getMedlemstyper();
foreach ($medlemstyp as $rad) {
    echo "<option value=\"" . $rad->id . "\">" . $rad->benamning . "</option>";
}
?>
				</select></td>
			</tr>
			<tr>
				<td>Betalsätt:</td>
				<td><select name = "BETWAY">
					<option value = "Konto">Konto</option>
					<option value = "Kassa">Kassa</option>
					<option value = "Online">Online</option>
				</select></td>
				<td>Betalat:</td>
				<td><input type="text" name="BET" \></td>
			</tr>

		</table>
		<div style="text-align:right">
		<input type="submit" value="Registrera Betalning">
		</div>
	</form>
</div>