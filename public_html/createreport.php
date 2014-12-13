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

if (isset($_GET['type']) && !($_GET['type']==0)) {
    switch ($_GET['type']) {
    case "1":
        $filename ="medlemmar" . date("Y") . ".xls";
        $persons = getMembers(true,true);
        $contents = "Personnummer;Efternamn;Förnamn;Epost;Telefon;Period;Medlemstyp;Avgift;Betalat \n";
        foreach ($persons as $key => $person) {
            $contents .= $person["member"]["ssn"] . ";\"" .
                         $person["member"]["lastname"] . "\";\"" .
                         $person["member"]["firstname"] . "\";\"" .
                         $person["member"]["email"] . "\";\"" .
                         $person["member"]["phone"] . "\";" .
                         $person["payment"]["period"] . ";" .
                         $person["payment"]["membershiptype"] . ";" .
                         $person["payment"]["fee"] . ";" .
                         $person["payment"]["paid"] . " \n";
        }
        break;
    case "2":
        $filename ="icke-medlemmar" . date("Y") . ".xls";
        $persons = getNonMembers();
        $contents = "Personnummer;Efternamn;Förnamn;Epost;Telefon\n";
        foreach ($persons as $key => $person) {
            $contents .= $person["ssn"] . ";" .
                         $person["lastname"] . ";" .
                         $person["firstname"] . ";" .
                         $person["email"] . ";" .
                         $person["phone"] . " \n";
        }
        break;
    }
    header('Content-type: application/ms-excel');
    header('Content-Disposition: attachment; filename='.$filename);
    echo $contents;
} else {
    echo "Ingen rapport kunde skapas";
    echo "<script type=\"text/javascript\">";
    echo "self.close();";
    echo "</script>";
}
?>
