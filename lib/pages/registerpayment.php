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
                    <a href="?page=member&id=<?php echo $_GET['id']; ?>">
                        Gå tillbaka till
                        <?php 
                            $member = getMember($_GET['id']);
                            echo $member["firstname"] . " " . $member["lastname"];
                        ?>
                    </a>
                </td>
                <td>Betaldatum:</td>
                <td><input type="text" name="PAYDATE" value="<?php echo date("Y-m-d");?>"/></td>
            </tr>
            <tr>
                <td>Period:</td>
                <td><select name="PERIOD">
<?php
$periods = getPeriods();
foreach ($periods as $row) {
    if ($row["last"] > date("Y-m-d")) {
        echo "<option value=\"" . $row["id"] . "\">" . $row["period"] . "</option>\n";
    }
}
?>
				</select></td>
				<td>Medlemstyp:</td>
				<td><select name="MEMTYPE">
<?php
$membershiptypes = getMembershiptypes(false);
foreach ($membershiptypes as $membershiptype) {
    echo "<option value=\"" . $membershiptype["id"] . "\">" . $membershiptype["naming"] . "</option>\n";
}
?>
				</select></td>
			</tr>
			<tr>
				<td>Betalsätt:</td>
				<td><select name = "PAYWAY">
<?php
$betalsatt = getPaymentway(false);
foreach ($betalsatt as $satt) {
    echo "<option value=\"" . $satt["id"] . "\">" . $satt["naming"] . "</option>\n";
}
?>
				</select></td>
				<td>Betalat:</td>
				<td><input type="text" name="PAID" \></td>
			</tr>

		</table>
		<div style="text-align:right">
		<input type="submit" value="Registrera Betalning">
		</div>
	</form>
</div>