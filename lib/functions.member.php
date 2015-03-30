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
 * API helper function that extracts all members and prints them in a
 * comma separated way to be parsed from the requestor side.
 *
 * @param string $ssn A personal number, can contain both numbers and
 *                    letters. Designed for Swedish customs.
 *
 * @return void
 */
function getAPIMember($ssn)
{
    $DBH = new DB();
    $DBH->query("SELECT id FROM member WHERE ssn= :ssn");
    $DBH->bind(":ssn", $ssn);
    $id = $DBH->single();
    $id = $id["id"];
    $person = getMember($id);
    $payments = getPayments($id);
    echo $person["ssn"] . "," . $person["firstname"] . "," .
         $person["lastname"] . "," . $person["co"] . "," .
         $person["address"] . "," . $person["postalnr"] . "," .
         $person["city"] . "," . $person["country"] . "," .
         $person["phone"] . "," . $person["email"] . "," .
         $person["donotadvertise"] . "," . $person["lastedit"];
    if (isset($payments)) {
        foreach ($payments as $row) {
            echo "," . $row["period"] . "," . $row["naming"] . "," . $row["paid"] . "," . $row["paymentdate"];
        }
    }
}

/**
 * Extracts members from the database using a search pattern.
 * echoes the membes in a structured CSV.
 */
function getAPIMembers()
{
    $DBH = new DB();
    unset($query);
    $query = "SELECT ssn, lastname, firstname, email, phone";
    $query .= ", co, address, postalnr, city, country, wrongaddress, donotadvertise";
    $query .= " FROM member
        WHERE deleted=0
        GROUP BY ssn
        ORDER BY ssn DESC";
    $DBH->query($query);
    $members = $DBH->resultset();
    $result = Array();
    foreach ($members as $member) {
        if (isMember($member["ssn"])) {
            echo $member["ssn"] . "," . $member["lastname"]  . "," . $member["firstname"] . "," . $member["email"]
            . "," . $member["co"] . "," . $member["address"] . "," . $member["postalnr"] . "," . $member["city"]
            . $member["country"] . "," . $member["wrongaddress"] . "," . $member["donotadvertise"] . "\n";
        }
    }
}

/**
 * Add records for a member.
 *
 * @param object $data API request data.
 *
 * @return void
 */
function addMember($data=false)
{
    print_r($data);
    if ($data) {
        $DBH = new DB();
        $PSTNR = str_replace(' ', '', $data->PSTNR);
        $DBH->query("INSERT INTO member SET ssn = :ssn,
                    firstname = :fnm,
                    lastname = :enm,
                    co = :co,
                    address = :addr,
                    postalnr = :psnr,
                    city = :city,
                    country = :country,
                    phone = :phone,
                    email = :eml,
                    donotadvertise= :avis,
                    wrongaddress = :fadr,
                    lastedit = DATE(NOW())");
        $DBH->bind(":ssn", $data->SSN);
        $DBH->bind(":fnm", $data->FNM);
        $DBH->bind(":enm", $data->LNM);
        $DBH->bind(":co", $data->CO);
        $DBH->bind(":addr", $data->ADDR);
        $DBH->bind(":psnr", $PSTNR);
        $DBH->bind(":city", $data->CITY);
        $DBH->bind(":country", $data->COUNTRY);
        $DBH->bind(":phone", $data->PHONE);
        $DBH->bind(":eml", $data->EMAIL);
        $DBH->bind(":avis", $data->DONOTAD);
        $DBH->bind(":fadr", $data->WRNADDR);
        $DBH->execute();
    } else {
        $DBH = new DB();
        $PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
        $DBH->query("INSERT INTO member SET ssn = :ssn,
                    firstname = :fnm,
                    lastname = :lnm,
                    co = :co,
                    address = :addr,
                    postalnr = :psnr,
                    city = :city,
                    country = :country,
                    phone = :pho,
                    email = :eml,
                    donotadvertise= :donotad,
                    wrongaddress = :wrngaddr,
                    lastedit = DATE(NOW())");
        $DBH->bind(":ssn", $_POST['SSN']);
        $DBH->bind(":fnm", $_POST['FNM']);
        $DBH->bind(":lnm", $_POST['LNM']);
        $DBH->bind(":co", $_POST['CO']);
        $DBH->bind(":addr", $_POST['ADDR']);
        $DBH->bind(":psnr", $PSTNR);
        $DBH->bind(":city", $_POST['CITY']);
        $DBH->bind(":country", $_POST['COUNTRY']);
        $DBH->bind(":pho", $_POST['PHO']);
        $DBH->bind(":eml", $_POST['EMAIL']);
        $DBH->bind(":donotad", "0");
        $DBH->bind(":wrngaddr", "0");
        $DBH->execute();
    }
    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "addMember", "Created a new member with the id " . $DBH->lastInsertId());
}

/**
 * Marks a member as deleted from the records.
 *
 * @return void
 */
function removeMember()
{
    $DBH = new DB();
    $DBH->query("UPDATE payment SET deleted = '1'
                WHERE member_id = :id");
    $DBH->bind(":id", $_POST['ID']);
    $DBH->execute();

    $DBH->query("UPDATE member SET deleted = '1'
                WHERE id = :id");
    $DBH->bind(":id", $_POST['ID']);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "removeMember", "Flagged member " . $_POST['ID'] . "as deleted");

}

/**
 * Updates the records for a specific member.
 *
 * @param object $data API request data.
 *
 * @return void
 */
function updateMember($data=false)
{
    $id = "";
    if ($data) {
        $PSTNR = str_replace(' ', '', $data->PSTNR);
        $DBH = new DB();

        $DBH->query("SELECT id FROM member WHERE ssn=:ssn");
        $DBH->bind(":ssn", $data->SSN);
        $memberID = $DBH->single();

        $id = $memberID["id"];

        $DBH->query("UPDATE member SET phone= :phone,
                        email = :email,
                        co= :co,
                        address= :address,
                        postalnr= :postalnr,
                        city= :city,
                        country= :country,
                        donotadvertise= :donotadvertise,
                        lastedit= DATE(NOW())
                    WHERE id= :id");
        $DBH->bind(":phone", $data->PHO);
        $DBH->bind(":email", $data->EMAIL);
        $DBH->bind(":co", $data->CO);
        $DBH->bind(":address", $data->ADDR);
        $DBH->bind(":postalnr", $PSTNR);
        $DBH->bind(":city", $data->CITY);
        $DBH->bind(":country", $data->COUNTRY);
        $DBH->bind(":donotadvertise", $data->DONOTAD);
        $DBH->bind(":id", $memberID["id"]);
        $DBH->execute();
    } else {
        $member = getMember($_POST['ID']);
        $id = $_POST['ID'];
        $haschanged = false;
        $PSTNR = str_replace(' ', '', urldecode($_POST['PSTNR']));
        // The ugly way to make sure that checkboxes are "set" when not ticked...
        if (!isset($_POST['WRONGADDR'])) {
            $_POST['WRONGADDR'] = 0;
        }
        if (!isset($_POST['DONOTAD'])) {
            $_POST['DONOTAD'] = 0;
        }
        if ($member["ssn"] != $_POST['SSN']) {
            $haschanged = true;
        } else if ($member["firstname"] != $_POST['FNM']) {
            $haschanged = true;
        } else if ($member["lastname"] != $_POST['LNM']) {
            $haschanged = true;
        } else if ($member["phone"] != $_POST['PHO']) {
            $haschanged = true;
        } else if ($member["email"] != $_POST['EMAIL']) {
            $haschanged = true;
        } else if ($member["co"] != $_POST['CO']) {
            $haschanged = true;
        } else if ($member["address"] != $_POST['ADDR']) {
            $haschanged = true;
        } else if ($member["postalnr"] != $PSTNR) {
            $haschanged = true;
        } else if ($member["city"] != $_POST['CITY']) {
            $haschanged = true;
        } else if ($member["country"] != $_POST['COUNTRY']) {
            $haschanged = true;
        } else if ($member["wrongaddress"] != $_POST['WRONGADDR']) {
            $haschanged = true;
        } else if ($member["donotadvertise"] != $_POST['DONOTAD']) {
            $haschanged = true;
        }

        if ($haschanged) {
            $DBH = new DB();
            $DBH->query("UPDATE member
                      SET ssn = :ssn,
                          firstname = :firstname,
                          lastname = :lastname,
                          phone= :phone,
                          email = :email,
                          co = :co,
                          address = :address,
                          postalnr = :postalnr,
                          city = :city,
                          country = :country,
                          wrongaddress = :wrongaddress,
                          donotadvertise = :donotadvertise,
                          lastedit = DATE(NOW())
                      WHERE id='". $_POST['ID'] . "'");
                $DBH->bind(":ssn", $_POST['SSN']);
                $DBH->bind(":firstname", $_POST['FNM']);
                $DBH->bind(":lastname", $_POST['LNM']);
                $DBH->bind(":phone", $_POST['PHO']);
                $DBH->bind(":email", $_POST['EMAIL']);
                $DBH->bind(":co", $_POST['CO']);
                $DBH->bind(":address", $_POST['ADDR']);
                $DBH->bind(":postalnr", $PSTNR);
                $DBH->bind(":city", $_POST['CITY']);
                $DBH->bind(":country", $_POST['COUNTRY']);
                $DBH->bind(":wrongaddress", $_POST['WRONGADDR']);
                $DBH->bind(":donotadvertise", $_POST['DONOTAD']);
            $DBH->execute();
        }
    }

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "updateMember", "updated member " . $id);
}

/**
 * Helper function to construct the search URL for the javascript
 * forwarding.
 *
 * @return void
 */
function composeSearchURL()
{
    if (isset($_POST["SSN"]) && !($_POST["SSN"]=="")) {
        $url = "?page=searchresult&ssn=" . $_POST["SSN"];
    } elseif (isset($_POST["FNM"]) && !($_POST["FNM"]=="")) {
        if (isset($_POST["LNM"]) && !($_POST["LNM"]=="")) {
            $url = "?page=searchresult&fnm=" . $_POST["FNM"] . "&lnm=" . $_POST["LNM"];
        } else {
            $url = "?page=searchresult&fnm=" . $_POST["FNM"];
        }
    } elseif (isset($_POST["LNM"]) && !($_POST["LNM"]=="")) {
        $url = "?page=searchresult&lnm=" . $_POST["LNM"];
    } elseif (isset($_POST["EMAIL"]) && !($_POST["EMAIL"]=="")) {
        $url = "?page=searchresult&email=" . $_POST["EMAIL"];
    }
    echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . $url . "\">Redirecting</a>";
    echo "<script type=\"text/javascript\">";
    echo "location.href=\"" . $url . "\"";
    echo "</script>";
}

/**
 * Extracts members from the database using a search pattern.  Returns
 * the membes in a array with a array per member, index "member" for
 * member information and "payment" for payment information if that
 * was requested.
 *
 * @param boolean $payment  If true the payment information is
 *                          included.
 * @param boolean $address  If true the address of the member is
 *                          included.
 * @param int     $page     Indicates a page size limit on number of
 *                          records.
 * @param int     $pagesize Defined but not used.
 *
 * @return mixed
 */
function getMembers($payment=true,$address=false,$page=0,$pagesize=20)
{
    $DBH = new DB();
    unset($query);
    $query = "SELECT id, ssn, lastname, firstname, email, phone";
    if ($address === true) {
        $query .= ", co, address, postalnr, country, wrongaddress, donotadvertise";
    }
    $query .= " FROM member
        WHERE deleted=0
        GROUP BY ssn
                ORDER BY ssn DESC";
    if ($page>0) {
        $query .= " LIMIT 20";
    }
    $DBH->query($query);
    $members = $DBH->resultset();
    $result = Array();
    foreach ($members as $member) {
        if (isMember($member["ssn"])) {
            $temp=array();
            $temp["member"] = $member;
            if ($payment == true) {
                $temp["payment"] = getPayments($member["id"], true)[0];
            }
            $result[] = $temp;

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
    $DBH = new DB();
    unset($query);
    $query = "SELECT ssn, lastname, firstname, email, phone";
    $query .= " FROM member
                WHERE deleted=0
                GROUP BY ssn
                ORDER BY ssn DESC";
    $DBH->query($query);
    $members = $DBH->resultset();
    $result = Array();
    foreach ($members as $member) {
        if (!isMember($member["ssn"])) {
            $result[] = $member;
        }
    }
    return $result;
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
    $DBH->query("SELECT COUNT(DISTINCT member_id) AS NumberOfMembers
                FROM payment
                LEFT JOIN fee ON payment.fee_id=fee.id
                LEFT JOIN period ON fee.period_id=period.id
                LEFT JOIN member ON payment.member_id=member.id
                WHERE period.first<=DATE(NOW()) AND
                period.last>=DATE(NOW()) AND
                payment.deleted != 1 AND
                member.deleted != 1");
    $Count = $DBH->single();
    $memberCount = $Count["NumberOfMembers"];
    return $memberCount;
}

/**
 * Helper function to check if a personal number is considered as
 * member or not.
 *
 * @param string $ssn A personal number, can contain both numbers and
 * letters. Based on Swedish customs.
 *
 * @return boolean
 */
function isMember($ssn)
{
    $DBH = new DB();
    $DBH->query("SELECT COUNT(member_id) AS IsMember
                FROM payment LEFT JOIN fee ON payment.fee_id=fee.id
                LEFT JOIN period ON fee.period_id=period.id
                LEFT JOIN member ON payment.member_id=member.id
                WHERE first<=DATE(NOW()) AND
                last>=DATE(NOW()) AND
                ssn = :ssn AND
                payment.deleted = 0");
    $DBH->bind(":ssn", $ssn);

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
function getMember($id, $getdeleted=false)
{
    $DBH = new DB();
    $query = "SELECT * FROM member
              WHERE id = :pid";
    if (!$getdeleted) {
        $query .= " AND deleted != 1";
    }
    $DBH->query($query);
    $DBH->bind(":pid", $id);

    $member = $DBH->single();
    return $member;
}

/**
 * Extract information about a person that is registered.
 * Even if the person is deleted
 * Returns the information in a array.
 *
 * @param int $ssn The person Socialsecuritynumber
 *
 * @return mixed
 */
function getRegisteredPersonBySsn($ssn)
{
    $DBH = new DB();
    $query = "SELECT * FROM member
              WHERE ssn = :ssn";
    $DBH->query($query);
    $DBH->bind(":ssn", $ssn);

    $member = $DBH->single();
    return $member;
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
    $DBH->query("SELECT * FROM member
                WHERE email LIKE :ema AND deleted != 1");
    $DBH->bind(":ema", "%".$ema."%");
    $members = $DBH->resultset();
    if (!empty($members)) {
        return $members;
    }
}

/**
 * Searches for members matching the personal number field.
 * Will not search for members marked as deleted.
 *
 * FIXME needs work.
 *
 * @param string $ssn Text to search for in the personal number field.
 *
 * @return mixed
 */
function findPNR($ssn)
{
    $DBH = new DB();
    $ssn = str_ireplace("-", "", $ssn);
    $DBH->query("SELECT * FROM member
              WHERE ssn LIKE :ssn AND deleted != 1");
    $DBH->bind(":ssn", $ssn."%");
    $members = $DBH->resultset();
    if (!empty($members)) {
        return $members;
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
    $DBH->query("SELECT * FROM member
              WHERE firstname SOUNDS LIKE :fnm AND deleted != 1");
    $DBH->bind(":fnm", $fnm);
    $members = $DBH->resultset();
    if (!empty($members)) {
        return $members;
    }
}

/**
 * Searches for members matching the surname field.
 * Will not search for members marked as deleted.
 *
 * FIXME needs work.
 *
 * @param string $lnm Text to search for in the surname field.
 *
 * @return mixed
 */
function findLNM($lnm)
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM member
              WHERE lastname SOUNDS LIKE :lnm AND deleted != 1");
    $DBH->bind(":lnm", $lnm);
    $members = $DBH->resultset();
    if (!empty($members)) {
        return $members;
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
 * @param string $lnm Text to search for in the surname field.
 *
 * @return mixed
 */
function findNM($fnm, $lnm)
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM member
                WHERE firstname SOUNDS LIKE :fnm AND
                    lastname SOUNDS LIKE :lnm
                    AND deleted != 1");
    $DBH->bind(":fnm", $fnm);
    $DBH->bind(":lnm", $lnm);
    $members = $DBH->resultset();
    if (!empty($members)) {
        return $members;
    }
}

/**
 * Counts the amount of members for the supplied type of member.
 *
 * @param string $membershiptype Membership type
 *
 * @return int
 */
function getNumberOfMembers($membershiptype)
{
    $DBH = new DB();
    $query = "SELECT count(DISTINCT member_id) AS count, membershiptype.naming
              FROM payment
              LEFT JOIN fee ON payment.fee_id = fee.id
              LEFT JOIN membershiptype ON fee.membershiptype_id = membershiptype.id
              LEFT JOIN period ON fee.period_id=period.id
              LEFT JOIN member ON payment.member_id=member.id
              WHERE period.first<=DATE(NOW()) AND
              period.last>=DATE(NOW()) AND
              payment.deleted = 0 AND
              membershiptype.naming = :naming AND
              member.deleted != 1";
    $DBH->query($query);
    $DBH->bind(":naming", $membershiptype);
    $result = $DBH->single();

    return $result["count"];
}
?>

