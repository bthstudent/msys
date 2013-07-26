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

$month = date("m");
$year  = date("y");

if ((int)$month < 7) {
    $term  = "HT" . ((int)$year - 1);
    $term2 = "VT" . ((int)$year - 1);
} else {
    $term  = "VT" . $year;
    $term2 = "HT" . ((int)$year - 1);
}
?>
<h2>Blekinge studentkårs medlemsregister</h2>
<div id="infoBoxes">
<?php
putInfoBox("Antal medlemmar", countMembers());
putInfoBox("Antal studenter", 4000);
putInfoBox("Anslutningsgrad", round(((countMembers()/4000)*100), 2) . "%");
putInfoBox("Antal campusmedlemmar", getNumberOfMembers('Campus'));
putInfoBox("Antal distans/Doktorandmedlemmar", getNumberOfMembers('Distans/Doktorand'));
putInfoBox("Antal stödmedlemmar", getNumberOfMembers('Stöd'));
putInfoBox("Antal helårsmedlemmar", countMembers());
putInfoBox("Antal halvårsmedlemmar", countMembers());
putInfoBox("Antal får på Gotland", 60504 . "<a href=\"http://lmgtfy.com/?q=hur+m%C3%A5nga+f%C3%A5r+finns+det+p%C3%A5+gotland%3F&l=1\">*</a>");
putInfoBox("Antal helavgift", countMembers());
putInfoBox("Antal halvavgift", countMembers());
putInfoBox("Antal stödmedlemmar", countMembers());
putInfoBox("Nya medlemmar denna vecka", countMembers());
putInfoBox("Anslutningsgrad " . $term, countMembers() . "%");
putInfoBox("Anslutningsgrad " . $term2, countMembers() . "%");
putInfoBox("Antal bultar i Ölandsbron", 7428954);
?>
</div>
<?php
putBoxEnd();
?>
