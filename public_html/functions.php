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
	if(isset($_SESSION['page']))
	{
		if ($_SESSION['page']=="admin")
		{
			getConnection();
			$query = "SELECT id FROM adminusers WHERE id='" . $_SESSION['id'] . "'";
			mysql_query($query);
			if(mysql_affected_rows()!=1)
			{
				session_destroy();
				header("Location: /");
				exit();
			}
		}
	}
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
    if (!empty($_POST["handler"])) {
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
    $query = "INSERT INTO api(username, apikey, permissions)
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
	if ($_POST['id']==$_SESSION['id'])
	{
		echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">Redirecting</a>";
		echo "<script type=\"text/javascript\">";
		echo "location.href=''";
		echo "</script>";
	}
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
		WHERE id='" . $id->id . "'";
    $result = mysql_query($query);
}

function addAPIPerson($data)
{
    getConnection();

	$PSTNR = str_replace(' ', '', $data->PSTNR);
    $query = "INSERT INTO personer(personnr, fornamn, efternamn,
                                   co, adress, postnr, ort, land,
                                   telefon, epost,
                                   aviseraej, feladress, senastandrad
                                  )
              VALUES ('" . mysqL_real_escape_string($data->PNR) . "',
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
    // Den här funktionen borde vara beroende av addPayment istället...
    // FIXME!!
    // Not even close. PNR && PERIOD && MED>TYPE är fel.
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
    $result = mysql_query($query);
}

function checkAdminLogin()
{
	getConnection();
	$anvNamn = mysql_real_escape_string($_POST['username']);
	$hashpass = sha1($_POST['pass2']);
	$query = "SELECT id FROM adminusers
              WHERE username='" . mysql_real_escape_string($anvNamn) . "'
			  AND hashpass='" . $hashpass . "'";
	$result = mysql_query($query);
	if (mysql_affected_rows() == 1)
	{
		$row = mysql_fetch_object($result);
        $_SESSION['page']="admin";
		$_SESSION['id']=$row->id;
    }
}

function addPayment()
{
    getConnection();

    $r=mysql_query("SELECT id AS avgift_id FROM avgift
                 WHERE medlemstyp_id=".mysql_real_escape_string($_POST['MEDTYPE'])." AND
                 perioder_id=".mysql_real_escape_string($_POST['PERIOD'])) or die(mysql_error());
    $a=mysql_fetch_assoc($r);

    $query = "INSERT INTO betalningar (personer_id, avgift_id, betalsatt_id, betaldatum, betalat)
              VALUES ('" . mysql_real_escape_string($_POST['ID']) . "',
                      '" . $a["avgift_id"] . "',
                      '" . mysql_real_escape_string($_POST['BETWAY']) . "',
                      '" . mysql_real_escape_string($_POST['BETDATE']) . "',
                      '" . mysql_real_escape_string($_POST['BET']) . "')";
    $result = mysql_query($query) or die(mysql_error());
}

function addPerson()
{
    getConnection();
    $PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    $query = "INSERT INTO personer(personnr, fornamn, efternamn, co,
                                   adress, postnr, ort, land, telefon,
                                   epost, aviseraej, feladress
                                  )
              VALUES (
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
                      '" . mysql_real_escape_string($_POST['FELADR']) . "'
                     )";
    $result = mysql_query($query);
    $_GET['id'] = mysql_insert_id();
}

function removePerson()
{
    getConnection();
    $query = "UPDATE betalningar SET deleted='1'
              WHERE personer_id=".mysql_real_escape_string($_POST['ID']);
    $result = mysql_query($query);
    $query = "UPDATE personer SET deleted='1'
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
    $person = getPerson(mysql_real_escape_string($_POST['ID']));
    $haschanged = false;
    $PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
    if($person->personnr != mysql_real_escape_string($_POST['PNR'])) {
        $haschanged = true;
    } else if ($person->fornamn != mysql_real_escape_string($_POST['FNM'])) {
        $haschanged = true;
    } else if ($person->efternamn != mysql_real_escape_string($_POST['ENM'])) {
        $haschanged = true;
    } else if ($person->telefon != mysql_real_escape_string($_POST['TEL'])) {
        $haschanged = true;
    } else if ($person->epost != mysql_real_escape_string($_POST['EMAIL'])) {
        $haschanged = true;
    } else if ($person->co != mysql_real_escape_string($_POST['CO'])) {
        $haschanged = true;
    } else if ($person->adress != mysql_real_escape_string($_POST['ADR'])) {
        $haschanged = true;
    } else if ($person->postnr != mysql_real_escape_string($PSTNR)) {
        $haschanged = true;
    } else if ($person->ort != mysql_real_escape_string($_POST['ORT'])) {
        $haschanged = true;
    } else if ($person->land != mysql_real_escape_string($_POST['LAND'])) {
        $haschanged = true;
    } else if ($person->feladress != mysql_real_escape_string($_POST['FELADR'])) {
        $haschanged = true;
    } else if ($person->aviseraej != mysql_real_escape_string($_POST['AVISEJ'])) {
        $haschanged = true;
    }

	if($haschanged)
    {
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
    if (isset($_POST["avgiftid"]) && $_POST["avgiftid"] == -1) {
        // FIXME make period_id + medlemstyp_id a UNIQUE key
        //       then convert this to INSERT INTO tbl ON DUPLICATE KEY UPDATE...
        $query = "INSERT INTO avgift (perioder_id, medlemstyp_id, avgift)
                  VALUES (" . mysql_real_escape_string($_POST['period_id']) . ",
                          " . mysql_real_escape_string($_POST['medlemstyp_id']) . ",
                          " . mysql_real_escape_string($_POST['avgiften']) . ")";
    } elseif (isset($_POST["avgiftid"]) && $_POST["avgiftid"] > 0) {
        $query = "UPDATE avgift
                  SET avgift = ".mysql_real_escape_string($_POST['avgiften'])."
                  WHERE id=".mysql_real_escape_string($_POST['avgiftid']);
    } else {
        exit("FATAL ERROR. Execution Stopped.");
    }
    mysql_query($query) or die(mysql_error());
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
        $query .= ", period, benamning, avgift, betalat, benamning";
    }
    $query .= " FROM betalningar
                LEFT JOIN personer ON betalningar.personer_id=personer.id
                LEFT JOIN avgift ON betalningar.avgift_id=avgift.id
                LEFT JOIN perioder ON avgift.perioder_id=perioder.id
                LEFT JOIN medlemstyp ON avgift.medlemstyp_id=medlemstyp.id
                WHERE forst<DATE(NOW()) AND
                      sist>DATE(NOW())
                ORDER BY personnr DESC";
    if ($page>0) {
        $query .= " LIMIT 20";
    }
    $result = mysql_query($query);
    $persons = null;
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
              LEFT JOIN avgift ON betalningar.avgift_id=avgift.id
              LEFT JOIN perioder ON avgift.perioder_id=perioder.id
              WHERE perioder.forst<DATE(NOW()) AND
              perioder.sist>DATE(NOW())";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);

    $memberCount = $row->NumberOfMembers;
    return $memberCount;
}

function isMember($pnr)
{
    $query = "SELECT COUNT(personer_id) AS IsMember
              FROM betalningar
              LEFT JOIN avgift ON betalningar.avgift_id=avgift.id
              LEFT JOIN perioder ON avgift.perioder_id=perioder.id
			  LEFT JOIN personer ON betalningar.personer_id=personer.id
              WHERE forst<DATE(NOW()) AND
              sist>DATE(NOW()) AND
              personnr='$pnr'";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);

    $IsMember = $row->IsMember;
	if($IsMember>0)
	{
		return true;
	}
    return false;
}

function getPerson($id,$getdeleted=false)
{
    getConnection();
    $query = "SELECT * FROM personer
              WHERE id=" . $id;
    if(!$getdeleted)
    {
        $query  .= " AND deleted != 1";
    }
    $result = mysql_query($query);
    $person = mysql_fetch_object($result);
    return $person;
}

function getPersons()
{
    getConnection();
    $query = "SELECT * FROM personer WHERE deleted != 1";
    $result = mysql_query($query);

    $i = 0;
    while ($row = mysql_fetch_object($result)) {
        $persons[] = $row;
    }
    return $persons;
}

function findEMA($ema) {
    getConnection();
    $query = "SELECT * FROM personer
              WHERE epost LIKE '$ema' AND deleted != 1";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
	  $persons[] = $row;
	} else {
        $query = "SELECT * FROM personer
                  WHERE epost LIKE '$ema%' AND deleted != 1";
		$result = mysql_query($query);
		while ($row = mysql_fetch_object($result)) {
		    if (!isset($persons[$row->epost])) {
			    $persons[$row->epost] = $row;
			}
		}
		$query = "SELECT * FROM personer
                  WHERE epost LIKE '%$ema%' AND deleted != 1";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->epost])) {
                $persons[$row->epost] = $row;
            }
        }
    }
    if(isset($persons))
    {
        return $persons;
    }
}

function findPNR($pnr)
{
    getConnection();
    $query = "SELECT * FROM personer
              WHERE personnr LIKE '$pnr' AND deleted != 1";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE personnr LIKE '$pnr%' AND deleted != 1";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE personnr LIKE '%$pnr%' AND deleted != 1";
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
              WHERE fornamn LIKE '$fnm' AND deleted != 1";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '$fnm%' AND deleted != 1";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '%$fnm%' AND deleted != 1";
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
              WHERE efternamn LIKE '$enm' AND deleted != 1";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE efternamn LIKE '$enm%' AND deleted != 1";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE efternamn LIKE '%$enm%' AND deleted != 1";
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
			  AND efternamn LIKE '$enm'
			  AND deleted != 1";
    $result = mysql_query($query);
    if ($row = mysql_fetch_object($result)) {
        $persons[$row->personnr] = $row;
    } else {
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '$fnm%' AND
                        efternamn LIKE '$enm%'
                        AND deleted != 1";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
            if (!isset($persons[$row->personnr])) {
                $persons[$row->personnr] = $row;
            }
        }
        $query = "SELECT * FROM personer
                  WHERE fornamn LIKE '%$fnm%' AND
                        efternamn LIKE '%$enm%'
                        AND deleted != 1";
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
              LEFT JOIN perioder ON personer_uppdrag.perioder_id=perioder.id
              WHERE personer.id='$id'
              AND personer.deleted != 1";
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
              LEFT JOIN personer_uppdrag ON personer_uppdrag.person_id = personer.id
              LEFT JOIN uppdrag ON personer_uppdrag.uppdrag_id=uppdrag.id
              LEFT JOIN perioder ON personer_uppdrag.perioder_id=perioder.id
              WHERE personnr=$pnr AND
                    forst<CURDATE() AND
                    sist>CURDATE()
              AND personer.deleted != 1";
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $mandates[] = $row;
    }
    return $mandates;
}

function getPayments($id)
{
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
              WHERE betalningar.personer_id='$id' AND deleted != 1";
    $result = mysql_query($query) or die(mysql_error());
    $payments = null;
    while ($row = mysql_fetch_object($result)) {
        $payments[] = $row;
    }
    return $payments;
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
    $query = "SELECT id, period, forst, sist FROM perioder
              ORDER BY forst, sist";
    $result = mysql_query($query);
    while ($row = mysql_fetch_object($result)) {
        $periods[] = $row;
    }
    return $periods;
}

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

    $result = mysql_query($query);
    if (mysql_affected_rows()>0) {
        return true;
    }
    return false;
}

function getAvgifter()
{
    getConnection();
    $query = "SELECT perioder.id AS perioder_id, period, medlemstyp_id, avgift, avgift.id AS avgift_id, forst, sist FROM perioder
              LEFT JOIN avgift ON perioder.id=avgift.perioder_id
              ORDER BY forst DESC, sist DESC, medlemstyp_id";
    $result = mysql_query($query);
    $avgifter = null;
    while ($row = mysql_fetch_assoc($result)) {
        $avgifter[$row["perioder_id"]][] = $row;
    }
    return $avgifter;
}

function getMedlemstyper()
{
    getConnection();
    $query = "SELECT * FROM medlemstyp";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $medlemstyper[$row["id"]] = $row["benamning"];
    }
    return $medlemstyper;
}

function getBetalsatt()
{
    getConnection();
    $query = "SELECT * FROM betalsatt";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $betalsatt[$row["id"]] = $row["benamning"];
    }
    return $betalsatt;
}

function removePayment()
{
	getConnection();
	$query = "UPDATE betalningar SET deleted='1' WHERE id='" . mysql_real_escape_string($_POST['betid']) . "'";
	mysql_query($query) or die(mysql_error());
}

function getNumberOfMembers($benamning)
{
	getConnection();
	$query = "SELECT count(betalningar.id) AS antal, medlemstyp.benamning
              FROM betalningar
              LEFT JOIN avgift ON betalningar.avgift_id = avgift.id
              LEFT JOIN medlemstyp ON avgift.medlemstyp_id = medlemstyp.id
              WHERE betalningar.deleted = 0 AND medlemstyp.benamning = '". $benamning ."'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_object($result);

	return $row->antal;
}
?>
