<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Tag Predictions</title>
</head>
<body>

<?php

        include_once 'db_connection_init.php';

        $highOpenessTags = array();
        $highAgreeTags = array();
        $highEmotTags = array();
        $highConscienTags = array();
        $highExtravTags = array();

        $sql = "SELECT movieId FROM `Movie`;";
        $rows = mysqli_query($con, $sql);
        $rowarr2 = $rows->fetch_all(MYSQLI_NUM);
        
        $counter = 0;

        foreach($rowarr2 as $t) {
            
            $movieId = $t[0];

            $counter++;

            if ($counter == 10) {
                break;
            }
            
            
            // Per personality rating

            // Openness

            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where openness > 5.25);";

            $rows = mysqli_query($con, $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighOpenness = $rowarr[0];

            // Agreeableness



            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where agreeableness > 5.25);";

            $rows = mysqli_query($con, $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighAgreeableness = $rowarr[0];

            // Emotional Stability


            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where emotional_stability > 5.25);";

            $rows = mysqli_query($con, $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighEmotionalStability = $rowarr[0];

            // Conscientiousness


            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where conscientiousness > 5.25);";

            $rows = mysqli_query($con, $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighConscientiousness = $rowarr[0];

            // Extraversion
            
            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where extraversion > 5.25);";

            $rows = mysqli_query($con, $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighExtraversion = $rowarr[0];




            $sql = "SELECT avg(rating) as AR, `tags`.`COL 3` FROM `Ratings` join `tags` ON Ratings.userId = `tags`.`COL 1` AND Ratings.movieId = `tags`.`COL 2` WHERE movieId = " . $movieId . " GROUP BY `tags`.`COL 3` HAVING avg(rating) > 2.5;";
    
            $rows = mysqli_query($con, $sql);
            $rowarr = $rows->fetch_all(MYSQLI_NUM);
        
            $tags = array();

            foreach($rowarr as $t) {
                array_push($tags, $t);
            }


            foreach($tags as $key => $t) {
                if (abs((int) $t[0] - $predictedRatingForHighOpenness) < 0.05) {
                    array_push($highOpenessTags, $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighAgreeableness) < 0.05) {
                    array_push($highAgreeTags, $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighEmotionalStability) < 0.05) {
                    array_push($highEmotTags, $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighConscientiousness) < 0.05) {
                    array_push($highConscienTags, $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighExtraversion) < 0.05) {
                    array_push($highExtravTags, $t[1]);
                }
            }
        }

        foreach ($highOpenessTags as $key => $value) {
            echo $value;
            echo "<br>";
        }

        echo "<br><br>";

        foreach ($highAgreeTags as $key => $value) {
            echo $value;
            echo "<br>";
        }

        echo "<br><br>";
        
        foreach ($highEmotTags as $key => $value) {
            echo $value;
            echo "<br>";
        }

        echo "<br><br>";

        foreach ($highConscienTags as $key => $value) {
            echo $value;
            echo "<br>";
        }

        echo "<br><br>";

        foreach ($highExtravTags as $key => $value) {
            echo $value;
            echo "<br>";
        }

        echo "<br><br>";

?>
    
</body>
</html>