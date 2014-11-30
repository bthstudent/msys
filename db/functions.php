<?php

function tableexists ($name)
{
    $DBH = new DB();
    $query= "
        SELECT *
        FROM information_schema.tables
        WHERE table_schema = '".$DBH->getDBName()."'
        AND table_name = '$name'
            ";

    $DBH->query($query);
    $DBH->execute();
    if ($DBH->rowCount() == 1) {
        return true;
    } else {
        return false;
    }

}

?>