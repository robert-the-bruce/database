<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>database</title>
    <link rel="stylesheet" href="./../css/skeleton.css" type="text/css"/>
    <link rel="stylesheet" href="./../css/layout.css" type="text/css"/>
</head>
<body>
<h1>Datenbankabfrage</h1>
<?php

function printTable($tbl_name, $db_query){
    //New PDO object
    $pdo = new Database();

    //Get column names
    $pdo->query("DESCRIBE ". $tbl_name);
    $col_names = $pdo->column();

    //Get number of columns
    $col_cnt = count($col_names);

    //Setup table - user css class db-table for design
    echo "<table class='db-table'>";
    echo "<tr colspan='". $col_cnt ."'>". $tbl_name ."</tr>";
    echo "<tr>";

    //Give each table column same name is db column name
    for($i=0;$i<$col_cnt;$i++){
        echo "<td>". $col_names[$i] ."</td>";
    }

    echo "</tr>";

    //Get db table data
    $pdo->query($db_query);

    $results = $pdo->resultset();
    $res_cnt = count($results);

    //Print out db table data
    for($i=0;$i<$res_cnt;$i++){
        echo "<tr>";
        for($y=0;$y<$col_cnt;$y++){
            echo "<td>". $results[$i][$col_names[$y]] ."</td>";
        }
        echo "</tr>";
    }
}



function connectToDB($dbname = NULL){
    try {
        // Verbindungsaufbau
        $server = 'localhost';
        $db = $dbname;
        $user = 'root';
        $pwd = '';
        $con = new PDO('mysql:host=' . $server . ';dbname=' . $db . ';charset=utf8', $user, $pwd);
        // Exception Handling explizit einschalten
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $con;
    } catch(Exception $e) {
        // Fehlerbehandlung - Verbindungsaufbau!
        echo $e->getMessage().'<br>';
    }
}



if (isset($_POST['showcolumns'])) {

    try {

        $table1 = isset($_POST['table']) ? $_POST['table'] : NULL;

        $database = isset($_POST['database']) ? $_POST['database'] : NULL;

        //$query = "Select * from " .$table.";";

        $query = 'Select * from :xyz';

        $con = connectToDB($database);

        $stmt = $con->prepare($query);

        $stmt->bindParam(':xyz', $table1);

        $stmt->execute();

        ?>
        <form method="POST">
            <select name="columns">
                <?php while ($row=$stmt->fetch(PDO::FETCH_NUM) ) {?>
                    <option value="<?php echo $row[1]?>"><?php echo $row[1]?></option>
                <?php }?>
            </select>
            <input type="submit" name="show" value="show all params from database">
            <a href="index.php">Back to Main</a>
        </form>

        <?php

    } catch (exception $e) {
        echo $e->getMessage();
    }

} elseif (isset($_POST['showtables'])) {

    try {

        $query = 'show tables;';

        $con = connectToDB($_POST['database']);

        $stmt = $con->prepare($query);

        $stmt->execute();

        ?>
        <form method="POST">
            <select name="table">
                <?php while ($row=$stmt->fetch(PDO::FETCH_NUM) ) {?>
                    <option value="<?php echo $row[0]?>"><?php echo $row[0]?></option>
                <?php }?>
            </select>
            <input type="hidden" name="database" value="<?php echo $_POST['database']?>">
            <input type="submit" name="showcolumns" value="show all columns from database">
            <a href="index.php">Back to Main</a>
        </form>

        <?php

    } catch (exception $e) {
       echo $e->getMessage();
    }

} else

    try {

        $query = 'show databases;';

        $con = connectToDB('');

        $stmt = $con->prepare($query);

        $stmt->execute();

        ?>
            <form method="POST">
                <select name="database">
                    <?php while ($row=$stmt->fetch(PDO::FETCH_NUM) ) {?>
                    <option value="<?php echo $row[0]?>"><?php echo $row[0]?></option>
                    <?php }?>
                </select>
                <input type="submit" name="showtables" value="show all tables from database">
            </form>

        <?php

    } catch (exception $e) {
        $e->getMessage();
    }


?>
</body>
</html>