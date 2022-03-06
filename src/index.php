<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
        <title>Test</title>
    </head>
    <body>
    </body>
</html>

<?php 

    include_once 'db_connection_init.php';  

    $sql = 'SELECT * FROM Movie';
    $rows = mysqli_query($conn, $sql);

?>

<div class = "centre-col-md-4">

    <div class = "card mt-4">

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