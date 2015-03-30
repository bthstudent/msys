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

function columnexists ($table, $column)
{
    $DBH = new DB();
    $query= "
        SELECT table_schema, table_name, column_name
        FROM information_schema.columns
        WHERE table_schema='".$DBH->getDBName()."'
            AND table_name='adminuser'
            AND column_name='deleted';
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