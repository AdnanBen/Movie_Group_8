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

    echo '<p>Hello World</p>'; 

    $host = 'db';
    $user = 'root';
    $pass = 'rootpassword';
    $dbname = 'MovieDB';

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        echo "Connected successfully<br><br>";
    }

    $sql = 'SELECT * FROM Movie';
    $rows = mysqli_query($conn, $sql);

?>

<div class = "col-md-4">

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
                                <td><?= $row['title'] ?></td>
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