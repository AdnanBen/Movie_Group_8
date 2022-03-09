<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="styles.css">
        <title>Movies</title>
        
    </head>
    <body>
    </body>
</html>

<?php 

    include_once 'db_connection_init.php';  

    $sql = 'SELECT Movie.title, Movie.year, Movie.movieId, avg(Ratings.rating) as AR from Ratings join Movie on Ratings.movieId = Movie.movieId
    GROUP BY Movie.title, Movie.year, Movie.movieId LIMIT 50';

?>



<div class = "container">
    <div class = "row">


        <div class = "col-md-8">
            <div class = "card mt-3">
                <div class = "card-body">

                <?php
                    $selectedGenres = [];
                    if (isset($_GET['genres']))

                    {
                        echo "IDs of selected genres: ";
                        $selectedGenres = [];
                        $selectedGenres = $_GET['genres'];
                        $arrSize = count($selectedGenres);
                        echo "size" . $arrSize;
                        echo ": ";
                        foreach ($selectedGenres as $selectedGenre) {
                            echo $selectedGenre . ',';
                        }

                        $sql = "SELECT Movie.movieId, Movie.title, Movie.year, avg(Ratings.rating) as AR, (((count(Ratings.rating) * avg(Ratings.rating))+(100*3.5))/(count(Ratings.rating)+100)) as BR from Ratings 
                        join Movie on Ratings.movieId = Movie.movieId 
                        join MovieGenreLink on MovieGenreLink.movieId = Movie.movieId 
                        WHERE MovieGenreLink.genreId in (". implode(',', $selectedGenres) . ")
                        GROUP BY Movie.title, Movie.movieId, Movie.year
                        HAVING COUNT(DISTINCT MovieGenreLink.genreId) = " . $arrSize . " ORDER BY movieId LIMIT 50";

                    }


                ?>


                    <table class = "table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Rating</th>
                                <th>Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $rows = mysqli_query($con, $sql);
                                foreach($rows as $row) 
                                {
                                    ?>
                                    <tr>
                                        <td><a href="/movie.php?id=<?= $row['movieId'] ?>"><?= $row['title'] ?></a></td>
                                        <td><?= round($row['AR'], 2) ?></td>
                                        <td><?= $row['year'] ?></td>
                                    </tr>
                                    <?php
                                }
                            ?>  
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class = "col-md-3">
        <form action= "" method = "GET">
            <div class = "card shadow mt-3">
                <div class = "card-header">
                    <h5>Filter
                        <button type = "apply" class = "btn btn-primary btn-sm float-end">Apply</button>
                    </h5>
                </div>
                <div class = "card-body">
                    <h6>Genres</h6>
                    <hr>
                    <?php
                        $sql = 'SELECT * FROM Genre';
                        $get_genre = mysqli_query($con, $sql);

                        foreach($get_genre as $genreArray) {
                            $selectedGenres = [];
                            if (isset($_GET['genres'])) {
                                $selectedGenres = $_GET['genres'];
                            }

                            ?>
                                <div>
                                    <input type="checkbox" name="genres[]" value="<?= $genreArray['genreId'];?>"
                                    <?php if(in_array($genreArray['genreId'], $selectedGenres)) {echo "checked";} ?>
                                    />
                                    <?= $genreArray['genre']; ?>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
        </form>
        </div>

        
    </div>
    
</div>
