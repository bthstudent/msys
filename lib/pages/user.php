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
	<h2>Lägg till Användarkonto</h2>
	<form name="AddUser" class="info" method="post">
		<input type="hidden" readonly="readonly" value="AddUser" name="handler" />
		<table>
			<tr>
				<td>Användarnamn:</td>
				<td><input type="text" name="USR" /></td>
				<td>Lösenord:</td>
				<td><input type="password" name="PAS" /></td>
			</tr>
		</table>
		<div style="text-align:right">
		<input type="submit" value="Lägg till användare">
		</div>
	</form>
</div>

<?php
     if (isset($_POST["handler"]) && $_POST["handler"] == "updateUserPassword" && isset($_POST["error"]) && $_POST["error"] != 0) {
         switch ($_POST["error"]) {
             case -1:
                 echo "
                    <div class=\"outerdivs\">
                        <div class=\"operation-error\">
                            <h2>FEL</h2>
                            <p>De angivna lösenorden var nog tomma eller så. Ingen användare har fått lösenordet bytt.</p>
                        </div>
                    </div>";
                 break;
         case -2:
                 echo "
                    <div class=\"outerdivs\">
                        <div class=\"operation-error\">
                            <h2>FEL</h2>
                            <p>De angivna lösenorden stämde inte överrens, användarens lösenord är inte bytt.</p>
                        </div>
                    </div>";
                 break;
         case -3:
                 echo "
                    <div class=\"outerdivs\">
                        <div class=\"operation-error\">
                            <h2>FEL</h2>
                            <p>Användaren som angavs existerar inte, det här är inte ett försök att lura systemet va?
                        </div>
                    </div>";
                 break;
         }
     }
?>


<div class="outerdivs">
	<h2>Lista över användarkonton</h2>
<?php
$users = getUsers();
foreach ($users as $row) {
    echo "<div style=\"text-align: left; border-top:1px solid black; margin-bottom: 5px;\">";
    echo "<span style=\"font-size: 20px;\">" . $row["username"] . "</span>";

    if (isset($_POST["handler"]) && $_POST["handler"] == "updateUserPasswordForm" && $row["id"] == $_POST['id']) {
        echo "
	<div style=\"text-align: right;\">
		<form name=\"updateUserPassword\" class=\"info\" method=\"post\">
			<label>Nytt</label> <input type=\"password\" value=\"\" name=\"newpassword1\" />
			<label>Upprepa</label> <input type=\"password\" value=\"\" name=\"newpassword2\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"updateUserPassword\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"id\"/>
			<input type=\"submit\" value=\"Byt lösenord\">
			<a href=\"?page=user\">Avbryt</a>
		</form>
	</div>";
        continue;
    } else {
        echo "
	<div style=\"text-align: right;\">
		<form name=\"updateUserPassword\" class=\"info\" method=\"post\">
			<input type=\"hidden\" readonly=\"readonly\" value=\"updateUserPasswordForm\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"id\"/>
			<input type=\"submit\" value=\"Byt lösenord\">
		</form>
	</div>";
    }

    if ($row["id"] != $_SESSION['id']) {
        echo "
	<div style=\"text-align: right;\">
		<form name=\"RemoveUser\" class=\"info\" method=\"post\">
			<input type=\"hidden\" readonly=\"readonly\" value=\"RemoveUser\" name=\"handler\" />
			<input type=\"hidden\" readonly=\"readonly\" value=\"" . $row["id"] . "\" name=\"id\"/>
			<input type=\"submit\" value=\"Ta Bort Användare\">
		</form>
	</div>";
    }
    echo "</div>";
}
?>
</div>
