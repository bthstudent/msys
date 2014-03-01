<?PHP
/**
    The membership tracker system.
    Copyright © 2012-2014 Blekinge studentkår <sis@bthstudent.se>
    Copyright © 2014 Martin Bagge <brother@bsnet.se>
    Copyright © 2014 Sebastian Hultstrand <sebastian@atlan.se>

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
 * A PDO interface class for msys
 *
 * Based on Philip Brown's guide for PDO quick start.
 * See http://culttt.com/2012/10/01/roll-your-own-pdo-php-class/
 *
 *  @package Msys
 *  @author  Martin Bagge <brother@bsnet.se>
 *  @author  Sebastian Hultstrand <sebastian@atlan.se>
 *  @author  Philip Brown
 *  @license AGPL3
 */
class DB {
    private $_host	= "";
    private $_user	= "";
    private $_pass	= "";
    private $_dbname	= "";

    private $_dbh;
    private $_error;
    private $_stmt;

    function __construct() {
        include "../local-config.php";
        $this->_host = $db["host"];
        $this->_user = $db["user"];
        $this->_pass = $db["pass"];
        $this->_dbname = $db["db"];

        $dsn = 'mysql:host=' . $this->_host . ';dbname=' . $this->_dbname;

        $options = array(
                         PDO::ATTR_PERSISTENT	=> true,
                         PDO::ATTR_ERRMODE	=> PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->_dbh = new PDO($dsn, $this->_user, $this->_pass, $options);
        } catch(PDOexception $e) {
            $this->_error = $e->getMessage();
        }
    }

    public function query($query) {
        $this->_stmt = $this->_dbh->prepare($query);
    }

    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
            }
        }
        $this->_stmt->bindValue($param, $value, $type);
    }

    public function execute() {
        return $this->_stmt->execute();
    }

    public function resultset() {
        $this->execute();
        return $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single() {
        $this->execute();
        return $this->_stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount() {
        return $this->_stmt->rowCount();
    }

    public function lastInsertId() {
        return $this->_dbh->lastInsertId();
    }

    public function beginTransaction() {
        return $this->_dbh->beginTransaction();
    }

    public function endTransaction() {
        return $this->_dbh->commit();
    }

    public function cancelTransaction() {
        return $this->_dbh->rollBack();
    }

    public function debugDumpParams() {
        return $this->_stmt->debugDumpParams();
    }
}
