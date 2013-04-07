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
?>

<!-- content goes here -->
<h2>Sök student</h2>
<form style="margin:0" id="Search" method="post">
	<input type="hidden" readonly="readonly" value="composeSearchURL" name="handler" />
	<table style="text-align:center">
    	<tr class="toptr">
        	<td>
            	Personnummer
            </td>
            <td>
            	Förnamn
            </td>
            <td>
            	Efternamn
            </td>
            <td>
            	Epost
            </td>
        </tr>
        <tr>
        	<td style="padding:0 5;">
            	<input type="text" name="PNR" tabindex="1" onkeydown="if(event.keyCode==13) document.forms['Search'].submit()"/>
            </td>
            <td style="padding:0 5;">
	            <input type="text" name="FNM" tabindex="2" onkeydown="if(event.keyCode==13) document.forms['Search'].submit()"/>
            </td>
            <td style="padding:0 5;">
	            <input type="text" name="ENM" tabindex="3" onkeydown="if(event.keyCode==13) document.forms['Search'].submit()"/>
            </td>
            <td style="padding:0 5;">
                <input type="text" name="EMAIL" tabindex="4" onkeydown="if(event.keyCode==13) document.forms['Search'].submit()"/>
            </td>
        </tr>
    </table>
</form>
<?php
putBoxEnd();
?>
