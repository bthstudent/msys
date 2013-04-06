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
case "getPerson":
    if ($userPermissions & 1) {
        if (isset($_GET['pnr'])) {
            $pnr = $_GET['pnr'];
        } else {
            echo "No Personalnumber included";
            die();
        }
        getAPIPerson($pnr);
    } else {
        echo "Insufficient permissions";
        die();
    }
    break;
case "setPerson":
    if ($userPermissions & 2) {
        if (isset($_GET['pnr'])) {
            $data->PNR = $_GET['pnr'];
        } else {
            echo "No Personalnumber included";
            die();
        }
        $data->CO = $_GET['cof'];
        $data->EMAIL = $_GET['ema'];
        $data->ADR = $_GET['adr'];
        $data->PSTNR = $_GET['psn'];
        $data->ORT = $_GET['ort'];
        $data->LAND = $_GET['lan'];
        $data->AVISEJ = $_GET['ave'];
        setAPIPersonData($data);
    } else {
        echo "Insufficient permissions";
        die();
    }
    break;
case "registerPay":
    if ($userPermissions & 4) {
        if (isset($_GET['pnr'])) {
            $data->PNR = $_GET['pnr'];
        } else {
            echo "No Personalnumber included";
            die();
        }

        if (isset($_GET['btw'])) {
            $data->BETWAY = $_GET['btw'];
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

        if (isset($_GET['dat'])) {
            $data->BETDATE = $_GET['dat'];
        } else {
            echo "No date of payment included";
            die();
        }

        if (isset($_GET['bet'])) {
            $data->BET = $_GET['bet'];
        } else {
            echo "No paysum included";
            die();
        }

        if (isset($_GET['med'])) {
            $data->MEDTYPE = $_GET['med'];
        } else {
            echo "No Membershiptype included";
            die();
        }

        registerAPIPayment($data);
    }
    break;
case "registerPerson":
    if ($userPermissions & 8) {
        if (isset($_GET['pnr'])) {
            $data->PNR = $_GET['pnr'];
        } else {
            echo "No Personalnumber included";
            die();
        }
        $data->FNM = urldecode($_GET['fnm']);
        $data->ENM = urldecode($_GET['enm']);
        $data->CO = urldecode($_GET['cof']);
        $data->EMAIL = urldecode($_GET['ema']);
        $data->ADR = urldecode($_GET['adr']);
        $data->PSTNR = urldecode($_GET['psn']);
        $data->ORT = urldecode($_GET['ort']);
        $data->LAND = urldecode($_GET['lan']);
        $data->AVISEJ = urldecode($_GET['ave']);
        $data->FELADR = 0;
        addAPIPerson($data);
    } else {
        echo "Insufficient permissions";
        die();
    }
    break;
case "isMember":
	if ($userPermissions & 16) {
		if (isset($_GET['pnr'])) {
			$IsMember = isMember($_GET['pnr']);
			if($IsMember)
			{
				echo $_GET['pnr'] . ",1";
			}
			else
			{
				echo $_GET['pnr'] . ",0";
			}
		} else {
			echo "No Personalnumber included";
			die();
		}
	}
}
?>
