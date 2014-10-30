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
require_once 'functions.php';

if (isset($_GET['key'])) {
    $apiKey = $_GET['key'];
} else {
    echo "No API-key submitted";
    die();
}
if (isset($_GET['usr'])) {
    $username = $_GET['usr'];
} else {
    echo "No username submitted";
    die();
}
if (isset($_GET['cmd'])) {
    $command = $_GET['cmd'];
} else {
    echo "No command submitted";
    die();
}

$userPermissions = authenticateAPIUser($apiKey, $username);

switch($command) {
case "getMember":
    if ($userPermissions & 1) {
        if (isset($_GET['ssn'])) {
            $ssn = $_GET['ssn'];
        } else {
            echo "No Socialsecuritynumber included";
            die();
        }
        getAPIMember($ssn);
    } else {
        echo "Insufficient permissions";
        die();
    }
    break;
case "setMember":
    if ($userPermissions & 2) {
        if (isset($_GET['ssn'])) {
            $data->SSN = $_GET['ssn'];
        } else {
            echo "No Socialsecuritynumber included";
            die();
        }
        $data->CO     = $_GET['co'];
        $data->EMAIL  = $_GET['ema'];
        $data->ADDR    = $_GET['addr'];
        $data->PSTNR  = $_GET['psn'];
        $data->CITY    = $_GET['city'];
        $data->COUNTRY   = $_GET['cou'];
        $data->DONOTAD = $_GET['adv'];
        $data->PHO = $_GET['pho'];
        updateMember($data);
    } else {
        echo "Insufficient permissions";
        die();
    }
    break;
case "registerPayment":
    if ($userPermissions & 4) {
        if (isset($_GET['ssn'])) {
            $data->SSN = $_GET['ssn'];
        } else {
            echo "No Socialsecuritynumber included";
            die();
        }

        if (isset($_GET['pwy'])) {
            $data->PAYWAY = $_GET['pwy'];
        } else {
            echo "No Way of payment included";
            die();
        }

        if (isset($_GET['per'])) {
            $data->PERIOD = $_GET['per'];
        } else {
            echo "No period included";
            die();
        }

        if (isset($_GET['pda'])) {
            $data->PAYDATE = $_GET['pda'];
        } else {
            echo "No date of payment included";
            die();
        }

        if (isset($_GET['paid'])) {
            $data->PAID = $_GET['paid'];
        } else {
            echo "No paysum included";
            die();
        }

        if (isset($_GET['met'])) {
            $data->MEMTYPE = $_GET['met'];
        } else {
            echo "No Membershiptype included";
            die();
        }
        $data->PAYWAY = 3;
        addPayment($data);
    }
    break;
case "registerMember":
    if ($userPermissions & 8) {
        if (isset($_GET['ssn'])) {
            $data->SSN = $_GET['ssn'];
        } else {
            echo "No Socialsecuritynumber included";
            die();
        }
        $data->FNM    = urldecode($_GET['fnm']);
        $data->LNM    = urldecode($_GET['lnm']);
        $data->CO     = urldecode($_GET['co']);
        $data->EMAIL  = urldecode($_GET['ema']);
        $data->ADDR    = urldecode($_GET['addr']);
        $data->PSTNR  = urldecode($_GET['psn']);
        $data->CITY    = urldecode($_GET['city']);
        $data->COUNTRY   = urldecode($_GET['coun']);
        $data->DONOTAD = urldecode($_GET['adv']);
        $data->WRNADDR = 0;
        addMember($data);
    } else {
        echo "Insufficient permissions";
        die();
    }
    break;
case "isMember":
    if ($userPermissions & 16) {
        if (isset($_GET['ssn'])) {
            $IsMember = isMember($_GET['ssn']);
            if ($IsMember) {
                echo $_GET['ssn'] . ",1";
            } else {
                echo $_GET['ssn'] . ",0";
            }
        } else {
            echo "No Socialsecuritynumber included";
            die();
        }
    }
    break;
case 'isRegistered':
    if($userPermissions & 16) {
        if(isset($_GET['ssn'])){
            $member = getRegisteredPersonBySsn($_GET['ssn']);
            if(isset($member["id"])){
                echo true;
            } else {
                echo false;
            }
        } else {
            echo false;
        }
    }
    break;
}
?>
