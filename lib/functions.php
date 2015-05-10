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

require_once "functions.admin.php";
require_once "functions.member.php";
require_once "functions.fee.php";

/**
 * Make sure the PHP session is intact and correct, or destroyed if
 * needed.
 *
 * @return void
 */
function handlesession()
{
    if (isset($_SESSION['page'])) {
        if ($_SESSION['page']=="admin") {
            $DBH = new DB();
            $DBH->query("SELECT id FROM adminuser WHERE id = :sid");
            $DBH->bind(":sid", $_SESSION['id']);
            $res = $DBH->resultset();
            if ($DBH->rowCount()!=1) {
                session_destroy();
                header("Location: /");
                exit();
            }
        }
    }
}

/**
 * Helper function to put the correct CSS style sheet file in place.
 *
 * @return void
 */
function handlestyle()
{
    if (!isset($_GET["page"])) {
        if (@file_exists("style/pages/start.css")) {
            echo "<link rel='stylesheet' href='style/pages/start.css' type='text/css'>";
        }
    } elseif (@file_exists("style/pages/" . $_GET["page"] . ".css")) {
        echo "<link rel='stylesheet' href='style/pages/" . $_GET["page"] . ".css' type='text/css'>";
    }
}

/**
 * Helper function to put the correct javascript file in place.
 *
 * @return void
 */
function handlejavascript()
{
    if (!isset($_GET["page"])) {
        if (@file_exists("script/start.js")) {
            echo "<script type='text/javascript' src='start.js'></script>";
        }
    } elseif (@file_exists("script/" . $_GET["page"] . ".js")) {
        echo "<script type='text/javascript' src='script/" . $_GET["page"] . ".js'></script>";
    }
}

/**
 * The main function for drawing the page for a administrator, will
 * import the underlying logic and model.
 *
 * @return void
 */
function adminpage()
{
    insertHead(true);
    include_once "pages/search.php";
    if (isset($_GET["page"])) {
        if (@file_exists("../lib/pages/" . $_GET["page"] . ".php")) {
            include_once "../lib/pages/" . $_GET["page"] . ".php";
        } else {
            putBoxStart();
            echo "Sidan du ville öppna kunde inte hittas!";
            putBoxEnd();
        }
    } else {
        include_once "pages/start.php";
    }
}

/**
 * Will start the login process by executing the underlying code.
 *
 * @return void
 */
function loginpage()
{
    insertHead();
    include_once "pages/login.php";
}


/**
 * The broker of the system, will take POST requests and process them
 * to make them execute in the correct part of the system.
 *
 * FIXME I assume that this must be better described...
 *
 * @return void
 */
function handlepost()
{
    if (!empty($_POST["handler"])) {
        switch($_POST["handler"]) {
        case "composeSearchURL":
            composeSearchURL();
            break;
        case "AdminLogin":
            checkAdminLogin();
            break;
        case "Logout":
            session_destroy();
            echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
            echo "<script type=\"text/javascript\">";
            echo "location.href=''";
            echo "</script>";
            break;
        case "AddMember":
            addMember();
            echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
            echo "<script type=\"text/javascript\">";
            echo "location.href='?page=member&id=" . $_GET['id'] . "'";
            echo "</script>";
            break;
        case "AddPeriod":
            addPeriod();
            break;
        case "addPayment":
            addPayment();
            echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
            echo "<script type=\"text/javascript\">";
            echo "location.href='?page=member&id=" . $_GET['id'] . "'";
            echo "</script>";
            break;
        case "RemovePayment":
            removePayment();
            break;
        case "ChangeMember":
            updateMember();
            break;
        case "ChangePeriod":
            changePeriod();
            break;
        case "ChangeFee":
            updateFee();
            break;
        case "RemoveMember":
            removeMember();
            break;
        case "RemoveUser":
            removeUser();
            break;
        case "updateUserPassword":
            updateUserPassword();
            break;
        case "updateUserPasswordForm":
            // Placeholder because the UI code is stupid...
            break;
        case "AddUser":
            addUser();
            break;
        case "AddAPIUser":
            addAPIUser();
            break;
        case "RemoveAPIUser":
            removeAPIUser();
            break;
        default:
            echo "Du har försökt skicka något med metoden 'POST' som sidan inte känner igen,<br />";
            echo "<script type=\"text/javascript\">";
            echo "alert('Du har försökt skicka något med metoden 'POST' som sidan inte känner igen');";
            echo "</script>";
            break;
        }
    }
}


/**
 * API helper function to make sure that the API based request is
 * indeed allowed to be executed.
 *
 * @param string $key  the passphrase like API key
 * @param string $user a username like identifier
 *
 * @return int
 */
function authenticateAPIUser($key, $user)
{
    $DBH = new DB();
    $DBH->query("SELECT id, apikey, permission FROM api
                WHERE username=:uname AND deleted != '1'");
    $DBH->bind(":uname", $user);
    $result = $DBH->single();
    if ($result["apikey"] == $key) {
        $_SESSION['id'] = $result['id'];
        $_SESSION['user_type'] = "api";
        return $result["permission"];
    } else {
        return 0;
    }
}

/**
 * Draws the main page header with menu and stuff.
 *
 * @param boolean $menu True if showing the menu is intended.
 *
 * @return void
 */
function insertHead($menu=false)
{
    global $customize;
    putBoxStart();
    echo "<h1>
            <a href=\"http://" . $_SERVER['HTTP_HOST'] . "\" title=\"Hem\">
                <img src='".$customize["style"]["logo"]."' width=\"80\" height=\"60\" align=\"absmiddle\" /></a>
            ".$customize["text"]["title"]."
          </h1>";

    if ($menu) {
        echo "
          <ul style=\"width: 500px; margin: 0 auto 0 auto;\">
              <li>
                  <a href=\"?page=newmember\" class=\"menu item\">Ny person</a>
              </li>
              <li>
                  <a href=\"#\" class=\"menu item\">Rapporter</a>
                  <ul>
                      <li>
                          <a href=\"?page=createreport&amp;type=1\" class=\"menu item\">Betalda</a>
                          <a href=\"?page=createreport&amp;type=2\" class=\"menu item\">Ej betalda</a>
                      </li>
                  </ul>
              </li>
              <li>
                  <a href=\"#\" class=\"menu item\">Administration</a>
                  <ul>
                      <li>
                          <a href=\"?page=fees\" class=\"menu item\">Avgifter</a>
                      </li>
                      <li>
                          <a href=\"?page=periods\" class=\"menu item\">Perioduppgifter</a>
                      </li>
                      <li>
                          <a href=\"?page=user\" class=\"menu item\">Användarkonton</a>
                      </li>
                      <li>
                          <a href=\"?page=webservice\" class=\"menu item\">Webservice</a>
                      </li>
                  </ul>
              </li>
              <li>
                  <a href=\"?page=about\" class=\"menu item\">Om</a>
              </li>
              <li>
                  <form name=\"logout\" method=\"post\">
                  <a href=\"#\" class=\"menu item\" onclick=\"document.forms['logout'].submit();\">Logga ut</a>
                  <input type=\"hidden\" readonly=\"readonly\" value=\"Logout\" name=\"handler\" />
                  </form>
              </li>
          </ul>";
    }
    putBoxEnd();
}

/**
 * Helper function to draw information boxes.
 *
 * @param string $head  The heading of the information box.
 * @param string $value The body text of the information box.
 *
 * @return void
 */
function putInfoBox($head, $value)
{
    echo "<div class=\"info\">
            <h3>$head</h3>
            <h7>" . $value . "</h7>
          </div>";
}

/**
 * Helper function to draw the standard HTML box start tag.
 *
 * @return void
 */
function putBoxStart()
{
    echo "<div class=\"outerdivs\">";
}

/**
 * Helper function to draw the standard HTML box end tag.
 *
 * @return void
 */
function putBoxEnd()
{
    echo "</div>";
}
?>
