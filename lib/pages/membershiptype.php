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
	<h2>Lägg till ny typ av medlemsskap</h2>
	<form name="AddMembershipType" class="info" method="post">
		<input type="hidden" readonly="readonly" value="AddMembershipType" name="handler" />
		<input type="text" name="membershiptype" />
		<input type="submit" value="Lägg till medlemsskap">
	</form>
</div>

<div class="outerdivs">
	<h2>Nuvarande medlemsskap</h2>
<?php
$types = getMembershiptypes();
foreach ($types as $row) {
    echo "<div style=\"text-align: left; border-top:1px solid black; margin-bottom: 5px;\">";
    echo "<span style=\"font-size: 20px;\">" . $row["naming"] . "</span>";

    if (isset($_POST["handler"]) && $_POST["handler"] == "EditMembershipTypeForm" && $row["id"] == $_POST["id"]) {
        echo "
	<div style=\"text-align: right;\">
		<form name=\"updateMembershipTypeForm\" class=\"info\" method=\"post\">
			<input type=\"text\" value=\"\" name=\"newlabel\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"EditMembershipType\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"mbtid\"/>
			<input type=\"submit\" value=\"Byt namn\">
			<a href=\"?page=membershiptype\">Avbryt</a>
		</form>
	</div>";
        continue;
    }

    echo "
	<div style=\"text-align: right;\">
		<form name=\"EditMembership\" class=\"info\" method=\"post\">
			<input type=\"hidden\" readonly=\"readonly\" value=\"EditMembershipTypeForm\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"id\"/>
			<input type=\"submit\" value=\"Ändra namn\">
		</form>
	</div>";
    echo "</div>";
}
?>
</div>
