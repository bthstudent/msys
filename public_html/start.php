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
?>
<h2>Blekinge studentkårs medlemsregister</h2>
<div id="infoBoxes">
<?php
putInfoBox("Antal medlemmar", countMembers());
putInfoBox("Antal studenter", 4000);
putInfoBox("Anslutningsgrad", round(((countMembers()/4000)*100), 2) . "%");
putInfoBox("Antal campusmedlemmar", getNumberOfMembers('Campus'));
putInfoBox("Antal distans/Doktorandmedlemmar", getNumberOfMembers('Distans/Doktorand'));
putInfoBox("Antal stödmedlemmar", getNumberOfMembers('Stod'));
?>
</div>
<?php
putBoxEnd();
?>
