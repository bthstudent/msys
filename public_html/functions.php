<?php
require "../local-config.php";
require "PDO.interface.class.php";
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
 * Opens a connection to the database server.
 *
 * @return resource
 */
function getConnection()
{
    global $db;
    $mysql = ($GLOBALS["___mysqli_ston"] = mysqli_connect($db["host"],  $db["user"],  $db["pass"]));
    if (!$mysql) {
        die("Error - " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . "<br />
             Code: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)));
    } else {
        if(!((bool)mysqli_select_db($GLOBALS["___mysqli_ston"], $db["db"]))) {
            die("cannot select DB");
	}
    }

    $mysql->set_charset("utf8");

    return $mysql;
}

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
            $DBH->query("SELECT id FROM adminusers WHERE id = :sid");
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
    include_once "search.php";
    if (isset($_GET["page"])) {
        if (@file_exists($_GET["page"] . ".php")) {
            include_once $_GET["page"] . ".php";
        } else {
            putBoxStart();
            echo "Sidan du ville öppna kunde inte hittas!";
            putBoxEnd();
        }
    } else {
        include_once "start.php";
    }
}

/**
 * The main function for drawing the page for a member, will
 * import the underlying logic and model.
 *
 * This function is not used in code for real.
 *
 * @return void
 */
function studentpage()
{
    insertHead();
    include_once "student.php";
}


/**
 * Will start the login process by executing the underlying code.
 *
 * @return void
 */
function loginpage()
{
    insertHead();
    include_once "login.php";
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
        case "StudentLogin":
            $_SESSION['page'] = "student";
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
        case "AddPerson":
            addPerson();
            echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
            echo "<script type=\"text/javascript\">";
            echo "location.href='?page=person&id=" . $_GET['id'] . "'";
            echo "</script>";
            break;
        case "SparaStudent":
            sparaStudent();
            break;
        case "AddPeriod":
            addPeriod();
            break;
        case "addPayment":
            addPayment();
            echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
            echo "<script type=\"text/javascript\">";
            echo "location.href='?page=person&id=" . $_GET['id'] . "'";
            echo "</script>";
            break;
        case "RemovePayment":
            removePayment();
            break;
        case "ChangePerson":
            updatePerson();
            break;
        case "ChangePeriod":
            changePeriod();
            break;
        case "ChangeAvgift":
            updateAvgift();
            break;
        case "RemovePerson":
            removePerson();
            break;
        case "RemoveUser":
            removeUser();
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
            break;
        }
    }
}

/**
 * Extracts all API users and presents them in a structurred array.
 *
 * @return mixed
 */
function getAPIUsers()
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM api");
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
    $permission = $_POST['getPerson'] + $_POST['setPerson'] + $_POST['regPayment'] + $_POST['regPerson']  + $_POST['isMember'];
    $query = "INSERT INTO api(username, apikey, permissions)
              VALUES (:usr,
                      :key,
                      :perms)";
    $DBH->query($query);
    $DBH->bind(":usr", $_POST['USR']);
    $DBH->bind(":key", $_POST['KEY']);
    $DBH->bind(":perms", $permission);
    $DBH->execute();
}

/**
 * Delete an API user from the database records.
 *
 * @return void
 */
function removeAPIUser()
{
    $DBH = new DB();
    $DBH->query("DELETE FROM api
                WHERE username=:usr");
    $DBH->bind(":usr", $_POST['USR']);
    $DBH->execute();
}

/**
 * Add record for an administrator user.
 *
 * @return void
 */
function addUser()
{
    $DBH = new DB();
    $password = mysql_real_escape_string($_POST['PAS']);
    $DBH->query("INSERT INTO adminusers(username, hashpass)
              VALUES (:uname,
              sha1(:pass))");
    $DBH->bind(":uname", $_POST['USR']);
    $DBH->bind(":pass", $password);
    $DBH->execute();
}

/**
 * Delete a administrator user.
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
    $DBH->query("DELETE FROM adminusers
              WHERE id=:auid");
    $DBH->bind(":auid", $_POST['id']);
    $DBH->execute();
    if ($_POST['id']==$_SESSION['id']) {
        echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
        echo "<script type=\"text/javascript\">";
        echo "location.href=''";
        echo "</script>";
    }
}

/**
 * Extract all administrator users and retirn them in a structurred
 * array
 *
 * @return mixed
 */
function getUsers()
{
    $DBH = new DB();
    $DBH->query("SELECT id, username FROM adminusers");
    $users = $DBH->resultset();
    return $users;
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
    $DBH->query("SELECT apikey, permissions FROM api
                WHERE username=:uname");
    $DBH->bind(":uname", $user);
    $result = $DBH->single();
    if($result["apikey"] == $key){
        return $result["permissions"];
    }else{
        return 0;
    }
}

/**
 * API helper function that extracts all members and prints them in a
 * comma separated way to be parsed from the requestor side.
 *
 * @param string $pnr A personal number, can contain both numbers and
 *                    letters. Designed for Swedish customs.
 *
 * @return void
 */
function getAPIPerson($pnr)
{
    $DBH = new DB();
    $DBH->query("SELECT id FROM personer WHERE personnr= :pnr");
    $DBH->bind(":pnr", $pnr);
    $id = $DBH->single();
    $id = $id["id"];
    $person = getPerson($id);
    $bank = getPayments($id);
    echo $person["personnr"] . "," . $person["fornamn"] . "," .
         $person["efternamn"] . "," . $person["co"] . "," .
         $person["adress"] . "," . $person["postnr"] . "," .
         $person["ort"] . "," . $person["land"] . "," .
         $person["telefon"] . "," . $person["epost"] . "," .
         $person["aviseraej"] . "," . $person["senastandrad"];
    if (isset($bank)) {
        foreach ($bank as $row) {
            echo "," . $row["period"] . "," . $row["benamning"] . "," . $row["betalat"] . "," . $row["betaldatum"];
        }
    }
}

/**
 * ???
 *
 * @param ?? $ssn ??
 *
 * @return object
 */
function returnAPIPerson($ssn)
{
    $DBH = new DB();
    $DBH->query("SELECT id FROM personer
              WHERE personnr=:ssn");
    $DBH->bind(":ssn", $ssn);
    $result = $DBH->single();
    return $result;
}

/**
 * API helper function to update a member record
 *
 * @param string $data Contains a comma separated member profile.
 *
 * @return void
 */
function setAPIPersonData($data)
{
    $PSTNR = str_replace(' ', '', $data->PSTNR);
    $DBH = new DB();

    $DBH->query("SELECT id FROM personer WHERE personnr=:pnr");
    $DBH->bind(":pnr", $data->PNR);
    $personID = $DBH->single();

    $DBH->query("UPDATE personer SET telefon='" . $data->TEL . "',
                    epost ='" . $data->EMAIL . "',
                    co='" . $data->CO . "',
                    adress='" . $data->ADR . "',
                    postnr='" . $PSTNR . "',
                    ort='". $data->ORT . "',
                    land='" . $data->LAND . "',
                    aviseraej='" . $data->AVISEJ . "',
                    senastandrad=DATE(NOW())
                WHERE id='" . $personID["id"] . "'");
    $DBH->bind(":tel", $data->TEL);
    $DBH->bind(":epost", $data->EMAIL);
    $DBH->bind(":co", $data->CO);
    $DBH->bind(":adress", $data->ADR);
    $DBH->bind(":postnr", $PSTNR);
    $DBH->bind(":ort", $data->ORT);
    $DBH->bind(":land", $data->LAND);
    $DBH->bind(":aviseraej", $data->AVISEJ);
    $DBH->bind(":id", $personID["id"]);
    $DBH->execute();
}

/**
 * API helper function to add a new member to the records.
 *
 * @param string $data Contains a comma separated member profile.
 *
 * @return void
 */
function addAPIPerson($data)
{
    $DBH = new DB();
    $PSTNR = str_replace(' ', '', $data->PSTNR);
    $DBH->query("INSERT INTO personer SET personnr = :pnr,
                fornamn = :fnm,
                efternamn = :enm,
                co = :co,
                adress = :adr,
                postnr = :psnr,
                ort = :ort,
                land = :land,
                telefon = :tel,
                epost = :eml,
                aviseraej = :avis,
                feladress = :fadr,
                senastandrad = DATE(NOW())");
    $DBH->bind(":pnr", $data->PNR);
    $DBH->bind(":fnm", $data->FNM);
    $DBH->bind(":enm", $data->ENM);
    $DBH->bind(":co", $data->CO);
    $DBH->bind(":adr", $data->ADR);
    $DBH->bind(":psnr", $PSTNR);
    $DBH->bind(":ort", $data->ORT);
    $DBH->bind(":land", $data->LAND);
    $DBH->bind(":tel", $data->TEL);
    $DBH->bind(":eml", $data->EMAIL);
    $DBH->bind(":avis", $data->AVISEJ);
    $DBH->bind(":fadr", $data->FELADR);
    $DBH->execute();
}

/**
 * API helper function to add a payment record for a member.
 *
 * FIXME This function is broken in this state.
 *
 * @param string $data Contains comma separated payment information.
 *
 * @return void
 */
function registerAPIPayment($data)
{
    /* Den här funktionen borde vara beroende av addPayment istället...
     * FIXME!!
     * Not even close. PNR && PERIOD && MED>TYPE är fel. */
    getConnection();
    $query = "INSERT INTO betalningar(personer_id, avgift_id,
                                      betalsatt_id, betaldatum, betalat,
                                      deleted
                                     )
              VALUES ('". $data->PNR . "',
                      '" . $data->PERIOD . "',
                      '" . $data->BETWAY . "',
                      '" . $data->BETDATE . "',
                      '" . $data->BET . "',
                      '" . $data->MEDTYPE . "')";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
}

/**
 * Validates the administrator trying to login.
 *
 * @return void
 */
function checkAdminLogin()
{
    getConnection();
    $anvNamn = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['username']);
    $hashpass = sha1($_POST['pass2']);
    $query = "SELECT id FROM adminusers
              WHERE username='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $anvNamn) . "'
              AND hashpass='" . $hashpass . "'";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 1) {
        $row = mysqli_fetch_object($result);
        $_SESSION['page']="admin";
        $_SESSION['id']=$row->id;
    }
}

/**
 * Registers a payment for a specific member.
 *
 * @return void
 */
function addPayment()
{
    $DBH = new DB();
    $DBH->query("SELECT id AS avgift_id FROM avgift
                 WHERE medlemstyp_id = :memtypeid AND
                 perioder_id = :periodid");
    $DBH->bind(":memtypeid", $_POST['MEDTYPE']);
    $DBH->bind(":periodid", $_POST['PERIOD']);
    $a = $DBH->single();

    $DBH->query("INSERT INTO betalningar (personer_id, avgift_id, betalsatt_id, betaldatum, betalat)
                  VALUES (:pid, :avgid, :ptid, :pdate, :payed)");
    $DBH->bind(":pid", $_POST['ID']);
    $DBH->bind(":avgid", $a["avgift_id"]);
    $DBH->bind(":ptid", $_POST['BETWAY']);
    $DBH->bind(":pdate", $_POST['BETDATE']);
    $DBH->bind(":payed", $_POST['BET']);
    $DBH->execute();
}

/**
 * Add records for a member.
 *
 * @return void
 */
function addPerson()
{
    getConnection();
    $PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    $query = "INSERT INTO personer(personnr, fornamn, efternamn, co,
                                   adress, postnr, ort, land, telefon,
                                   epost, aviseraej, feladress
                                  )
              VALUES (
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['PNR']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['FNM']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ENM']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['CO']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ADR']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $PSTNR) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ORT']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['LAND']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['TEL']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['EMAIL']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['AVISEJ']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['FELADR']) . "'
                     )";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
    $_GET['id'] = mysqli_insert_id($GLOBALS["___mysqli_ston"]);
}

/**
 * Marks a member as deleted from the records.
 *
 * @return void
 */
function removePerson()
{
    getConnection();
    $query = "UPDATE betalningar SET deleted='1'
              WHERE personer_id=".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ID']);
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
    $query = "UPDATE personer SET deleted='1'
              WHERE id=".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ID']);
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
}

/**
 * Inserts a time period
 *
 * @return boolean
 */
function addPeriod()
{
    getConnection();
    $query = "INSERT INTO perioder (period, forst, sist)
              VALUES ('" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['period']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['forst']) . "',
                      '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['sist']) . "')";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])>0) {
        return true;
    }
    return false;
}

/**
 * Update values for a time period
 *
 * @return boolean
 */
function changePeriod()
{
    getConnection();
    $query = "UPDATE perioder
              SET forst='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['forst']) . "',
                  sist='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['sist']) . "'
              WHERE period='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['period']) . "'";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])>0) {
        return true;
    }
    return false;
}

/**
 * Updates the records for a specific member.
 *
 * @return void
 */
function updatePerson()
{
    $person = getPerson(mysql_real_escape_string($_POST['ID']));
    $haschanged = false;
    $PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    // The ugly way to make sure that checkboxes are "set" when not ticked...
    if (!isset($_POST['FELADR'])) {
        $_POST['FELADR'] = 0;
    }
    if (!isset($_POST['AVISEJ'])) {
        $_POST['AVISEJ'] = 0;
    }
    if ($person["personnr"] != $_POST['PNR']) {
        $haschanged = true;
    } else if ($person["fornamn"] != $_POST['FNM']) {
        $haschanged = true;
    } else if ($person["efternamn"] != $_POST['ENM']) {
        $haschanged = true;
    } else if ($person["telefon"] != $_POST['TEL']) {
        $haschanged = true;
    } else if ($person["epost"] != $_POST['EMAIL']) {
        $haschanged = true;
    } else if ($person["co"] != $_POST['CO']) {
        $haschanged = true;
    } else if ($person["adress"] != $_POST['ADR']) {
        $haschanged = true;
    } else if ($person["postnr"] != $PSTNR) {
        $haschanged = true;
    } else if ($person["ort"] != $_POST['ORT']) {
        $haschanged = true;
    } else if ($person["land"] != $_POST['LAND']) {
        $haschanged = true;
    } else if ($person["feladress"] != $_POST['FELADR']) {
        $haschanged = true;
    } else if ($person["aviseraej"] != $_POST['AVISEJ']) {
        $haschanged = true;
    }

    if ($haschanged) {
        $DBH = new DB();
        $DBH->query("UPDATE personer
                  SET personnr = :personnr,
                      fornamn = :fornamn,
                      efternamn = :efternamn,
                      telefon= :telefon,
                      epost = :epost,
                      co = :co,
                      adress = :adress,
                      postnr = :postnr,
                      ort = :ort,
                      land = :land,
                      feladress = :feladress,
                      aviseraej = :aviseraej,
                      senastandrad = DATE(NOW())
                  WHERE id='". $_POST['ID'] . "'");
            $DBH->bind(":personnr", $_POST['PNR']);
            $DBH->bind(":fornamn", $_POST['FNM']);
            $DBH->bind(":efternamn", $_POST['ENM']);
            $DBH->bind(":telefon", $_POST['TEL']);
            $DBH->bind(":epost", $_POST['EMAIL']);
            $DBH->bind(":co", $_POST['CO']);
            $DBH->bind(":adress", $_POST['ADR']);
            $DBH->bind(":postnr", $PSTNR);
            $DBH->bind(":ort", $_POST['ORT']);
            $DBH->bind(":land", $_POST['LAND']);
            $DBH->bind(":feladress", $_POST['FELADR']);
            $DBH->bind(":aviseraej", $_POST['AVISEJ']);
        $DBH->execute();
    }
}

/**
 * Updates the records for a specific member.
 *
 * Probably not in use at this moment. Should be merged with others
 * doing the same.
 *
 * @return void
 */
function sparaStudent()
{
    $PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    $query = "UPDATE personer
              SET fornamn='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['FNM']) . "',
                  efternamn='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ENM']) . "',
                  telefon='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['TEL']) . "',
                  epost ='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['EMAIL']) . "',
                  co='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['CO']) . "',
                  adress='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ADR']) . "',
                  postnr='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $PSTNR) . "',
                  ort='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ORT']) . "',
                  land='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['LAND']) . "',
                  feladress='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['FELADR']) . "',
                  aviseraej='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['AVISEJ']) . "',
                  senastandrad=DATE(NOW())
              WHERE id='" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['ID']) . "'";
    $DBH = new DB();
    $DBH->query($query);
    $DBH->execute();
}


/**
 * Updates the fee information.
 *
 * @return void
 */
function updateAvgift()
{
    $DBH = new DB();
    if (isset($_POST["avgiftid"]) && $_POST["avgiftid"] == -1) {
        // FIXME make period_id + medlemstyp_id a UNIQUE key
        //       then convert this to INSERT INTO tbl ON DUPLICATE KEY UPDATE...
        $query = "INSERT INTO avgift (perioder_id, medlemstyp_id, avgift)
                  VALUES (" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['period_id']) . ",
                          " . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['medlemstyp_id']) . ",
                          " . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['avgiften']) . ")";
    } elseif (isset($_POST["avgiftid"]) && $_POST["avgiftid"] > 0) {
        $query = "UPDATE avgift
                  SET avgift = ".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['avgiften'])."
                  WHERE id=".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['avgiftid']);
    } else {
        exit("FATAL ERROR. Execution Stopped.");
    }
    $DBH->query($query);
    $DBH->execute();
}

/**
 * Helper function to construct the search URL for the javascript
 * forwarding.
 *
 * @return void
 */
function composeSearchURL()
{
    if (isset($_POST["PNR"]) && !($_POST["PNR"]=="")) {
        $url = "?page=searchresult&pnr=" . $_POST["PNR"];
    } elseif (isset($_POST["FNM"]) && !($_POST["FNM"]=="")) {
        if (isset($_POST["ENM"]) && !($_POST["ENM"]=="")) {
            $url = "?page=searchresult&fnm=" . $_POST["FNM"] . "&enm=" . $_POST["ENM"];
        } else {
            $url = "?page=searchresult&fnm=" . $_POST["FNM"];
        }
    } elseif (isset($_POST["ENM"]) && !($_POST["ENM"]=="")) {
        $url = "?page=searchresult&enm=" . $_POST["ENM"];
    } elseif (isset($_POST["EMAIL"]) && !($_POST["EMAIL"]=="")) {
        $url = "?page=searchresult&email=" . $_POST["EMAIL"];
    }
    echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . $url . "\">Redirecting</a>";
    echo "<script type=\"text/javascript\">";
    echo "location.href=\"" . $url . "\"";
    echo "</script>";
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
                <img src='".$customize["style"]["logo"]."' width=\"80\" height=\"60\" align=\"absmiddle\" />
            </a>
            ".$customize["text"]["title"]."
          </h1>";

    if ($menu) {
        echo "<div id=\"logout\">
				<form name=\"logout\" method=\"post\">
					<select name=\"Medlem\" onChange=\"location = '?page='+this.options[this.selectedIndex].value; this.selectedIndex=0;\">
						<option value=\"0\" selected=\"selected\">-Medlemmar</option>
						<option value=\"nyperson\">Ny person</option>
						<!-- <option value=\"felaktigbetalning\">Felaktiga betalningar</option> -->
						<!-- <option value=\"okoppladbetalning\">Okopplade betalningar</option> -->
						<!-- <option value=\"senastandrad\">Senast ändrad</option> -->
					</select>
					<select name=\"Rapporter\" onChange=\"window.open('/skaparapport.php?typ='+this.options[this.selectedIndex].value); this.selectedIndex=0;\">
						<option value=\"0\" selected=\"selected\">-Rapporter</option>
						<option value=\"1\">Vilka har betalat?</option>
						<option value=\"2\">Vilka har ej betalat?</option>
					</select>
					<select name=\"Administration\" onChange=\"location = '?page='+this.options[this.selectedIndex].value; this.selectedIndex=0;\">
						<option value=\"0\" selected=\"selected\">-Administration</option>
						<option value=\"avgifter\">Avgifter</option>
						<option value=\"perioder\">Perioduppgifter</option>
						<!-- <option value=\"uppdrag\">Uppdrag</option> -->
						<option value=\"anvandare\">Användarkonton</option>
						<!-- <option value=\"avimeddelande\">Avimeddelande</option> -->
						<!-- <option value=\"bankgiro\">Bankgiro</option> -->
						<option value=\"webservice\">Webservice</option>
					</select>
					<img class=\"menu_button\" src=\"misc/logout.png\" onclick=\"document.forms['logout'].submit();\" />
					<img class=\"menu_button\" src=\"misc/about.png\" onclick=\"location = '?page=om';\" />
					<input type=\"hidden\" readonly=\"readonly\" value=\"Logout\" name=\"handler\" />
				</form>
			  </div>";
    }
    putBoxEnd();
}

/**
 * Extracts members from the database using a search pattern.
 * Returns the membes in a structurred array.
 *
 * @param boolean $payment  If true the payment information is
 *                          included.
 * @param boolean $adress   If true the address of the member is
 *                          included.
 * @param int     $page     Indicates a page size limit on number of
 *                          records.
 * @param int     $pagesize Defined but not used.
 *
 * @return mixed
 */
function getMembers($payment=true,$adress=false,$page=0,$pagesize=20)
{
    $DBH = new DB();
    unset($query);
    $query = "SELECT personnr, efternamn, fornamn, epost, telefon";
    if ($adress === true) {
        $query .= ", co, adress, postnr, land, feladress, aviseraej";
    }
    $query .= " FROM personer
		GROUP BY personnr
                ORDER BY personnr DESC";
    if ($page>0) {
        $query .= " LIMIT 20";
    }
    $DBH->query($query);
    $persons = $DBH->resultset();
    $result = Array();
    foreach ($persons as $person) {
        if(isMember($person["personnr"])) {
	    $result[] = $person;
	}
    }
    return $result;
}

/**
 * Extracts information about people registred in the database who are
 * not currently members.
 * Returns the information in a structurred array.
 *
 * @return mixed
 */
function getNonMembers()
{
    $Persons = getPersons();
    $Members = getMembers();
    $nonmembers = array();
    $i = 0;

    foreach ($Persons as $prs) {
        $add = true;
        foreach ($Members as $mbr) {
            if ($mbr["personnr"] == $prs["personnr"]) {
                $add = false;
            }
        }
        if ($add) {
            $nonmembers[$i] = $prs;
            $i++;
        }
    }
    return $nonmembers;
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
 * Counts the number of current members and returns that for further
 * processing.
 *
 * @return int
 */
function countMembers()
{
    $DBH = new DB();
    $DBH->query("SELECT COUNT(personer_id) AS NumberOfMembers
                FROM betalningar
                LEFT JOIN avgift ON betalningar.avgift_id=avgift.id
                LEFT JOIN perioder ON avgift.perioder_id=perioder.id
                WHERE perioder.forst<=DATE(NOW()) AND
                perioder.sist>=DATE(NOW()) AND
                betalningar.deleted != 1");
    $Count = $DBH->single();
    $memberCount = $Count["NumberOfMembers"];
    return $memberCount;
}

/**
 * Helper function to check if a personal number is considered as
 * member or not.
 *
 * @param string $pnr A personal number, can contain both numbers and
 * letters. Based on Swedish customs.
 *
 * @return boolean
 */
function isMember($pnr)
{
    $DBH = new DB();
    $DBH->query("SELECT COUNT(personer_id) AS IsMember
                FROM betalningar LEFT JOIN avgift ON betalningar.avgift_id=avgift.id
                LEFT JOIN perioder ON avgift.perioder_id=perioder.id
                LEFT JOIN personer ON betalningar.personer_id=personer.id
                WHERE forst<=DATE(NOW()) AND
                sist>=DATE(NOW()) AND
                personnr = :pnr");
    $DBH->bind(":pnr", $pnr);

    $IsMember = $DBH->single();
    $IsMember = $IsMember["IsMember"];
    if ($IsMember>0) {
        return true;
    }
    return false;
}

/**
 * Extract information about a member.
 * Returns the information in a array.
 *
 * @param int     $id         A internal identification number of a
 *                            person.
 * @param boolean $getdeleted If true a person marked as delted can
 *                            also be found.
 *
 * @return mixed
 */
function getPerson($id, $getdeleted=false)
{
    $DBH = new DB();
    $query = "SELECT * FROM personer
              WHERE id = :pid";
    if (!$getdeleted) {
        $query .= " AND deleted != 1";
    }
    $DBH->query($query);
    $DBH->bind(":pid", $id);

    $person = $DBH->single();
    return $person;
}

/**
 * Extract information about all members.
 * Returns the information in a array.
 *
 * A rather stupid function really...
 *
 * @return mixed
 */
function getPersons()
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM personer WHERE deleted != 1");
    $persons = $DBH->resultset();
    return $persons;
}

/**
 * Searches for members matching the email field.
 * Will not search for members marked as deleted.
 *
 * FIXME needs work.
 *
 * @param string $ema Text to search for in the email field.
 *
 * @return mixed
 */
function findEMA($ema)
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM personer
                WHERE epost LIKE :ema AND deleted != 1");
    $DBH->bind(":ema", "%".$ema."%");
    $persons = $DBH->resultset();
    if(!empty($persons)){
        return $persons;
    }
}

/**
 * Searches for members matching the personal number field.
 * Will not search for members marked as deleted.
 *
 * FIXME needs work.
 *
 * @param string $pnr Text to search for in the personal number field.
 *
 * @return mixed
 */
function findPNR($pnr)
{
    $DBH = new DB();
    $pnr = str_ireplace("-", "", $pnr);
    $DBH->query("SELECT * FROM personer
              WHERE personnr LIKE :pnr AND deleted != 1");
    $DBH->bind(":pnr", $pnr."%");
    $persons = $DBH->resultset();
    if(!empty($persons)){
        return $persons;
    }
}

/**
 * Searches for members matching the first name field.
 * Will not search for members marked as deleted.
 *
 * FIXME needs work.
 *
 * @param string $fnm Text to search for in the first name field.
 *
 * @return mixed
 */
function findFNM($fnm)
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM personer
              WHERE fornamn LIKE '%$fnm%' AND deleted != 1");
    $DBH->bind(":fnm", "%".$fnm."%");
    $persons = $DBH->resultset();
    if(!empty($persons)){
        return $persons;
    }
}

/**
 * Searches for members matching the surname field.
 * Will not search for members marked as deleted.
 *
 * FIXME needs work.
 *
 * @param string $enm Text to search for in the surname field.
 *
 * @return mixed
 */
function findENM($enm)
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM personer
              WHERE efternamn LIKE :enm AND deleted != 1");
    $DBH->bind(":enm", "%".$enm."%");
    $persons = $DBH->resultset();
    if(!empty($persons)){
        return $persons;
    }
}

/**
 * Searches for members matching the firstname and surname fields.
 * Will not search for members marked as deleted.
 *
 * FIXME needs work.
 * FIXME can be combined with the specific search stuff. At least.
 *
 * @param string $fnm Text to search for in the first name field.
 * @param string $enm Text to search for in the surname field.
 *
 * @return mixed
 */
function findNM($fnm, $enm)
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM personer
                WHERE fornamn LIKE :fnm AND
                    efternamn LIKE :enm
                    AND deleted != 1");
    $DBH->bind(":fnm", "%".$fnm."%");
    $DBH->bind(":enm", "%".$enm."%");
    $persons = $DBH->resultset();
    if(!empty($persons)){
        return $persons;
    }
}

/**
 * Extracts the mandates a member have had.
 *
 * FIXME This does probably not work, it's not in real life use at the
 * FIXME moment.
 *
 * FIXME The return is conditioned to at least have something, will
 * FIXME not return False or such things though.
 *
 * @param int $id The internal personal number of a member.
 *
 * @return mixed
 */
function getMandates($id)
{
    $DBH = new DB();
    $query = "SELECT benamning, beskrivning, period, forst, sist FROM personer
              LEFT JOIN personer_uppdrag ON personer.id = personer_uppdrag.personer_id
              LEFT JOIN uppdrag ON personer_uppdrag.uppdrag_id=uppdrag.id
              LEFT JOIN perioder ON personer_uppdrag.perioder_id=perioder.id
              WHERE personer.id=:id
              AND personer.deleted != 1";
    $DBH->query($query);
    $DBH->bind(":id", $id);
    $mandates = $DBH->resultset();
    if(!empty($mandates)){
        return $mandates;
    }
}

/**
 * Some kind of mandate extraction thing. Probably broken.
 *
 * FIXME Not in use in a real world example.
 * FIXME This is horribly broken, uses the assumption that a PNR can
 * FIXME not have letters in it.
 *
 * @param string $pnr A string of letters and numbers representing the
 *                    personal number, by Swedish customs.
 *
 * @return mixed
 */
function getCurrentMandates($pnr)
{
    $DBH = new DB();
    $query = "SELECT benamning, beskrivning,
              FROM personer
              LEFT JOIN personer_uppdrag ON personer_uppdrag.person_id = personer.id
              LEFT JOIN uppdrag ON personer_uppdrag.uppdrag_id=uppdrag.id
              LEFT JOIN perioder ON personer_uppdrag.perioder_id=perioder.id
              WHERE personnr=:pnr AND
                    forst<CURDATE() AND
                    sist>CURDATE()
              AND personer.deleted != 1";
    $DBH->query($query);
    $DBH->bind(":pnr", $pnr);
    $mandates = $DBH->resultset();
}

/**
 * Extrats all payments for a member.
 *
 * @param int $id The internal personal number of a member.
 *
 * @return mixed
 */
function getPayments($id)
{
    $DBH = new DB();
    $query = "SELECT betalningar.id AS id, betalsatt.benamning AS betalsatt,
                     betalningar.betalat AS betalat, betalningar.betaldatum AS betaldatum,
                     avgift.avgift AS avgift, medlemstyp.benamning AS benamning,
                     perioder.forst AS forst, perioder.sist AS sist,
                     perioder.period AS period
              FROM betalningar
              LEFT JOIN avgift ON betalningar.avgift_id=avgift.id
              LEFT JOIN betalsatt ON betalningar.betalsatt_id=betalsatt.id
              LEFT JOIN perioder ON avgift.perioder_id=perioder.id
              LEFT JOIN medlemstyp ON avgift.medlemstyp_id=medlemstyp.id
              WHERE betalningar.personer_id=:id AND deleted != 1";
    $DBH->query($query);
    $DBH->bind(":id", $id);
    $payments = $DBH->resultset();
    if(!empty($payments)){
        return $payments;
    }
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

/**
 * Extracts the current time periods and returns them in a structurred array.
 *
 * @return mixed
 */
function getPeriods()
{
    $DBH = new DB();
    $query = "SELECT id, period, forst, sist FROM perioder
              ORDER BY forst, sist";
    $DBH->query($query);
    $periods = $DBH->resultset();
    return $periods;
}

/**
 * Update period information.
 *
 * FIXME needs work =)
 *
 * @param mixed $period A object representing the period to update
 *                      information about.
 *
 * @return boolean
 */
function updatePeriod($period)
{

    /*

       _____ _____  ____  __ _____
      |  ___|_ _\ \/ /  \/  | ____|
      | |_   | | \  /| |\/| |  _|
      |  _|  | | /  \| |  | | |___
      |_|   |___/_/\_\_|  |_|_____|


     */


    getConnection();
    $query = "UPDATE perioder
              SET forst='$period->Forst',
                  sist='$period->Sist'
              WHERE period='$period->Period'";
    //--------------^^^^^^^^^^^^^^^

    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])>0) {
        return true;
    }
    return false;
}

/**
 * Extracts the current fees and returns them in a structurred array.
 *
 * @return mixed
 */
function getAvgifter()
{
    $DBH = new DB();
    $query = "SELECT perioder.id AS perioder_id, period, medlemstyp_id, avgift, avgift.id AS avgift_id, forst, sist FROM perioder
              LEFT JOIN avgift ON perioder.id=avgift.perioder_id
              ORDER BY forst DESC, sist DESC, medlemstyp_id";
    $DBH->query($query);
    $avgifter = $DBH->resultset();
    if(!empty($avgifter)){
        return $avgifter;
    }else{
        return NULL;
    }
}

/**
 * ??
 *
 * @param ?? $fee ??
 * @param ?? $membertype ??
 *
 * @return object
 */
function getFeeId($fee, $membertype){
    $DBH = new DB();
    $query = "SELECT avgift.id FROM avgift
              INNER JOIN perioder ON avgift.perioder_id=perioder.id
              INNER JOIN medlemstyp on avgift.medlemstyp_id=medlemstyp.id
              WHERE perioder.period=:fee AND
                    avgift.medlemstyp_id=:memtype";
    $DBH->query($query);
    $DBH->bind(":fee", $fee);
    $DBH->bind(":memtype", $membertype);
    return $DBH->single();
}

/**
 * Extract the membership types and returns them in a structurred array.
 *
 * @return mixed
 */
function getMedlemstyper()
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM medlemstyp");
    $medlemstyper = $DBH->resultset();
    return $medlemstyper;
}

/**
 * Extract the payment types and returns them in a structurred array.
 *
 * @return mixed
 */
function getBetalsatt()
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM betalsatt");
    $betalsatt = $DBH->resultset();
    return $betalsatt;
}

/**
 * Marks a payment as deleted.
 *
 * @return void
 */
function removePayment()
{
    $DBH = new DB();
    $DBH->query("UPDATE betalningar SET deleted='1' 
                WHERE id=:bid");
    $DBH->bind(":bid", $_POST['betid']);
    $DBH->execute();
}

/**
 * Counts the amount of members for the supplied type of member.
 *
 * @param string $benamning Membership type
 *
 * @return int
 */
function getNumberOfMembers($benamning)
{
    $DBH = new DB();
    $query = "SELECT count(betalningar.id) AS antal, medlemstyp.benamning
              FROM betalningar
              LEFT JOIN avgift ON betalningar.avgift_id = avgift.id
              LEFT JOIN medlemstyp ON avgift.medlemstyp_id = medlemstyp.id
              LEFT JOIN perioder ON avgift.perioder_id=perioder.id
              WHERE perioder.forst<=DATE(NOW()) AND
              perioder.sist>=DATE(NOW()) AND
              betalningar.deleted = 0 AND
              medlemstyp.benamning = :benamning";
    $DBH->query($query);
    $DBH->bind(":benamning",$benamning);
    $result = $DBH->single();

    return $result["antal"];
}
?>
