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
        WHERE Movie.movieId = " . $id ."
        GROUP BY Movie.title, Movie.year, Movie.movieId;";
        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $movieId = $rowarr[0];
        $title = $rowarr[1];
        $year = $rowarr[2];
        $avgRating = $rowarr[3];

        /////////////// 
        // Use case (4)
        ///////////////

        $sql = "SELECT COUNT(rating)
        FROM `Ratings`
        WHERE Ratings.movieId = ". $rowarr[0] . ";";
        
        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfNormalReviews = $rowarr[0];
        $subsetNumOfNormalReviews = (int) ceil($numOfNormalReviews * (3/10));


        // First average, 10% random sample

        $sql = "SELECT AVG(rating) as ar
                FROM 
                (
                SELECT Ratings.rating
                FROM `Ratings`
                WHERE Ratings.movieId = " . $movieId . "
                ORDER BY RAND()
                LIMIT ". $subsetNumOfNormalReviews . "
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
                LIMIT ". $subsetNumOfNormalReviews . "
                ) t1;";

        
        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedRatingTimestampSample = $rowarr[0];


        // Use case (5)

        $sql = "SELECT COUNT(*) FROM `Personality` WHERE userid IN ( SELECT userid FROM `PersonalityRatings` WHERE PersonalityRatings.rating > 2.5 AND movieId = " . $movieId ." );";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $numOfPersonalityRatings = $rowarr[0];
        $subsetNumOfPersonalityRatings = (int) ceil($numOfPersonalityRatings * (3/10));

        $sql = "SELECT AVG(openness), AVG(agreeableness), AVG(emotional_stability), AVG(conscientiousness), AVG(extraversion)
        FROM `Personality` 
        WHERE userid IN
        (
        SELECT userid
        FROM `PersonalityRatings`
        WHERE PersonalityRatings.rating > 2.5 AND movieId = " . $movieId ."
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
        AND movieId = " . $movieId ." ORDER BY RAND() LIMIT " . $subsetNumOfPersonalityRatings .") as t1)";

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
        $numOfHighOpenness = (int) ceil($numOfHighOpenness * (3/10));

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
        $numOfHighAgreeableness = (int) ceil($numOfHighAgreeableness * (3/10));

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
        $numOfHighEmotionalStability = (int) ceil($numOfHighEmotionalStability * (3/10));


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
        $numOfHighConscientiousness = (int) ceil($numOfHighConscientiousness * (3/10));


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
        $numOfHighExtraversion = (int) ceil($numOfHighExtraversion * (3/10));

        $sql = "SELECT AVG(rating) FROM (SELECT * FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
        (SELECT * FROM (SELECT Personality.userid FROM `Personality` where extraversion > 5.25) as t1) ORDER BY RAND() LIMIT " . $numOfHighExtraversion . "
        ) as t2;";

        $rows = mysqli_query($con, $sql);
        $rowarr = $rows->fetch_array();

        $predictedRatingForHighExtraversion = $rowarr[0];



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
        echo "Number of ratings in 30% subset: " . $subsetNumOfNormalReviews;

        echo '<br><br>';

        echo "Average rating by random sample at 30%: " . $predictedRatingRandomSample;

        echo '<br>';

        echo "Average rating by first 30% of reviews by timestamp: " . $predictedRatingTimestampSample;


        echo '<br><br>';

        echo "Number of ratings in personality data: " . $numOfPersonalityRatings;
        echo '<br>';
        echo "Number of ratings in personality data 30% subset: " . $subsetNumOfPersonalityRatings;
        echo '<br><br>';

        echo "Average personality ratings";
        echo '<br><br>';
        echo "Openess: " . round($personalityScores[0], 1);
        echo '<br>';
        echo "Agreeableness: " . round($personalityScores[1], 1);
        echo '<br>';
        echo "Emotional Stability: " . round($personalityScores[2], 1);
        echo '<br>';
        echo "Conscientiousness: " . round($personalityScores[3], 1);
        echo '<br>';
        echo "Extraversion: " . round($personalityScores[4], 1);

        echo '<br><br>';

        echo "Average personalities data based on random 30% subset";
        echo '<br><br>';

        echo "Openess: " . round($predictedPersonalityScores[0], 1);
        echo '<br>';
        echo "Agreeableness: " . round($predictedPersonalityScores[1], 1);
        echo '<br>';
        echo "Emotional Stability: " . round($predictedPersonalityScores[2], 1);
        echo '<br>';
        echo "Conscientiousness: " . round($predictedPersonalityScores[3], 1);
        echo '<br>';
        echo "Extraversion: " . round($predictedPersonalityScores[4], 1);


        echo '<br><br>';
        echo "A person with high Openess would rate this film: " . round($predictedRatingForHighOpenness, 2);
        echo '<br>';
        echo "A person with high Agreeableness would rate this film: " . round($predictedRatingForHighAgreeableness, 2);
        echo '<br>';
        echo "A person with high Emotional Stability would rate this film: " . round($predictedRatingForHighEmotionalStability, 2);
        echo '<br>';
        echo "A person with high Conscientiousness would rate this film: " . round($predictedRatingForHighConscientiousness, 2);
        echo '<br>';
        echo "A person with high Extraversion would rate this film: " . round($predictedRatingForHighExtraversion, 2);
        echo '<br>';
        






    ?>
</body>
</html>