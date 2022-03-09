<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

        include_once 'db_connection_init.php'; 

        $id = $_GET['id'];

        $sql = "SELECT * FROM Movie WHERE movieId = $id";
        $rows = mysqli_query($con, $sql);

        $rowarr = $rows->fetch_array();


        echo 'Stats: <br><br>';
        echo 'ID: ' . $rowarr[0];
        echo '<br>';
        echo 'Title: ' . $rowarr[1];
        echo '<br>';
        echo 'Year: ' . $rowarr[2];

        echo '<br><br>';

        echo 'Rating: '


    ?>
</body>
</html>