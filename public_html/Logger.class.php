<?php
    /**
        The membership tracker system.
        Copyright © 2012-2014 Blekinge studentkår <sis@bthstudent.se>
        Copyright © 2014 Niclas Björner <niclas@cromigon.se>
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
     * A Logger class for msys
     *
     *  @package Msys
     *  @author  Niclas Björner <niclas@cromigon.se>
     *  @license AGPL3
     */
    class Logger {
        function __construct() {

        }

        function log($user_id, $user_type, $function, $msg) {
            $message->time = time();
            $message->user_id = $user_id;
            $message->user_type = $user_type;
            $message->function = $function;
            $message->message = $msg;

            $DBH = new DB();
            $query = "INSERT INTO log(message)
                      VALUES (:message)";
            $DBH->query($query);
            $DBH->bind(":message", json_encode($message));
            $DBH->execute();
        }
    }
?>