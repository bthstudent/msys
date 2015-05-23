<?php
/**
    The membership tracker system.
    Copyright © 2012-2013 Blekinge studentkår <sis@bthstudent.se>
    Copyright © 2013-2015 Martin Bagge <brother@bsnet.se>

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
	<h2>Lägg till ny typ av betalning</h2>
	<form name="AddPaymentType" class="info" method="post">
		<input type="hidden" readonly="readonly" value="AddPaymentType" name="handler" />
		<input type="text" name="paymenttype" />
		<input type="submit" value="Lägg till betalväg">
	</form>
</div>

<div class="outerdivs">
	<h2>Nuvarande betalvägar</h2>
<?php
// true as parameter to also fetch hidden types.
$types = getPaymentway(true);
foreach ($types as $row) {
    echo "<div style=\"text-align: left; border-top:1px solid black; margin-bottom: 5px;\">";
    echo "<span style=\"font-size: 20px;\">" . $row["naming"] . "</span>";

    if (isset($_POST["handler"]) && $_POST["handler"] == "EditPaymentTypeForm" && $row["id"] == $_POST["id"]) {
        echo "
	<div style=\"text-align: right;\">
		<form name=\"updatePaymentTypeForm\" class=\"info\" method=\"post\">
			<input type=\"text\" value=\"\" name=\"newlabel\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"EditPaymentType\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"ptid\"/>
			<input type=\"submit\" value=\"Byt namn\">
			<a href=\"?page=paymenttype\">Avbryt</a>
		</form>
	</div>";
        continue;
    }

    echo "
	<div style=\"text-align: right;\">
		<form name=\"EditPayment\" class=\"info\" method=\"post\">
			<input type=\"hidden\" readonly=\"readonly\" value=\"EditPaymentTypeForm\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"id\"/>
			<input type=\"submit\" value=\"Ändra namn\">
		</form>
	</div>";
    $active="Inaktivera";
    if ($row["deleted"] == 1) { $active = "Aktivera"; }
    echo "
	<div style=\"text-align: right;\">
		<form name=\"TogglePayment\" class=\"info\" method=\"post\">
			<input type=\"hidden\" readonly=\"readonly\" value=\"TogglePaymentType\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"id\"/>
			<input type=\"submit\" value=\"$active\">
		</form>
	</div>";
    echo "</div>";
}
?>
</div>
