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
<div class="logindivs" id="logindivleft">
	<form name="student" method="post">
    	<input type="hidden" readonly value="StudentLogin" name="handler" />
        <h2>Studentinlogg</h2>
        <div class="field_holder">
            <label>Akronym:</label>
            <input name="akronym" type="text" value="Student Login Disabled" tabindex="1" disabled ><br>
            <label>Lösenord:</label>
            <input name="pass1" type="password" tabindex="2" disabled >
        </div>
        <div class="right_align">
			<input type="submit" value="Logga in" disabled >
        </div>
    </form>
</div>
<div class="logindivs">
    <form name="admin" method="post">
    	<input type="hidden" readonly value="AdminLogin" name="handler" />
        <h2>Administratörinlogg</h2>
        <div class="field_holder">
            <label>Användarnamn:</label>
            <input name="username" type="text" tabindex="3"><br>
            <label>Lösenord:</label>
            <input name="pass2" type="password" tabindex="4">
        </div>
        <div class="right_align">
	        <input type="submit" value="Logga in">
        </div>
	</form>
</div>
