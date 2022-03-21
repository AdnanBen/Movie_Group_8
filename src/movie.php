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
$query = "SELECT tmdbId FROM `Link` WHERE movieId = " . $movieId;
$tmdbId = mysqli_fetch_assoc(mysqli_query($con, $query))["tmdbId"];
$apiKey = "4151ad2fa7bfee8d0bb9a9b7a603cf96";
$apiGetUrl = "https://api.themoviedb.org/3/movie/" . $tmdbId . "?api_key=" . $apiKey;
$apiResult = json_decode(file_get_contents($apiGetUrl), true);
$movieDescription = $apiResult["overview"];
$moviePoster = "https://image.tmdb.org/t/p/w500" . $apiResult["poster_path"];

$showMore = 0;
if (isset($_GET['showMore'])) $showMore = $_GET['showMore'];

function getShowMoreUrl()
{
    $url = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $query = parse_url($url, PHP_URL_QUERY);
    if ($query) {
        parse_str($query, $queryParams);
        $queryParams["showMore"] = 1;
        $url = str_replace("?$query", '?' . http_build_query($queryParams), $url);
    } else {
        $url .= '?' . urlencode("showMore") . '=' . urlencode(1);
    }
    return $url;
}


?>

<div class="container mt-4 mb-5">

    <!--HEADER-->
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

    <!--ABOUT-->
    <div class=" row mt-3">
        <div class="col-md-7 card shadow mr-3" style="max-height: 45rem">

            <h3 class="mb-2">About</h3>
            <p class="ml-5 mr-5 mb-4" style="font-size: 0.8rem; text-align: justify"><?= $movieDescription ?></p>

            <h3 class="mb-2">Genres</h3>
            <div class="row d-flex justify-content-center mb-5">
                <?php
                foreach ($genres as $genre) {
                    ?>
                    <div class="card shadow mr-2 ml-2"
                         style="padding-left: 10px; padding-right: 10px;border-radius: 10%; background-color: whitesmoke">
                        <p class="centre" style="font-size: 0.8rem"><?= $genre ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <h3 class="mb-3">Average ratings from a sample</h3>
            <div class="row d-flex justify-content-center mb-4">
                <div class="card bg-light ml-2 mr-2" style="max-width: 10rem;">
                    <div class="card-body">
                        <h1><?= $subsetNumOfNormalReviews ?></h1>
                        <p class="card-text">Number of ratings in 30% subset</p>
                    </div>
                </div>
                <div class="card bg-light ml-2 mr-2" style="max-width: 10rem;">
                    <div class="card-body">
                        <h1><?= round($predictedRatingRandomSample, 2) ?></h1>
                        <p class="card-text">Average rating by random sample at 30%</p>
                    </div>
                </div>
                <div class="card bg-light ml-2 mr-2" style="max-width: 10rem;">
                    <div class="card-body">
                        <h1><?= round($predictedRatingTimestampSample, 2) ?></h1>
                        <p class="card-text">Average rating by first 30% of reviews by timestamp</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow col-md-4"
             style='background-image: url("<?= $moviePoster ?>");
                     height: 45rem;
                     background-repeat: no-repeat;
                     background-size: cover;'></div>
    </div>

    <?php
    if ($showMore == 1) {
        $sql = "SELECT COUNT(*) FROM `Personality` WHERE userid IN ( SELECT userid FROM `PersonalityRatings` WHERE PersonalityRatings.rating > 2.5 AND movieId = " . $movieId . " );";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfPersonalityRatings = $rowarr[0];
        $subsetNumOfPersonalityRatings = (int)ceil($numOfPersonalityRatings * (3 / 10));

        $sql = "SELECT AVG(openness), AVG(agreeableness), AVG(emotional_stability), AVG(conscientiousness), AVG(extraversion)
        FROM `Personality` 
        WHERE userid IN
        (
        SELECT userid
        FROM `PersonalityRatings`
        WHERE PersonalityRatings.rating > 2.5 AND movieId = " . $movieId . "
        );";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $personalityScores = [];

        $personalityScores[0] = $rowarr[0]; // Openness
        $personalityScores[1] = $rowarr[1]; // Agreeableness
        $personalityScores[2] = $rowarr[2]; // Emotional Stability
        $personalityScores[3] = $rowarr[3]; // Conscientiousness
        $personalityScores[4] = $rowarr[4]; // Extraversion


        $sql = "SELECT AVG(openness), AVG(agreeableness), AVG(emotional_stability), AVG(conscientiousness), AVG(extraversion) FROM `Personality` 
        WHERE userid IN (SELECT * FROM ( SELECT userid FROM `PersonalityRatings` WHERE PersonalityRatings.rating > 2.5
        AND movieId = " . $movieId . " ORDER BY RAND() LIMIT " . $subsetNumOfPersonalityRatings . ") as t1)";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedPersonalityScores = [];

        $predictedPersonalityScores[0] = $rowarr[0]; // Openness
        $predictedPersonalityScores[1] = $rowarr[1]; // Agreeableness
        $predictedPersonalityScores[2] = $rowarr[2]; // Emotional Stability
        $predictedPersonalityScores[3] = $rowarr[3]; // Conscientiousness
        $predictedPersonalityScores[4] = $rowarr[4]; // Extraversion

// Per personality rating

// Openness

        $sql = "SELECT COUNT(*) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN (SELECT * FROM (SELECT Personality.userid FROM `Personality` where openness > 5.25) as t1);";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfHighOpenness = $rowarr[0];
        $numOfHighOpenness = (int)ceil($numOfHighOpenness * (3 / 10));

        $sql = "SELECT AVG(rating) FROM (SELECT * FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
        (SELECT * FROM (SELECT Personality.userid FROM `Personality` where openness > 5.25) as t1) ORDER BY RAND() LIMIT " . $numOfHighOpenness . "
        ) as t2;";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedRatingForHighOpenness = $rowarr[0];

// Agreeableness

        $sql = "SELECT COUNT(*) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN (SELECT * FROM (SELECT Personality.userid FROM `Personality` where agreeableness > 5.25) as t1);";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfHighAgreeableness = $rowarr[0];
        $numOfHighAgreeableness = (int)ceil($numOfHighAgreeableness * (3 / 10));

        $sql = "SELECT AVG(rating) FROM (SELECT * FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
        (SELECT * FROM (SELECT Personality.userid FROM `Personality` where agreeableness > 5.25) as t1) ORDER BY RAND() LIMIT " . $numOfHighAgreeableness . "
        ) as t2;";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedRatingForHighAgreeableness = $rowarr[0];

// Emotional Stability

        $sql = "SELECT COUNT(*) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN (SELECT * FROM (SELECT Personality.userid FROM `Personality` where emotional_stability > 5.25) as t1);";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfHighEmotionalStability = $rowarr[0];
        $numOfHighEmotionalStability = (int)ceil($numOfHighEmotionalStability * (3 / 10));


        $sql = "SELECT AVG(rating) FROM (SELECT * FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
        (SELECT * FROM (SELECT Personality.userid FROM `Personality` where emotional_stability > 5.25) as t1) ORDER BY RAND() LIMIT " . $numOfHighEmotionalStability . "
        ) as t2;";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedRatingForHighEmotionalStability = $rowarr[0];

// Conscientiousness

        $sql = "SELECT COUNT(*) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN (SELECT * FROM (SELECT Personality.userid FROM `Personality` where conscientiousness > 5.25) as t1);";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfHighConscientiousness = $rowarr[0];
        $numOfHighConscientiousness = (int)ceil($numOfHighConscientiousness * (3 / 10));


        $sql = "SELECT AVG(rating) FROM (SELECT * FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
        (SELECT * FROM (SELECT Personality.userid FROM `Personality` where conscientiousness > 5.25) as t1) ORDER BY RAND() LIMIT " . $numOfHighConscientiousness . "
        ) as t2;";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedRatingForHighConscientiousness = $rowarr[0];

// Extraversion

        $sql = "SELECT COUNT(*) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN (SELECT * FROM (SELECT Personality.userid FROM `Personality` where extraversion > 5.25) as t1);";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfHighExtraversion = $rowarr[0];
        $numOfHighExtraversion = (int)ceil($numOfHighExtraversion * (3 / 10));

        $sql = "SELECT AVG(rating) FROM (SELECT * FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
        (SELECT * FROM (SELECT Personality.userid FROM `Personality` where extraversion > 5.25) as t1) ORDER BY RAND() LIMIT " . $numOfHighExtraversion . "
        ) as t2;";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedRatingForHighExtraversion = $rowarr[0];

///////////////
// Use case (6)
///////////////


// Get tags

        $sql = "SELECT `Tags`.`tag`, COUNT(`Tags`.`tag`) FROM `Tags` where `Tags`.`movieId` = " . $movieId . " GROUP BY `Tags`.`tag`;";
        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_all(MYSQLI_NUM);

        $tags = array();

        foreach ($rowarr as $t) {
            array_push($tags, $t);
        }

// Get average of corresponding user ratings of tags where rating is above 2.5

        $sql = "SELECT avg(ar) FROM ( SELECT avg(rating) as AR, `Tags`.`tag` FROM `Ratings` join `Tags` ON Ratings.userId = `Tags`.`userId` AND Ratings.movieId = `Tags`.`movieId` WHERE Tags.movieId = " . $movieId . " GROUP BY `Tags`.`tag` HAVING avg(rating) > 2.5 ) as t1;";
        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $avgTagRating = $rowarr[0];

// Predicted but with tags

        $sql = "SELECT AVG(openness), AVG(agreeableness), AVG(emotional_stability), AVG(conscientiousness), AVG(extraversion)
        FROM `Personality` 
        WHERE userid IN
        (
        SELECT userId FROM `PersonalityRatings` WHERE movieId IN (SELECT movieId
        FROM Tags WHERE tag IN (SELECT `Tags`.`tag` FROM `Tags` where `Tags`.`movieId` = " . $movieId ." GROUP BY `Tags`.`tag`)) AND rating > 4
        );";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $personalityScoresTags = [];

        $personalityScoresTags[0] = $rowarr[0]; // Openness
        $personalityScoresTags[1] = $rowarr[1]; // Agreeableness
        $personalityScoresTags[2] = $rowarr[2]; // Emotional Stability
        $personalityScoresTags[3] = $rowarr[3]; // Conscientiousness
        $personalityScoresTags[4] = $rowarr[4]; // Extraversion


    }

    if ($showMore == 1) : ?>
        <!--PERSONALITY-->
        <div class="card row col-md-11 shadow mt-4 ml-2">

            <h3 class="mb-3">Average personality ratings (1-7)</h3>
            <div class="row d-flex justify-content-center mb-3">
                <div class="card bg-light ml-2 mr-2" style="max-width: 30rem;">
                    <div class="card-body">
                        <h1><?= $numOfPersonalityRatings ?></h1>
                        <p class="card-text">Number of ratings in personality data</p>
                    </div>
                </div>
                <div class="card bg-light ml-2 mr-2" style="max-width: 30rem;">
                    <div class="card-body">
                        <h1><?= $subsetNumOfPersonalityRatings ?></h1>
                        <p class="card-text">Number of ratings in personality data 30% subset</p>
                    </div>
                </div>
            </div>
            <div class="row d-flex justify-content-center mb-4">
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScores[0], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Openness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScores[1], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Agreeableness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScores[2], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Emotional Stability</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScores[3], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Conscientiousness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScores[4], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Extraversion</p>
                    </div>
                </div>
            </div>

            <h3 class="mb-3">Average personality ratings based on random 30% subset</h3>
            <div class="row d-flex justify-content-center mb-6">
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($predictedPersonalityScores[0], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Openness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($predictedPersonalityScores[1], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Agreeableness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($predictedPersonalityScores[2], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Emotional Stability</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($predictedPersonalityScores[3], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Conscientiousness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($predictedPersonalityScores[4], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Extraversion</p>
                    </div>
                </div>
            </div>
            <br>
            

            <h3 class="mb-3 mt-5">Personality predictions</h3>
            <div class="row d-flex justify-content-center mb-4">
                <div class="card bg-light mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h1><?= round($predictedRatingForHighOpenness, 2); ?></h1>
                        <p class="card-text">The predicted rating from a person with high openness</p>

                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h1><?= round($predictedRatingForHighAgreeableness, 2); ?></h1>
                        <p class="card-text">The predicted rating from a person with high agreeableness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h1><?= round($predictedRatingForHighEmotionalStability, 2); ?></h1>
                        <p class="card-text">The predicted rating from a person with high emotional stability</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h1><?= round($predictedRatingForHighConscientiousness, 2); ?></h1>
                        <p class="card-text">The predicted rating from a person with high conscientiousness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h1><?= round($predictedRatingForHighExtraversion, 2); ?></h1>
                        <p class="card-text">The predicted rating from a person with high extraversion</p>
                    </div>
                </div>
            </div>
        </div>

        <!--TAGS-->
        <div class="card row col-md-11 shadow mt-4 ml-2">
            <h3 class="mb-3 mt-1">Tags</h3>

            <div class="row d-flex justify-content-center mb-3">
                <?php
                foreach ($tags as $key) {
                    ?>
                    <div class="card shadow mr-2 ml-2"
                         style="padding-left: 1rem; padding-right: 1rem;border-radius: 20%; background-color: whitesmoke">
                        <p class="centre" style="font-size: 2rem"><?= $key[0] ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>
                
            <h3 class="mb-3">Average personality ratings based on tags (1-7)</h3>
            <div class="row d-flex justify-content-center mb-6">
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScoresTags[0], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Openness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScoresTags[1], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Agreeableness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScoresTags[2], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Emotional Stability</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScoresTags[3], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Conscientiousness</p>
                    </div>
                </div>
                <div class="card bg-light ml-1 mr-1" style="max-width: 12rem;">
                    <div class="card-body">
                        <h2><?= round($personalityScoresTags[4], 1) ?></h2>
                        <p class="card-text" style="font-size: 0.8rem">Extraversion</p>
                    </div>
                </div>
            </div>
            <br>

            <?php

            echo '<h3 class="mb-3 mt-1">Recommended target audience</h3>';

            $predictedRatingsPerPersonality = [];
            $predictedRatingsPerPersonality[0] = $predictedRatingForHighOpenness;
            $predictedRatingsPerPersonality[1] = $predictedRatingForHighAgreeableness;
            $predictedRatingsPerPersonality[2] = $predictedRatingForHighEmotionalStability;
            $predictedRatingsPerPersonality[3] = $predictedRatingForHighConscientiousness;
            $predictedRatingsPerPersonality[4] = $predictedRatingForHighExtraversion;


            if ($avgTagRating == NULL) {
                echo "Not enough data for results";
            } else {
                $closest = 0;
                $lowestDiff = 6;

                $chosenPersonalities = array();

                for ($i = 0; $i < 5; $i++) {
                    $diff = abs($avgTagRating - $predictedRatingsPerPersonality[$i]);
                    if ($diff < $lowestDiff) {
                        $lowestDiff = $diff;
                        $closest = $i;
                    }
                }

                array_push($chosenPersonalities, $closest);

                for ($i = 0; $i < 5; $i++) {
                    $diff = abs($predictedRatingsPerPersonality[$i] - $predictedRatingsPerPersonality[$closest]);
                    if ($diff < 0.05 and $i != $closest) {
                        array_push($chosenPersonalities, $i);
                    }
                }

                echo " ";

                foreach ($chosenPersonalities as $key => $personality) {
                    if ($personality == 0) {
                        echo '<div class="card shadow mr-2 ml-2 mb-1"><h1>High Openness</h1></div>';
                    }
                    if ($personality == 1) {
                        echo '<div class="card shadow mr-2 ml-2 mb-1"><h1>High Agreeableness</h1></div>';
                    }
                    if ($personality == 2) {
                        echo '<div class="card shadow mr-2 ml-2 mb-1"><h1>High Emotional Stability</h1></div>';
                    }
                    if ($personality == 3) {
                        echo '<div class="card shadow mr-2 ml-2 mb-1"><h1>High Conscientiousness</h1></div>';
                    }
                    if ($personality == 4) {
                        echo '<div class="card shadow mr-2 ml-2 mb-1"><h1>High Extraversion</h1></div>';
                    }

                    if (!($key === array_key_last($chosenPersonalities))) {
                        echo "";
                    }
                }
            }
            ?>
            <br><br>
        </div>
    <?php endif; ?>

    <?php
    if ($showMore == 0)
        echo '<a class="d-flex justify-content-center mt-4" href=/movie.php?id=' . $movieId . '&showMore=1>Compute recommendations</a>';
    else
        echo '<a class="d-flex justify-content-center mt-4" href=/movie.php?id=' . $movieId . '&showMore=0>Show less</a>';
    ?>


</div>


</div>
</body>
</html>