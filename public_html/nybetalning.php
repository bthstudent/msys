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
		<input type="hidden" readonly="readonly" value="<?php echo $_GET['id']; ?>" name="ID" />
        <table>
            <tr>
                <td colspan="2">
                    <a href="?page=person&id=<?php echo $_GET['id']; ?>">
                        Gå tillbaka till
                        <?php 
                            $person = getPerson($_GET['id']);
                            echo $person->fornamn . " " . $person->efternamn;
                        ?>
                    </a>
                </td>
                <td>Betaldatum:</td>
                <td><input type="text" name="BETDATE" value="<?php echo date("Y-m-d");?>"/></td>
            </tr>
            <tr>
                <td>Period:</td>
                <td><select name="PERIOD">
<?php
$perioder = getPeriods();
foreach ($perioder as $rad) {
    if($rad->sist > date("Y-m-d"))
    {
        echo "<option value=\"" . $rad->id . "\">" . $rad->period . "</option>\n";
    }
}
?>
				</select></td>
				<td>Medlemstyp:</td>
				<td><select name="MEDTYPE">
<?php
$medlemstyp = getMedlemstyper();
foreach ($medlemstyp as $id => $benamning) {
    echo "<option value=\"" . $id . "\">" . $benamning . "</option>\n";
}
?>
				</select></td>
			</tr>
			<tr>
				<td>Betalsätt:</td>
				<td><select name = "BETWAY">
<?
$betalsatt = getBetalsatt();
foreach ($betalsatt as $id => $benamning) {
    echo "<option value=\"" . $id . "\">" . $benamning . "</option>\n";
}
?>
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