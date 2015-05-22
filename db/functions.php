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
            AND table_name=:tname
            AND column_name=:cname;
            ";

    $DBH->query($query);
    $DBH->bind(":tname", $table);
    $DBH->bind(":cname", $column);
    $DBH->execute();
    if ($DBH->rowCount() == 1) {
        return true;
    } else {
        return false;
    }
}

function indexexists ($table, $indexname)
{
    $DBH = new DB();
    $DBH->query("SHOW INDEX FROM $table WHERE Key_name = :indexname");
    $DBH->bind(":indexname", $indexname);
    $DBH->execute();
    if ($DBH->rowCount() == 1) {
        return true;
    } else {
        return false;
    }
}
?>
