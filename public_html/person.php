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
getConnection();
$result = getPerson($_GET["id"]);
echo "<h2>Personuppgifter</h2>";
if ($result) {
    echo "<form name=\"Person\" class=\"info\" method=\"post\">
			<input type=\"hidden\" readonly=\"readonly\" value=\"ChangePerson\" name=\"handler\" />
			<input name=\"ID\" readonly=\"readonly\" type=\"hidden\" value=\"" . $result->id . "\">
			<input name=\"url\" readonly=\"readonly\" type=\"hidden\" value=\"" . "?" . $_SERVER['QUERY_STRING'] . "\">
			<table>
				<tr>
					<td>Personnr:</td>
					<td><input type=\"text\" name=\"PNR\" value=\"" . $result->personnr . "\" tabindex=\"1\"/></td>
					<td>C/O:</td>
					<td><input type=\"text\" name=\"CO\" value=\"" . $result->co . "\" tabindex=\"4\" /></td>
					<td class=\"right_col\">Fel Adress:</td>
					<td class=\"right_col\"><input type=\"checkbox\" name=\"FELADR\" value=\"1\" onclick=\"document.forms['Person'].submit()\" ";
    if ($result->feladress)  echo "checked";
    echo			" tabindex=\"12\" /></td>
				</tr>
				<tr>
					<td>Förnamn:</td>
					<td><input type=\"text\" name=\"FNM\" value=\"" . $result->fornamn . "\" tabindex=\"2\"/></td>
					<td>Adress:</td>
					<td><input type=\"text\" name=\"ADR\" value=\"" . $result->adress . "\" tabindex=\"5\" /></td>
					<td>Avisera ej:</td>
					<td><input type=\"checkbox\" name=\"AVISEJ\" value=\"1\" onclick=\"document.forms['Person'].submit()\" ";
    if ($result->aviseraej == 1) echo "checked";
    echo 			" tabindex=\"13\" /></td>
				</tr>
				<tr>
					<td>Efternamn:</td>
					<td><input type=\"text\" name=\"ENM\" value=\"" . $result->efternamn . "\" tabindex=\"3\"/></td>
					<td>Postnummer:</td>
					<td><input type=\"text\" name=\"PSTNR\" value=\"" . $result->postnr . "\" tabindex=\"6\" /></td>
					<td>Senast ändrad</td>
					<td>" . $result->senastandrad . "</td>
				</tr>
				<tr>
					<td>Telefon:</td>
					<td><input type=\"text\" name=\"TEL\" value=\"" . $result->telefon . "\" tabindex=\"10\" /></td>
					<td>Ort:</td>
					<td><input type=\"text\" name=\"ORT\" value=\"" . $result->ort . "\" tabindex=\"7\" /></td>
				</tr>
				<tr>
					<td>Epost:</td>
					<td><input type=\"text\" name=\"EMAIL\" value=\"" . $result->epost . "\" tabindex=\"11\" /></td>
					<td>Land:</td>
					<td><input type=\"text\" name=\"LAND\" value=\"" . $result->land . "\" tabindex=\"8\" /></td>
				</tr>
			</table>
			<div style=\"text-align:right\">
				<input type=\"submit\" value=\"Spara ändringar\">
			</div>
		  </form>";

    echo "<form name=\"Whatever\" class=\"info\" method=\"post\">
		  <input type=\"hidden\" readonly=\"readonly\" value=\"RemovePerson\" name=\"handler\" />
		  <input name=\"ID\" readonly=\"readonly\" type=\"hidden\" value=\"" . $_GET['id'] . "\">
		  <div style=\"text-align:right\">
		  <input type=\"submit\" value=\"Ta Bort Högvördige Medlem\">
		  </div>
		  </form>";
    putBoxEnd();
    // move to CSS
    $bgcolor[] = "e7e7e7";
    $bgcolor[] = "ffffff";
    $hovercolor = "#d7d7ff";
    $validcolor = "#d7ffd7";
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
            $forst = strtotime($value->forst);
            $sist = strtotime($value->sist);
            if (!($forst < $today && $today < $sist)) {
                $color = $bgcolor[$i%2];
            } else {
                $color = $validcolor;
            }
            echo "<tr bgcolor=\"" . $color . "\" onmouseover=\"this.bgColor='" . $hovercolor . "'\" onmouseout=\"this.bgColor='" . $color . "'\">
                    <td>" . $value->period . "</td>
                    <td>" . $value->benamning . "</td>
                    <td>" . $value->avgift . "</td>
                    <td>" . $value->betalat . "</td>
                    <td>" . $value->betaldatum . "</td>
                    <td>" . $value->betalsatt . "</td>
                    <td><form name=\"RemovePayment\" class=\"info\" method=\"post\">
  						<input type=\"hidden\" readonly=\"readonly\" value=\"RemovePayment\" name=\"handler\" />
    					<input type=\"hidden\" readonly=\"readonly\" value=\"" . $_GET['id'] . "\" name=\"id\"/>
    					<input type=\"hidden\" readonly=\"readonly\" value=\"" . $value->period . "\" name=\"per\"/>
    					<input type=\"hidden\" readonly=\"readonly\" value=\"" . $value->betalsatt . "\" name=\"bets\"/>
    					<div style=\"text-align:right;float:right\">
    						<input type=\"submit\" value=\"X\">
    					</div></td>
                 </tr>";
            $i++;
        }
    } else {
        $color = $bgcolor[$i%2];
        echo "<tr bgcolor=\"" . $color . "\" onmouseover=\"this.bgColor='" . $hovercolor . "'\" onmouseout=\"this.bgColor='" . $color . "'\">
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                 </tr>";
    }
    echo "</table>
                <form style=\"margin:0\" name=\"Betalning\" action=\"?page=nybetalning&amp;pnr=" . $_GET['pnr'] . "\" method=\"post\">
                <input name=\"url\" readonly=\"readonly\" type=\"hidden\" value=\"" . "?" . $_SERVER['QUERY_STRING'] . "\">
			<div style=\"text-align:right\">
				<input type=\"submit\" value=\"Registrera betalning\">
			</div>
		</form>";
    putBoxEnd();

    putBoxStart();
    echo"<h2>Uppdrag</h2>
		 <table>
			<tr class=\"toptr\">
				<td>Period</td>
				<td>Uppdrag</td>
				<td>Beskrivning</td>
			</tr>";
    $result = getMandates($_GET['id']);
    $i = 0;
    if ($result) {
        $today = strtotime(date("Y-m-d"));
        foreach ($result as $value) {
            $forst = strtotime($value->Forst);
            $sist = strtotime($value->Sist);
            if (!($forst < $today && $today < $sist)) {
                $color = $bgcolor[$i%2];
            } else {
                $color = $validcolor;
            }
            echo "<tr bgcolor=\"" . $color . "\" onmouseover=\"this.bgColor=" . $hovercolor . "'\" onmouseout=\"this.bgColor='" . $color . "'\">
					<td>" . $value->Period . "</td>
					<td>" . $value->Benamning . "</td>
					<td>" . $value->Beskrivning . "</td>
				  </tr>";
            $i++;
        }
    } else {
        echo "<tr bgcolor=\"" . $bgcolor[$i%2] . "\" onmouseover=\"this.bgColor=" . $hovercolor . "'\" onmouseout=\"this.bgColor='" . $bgcolor[$i%2] . "'\">
				<td>-</td>
				<td>-</td>
				<td>-</td>
			  </tr>";
    }
    echo "</table>";
} else {
    $result = getPerson($_GET["id"],true);
    if($result)
    {
        echo "Personen är borttagen";
    }
    else
    {
        echo "Ingen person kunde hittas.";
    }
}
putBoxEnd();
?>
