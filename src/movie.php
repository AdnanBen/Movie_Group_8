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
        $movieId = $rowarr[0];
        echo 'ID: ' . $rowarr[0];
        echo '<br>';
        echo 'Title: ' . $rowarr[1];
        echo '<br>';
        echo 'Year: ' . $rowarr[2];
        echo '<br><br>';
        echo 'Rating: ' . $rowarr[3];


        // Use case (4)

        $sql = "SELECT COUNT(rating)
        FROM `Ratings`
        WHERE Ratings.movieId = ". $rowarr[0] . ";";
        
        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        echo '<br><br>';
        $numOfReviews = $rowarr[0];
        $subsetNumOfReviews = intdiv($numOfReviews, 10);

        echo "Number of ratings: " . $numOfReviews;
        echo '<br>';
        echo "Number of ratings in subset: " . $subsetNumOfReviews;


        // First average, 10% random sample

        $sql = "SELECT AVG(rating) as ar
                FROM 
                (
                SELECT Ratings.rating
                FROM `Ratings`
                WHERE Ratings.movieId = " . $movieId . "
                ORDER BY RAND()
                LIMIT ". $subsetNumOfReviews . "
                ) t1;";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();
        

        echo '<br><br>';
        $avg1 = $rowarr[0];
        echo "Average rating by random sample at 10%: " . $avg1;


        // Second average, first 10% by timestamp

        $sql = "SELECT AVG(rating) as ar
                FROM 
                (
                SELECT Ratings.rating
                FROM `Ratings`
                WHERE Ratings.movieId = " . $movieId . "
                ORDER BY timestamp
                LIMIT ". $subsetNumOfReviews . "
                ) t1;";

        
        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();
        
        echo '<br><br>';
        $numOfReviews = $rowarr[0];
        echo "Average rating by first 10% of reviews by timestamp: " . $numOfReviews;
        echo $subsetNumOfReviews;


        // Use case (5)


    ?>
</body>
</html>