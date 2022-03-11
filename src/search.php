<html>
<head>
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
    <title>Search results</title>

</head>
<body>
</body>
</html>

<?php
include_once 'db_connection_init.php';

$query = $_GET['search'];

$query = htmlspecialchars($query);

$query = mysqli_real_escape_string($con, $query);

$sql = 'SELECT Movie.title, Movie.year, Movie.movieId, avg(Ratings.rating) as AR, (((count(Ratings.rating) * avg(Ratings.rating))+(100*3.5))/(count(Ratings.rating)+100)) as BR 
    from Ratings 
    join Movie on Ratings.movieId = Movie.movieId
    WHERE Movie.title LIKE "%' . $query . '%"
    GROUP BY Movie.title, Movie.year, Movie.movieId 
    ';
?>

<div class="container">
    <div class="row">


        <div class="col-md-8">
            <div class="card mt-3 card shadow mt-3">

                <nav class="navbar navbar-light bg-light justify-content-between" style="background-color: #e3f2fd;">
                    <a class="navbar-brand justify-content-center">UCL Movie Library</a>
                    <form class="form-inline justify-content-center mt-3" action="search.php" method="GET">
                        <input class="form-control mr-1" value="<?= $query ?>" type="search" name="search"
                               placeholder="Search" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>

                </nav>

                <?php
                $selectedGenres = [];
                if (isset($_GET['genres'])) {
                    $selectedGenres = [];
                    $selectedGenres = $_GET['genres'];
                    $arrSize = count($selectedGenres);
                    /*
                    echo "IDs of selected genres: ";
                    echo "size" . $arrSize;
                    echo ": ";
                    foreach ($selectedGenres as $selectedGenre) {
                        echo $selectedGenre . ',';
                    }
                    */

                    $sql = "SELECT Movie.movieId, Movie.title, Movie.year, avg(Ratings.rating) as AR, (((count(Ratings.rating) * avg(Ratings.rating))+(100*3.5))/(count(Ratings.rating)+100)) as BR from Ratings 
                        join Movie on Ratings.movieId = Movie.movieId 
                        join MovieGenreLink on MovieGenreLink.movieId = Movie.movieId 
                        WHERE MovieGenreLink.genreId in (" . implode(',', $selectedGenres) . ")
                        GROUP BY Movie.title, Movie.movieId, Movie.year
                        HAVING COUNT(DISTINCT MovieGenreLink.genreId) = " . $arrSize;
                }

                $selectedSort;
                if (isset($_GET['sortmode'])) {
                    $selectedSort = $_GET['sortmode'];
                    if ($selectedSort == "1") {
                        $sql = $sql . " ORDER BY movieId";
                    }
                    if ($selectedSort == "2") {
                        $sql = $sql . " ORDER BY `BR` ASC";
                    }
                    if ($selectedSort == "3") {
                        $sql = $sql . " ORDER BY `BR` DESC";
                    }
                    if ($selectedSort == "4") {
                        $sql = $sql . " ORDER BY `Movie`.`year` ASC";
                    }
                    if ($selectedSort == "5") {
                        $sql = $sql . " ORDER BY `Movie`.`year` DESC";
                    }
                    if ($selectedSort == "6") {
                        $sql = $sql . " ORDER BY `Movie`.`title` ASC";
                    }
                    if ($selectedSort == "7") {
                        $sql = $sql . " ORDER BY `Movie`.`title` DESC";
                    }
                    if ($selectedSort == "8") {
                        $sql = $sql . " ORDER BY `AR` ASC";
                    }
                    if ($selectedSort == "9") {
                        $sql = $sql . " ORDER BY `AR` DESC";
                    }
                } else {
                    $sql = $sql . " ORDER BY movieId";
                }

                $sql = $sql . " LIMIT 50";
                ?>


                <table class="table table-striped">
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
                    foreach ($rows as $row) {
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

        <div class="col-md-3">
            <form action="" method="GET">
                <div class="card shadow mt-3">
                    <div class="card-body">
                        <h5>Sort</h5>
                        <?php
                        $selectedSort;
                        if (isset($_GET['sortmode'])) {
                            $selectedSort = $_GET['sortmode'];
                        }

                        ?>
                        <select name="sortmode" class="form-control" id="sort1">
                            <option value="1" <?php if (isset($selectedSort) and $selectedSort == "1") {
                                echo '" selected = "selected"';
                            } ?>> Default
                            </option>
                            <option value="2"<?php if (isset($selectedSort) and $selectedSort == "2") {
                                echo '" selected = "selected"';
                            } ?>> Popularity Ascending
                            </option>
                            <option value="3" <?php if (isset($selectedSort) and $selectedSort == "3") {
                                echo '" selected = "selected"';
                            } ?>> Popularity Descending
                            </option>
                            <option value="4" <?php if (isset($selectedSort) and $selectedSort == "4") {
                                echo '" selected = "selected"';
                            } ?>> Release Year Ascending
                            </option>
                            <option value="5" <?php if (isset($selectedSort) and $selectedSort == "5") {
                                echo '" selected = "selected"';
                            } ?>> Release Year Descending
                            </option>
                            <option value="6" <?php if (isset($selectedSort) and $selectedSort == "6") {
                                echo '" selected = "selected"';
                            } ?>> Title A-Z
                            </option>
                            <option value="7" <?php if (isset($selectedSort) and $selectedSort == "7") {
                                echo '" selected = "selected"';
                            } ?>> Title Z-A
                            </option>
                            <option value="8" <?php if (isset($selectedSort) and $selectedSort == "8") {
                                echo '" selected = "selected"';
                            } ?>> Rating Ascending
                            </option>
                            <option value="9" <?php if (isset($selectedSort) and $selectedSort == "9") {
                                echo '" selected = "selected"';
                            } ?>> Rating Descending
                            </option>
                        </select>

                    </div>
                    <div class="card-body">
                        <h5>Genres</h5>
                        <?php
                        $sql = 'SELECT * FROM Genre';
                        $get_genre = mysqli_query($con, $sql);

                        foreach ($get_genre as $genreArray) {
                            $selectedGenres = [];
                            if (isset($_GET['genres'])) {
                                $selectedGenres = $_GET['genres'];
                            }

                            ?>
                            <div>
                                <input type="checkbox" name="genres[]" value="<?= $genreArray['genreId']; ?>"
                                    <?php if (in_array($genreArray['genreId'], $selectedGenres)) {
                                        echo "checked";
                                    } ?>
                                />
                                <?= $genreArray['genre']; ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="card-body">
                        <button type="apply" class="btn btn-primary btn-block">Apply filters</button>
                    </div>
                </div>
            </form>
        </div>


    </div>

</div>
