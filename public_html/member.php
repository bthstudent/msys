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
require_once "functions.php";
putBoxStart();
$result = getMember($_GET["id"]);
echo "<h2>Medlemsuppgifter</h2>";
if ($result) {
    echo "<form name=\"Member\" class=\"info\" method=\"post\">
			<input type=\"hidden\" readonly=\"readonly\" value=\"ChangeMember\" name=\"handler\" />
			<input name=\"ID\" readonly=\"readonly\" type=\"hidden\" value=\"" . $result["id"] . "\">
			<input name=\"url\" readonly=\"readonly\" type=\"hidden\" value=\"" . "?" . $_SERVER['QUERY_STRING'] . "\">
			<table>
				<tr>
					<td>Personnr:</td>
					<td><input type=\"text\" name=\"SSN\" value=\"" . $result["ssn"] . "\" tabindex=\"1\"/></td>
					<td>C/O:</td>
					<td><input type=\"text\" name=\"CO\" value=\"" . $result["co"] . "\" tabindex=\"4\" /></td>
					<td class=\"right_col\">Fel Adress:</td>
					<td class=\"right_col\"><input type=\"checkbox\" name=\"FELADR\" value=\"1\" onclick=\"document.forms['Member'].submit()\" ";
    if ($result["wrongaddress"]) {
        echo "checked";
    }
    echo			" tabindex=\"12\" /></td>
				</tr>
				<tr>
					<td>Förnamn:</td>
					<td><input type=\"text\" name=\"FNM\" value=\"" . $result["firstname"] . "\" tabindex=\"2\"/></td>
					<td>Adress:</td>
					<td><input type=\"text\" name=\"ADDR\" value=\"" . $result["address"] . "\" tabindex=\"5\" /></td>
					<td>Avisera ej:</td>
					<td><input type=\"checkbox\" name=\"DONOTAD\" value=\"1\" onclick=\"document.forms['Member'].submit()\" ";
    if ($result["donotadvertise"] == 1) {
        echo "checked";
    }
    echo 			" tabindex=\"13\" /></td>
				</tr>
				<tr>
					<td>Efternamn:</td>
					<td><input type=\"text\" name=\"LNM\" value=\"" . $result["lastname"] . "\" tabindex=\"3\"/></td>
					<td>Postnummer:</td>
					<td><input type=\"text\" name=\"PSTNR\" value=\"" . $result["postalnr"] . "\" tabindex=\"6\" /></td>
					<td>Senast ändrad</td>
					<td>" . $result["lastedit"] . "</td>
				</tr>
				<tr>
					<td>Telefon:</td>
					<td><input type=\"text\" name=\"PHO\" value=\"" . $result["phone"] . "\" tabindex=\"10\" /></td>
					<td>Ort:</td>
					<td><input type=\"text\" name=\"CITY\" value=\"" . $result["city"] . "\" tabindex=\"7\" /></td>
				</tr>
				<tr>
					<td>Epost:</td>
					<td><input type=\"text\" name=\"EMAIL\" value=\"" . $result["email"] . "\" tabindex=\"11\" /></td>
					<td>Land:</td>
					<td><input type=\"text\" name=\"COUNTRY\" value=\"" . $result["country"] . "\" tabindex=\"8\" /></td>
				</tr>
			</table>
			<div style=\"text-align:right\">
				<input type=\"submit\" value=\"Spara ändringar\">
			</div>
		  </form>";

    echo "<form name=\"Whatever\" class=\"info\" method=\"post\">
		  <input type=\"hidden\" readonly=\"readonly\" value=\"RemoveMember\" name=\"handler\" />
		  <input name=\"ID\" readonly=\"readonly\" type=\"hidden\" value=\"" . $_GET['id'] . "\">
		  <div style=\"text-align:right\">
		  <input type=\"submit\" value=\"Ta Bort Högvördige Medlem\">
		  </div>
		  </form>";
    putBoxEnd();
    $bgcolor = array("even", "odd");
    putBoxStart();
    echo "<h2>Medlemskap</h2>
            <table>
                <tr class=\"toptr\">
                    <td>Period</td>
                    <td>Medlemstyp</td>
                    <td>Avgift</td>
                    <td>Betalat</td>
                    <td>Betaldatum</td>
                    <td>Betalst&auml;tt</td>
                </tr>";
    $result = getPayments($_GET['id']);
    $i = 0;
    if ($result) {
        $today = strtotime(date("Y-m-d"));
        foreach ($result as $value) {
            $first = strtotime($value["first"]);
            $last = strtotime($value["last"]);
            if (!($first < $today && $today < $last)) {
                $color = $bgcolor[$i%2];
            } else {
                $color = "valid";
            }
            echo "<tr class=\"$color\">
                    <td>" . $value["period"] . "</td>
                    <td>" . $value["membershiptype"] . "</td>
                    <td>" . $value["fee"] . "</td>
                    <td>" . $value["paid"] . "</td>
                    <td>" . $value["paymentdate"] . "</td>
                    <td>" . $value["paymenttype"] . "</td>
                    <td>
                      <form name=\"RemovePayment\" class=\"info\" method=\"post\">
                        <input type=\"hidden\" readonly=\"readonly\" value=\"RemovePayment\" name=\"handler\" />
                        <input type=\"hidden\" readonly=\"readonly\" value=\"" . $value["id"] . "\" name=\"paymentId\"/>
                        <div style=\"text-align:right;float:right\">
                          <input type=\"submit\" value=\"X\">
                        </div>
                      </form>
                   </td>
                 </tr>";
            $i++;
        }
    } else {
        echo "<tr class=\"even\">
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                 </tr>";
    }
    echo "</table>
                <form style=\"margin:0\" name=\"Betalning\" action=\"?page=registerpayment&amp;id=" . $_GET['id'] . "\" method=\"post\">
                <input name=\"url\" readonly=\"readonly\" type=\"hidden\" value=\"" . "?" . $_SERVER['QUERY_STRING'] . "\">
			<div style=\"text-align:right\">
				<input type=\"submit\" value=\"Registrera betalning\">
			</div>
		</form>";
    putBoxEnd();
} else {
    $result = getMember($_GET["id"], true);
    if ($result) {
        echo "Personen är borttagen";
    } else {
        echo "Ingen person kunde hittas.";
    }
}
putBoxEnd();
?>
