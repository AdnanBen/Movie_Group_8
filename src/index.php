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

    $sql = 'SELECT * FROM Movie';
    $rows = mysqli_query($con, $sql);

?>



<div class = "container">
    <div class = "row">


        <div class = "col-md-8">
            <div class = "card mt-3">
                <div class = "card-body">
                    <table class = "table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($rows as $row) 
                                {
                                    ?>
                                    <tr>
                                        <td><a href="/movie.php?id=<?= $row['movieId'] ?>"><?= $row['title'] ?></a></td>
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
            <div class = "card shadow mt-3">
                <div class = "card-header">
                    <h5>Filter
                        <button type = "submit" class = "btn btn-primary btn-sm float-end">Apply</button>
                    </h5>
                </div>
                <div class = "card-body">
                    <h6>Test</h6>
                    <hr>
                    <?php
                        $sql = 'SELECT * FROM Genre';
                        $get_genre = mysqli_query($con, $sql);

                        foreach($get_genre as $genreArray) {
                            ?>
                                <div>
                                    <input type="checkbox" name="genres[]" value="<?= $genreArray['genreId'];?>">
                                    <?= $genreArray['genre']; ?>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
        </div>


    </div>
</div>
