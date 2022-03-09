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

        $sql = "SELECT Movie.movieId, Movie.title, Movie.year, avg(Ratings.rating) as AR, (((count(Ratings.rating) * avg(Ratings.rating))+(100*3.5))/(count(Ratings.rating)+100)) as BR 
        from Ratings 
        join Movie on Ratings.movieId = Movie.movieId
        WHERE Movie.movieId = " . $id ."
        GROUP BY Movie.title, Movie.year, Movie.movieId;";
        $rows = mysqli_query($con, $sql);

        $rowarr = $rows->fetch_array();


        echo 'Stats: <br><br>';
        echo 'ID: ' . $rowarr[0];
        echo '<br>';
        echo 'Title: ' . $rowarr[1];
        echo '<br>';
        echo 'Year: ' . $rowarr[2];

        echo '<br><br>';

        echo 'Rating: ' . $rowarr[3];


    ?>
</body>
</html>