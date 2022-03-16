<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,  initial-scale=1.0">
    <title>Prediction Tag Predictions</title>
</head>
<body>

<?php

        include_once 'db_connection_init.php';

        /* 
        
        ////////////////
        Data Analysis 
        ////////////////
        
        
        $highOpenessTags = array();
        $highAgreeTags = array();
        $highEmotTags = array();
        $highConscienTags = array();
        $highExtravTags = array();

        $sql = "SELECT movieId FROM `Movie`;";

        //$sql = "SELECT movieId FROM `Movie` WHERE movieId > 1000;";

        $rows = mysqli_query($con,  $sql);
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

            $rows = mysqli_query($con,  $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighOpenness = $rowarr[0];

            // Agreeableness



            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where agreeableness > 5.25);";

            $rows = mysqli_query($con,  $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighAgreeableness = $rowarr[0];

            // Emotional Stability


            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where emotional_stability > 5.25);";

            $rows = mysqli_query($con,  $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighEmotionalStability = $rowarr[0];

            // Conscientiousness
§

            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where conscientiousness > 5.25);";

            $rows = mysqli_query($con,  $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighConscientiousness = $rowarr[0];

            // Extraversion
            
            $sql = "SELECT AVG(rating) FROM `PersonalityRatings` WHERE movieId = " . $movieId . " AND userId IN 
            (SELECT Personality.userid FROM `Personality` where extraversion > 5.25);";

            $rows = mysqli_query($con,  $sql);
            $rowarr = $rows->fetch_array();

            $predictedRatingForHighExtraversion = $rowarr[0];




            $sql = "SELECT avg(rating) as AR,  `tags`.`COL 3` FROM `Ratings` join `tags` ON Ratings.userId = `tags`.`COL 1` AND Ratings.movieId = `tags`.`COL 2` WHERE movieId = " . $movieId . " GROUP BY `tags`.`COL 3` HAVING avg(rating) > 2.5;";
    
            $rows = mysqli_query($con,  $sql);
            $rowarr = $rows->fetch_all(MYSQLI_NUM);
        
            $tags = array();

            foreach($rowarr as $t) {
                array_push($tags,  $t);
            }


            foreach($tags as $key => $t) {
                if (abs((int) $t[0] - $predictedRatingForHighOpenness) < 0.05) {
                    array_push($highOpenessTags,  $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighAgreeableness) < 0.05) {
                    array_push($highAgreeTags,  $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighEmotionalStability) < 0.05) {
                    array_push($highEmotTags,  $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighConscientiousness) < 0.05) {
                    array_push($highConscienTags,  $t[1]);
                }
                if (abs((int) $t[0] - $predictedRatingForHighExtraversion) < 0.05) {
                    array_push($highExtravTags,  $t[1]);
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

        */

        echo "<b>Positive tags most associated with openness: </b>";
        echo "<br><br>";
        echo "Coen Brothers, memory, thought-provoking, racism, politics, emotional, edward norton, workplace, stapler, heist, adultery, racism, Stephen King, black-and-white, circus, big top, Insurance, factory, journalism, drug abuse, visually stunning, visually appealing, Unique, romantic, nocturnal, moody, melancholy, melancholic, longing, long takes, loneliness, intimate, heartbreaking, elegant, dreamy, bittersweet, Beautiful, atmospheric, reciprocal spectator, Tradition!, Judaism, Oscar Wilde, Myth, true story, holocaust, World War II, England, prostitution, anime, murder, multiple roles, family, Watergate, journalism, Deep Throat, Rita Hayworth can dance!, India, Vietnam, memory, surreal, love, Jim Carrey, imagination, cult film, In Netflix queue, MacBeth, military, religion, convent, psychology, mecha, epic, end of the world, anime, twisted, surreal, paranoid, hallucinatory, disturbing, depressing, claustrophobic, bizarre, motherhood, orphans, In Netflix queue, 06 Oscar Nominated Best Movie - Animation, marriage, love, undercover cop, twist ending, suspense, Leonardo DiCaprio, Martin Scorsese, Jack Nicholson, atmospheric, ummarti2006, Steve Carell, Anne Hathaway, satire, Quentin Tarantino, Christoph Waltz, Brad Pitt, black comedy, music, indie record label, britpop, procedural, murder, human rights, crime, Cambodia, visually appealing, thought-provoking, smart, good cinematography, cinematography, Cerebral, beautiful visuals, remake, serial killer, time travel, twist ending, remake, post-apocalyptic, Post apocalyptic, mindfuck, Bruce Willis, Brad Pitt, assassination, court, sci-fi, action, great soundtrack, EPIC, Star Wars, oldie but goodie, space opera, luke skywalker, darth vader, twins, disability, Death, marriage, Coen Brothers, India, narrated, dark comedy, based on a book, drug abuse, imdb top 250, Alfred Hitchcock, statue, space, Hal, Nick and Nora Charles, music, dance, police, In Netflix queue, racism, Harper Lee, zither, Venice, ferris wheel, samurai, tense, suspenseful, psychology, Alfred Hitchcock, Norman Bates, remade, imdb top 250, black and white, Vietnam, Salieri, Mozart, incest, Cold, suspense, Stanley Kubrick, psychological, masterpiece, jack nicholson, Horror, disturbing, atmospheric, Stephen King, serial killer, oninous, mental illness, menacing, creepy, chilly, atmospheric, brainwashing, assassination, time travel";
        echo "<br><br>";

        echo "<b>Positive tags most associated with agreeableness: </b>";
        echo "<br><br>";
        echo "mockumentary, 1970s, TV, music, comic book, screwball, island, true story, tobacco, Stephen King, circus, big top, journalism, 1920s, Russell Crowe, Rome, revenge, imdb top 250, history, Epic, ancient Rome, court, drug abuse, family, visually stunning, visually appealing, Unique, romantic, nocturnal, moody, melancholy, melancholic, longing, long takes, loneliness, intimate, heartbreaking, elegant, dreamy, bittersweet, Beautiful, atmospheric, twist ending, psychological, mystery, accident, reciprocal spectator, samurai, In Netflix queue, Australia, sequel, first was much better, crappy sequel, heist, crime, Holocaust, Graham Greene, spoof, World War II, prostitution, Titanic, art, anime, Renee Zellweger, Ewan McGregor, fish, Disney, religion, Watergate, journalism, Deep Throat, Rita Hayworth can dance!, surfing, big wave, family, good and evil, Charlotte Bronte, memory, surreal, love, Jim Carrey, imagination, cult film, comic book, cancer, police, assassination, military, religion, convent, In Netflix queue, generation X, immigrants, drugs, Oscar (Best Actress), superhero, Disney, Samuel L. Jackson, animation, twisted, surreal, paranoid, hallucinatory, disturbing, depressing, claustrophobic, bizarre, India, genocide, Robert De Niro, Ben Stiller, In Netflix queue, 06 Oscar Nominated Best Movie - Animation, In Netflix queue, courtroom drama, treasure hunt, Tom Hanks, Paris, Mystery, conspiracy theory, Audrey Tautou, adventure, undercover cop, twist ending, suspense, Leonardo DiCaprio, Martin Scorsese, Jack Nicholson, atmospheric, ummarti2006, inspirational, clever, solitude, Sci-fi, 2001-like, fantasy, animation, adventure, political right versus left, italy, city politics, time-travel, sci-fi, black hole, Creature Feature, Seth Rogen, James Franco, funny, comedy, bromance, visually appealing, cinematography, beautiful, remake, serial killer, Jane Austen, In Netflix queue, teacher, high school, sword fight, revenge, Oscar (Best Cinematography), mel gibson, Medieval, inspirational, historical, epic, beautiful scenery, Tarantino, Quentin Tarantino, drugs, cult film, hit men, marriage, gambling, Disney, soundtrack, chess, Coen Brothers, aliens, divorce, movie business, falling, imdb top 250, Alfred Hitchcock, statue, Rosebud, Nick and Nora Charles, Politics, John Grisham, death penalty, adolescence, 1950s, King Arthur, England, Aardman, In Netflix queue, police, In Netflix queue, indiana jones, ark of the covenant, archaeology, tense, suspenseful, psychology, Alfred Hitchcock, Norman Bates, remade, imdb top 250, black and white, Vietnam, incest, suspense, Stanley Kubrick, psychological, masterpiece, jack nicholson, Horror, disturbing, atmospheric, Stephen King, Stephen King, revenge, time travel, Holy Grail, archaeology, television, India";
        echo "<br><br>";

        echo "<b>Positive tags most associated with emotional stability: </b>";
        echo "<br><br>";
        echo "Shakespeare, In Netflix queue, Judaism, anti-Semitism, coulda been a contender, boxing, journalism, Indonesia, Academy award (Best Supporting Actress), adultery, lawn mower, brothers, anime, racism, Stephen King, Insurance, missing children, drug abuse, visually stunning, visually appealing, Unique, romantic, nocturnal, moody, melancholy, melancholic, longing, long takes, loneliness, intimate, heartbreaking, elegant, dreamy, bittersweet, Beautiful, atmospheric, Tradition!, Judaism, In Netflix queue, sequel, first was much better, crappy sequel, Lou Gehrig, baseball, World War I, true story, holocaust, ghosts, spoof, World War II, anime, religion, murder, multiple roles, family, Watergate, journalism, Deep Throat, Rita Hayworth can dance!, Vietnam, Charlotte Bronte, cancer, MacBeth, Anne Boleyn, heist, Oscar (Best Actress), fatherhood, genocide, motherhood, Dickens, treasure hunt, Tom Hanks, Paris, Mystery, conspiracy theory, Audrey Tautou, adventure, undercover cop, twist ending, suspense, Leonardo DiCaprio, Martin Scorsese, Jack Nicholson, atmospheric, satire, Quentin Tarantino, Christoph Waltz, Brad Pitt, black comedy, solitude, Sci-fi, 2001-like, time-travel, sci-fi, black hole, Seth Rogen, James Franco, funny, comedy, bromance, procedural, murder, human rights, crime, Cambodia, teacher, high school, time travel, twist ending, remake, post-apocalyptic, Post apocalyptic, mindfuck, Bruce Willis, Brad Pitt, serial killer, Death, gambling, atmospheric, Coen Brothers, Aardman, aliens, Atomic bomb, dark comedy, black comedy, voyeurism, imdb top 250, Alfred Hitchcock, adultery, movies, movie business, eerie, Politics, violence, heist, Tarantino, religion, neo-noir, humorous, racism, Harper Lee, space opera, luke skywalker, darth vader, samurai, Vietnam, space, NASA, Cold, POW, revenge, brainwashing, assassination, time travel, World War II, Holy Grail, archaeology, television";
        echo "<br><br>";

        echo "<b>Positive tags most associated with conscientiousness: </b>";
        echo "<br><br>";
        echo "In Netflix queue, memory, survival, motherhood, samurai, Stephen King, E.M. Forster, Pearl S Buck, class, 1920s, drug abuse, Tradition!, Judaism, movie business, Australia, anime, World War I, Myth, anime, Dickens, true story, holocaust, ghosts, World War II, England, race, blindness, blind, mental illness, Watergate, journalism, Deep Throat, amnesia, Rita Hayworth can dance!, surfing, big wave, In Netflix queue, Charlotte Bronte, mountain climbing, memory, surreal, love, Jim Carrey, imagination, cult film, MacBeth, Oscar (Best Actress), Stand Up, In Netflix queue, revenge, genocide, marriage, Dickens, treasure hunt, Tom Hanks, Paris, Mystery, conspiracy theory, Audrey Tautou, adventure, undercover cop, twist ending, suspense, Leonardo DiCaprio, Martin Scorsese, Jack Nicholson, atmospheric, ummarti2006, Will Smith, Charlize Theron, bad script, satire, Quentin Tarantino, Christoph Waltz, Brad Pitt, black comedy, comic book, too many characters, Ryan Reynolds, hugh jackman, bad plot, music, indie record label, britpop, Soundtrack, animation, Adam Sandler, time-travel, sci-fi, black hole, short stories, multiple short stories, ironic, dark humor, dark comedy, black comedy, procedural, murder, human rights, crime, Cambodia, remake, teacher, high school, sci-fi, action, great soundtrack, EPIC, Star Wars, oldie but goodie, space opera, luke skywalker, darth vader, Death, marriage, gambling, sexuality, Japan, atmospheric, knights, robots, Coen Brothers, Aardman, voyeurism, statue, Hollywood, John Grisham, death penalty, music, dance, violence, heist, Tarantino, religion, neo-noir, humorous, King Arthur, England, Middle East, racism, Harper Lee, aliens, tense, suspenseful, psychology, Alfred Hitchcock, Norman Bates, remade, imdb top 250, black and white, Vietnam, POW, serial killer, oninous, mental illness, menacing, creepy, chilly, atmospheric, revenge, time travel, World War II, Holy Grail, archaeology";
        echo "<br><br>";

        echo "<b>Positive tags most associated with extraversion: </b>";
        echo "<br><br>";
        echo "death, twist ending, suspense, psychological, plot twist, Mystery, mindfuck, future, TV, twist ending, In Netflix queue, alcoholism, coulda been a contender, boxing, divorce, rasicm, small towns, heist, Star Trek, space opera, cameo:Whoopi Goldberg, fatherhood, black-and-white, Pearl S Buck, In Netflix queue, Russell Crowe, Rome, revenge, imdb top 250, history, Epic, ancient Rome, court, Dogs, drug abuse, Quakers, visually stunning, visually appealing, Unique, romantic, nocturnal, moody, melancholy, melancholic, longing, long takes, loneliness, intimate, heartbreaking, elegant, dreamy, bittersweet, Beautiful, atmospheric, religion, preacher, alcoholism, Holocaust, family, union, Nuclear disaster, In Netflix queue, Judaism, anti-Semitism, boxing, Holocaust, 70mm, art, teenagers, Graham Greene, crime, widows/widowers, planes, drugs, ghosts, obsession, race, blindness, blind, Stones of Summer, books, prostitution, prostitution, mockumentary, child abuse, Ninotchka remake, Civil War, religion, trains, Rita Hayworth can dance!, Amish, real estate, Charlotte Bronte, crime, adolescence, for katie, sports, Hepburn and Tracy, adultery, swashbuckler, assassination, marriage, alcoholism, abortion, superhero, Disney, Samuel L. Jackson, animation, journalism, revenge, In Netflix queue, lesbian, drugs, psychology, mecha, epic, end of the world, anime, twisted, surreal, paranoid, hallucinatory, disturbing, depressing, claustrophobic, bizarre, boksdrama, boxing, Will Smith, Charlize Theron, bad script, solitude, Sci-fi, 2001-like, time travel, space travel, space, Simon Pegg, sci-fi, quick cuts, lack of story, lack of development, future, stop using useless characters for filler, Shia LaBeouf, ridiculous, plot holes, needed more autobots, Michael Bay, bad plot, bad humor, whimsical, surreal, music, indie record label, britpop, superhero, political commentary, Morgan Freeman, great ending, comic book, Christopher Nolan, Christian Bale, Anne Hathaway, zoe kazan, daniel radcliffe, time-travel, sci-fi, black hole, Thor, Thanos, Robert Downey Jr., MCU, Marvel, Guardians of the Galaxy, Great villain, Dr. Strange, comic book, Viggo Mortensen, individualism, good writing, freedom, creative, building a family, Mafia, teacher, high school, time travel, twist ending, remake, post-apocalyptic, Post apocalyptic, mindfuck, Bruce Willis, Brad Pitt, Made me cry, marriage, coma, Disney, soundtrack, Ireland, atmospheric, robots, Hannibal Lector, seen at the cinema, imdb top 250, Alfred Hitchcock, George Bernard Shaw, Rosebud, space, Hal, aging, King Arthur, England, space opera, luke skywalker, darth vader, samurai, tense, suspenseful, psychology, Alfred Hitchcock, Norman Bates, remade, imdb top 250, black and white, Vietnam, New York, Stephen King, serial killer, oninous, mental illness, menacing, creepy, chilly, atmospheric, POW, revenge, brainwashing, assassination, spoof, Holy Grail, archaeology, television";
        echo "<br><br>";

?>
    
</body>
</html>