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
 * Registers a payment for a specific member.
 *
 * @param object $data API request data.
 *
 * @return void
 */
function addPayment($data=false)
{
    if ($data) {
        $DBH = new DB();
        $DBH->query("SELECT id FROM member
                     WHERE ssn = :ssn");
        $DBH->bind(":ssn", $data->SSN);
        $DBH->execute();
        $pid = $DBH->single();

        $DBH->query("SELECT id FROM membershiptype
                     WHERE id = :mtp");
        $DBH->bind(":mtp", $data->MEMTYPE);
        $DBH->execute();
        $mtp = $DBH->single();

        $DBH->query("SELECT id FROM period
                     WHERE period = :per");
        $DBH->bind(":per", $data->PERIOD);
        $DBH->execute();
        $per = $DBH->single();

        $DBH->query("SELECT id AS fee_id FROM fee
                     WHERE membershiptype_id = :memtypeid AND
                     period_id = :periodid");
        $DBH->bind(":memtypeid", $mtp['id']);
        $DBH->bind(":periodid", $per['id']);
        $a = $DBH->single();

        $DBH->query("INSERT INTO payment (member_id, fee_id, paymenttype_id, paymentdate, paid)
                      VALUES (:pid, :feeid, :ptid, :pdate, :payed)");
        $DBH->bind(":pid", $pid['id']);
        $DBH->bind(":feeid", $a['fee_id']);
        $DBH->bind(":ptid", $data->PAYWAY);
        $DBH->bind(":pdate", $data->PAYDATE);
        $DBH->bind(":payed", $data->PAID);
        $DBH->execute();
    } else {
        $DBH = new DB();
        $DBH->query("SELECT id AS fee_id FROM fee
                     WHERE membershiptype_id = :memtypeid AND
                     period_id = :periodid");
        $DBH->bind(":memtypeid", $_POST['MEMTYPE']);
        $DBH->bind(":periodid", $_POST['PERIOD']);
        $a = $DBH->single();

        $DBH->query("INSERT INTO payment (member_id, fee_id, paymenttype_id, paymentdate, paid)
                      VALUES (:pid, :feeid, :ptid, :pdate, :payed)");
        $DBH->bind(":pid", $_POST['ID']);
        $DBH->bind(":feeid", $a["fee_id"]);
        $DBH->bind(":ptid", $_POST['PAYWAY']);
        $DBH->bind(":pdate", $_POST['PAYDATE']);
        $DBH->bind(":payed", $_POST['PAID']);
        $DBH->execute();
    }

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "addPayment", "Created a new Payment with the id " . $DBH->lastInsertId());
}

/**
 * Inserts a time period
 *
 * @return void
 */
function addPeriod()
{
    $DBH = new DB();
    $DBH->query("INSERT INTO period SET period = :period,
                first = :first,
                last = :last");
    $DBH->bind(":period", $_POST['period']);
    $DBH->bind(":first", $_POST['first']);
    $DBH->bind(":last", $_POST['last']);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "addPeriod", "Created a new period " . $_POST['period']);
}

/**
 * Update values for a time period
 *
 * @return boolean
 */
function changePeriod()
{
    $DBH = new DB();
    $DBH->query("UPDATE period
        SET first = :firstname,
            last = :lastname
        WHERE period= :period");
    $DBH->bind(":firstname", $_POST['first']);
    $DBH->bind(":lastname", $_POST['last']);
    $DBH->bind(":period", $_POST['period']);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "changePeriod", "Edited period " . $_POST['period']);
}


/**
 * Updates the fee information.
 *
 * @return void
 */
function updateFee()
{
    $DBH = new DB();
    if (isset($_POST["feeid"]) && $_POST["feeid"] == -1) {
        // FIXME make period_id + medlemstyp_id a UNIQUE key
        //       then convert this to INSERT INTO tbl ON DUPLICATE KEY UPDATE...
        $DBH->query("INSERT INTO fee
                      SET period_id = :period_id,
                          membershiptype_id = :membershiptype_id,
                          fee = :fee");
        $DBH->bind(":period_id", $_POST['period_id']);
        $DBH->bind(":membershiptype_id", $_POST['membershiptype_id']);
        $DBH->bind(":fee", $_POST['fee']);
    } elseif (isset($_POST["feeid"]) && $_POST["feeid"] > 0) {

        $DBH->query("UPDATE fee
            SET fee = :fee
            WHERE id= :feeid");
        $DBH->bind(":fee", $_POST['fee']);
        $DBH->bind(":feeid", $_POST['feeid']);
    } else {
        exit("FATAL ERROR. Execution Stopped.");
    }
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "updateDee", "Updated fee " . $_POST['feeid']);
}

/**
 * Extrats all payments for a member.
 *
 * @param int  $id         The internal personal number of a member.
 * @param bool $onlyActive Only return payment(s) for current periods.
 *
 * @return mixed
 */
function getPayments($id, $onlyActive=false)
{
    $DBH = new DB();
    $query = "SELECT payment.id AS id, paymenttype.naming AS paymenttype,
                     payment.paid AS paid, payment.paymentdate AS paymentdate,
                     fee.fee AS fee, membershiptype.naming AS membershiptype,
                     period.first AS first, period.last AS last,
                     period.period AS period
              FROM payment
              LEFT JOIN fee ON payment.fee_id=fee.id
              LEFT JOIN paymenttype ON payment.paymenttype_id=paymenttype.id
              LEFT JOIN period ON fee.period_id=period.id
              LEFT JOIN membershiptype ON fee.membershiptype_id=membershiptype.id
              WHERE payment.member_id=:id AND deleted != 1";
    if ($onlyActive == true) {
        $query .= " AND first<=DATE(NOW()) AND last>=DATE(NOW())";
    }
    $DBH->query($query);
    $DBH->bind(":id", $id);
    $payments = $DBH->resultset();
    if (!empty($payments)) {
        return $payments;
    }
}

/**
 * Extracts the current time periods and returns them in a structurred array.
 *
 * @return mixed
 */
function getPeriods()
{
    $DBH = new DB();
    $query = "SELECT id, period, first, last FROM period
              ORDER BY first, last";
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
function getFees()
{
    $DBH = new DB();
    $query = "SELECT period.id AS period_id, period, membershiptype_id, fee, fee.id AS fee_id, first, last FROM period
              LEFT JOIN fee ON period.id=fee.period_id
              ORDER BY first DESC, last DESC, membershiptype_id";
    $DBH->query($query);
    $fees = $DBH->resultset();
    if (!empty($fees)) {
        return $fees;
    } else {
        return null;
    }
}

/**
 * ??
 *
 * @param ?? $fee        ??
 * @param ?? $membertype ??
 *
 * @return object
 */
function getFeeId($fee, $membertype)
{
    $DBH = new DB();
    $query = "SELECT fee.id FROM fee
              INNER JOIN period ON fee.period_id=period.id
              INNER JOIN membershiptype on fee.membershiptype_id=membershiptype.id
              WHERE period.period=:fee AND
                    fee.membershiptype_id=:memtype";
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
function getMembershiptypes()
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM membershiptype");
    $membershiptypes = $DBH->resultset();
    return $membershiptypes;
}

/**
 * Update the description field of a membership type
 *
 * @return void
 */
function updateMembershipType()
{
    $currenttypes = getMembershiptypes();
    $currentname="N/A";
    foreach ($currenttypes as $type) {
        if ($type["id"] == $_POST["mbtid"]) {
            $currentname = $type["naming"];
            break;
        }
    }
    $DBH = new DB();
    $DBH->query("UPDATE membershiptype SET naming=:mbtname
                WHERE id=:mbtid");
    $DBH->bind(":mbtname", $_POST["newlabel"]);
    $DBH->bind(":mbtid", $_POST["mbtid"]);
    if ($DBH->execute()) {
        $LOGGER = new Logger();
        $LOGGER->log($_SESSION["id"], $_SESSION["user_type"], "updateMembershipType", "Renamed membership type from $currentname to " . $_POST["newlabel"] . ".");
    }
}

/**
 * Add a new membership type
 *
 * @return void
 */
function addMembershipType()
{
    $DBH = new DB();
    $DBH->query("INSERT INTO membershiptype SET naming = :mbtnewname");
    $DBH->bind(":mbtnewname", $_POST["membershiptype"]);
    if ($DBH->execute()) {
        $LOGGER = new Logger();
        $LOGGER->log($_SESSION["id"], $_SESSION["user_type"], "addMembershipType", "Added a new membership type " . $_POST["membershiptype"]);
    }
}

/**
 * Extract the payment types and returns them in a structurred array.
 *
 * @return mixed
 */
function getPaymentway()
{
    $DBH = new DB();
    $DBH->query("SELECT * FROM paymenttype");
    $paymentway = $DBH->resultset();
    return $paymentway;
}

/**
 * Update the description field of a payment type
 *
 * @return void
 */
function updatePaymentType()
{
    $currenttypes = getPaymentway();
    $currentname="N/A";
    foreach ($currenttypes as $type) {
        if ($type["id"] == $_POST["ptid"]) {
            $currentname = $type["naming"];
            break;
        }
    }
    $DBH = new DB();
    $DBH->query("UPDATE paymenttype SET naming=:ptname
                WHERE id=:ptid");
    $DBH->bind(":ptname", $_POST["newlabel"]);
    $DBH->bind(":ptid", $_POST["ptid"]);
    if ($DBH->execute()) {
        $LOGGER = new Logger();
        $LOGGER->log($_SESSION["id"], $_SESSION["user_type"], "updatePaymentType", "Renamed payment type from $currentname to " . $_POST["newlabel"] . ".");
    }
}

/**
 * Add a new payment type
 *
 * @return void
 */
function addPaymentType()
{
    $DBH = new DB();
    $DBH->query("INSERT INTO paymenttype SET naming = :ptnewname");
    $DBH->bind(":ptnewname", $_POST["paymenttype"]);
    if ($DBH->execute()) {
        $LOGGER = new Logger();
        $LOGGER->log($_SESSION["id"], $_SESSION["user_type"], "addPaymentType", "Added a new payment type " . $_POST["paymenttype"]);
    }
}


/**
 * Marks a payment as deleted.
 *
 * @return void
 */
function removePayment()
{
    $DBH = new DB();
    $DBH->query("UPDATE payment SET deleted='1'
                WHERE id=:bid");
    $DBH->bind(":bid", $_POST['paymentId']);
    $DBH->execute();

    $LOGGER = new Logger();
    $LOGGER->log($_SESSION['id'], $_SESSION['user_type'], "removePayment", "Flagged payment " . $_POST['paymentId'] . " as deleted");
}

?>