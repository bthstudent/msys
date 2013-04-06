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
$result = getPerson("");
putBoxStart();
?>
<h2>Personuppgifter</h2>
<form name="Person" method="post">
	<input type="hidden" readonly="readonly" value="SparaStudent" name="handler" />
    <table>
    	<tr>
            <td>Personnr:</td>
            <td><input type="text" name="PNR" readonly="readonly" value="<?php echo $result->PersonNr;?>" tabindex="1"/></td>
            <td>C/O:</td>
            <td><input type="text" name="CO" value="<?php echo $result->CO;?>" tabindex="4" /></td>
            <td>Fel Adress:</td>
            <td><input type="checkbox" name="FELADR" value="1" <?php if ($result->FelAdress) { echo "checked"; }?> tabindex="12" /></td>
        </tr>
        <tr>
            <td>Förnamn:</td>
            <td><input type="text" name="FNM" value="<?php echo $result->Fornamn;?>" tabindex="2"/></td>
            <td>Adress:</td>
            <td><input type="text" name="ADR" value="<?php echo $result->Adress;?>" tabindex="5" /></td>
            <td>Avisera ej:</td>
            <td><input type="checkbox" name="AVISEJ" value="1" <?php if ($result->AviseraEj) { echo "checked"; } ?> tabindex="13" /></td>
        </tr>
        <tr>
            <td>Efternamn:</td>
            <td><input type="text" name="ENM" value="<?php echo $result->Efternamn;?>" tabindex="3"/></td>
            <td>Postnummer:</td>
            <td><input type="text" name="PSTNR" value="<?php echo $result->PostNr;?>" tabindex="6" /></td>
            <td>Senast ändrad</td>
            <td><?php echo $result->SenastAndrad;?></td>
        </tr>
        <tr>
            <td>Telefon:</td>
            <td><input type="text" name="TEL" value="<?php echo $result->Telefon;?>" tabindex="10" /></td>
            <td>Ort:</td>
            <td><input type="text" name="ORT" value="<?php echo $result->Ort;?>" tabindex="7" /></td>
        </tr>
        <tr>
            <td>Epost:</td>
            <td><input type="text" name="EMAIL" value="<?php echo $result->Epost;?>" tabindex="11" /></td>
            <td>Land:</td>
            <td><input type="text" name="LAND" value="<?php echo $result->Land;?>" tabindex="8" /></td>
        </tr>
	</table>
    <div style="text-align:right">
        <input type="submit" value="Spara ändringar" onclick="document.forms['Person'].handler.value='SparaStudent';">
        <input type="submit" value="Logga ut" onclick="document.forms['Person'].handler.value='Logout';">
	</div>
</form>
<?php
putBoxEnd();
?>
