<?php
include "../local-config.php";
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
function getConnection()
{
    global $db;
    $con = mysql_connect($db["host"], $db["user"], $db["pass"]);
    mysql_select_db($db["db"], $con);
    return $con;
}

function handlesession()
{

    /**
    if (!empty($_SESSION)) {
        echo "Detta är en placeholder för handlesessions - det finns sessions variabler<br />";
    } else {
        echo "Detta är en placeholder för handlesessions - det finns inga sessions variabler<br />";
    }
    */
}

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

function studentpage()
{
    insertHead();
    include_once "student.php";
}

function loginpage()
{
    insertHead();
    include_once "login.php";
}

function handlepost()
{
    if (!empty($_POST)) {
        switch($_POST["handler"]) {
        case "composeSearchURL":
            composeSearchURL();
            break;
        case "StudentLogin":
            $_SESSION['page']="student";
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
			echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
            echo "<script type=\"text/javascript\">";
            echo "location.href='?page=person&pnr=" . $_GET['pnr'] . "'";
            echo "</script>";
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

function getAPIUsers()
{
    getConnection();
    $query = "SELECT * FROM api";
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $users[] = $row;
    }
    return $users;
}

function addAPIUser()
{
    getConnection();
    $permission = mysql_real_escape_string($_POST['getPerson'] + $_POST['setPerson'] + $_POST['regPayment'] + $_POST['regPerson']  + $_POST['isMember']);
    $query = "INSERT INTO api
              VALUES ('" . mysql_real_escape_string($_POST['USR']) . "',
                      '" . mysql_real_escape_string($_POST['KEY']) . "',
                      '" . $permission . "')";
    $result = mysql_query($query);
}

function removeAPIUser()
{
    getConnection();
    $query = "DELETE FROM api
              WHERE username='" . mysql_real_escape_string($_POST['USR']) . "'";
    $result = mysql_query($query);
}

function addUser()
{
    getConnection();
    $password = mysql_real_escape_string($_POST['PAS']);
    $query = "INSERT INTO adminusers(username, hashpass)
              VALUES ('" . mysql_real_escape_string($_POST['USR']) . "',
              sha1('" . $password . "'))";
    $result = mysql_query($query);
}

function removeUser()
{
    getConnection();
    /**
       OBS! if $_POST['id'] == %?
       possible?
    */
    $query = "DELETE FROM adminusers
              WHERE id=" . mysql_real_escape_string($_POST['id']);
    $result = mysql_query($query);
}

function getUsers()
{
    getConnection();
    $result = mysql_query("SELECT id, username FROM adminusers");
    while ($row = mysql_fetch_object($result)) {
        $users[] = $row;
    }
    return $users;
}

function authenticateAPIUser($key, $user)
{
    getConnection();
    $result = mysql_query("SELECT apikey, permissions FROM api
                           WHERE username='" . $user ."'");

    $endresult = mysql_fetch_object($result);

    if ($endresult->apikey == $key) {
        return $endresult->permissions;
    } else {
        return 0;
    }
}

function getAPIPerson($pnr)
{
	getConnection();
	$query = "SELECT id FROM personer WHERE personnr=" . $pnr;
	$result = mysql_query($query);
	$id = mysql_fetch_object($result);
    $person = getPerson($id->id);
    $bank = getPayments($id->id);
    echo $person->personnr . "," . $person->fornamn . "," .
         $person->efternamn . "," . $person->co . "," .
         $person->adress . "," . $person->postnr . "," .
         $person->ort . "," . $person->land . "," .
         $person->telefon . "," . $person->epost . "," .
         $person->aviseraej . "," . $person->senastandrad;
    if (isset($bank)) {
        foreach ($bank as $row) {
            echo "," . $row->period . "," . $row->benamning . "," . $row->betalat . "," . $row->betaldatum;
        }
    }
}

function setAPIPersonData($data)
{
	$PSTNR = str_replace(' ', '', $data->PSTNR);
    getConnection();
	$query = "SELECT id FROM personer WHERE personnr=" . $data->PNR;
	$result = mysql_query($query);
	$id = mysql_fetch_object($result);
    $query = "UPDATE personer SET telefon='" . $data->TEL . "',
			epost ='" . $data->EMAIL . "',
			co='" . $data->CO . "',
			adress='" . $data->ADR . "',
			postnr='" . $PSTNR . "',
			ort='". $data->ORT . "',
			land='" . $data->LAND . "',
			aviseraej='" . $data->AVISEJ . "',
			senastandrad=DATE(NOW())
		WHERE id='" . $id . "'";
    $result = mysql_query($query);
}

function addAPIPerson($data)
{
    getConnection();

	$PSTNR = str_replace(' ', '', $data->PSTNR);
    $query = "INSERT INTO personer
              VALUES ('NULL',
					  '" . mysqL_real_escape_string($data->PNR) . "',
                      '" . mysqL_real_escape_string($data->FNM) . "',
                      '" . mysqL_real_escape_string($data->ENM) . "',
                      '" . mysqL_real_escape_string($data->CO) . "',
                      '" . mysqL_real_escape_string($data->ADR) . "',
                      '" . mysqL_real_escape_string($PSTNR) . "',
                      '" . mysqL_real_escape_string($data->ORT) . "',
                      '" . mysqL_real_escape_string($data->LAND) . "',
                      '" . mysqL_real_escape_string($data->TEL) . "',
                      '" . mysqL_real_escape_string($data->EMAIL) . "',
                      '" . mysqL_real_escape_string($data->AVISEJ) . "',
                      '" . mysqL_real_escape_string($data->FELADR) . "',
                      DATE(NOW()))";
    $result = mysql_query($query);
}

function registerAPIPayment($data)
{
    getConnection();
    $query = "INSERT INTO betalningar
              VALUES ('". $data->PNR . "',
                      '" . $data->PERIOD . "',
                      '" . $data->BETWAY . "',
                      '" . $data->BETDATE . "',
                      '" . $data->BET . "',
                      '" . $data->MEDTYPE . "')";
    $result = mysql_query($query);
}

function checkAdminLogin()
{
    /** FIXME rewrite to check password via database, this is stupid. */
    getConnection();
    $anvNamn = mysql_real_escape_string($_POST['username']);
    $losen = $_POST['pass2'];
    $losen = sha1($losen);

    $query = "SELECT hashpass FROM adminusers
              WHERE username='" . mysql_real_escape_string($anvNamn) . "'";
    $result = mysql_query($query);

    if ($losen == mysql_fetch_object($result)->hashpass) {
        $_SESSION['page']="admin";
    }
}

function addPayment()
{
    getConnection();
    $query = "INSERT INTO betalningar
              VALUES ('" . mysql_real_escape_string($_POST['ID']) . "',
                      '" . mysql_real_escape_string($_POST['PERIOD']) . "',
                      '" . mysql_real_escape_string($_POST['BETWAY']) . "',
                      '" . mysql_real_escape_string($_POST['BETDATE']) . "',
                      '" . mysql_real_escape_string($_POST['BET']) . "',
                      '" . mysql_real_escape_string($_POST['MEDTYPE']) . "')";
	echo $query;
    $result = mysql_query($query);
}

function addPerson()
{
    getConnection();
	$PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    $query = "INSERT INTO personer
              VALUES ('NULL',
					  '" . mysql_real_escape_string($_POST['PNR']) . "',
                      '" . mysql_real_escape_string($_POST['FNM']) . "',
                      '" . mysql_real_escape_string($_POST['ENM']) . "',
                      '" . mysql_real_escape_string($_POST['CO']) . "',
                      '" . mysql_real_escape_string($_POST['ADR']) . "',
                      '" . mysql_real_escape_string($PSTNR) . "',
                      '" . mysql_real_escape_string($_POST['ORT']) . "',
                      '" . mysql_real_escape_string($_POST['LAND']) . "',
                      '" . mysql_real_escape_string($_POST['TEL']) . "',
                      '" . mysql_real_escape_string($_POST['EMAIL']) . "',
                      '" . mysql_real_escape_string($_POST['AVISEJ']) . "',
                      '" . mysql_real_escape_string($_POST['FELADR']) . "',
                      DATE(NOW()))";
	echo $query;
    $result = mysql_query($query);
}

function removePerson()
{
    getConnection();
    $query = "DELETE FROM betalningar
              WHERE personer_id=".mysql_real_escape_string($_POST['ID']);
    $result = mysql_query($query);
    $query = "DELETE FROM personer
              WHERE id=".mysql_real_escape_string($_POST['ID']);
    $result = mysql_query($query);
}

function addPeriod()
{
    getConnection();
    $query = "INSERT INTO perioder (period, forst, sist)
              VALUES ('" . mysql_real_escape_string($_POST['period']) . "',
                      '" . mysql_real_escape_string($_POST['forst']) . "',
                      '" . mysql_real_escape_string($_POST['sist']) . "')";
    $result = mysql_query($query);
    if (mysql_affected_rows()>0) {
        return true;
    }
    return false;
}

function changePeriod()
{
    getConnection();
    $query = "UPDATE perioder
              SET forst='" . mysql_real_escape_string($_POST['forst']) . "',
                  sist='" . mysql_real_escape_string($_POST['sist']) . "'
              WHERE period='" . mysql_real_escape_string($_POST['period']) . "'";
    $result = mysql_query($query);
    if (mysql_affected_rows()>0) {
        return true;
    }
    return false;
}

function updatePerson()
{
	$PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    getConnection();
    $query = "UPDATE personer
              SET personnr='" . mysql_real_escape_string($_POST['PNR']) . "',
                  fornamn='" . mysql_real_escape_string($_POST['FNM']) . "',
                  efternamn='" . mysql_real_escape_string($_POST['ENM']) . "',
                  telefon='" . mysql_real_escape_string($_POST['TEL']) . "',
                  epost ='" . mysql_real_escape_string($_POST['EMAIL']) . "',
                  co='" . mysql_real_escape_string($_POST['CO']) . "',
                  adress='" . mysql_real_escape_string($_POST['ADR']) . "',
                  postnr='" . mysql_real_escape_string($PSTNR) . "',
                  ort='" . mysql_real_escape_string($_POST['ORT']) . "',
                  land='" . mysql_real_escape_string($_POST['LAND']) . "',
                  feladress='" . mysql_real_escape_string($_POST['FELADR']) . "',
                  aviseraej='" . mysql_real_escape_string($_POST['AVISEJ']) . "',
                  senastandrad=DATE(NOW())
              WHERE id='". mysql_real_escape_string($_POST['ID']) . "'";
    $result = mysql_query($query);
}

function sparaStudent()
{
	$PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    getConnection();

    $query = "UPDATE personer
              SET fornamn='" . mysql_real_escape_string($_POST['FNM']) . "',
                  efternamn='" . mysql_real_escape_string($_POST['ENM']) . "',
                  telefon='" . mysql_real_escape_string($_POST['TEL']) . "',
                  epost ='" . mysql_real_escape_string($_POST['EMAIL']) . "',
                  co='" . mysql_real_escape_string($_POST['CO']) . "',
                  adress='" . mysql_real_escape_string($_POST['ADR']) . "',
                  postnr='" . mysql_real_escape_string($PSTNR) . "',
                  ort='" . mysql_real_escape_string($_POST['ORT']) . "',
                  land='" . mysql_real_escape_string($_POST['LAND']) . "',
                  feladress='" . mysql_real_escape_string($_POST['FELADR']) . "',
                  aviseraej='" . mysql_real_escape_string($_POST['AVISEJ']) . "',
                  senastandrad=DATE(NOW())
              WHERE id='" . mysql_real_escape_string($_POST['ID']) . "'";
    $result = mysql_query($query);
}


function updateAvgift()
{
    getConnection();
    for ($i=1;$i<=3;$i++) {
        $avg = "avg" . $i;
        if ($_POST[$avg]==0 || $_POST[$avg]=="") {
            $query = "DELETE FROM avgift
                      WHERE perioder_period='". mysql_real_escape_string($_POST["period"]) . "' AND
                            medlemstyp_id='" . $i . "'";
            $result = mysql_query($query);
        } else {
            $query = "SELECT avgift FROM avgift
                      WHERE perioder_period='" . mysql_real_escape_string($_POST["period"]) . "' AND
                            medlemstyp_id='" . $i . "'";
            $result = mysql_query($query);
            if (mysql_affected_rows()>0) {
                $query = "UPDATE avgift
                          SET avgift='" . mysql_real_escape_string($_POST[$avg]) . "'
                          WHERE perioder_period='" . mysql_real_escape_string($_POST["period"]) . "' AND
                                medlemstyp_id='" . $i . "'";
                $result = mysql_query($query);
            } else {
                $query = "INSERT INTO avgift (perioder_period, medlemstyp_id, avgift)
                          VALUES ('" . mysql_real_escape_string($_POST["period"]) . "',
                                  '" . $i . "',
                                  ". mysql_real_escape_string($_POST[$avg]) . ")";
                $result = mysql_query($query);
            }
        }
    }
}

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

function getMembers($payment=true,$adress=false,$page=0,$pagesize=20)
{
    unset($query);
    $query = "SELECT personnr, efternamn, fornamn, epost, telefon";
    if ($adress) {
        $query .= ", co, adress, postnr, land, feladress, aviseraej";
    }
    if ($payment) {
        $query .= ", period, benamning, avgift, betalat";
    }
    $query .= " FROM betalningar
                LEFT JOIN personer ON id=personer_id
                LEFT JOIN perioder ON perioder_period=period
                LEFT JOIN avgift ON
                          avgift.perioder_period=betalningar.perioder_period AND
                          avgift.medlemstyp_id=betalningar.medlemstyp_id
                LEFT JOIN medlemstyp ON id=betalningar.medlemstyp_id
                WHERE forst<DATE(NOW()) AND
                      sist>DATE(NOW())
                ORDER BY personnr DESC";
    if ($page>0) {
        $query .= " LIMIT 20";
    }
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $persons[] = $row;
    }
    return $persons;
}


function getNonMembers()
{
    $Persons = getPersons();
    $Members = getMembers();
    $i = 0;

    foreach ($Persons as $prs) {
        $add = true;
        foreach ($Members as $mbr) {
            if ($mbr->personnr == $prs->personnr) {
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

function putInfoBox($head,$value)
{
    echo "<div class=\"info\">
			<h3>$head</h3>
			<h7>" . $value . "</h7>
		  </div>";
}

function countMembers()
{
    $query = "SELECT COUNT(personer_id) AS NumberOfMembers
              FROM betalningar
              LEFT JOIN perioder ON perioder_period=period
              WHERE forst<DATE(NOW()) AND
              sist>DATE(NOW())";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);

    $memberCount = $row->NumberOfMembers;
    return $memberCount;
}

function isMember($pnr)
{
    $query = "SELECT COUNT(personer_id) AS IsMember
              FROM betalningar
              LEFT JOIN perioder ON perioder_period=period
			  LEFT JOIN personer ON personer_id=id
              WHERE forst<DATE(NOW()) AND
              sist>DATE(NOW()) AND
			  personnr=" . $pnr;
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);

    $IsMember = $row->IsMember;
	if($IsMember>0)
	{
		return true;
	}
    return false;
}

function getPerson($id)
{
    getConnection();
    $query = "SELECT * FROM personer
              WHERE id=" . $id;
    $result = mysql_query($query);
    $person = mysql_fetch_object($result);
    return $person;
}

function getPersons()
{
    getConnection();
    $query = "SELECT * FROM personer";
    $result = mysql_query($query);

    $i = 0;
    while ($row = mysql_fetch_object($result)) {
        $persons[] = $row;
    }
    return $persons;
}

function findPNR($pnr)
{
    getConnection();
    $query = "SELECT * FROM personer
              WHERE personnr LIKE '$pnr'";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE personnr LIKE '$pnr%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE personnr LIKE '%$pnr%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
    }
	if(isset($persons))
	{
		return $persons;
	}
}

function findFNM($fnm)
{
    getConnection();
    $query = "SELECT * FROM personer
              WHERE fornamn LIKE '$fnm'";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '$fnm%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '%$fnm%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
    }
    return $persons;
}

function findENM($enm)
{
    getConnection();
    $query = "SELECT * FROM personer
              WHERE efternamn LIKE '$enm'";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE efternamn LIKE '$enm%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE efternamn LIKE '%$enm%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
    }
    return $persons;
}

function findNM($fnm, $enm)
{
    getConnection();
    $query = "SELECT * FROM personer
              WHERE fornamn LIKE '$fnm'
			  AND efternamn LIKE '$enm'";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '$fnm%' AND
                        efternamn LIKE '$enm%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '%$fnm%' AND
                        efternamn LIKE '%$enm%'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
    }
    return $persons;
}

function getMandates($id)
{
    $query = "SELECT benamning, beskrivning, period, forst, sist FROM personer
              LEFT JOIN personer_uppdrag ON personer.id = personer_uppdrag.personer_id
              LEFT JOIN uppdrag ON personer_uppdrag.uppdrag_id=uppdrag.id
              LEFT JOIN perioder ON personer_uppdrag.perioder_period=perioder.period
              WHERE id='$id'";
	$result = mysql_query($query);
	if($result)
	{
		while ($row = mysql_fetch_object($result)) {
			$mandates[] = $row;
		}
	}
    
	if(isset($mandates))
	{
		return $mandates;
	}
}

function getCurrentMandates($pnr)
{
    $query = "SELECT benamning, beskrivning,
              FROM personer
              LEFT JOIN personer_uppdrag ON personnr = personer_personnr
              LEFT JOIN uppdrag ON uppdrag_id=id
              LEFT JOIN perioder ON perioder_period=period
              WHERE personnr=$pnr AND
                    forst<CURDATE() AND
                    sist>CURDATE()";
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $mandates[] = $row;
    }
    return $mandates;
}

function getPayments($id)
{
    $query = "SELECT period, betalsatt, betalat, avgift, benamning, forst, sist, betaldatum FROM betalningar
              LEFT JOIN perioder ON perioder_period=period
              LEFT JOIN avgift ON
                        betalningar.perioder_period=avgift.perioder_period AND
                        betalningar.medlemstyp_id=avgift.medlemstyp_id
              LEFT JOIN medlemstyp ON id=betalningar.medlemstyp_id
              WHERE personer_id='$id' AND deleted != 1";
    $result = mysql_query($query);
	if($result)
	{
		while ($row = mysql_fetch_object($result)) {
			$payments[] = $row;
		}
	}
	if(isset($payments))
	{
		return $payments;
	}
}

function putBoxStart()
{
    echo "<div class=\"outerdivs\">";
}

function putBoxEnd()
{
    echo "</div>";
}

function getPeriods()
{
    getConnection();
    $query = "SELECT period, forst, sist FROM perioder
              ORDER BY forst DESC, sist DESC";
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $periods[] = $row;
    }
    return $periods;
}

function getPeriod($period)
{
    getConnection();
    $query = "SELECT period, forst, sist FROM perioder
              WHERE period='$period'";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    return $row;
}

function updatePeriod($period)
{
    getConnection();
    $query = "UPDATE perioder
              SET forst='$period->Forst',
                  sist='$period->Sist'
              WHERE period='$period->Period'";
    $result = mysql_query($query);
    if (mysql_affected_rows()>0) {
        return true;
    }
    return false;
}

function getAvgifter()
{
    getConnection();
    $query = "SELECT period, medlemstyp_id, avgift, forst, sist FROM perioder
              LEFT JOIN avgift ON period=perioder_period
              ORDER BY forst DESC, sist DESC";
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $avgifter[$row->period]->period = $row->period;
        $avgifter[$row->period]->avgift[$row->medlemstyp_id] = $row->avgift;
        $avgifter[$row->period]->forst = $row->forst;
        $avgifter[$row->period]->sist = $row->sist;
    }
    return $avgifter;
}

function getMedlemstyper()
{
    getConnection();
    $query = "SELECT * FROM medlemstyp";
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $medlemstyper[$row->id]->id = $row->id;
        $medlemstyper[$row->id]->benamning = $row->benamning;
    }
    return $medlemstyper;
}

function removePayment()
{
	getConnection();
	$query = "UPDATE betalningar SET deleted='1' WHERE personer_personnr='" . mysql_real_escape_string($_POST['pnr']) . 
			 "' AND perioder_period='" . mysql_real_escape_string($_POST['per']) . "' AND betalsatt='" . mysql_real_escape_string($_POST['bets']) . "'";
	mysql_query($query);
}
?>
