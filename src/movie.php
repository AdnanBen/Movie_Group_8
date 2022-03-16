<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Page</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php

include_once 'db_connection_init.php';

$id = $_GET['id'];

$sql = "SELECT Movie.movieId, Movie.title, Movie.year, avg(Ratings.rating) as AR, (((count(Ratings.rating) * avg(Ratings.rating))+(100*3.5))/(count(Ratings.rating)+100)) as BR 
        from Ratings 
        join Movie on Ratings.movieId = Movie.movieId
        WHERE Movie.movieId = " . $id . "
        GROUP BY Movie.title, Movie.year, Movie.movieId;";

$rows = mysqli_query($con, $sql);
$rowarr = $rows->fetch_array();

$movieId = $rowarr[0];
$title = $rowarr[1];
$year = $rowarr[2];
$avgRating = $rowarr[3];

$sql = "SELECT Genre.genre FROM `Genre` WHERE Genre.genreId IN 
        (SELECT MovieGenreLink.genreId FROM `MovieGenreLink` where MovieGenreLink.movieId = " . $movieId . ");";


$rows = mysqli_query($con, $sql);
$rowarr = $rows->fetch_all(MYSQLI_NUM);

$genres = array();

foreach ($rowarr as $g) {
    array_push($genres, $g[0]);
}

///////////////
// Use case (4)
///////////////

$sql = "SELECT COUNT(rating)
        FROM `Ratings`
        WHERE Ratings.movieId = " . $movieId . ";";

$rows = mysqli_query($con, $sql);
$rowarr = $rows->fetch_array();

$numOfNormalReviews = $rowarr[0];
$subsetNumOfNormalReviews = (int)ceil($numOfNormalReviews * (3 / 10));


// First average, 10% random sample

$sql = "SELECT AVG(rating) as ar
                FROM 
                (
                SELECT Ratings.rating
                FROM `Ratings`
                WHERE Ratings.movieId = " . $movieId . "
                ORDER BY RAND()
                LIMIT " . $subsetNumOfNormalReviews . "
                ) t1;";

$rows = mysqli_query($con, $sql);
$rowarr = $rows->fetch_array();


$predictedRatingRandomSample = $rowarr[0];


// Second average, first 10% by timestamp

$sql = "SELECT AVG(rating) as ar
                FROM 
                (
                SELECT Ratings.rating
                FROM `Ratings`
                WHERE Ratings.movieId = " . $movieId . "
                ORDER BY timestamp
                LIMIT " . $subsetNumOfNormalReviews . "
                ) t1;";


$rows = mysqli_query($con, $sql);
$rowarr = $rows->fetch_array();

$predictedRatingTimestampSample = $rowarr[0];


//echo "Number of ratings in 30% subset: " . $subsetNumOfNormalReviews;
//
//echo '<br><br>';
//
//echo "Average rating by random sample at 30%: " . round($predictedRatingRandomSample, 2);
//
//echo '<br>';
//
//echo "Average rating by first 30% of reviews by timestamp: " . round($predictedRatingTimestampSample, 2);
//
//echo '<br><br>';

$query = "SELECT tmdbId FROM `Link` WHERE movieId = " . $movieId;
$tmdbId = mysqli_fetch_assoc(mysqli_query($con, $query))["tmdbId"];
$apiKey = "4151ad2fa7bfee8d0bb9a9b7a603cf96";
$apiGetUrl = "https://api.themoviedb.org/3/movie/" . $tmdbId . "?api_key=" . $apiKey;
$apiResult = json_decode(file_get_contents($apiGetUrl), true);
$movieDescription = $apiResult["overview"];
$moviePoster = "https://image.tmdb.org/t/p/w500" . $apiResult["poster_path"];

?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="card shadow col-md-8 mr-3" style="border-radius: 1.5em">
            <p style="font-size: 1.5rem"><a style="font-size: 1.7rem; font-weight: bold"><?= $title ?></a>(<?= $year ?>)
            </p>
        </div>

        <div class="col-md-3 card shadow ">
            <p style="font-size: 1.7rem">⭐ <?= number_format((float)$avgRating, 2) ?><a style="font-size: 0.7rem">
                    (<?= number_format((float)$numOfNormalReviews, 0) ?> reviews)</a></p>
        </div>
    </div>
    <div class=" row mt-3">


        <div class="col-md-6 card shadow mr-3">

            <div class="row d-flex justify-content-center">

                <?php
                foreach ($genres as $genre) {
                    ?>
                    <div class="card shadow mr-2 ml-2 mb-3"
                         style="padding-left: 10px; padding-right: 10px;border-radius: 10%; background-color: whitesmoke">
                        <p class="centre" style="font-size: 0.8rem"><?= $genre ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p class="ml-2 mr-2 " style="font-size: 0.8rem; text-align: justify"><?= $movieDescription ?></p>
        </div>

        <div class="card shadow col-md-5"
             style='background-image: url("<?= $moviePoster ?>"); height: calc(0.664 * 100vw); background-repeat: no-repeat; background-size: cover;
                     '></div>


    </div>
</div>
</body>
</html>