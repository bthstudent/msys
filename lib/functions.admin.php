<?php
require_once "../local-config.php";
require_once "PDO.interface.class.php";
require_once "password.php";
require_once "Logger.class.php";

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

/**
 * Extracts all API users and presents them in a structurred array.
 *
 * @return mixed
 */
function getAPIUsers()
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM api WHERE deleted != '1'");
    $users = $DBH->resultset();
    return $users;
}

/**
 * Add record for a API user.
 *
 * @return void
 */
function addAPIUser()
{
    $DBH = new DB();

    // Here be sloppy error handling. Will render NOTICEs.
    $permission = $_POST['getPerson'] + $_POST['setPerson'] + $_POST['regPayment'] + $_POST['regPerson']  + $_POST['isMember'];
    $query = "INSERT INTO api(username, apikey, permission)
              VALUES (:usr,
                      :key,
                      :perms)";
    $DBH->query($query);
    $DBH->bind(":usr", $_POST['USR']);
    $DBH->bind(":key", $_POST['KEY']);
    $DBH->bind(":perms", $permission);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "addAPIUser", "Created a new API user " . $_POST['USR']);
}

/**
 * Delete an API user from the database records.
 *
 * @return void
 */
function removeAPIUser()
{
    $DBH = new DB();
    $DBH->query("UPDATE api
                SET deleted = '1'
                WHERE username=:usr");
    $DBH->bind(":usr", $_POST['USR']);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "removeAPIUser", "Flagged API user " . $_POST['USR'] . " as deleted");
}

/**
 * Add record for an administrator.
 *
 * @return void
 */
function addUser()
{
    global $globalsalt;
    $DBH = new DB();
    $password = password_hash($_POST['USR'] . $_POST['PAS'] . $globalsalt, PASSWORD_BCRYPT, array("cost" => 13));
    $DBH->query("INSERT INTO adminuser(username, hashpass)
              VALUES (:uname,
              :pass)");
    $DBH->bind(":uname", $_POST['USR']);
    $DBH->bind(":pass", $password);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "addUser", "Created a new user " . $_POST['USR']);
}

/**
 * Delete a administrator.
 *
 * @return void
 */
function removeUser()
{
     /**
       OBS! if $_POST['id'] == %?
       possible?
    */
    $DBH = new DB();
    $DBH->query("UPDATE adminuser
              SET deleted = '1'
              WHERE id=:auid");
    $DBH->bind(":auid", $_POST['id']);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "removeUser", "Flagged the user with id " . $_POST['id'] . " as deleted");

    if ($_POST['id']==$_SESSION['id']) {
        echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
        echo "<script type=\"text/javascript\">";
        echo "location.href=''";
        echo "</script>";
    }
}

/**
 * Extract all administrator users and return them in a structurred
 * array
 *
 * @return mixed
 */
function getUsers()
{
    $DBH = new DB();
    $DBH->query("SELECT id, username FROM adminuser WHERE deleted != '1'");
    $users = $DBH->resultset();
    return $users;
}

/**
 * Validates the administrator trying to login.
 *
 * @return void
 */
function checkAdminLogin()
{
    global $globalsalt;
    $DBH = new DB();
    $DBH->query("SELECT id, hashpass FROM adminuser
                 WHERE username = :username
                 AND deleted != '1'");

    $DBH->bind(":username", $_POST['username']);
    $DBH->execute();

    if (($DBH->rowCount()) == 1) {
        $row = $DBH->single();
        if (password_verify($_POST['username'] . $_POST['pass'] . $globalsalt,
            $row['hashpass'])) {
            $_SESSION['page']="admin";
            $_SESSION['id']=$row['id'];
        } elseif ($row['hashpass'] == sha1($_POST['pass'])) {
            $_SESSION['page']="admin";
            $_SESSION['id']=$row['id'];
            $password = password_hash($_POST['username'] . $_POST['pass'] .
                $globalsalt, PASSWORD_BCRYPT, array("cost" => 13));

            $DBH->query("UPDATE adminuser
                         SET hashpass = :password
                         WHERE username = :username");
            $DBH->bind(":password", $password);
            $DBH->bind(":username", $_POST['username']);
            $DBH->execute();
        }
        $_SESSION['user_type']="local";
    }
}

?>