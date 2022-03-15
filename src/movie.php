<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Page</title>
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

// Use case (4)

$sql = "SELECT COUNT(rating)
        FROM `Ratings`
        WHERE Ratings.movieId = " . $rowarr[0] . ";";

$rows = mysqli_query($con, $sql);
$rowarr = $rows->fetch_array();

$numOfNormalReviews = $rowarr[0];
$subsetNumOfNormalReviews = intdiv($numOfNormalReviews, 10);


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


// Use case (5)

$sql = "SELECT COUNT(*) FROM `Personality` WHERE userid IN ( SELECT userid FROM `PersonalityRatings` WHERE PersonalityRatings.rating > 2.5 AND movieId = " . $movieId . " );";

$rows = mysqli_query($con, $sql);
$rowarr = $rows->fetch_array();

$numOfPersonalityRatings = $rowarr[0];
$subsetNumOfPersonalityRatings = intdiv($numOfPersonalityRatings, 10);

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


/////////// UI


// $movieId
// $title
// $year
// %avgRating

// $sumOfNormalReviews
// $subsetNumOfNormalReviews

// $predictedRatingRandomSample
// $predictedRatingTimestampSample

// $numOfPersonalityRatings
// $subsetNumOfPersonalityRatings

// $personalityScores[]
// $predictedPersonalityScores[]


echo 'Stats: <br><br>';
echo 'ID: ' . $movieId;
echo '<br>';
echo 'Title: ' . $title;
echo '<br>';
echo 'Year: ' . $year;
echo '<br><br>';
echo 'Rating: ' . $avgRating;

echo '<br><br>';

echo "Number of ratings: " . $numOfNormalReviews;
echo '<br>';
echo "Number of ratings in 10% subset: " . $subsetNumOfNormalReviews;

echo '<br><br>';

echo "Average rating by random sample at 10%: " . $predictedRatingRandomSample;

echo '<br>';

echo "Average rating by first 10% of reviews by timestamp: " . $predictedRatingTimestampSample;


echo '<br><br>';

echo "Number of ratings in personality data: " . $numOfPersonalityRatings;
echo '<br>';
echo "Number of ratings in personality data 10% subset: " . $subsetNumOfPersonalityRatings;
echo '<br><br>';
?>

<h2>Average personality ratings</h2>

<?php
echo "Openess: " . $personalityScores[0];
echo '<br>';
echo "Agreeableness: " . $personalityScores[1];
echo '<br>';
echo "Emotional Stability: " . $personalityScores[2];
echo '<br>';
echo "Conscientiousness: " . $personalityScores[3];
echo '<br>';
echo "Extraversion: " . $personalityScores[4];

echo '<br><br>';
?>

<h2>Average personalities data based on random 10% subset</h2>
<?php
echo "Openess: " . $predictedPersonalityScores[0];
echo '<br>';
echo "Agreeableness: " . $predictedPersonalityScores[1];
echo '<br>';
echo "Emotional Stability: " . $predictedPersonalityScores[2];
echo '<br>';
echo "Conscientiousness: " . $predictedPersonalityScores[3];
echo '<br>';
echo "Extraversion: " . $predictedPersonalityScores[4];


?>
</body>
</html>