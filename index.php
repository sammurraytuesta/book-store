<?php
//include database connection
include("databaseConnection.php");

//connect to database
$link = fConnectToDatabase();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>GeekBooks - MIS 314 Sample Bookstore</title>
    <link rel="stylesheet" href="/sandvig/mis314/assignments/bookstore/StyleSheet.css" type="text/css" />
</head>


<body>

    <?php include 'header.php';?>

    <div id="pageContainer">
        <div id="leftColumn">
            <?php include 'menu.php';?>
        </div>
        <!-- start dynamic content -->
        <div id='pageContent'>

            <div class='bookSimple'>

                <?php
            

            //Retrieve parameters from querystring and sanitize
            $ISBN = fCleanString($link, $_GET['ISBN'], 15);
            $title = fCleanString($link, $_GET['title'], 15);
            $description = fCleanString($link, $_GET['description'], 15);

            //List records
            $sql = 'SELECT ISBN, title, description  
            FROM bookdescriptions
            order by rand() limit 3;';

            //$result is an array containing query results
            $result = mysqli_query($link, $sql)
                    or die('SQL syntax error: ' . mysqli_error($link));

                    while ($row = mysqli_fetch_array($result)) {
                        //Field names are case sensitive and must match
                        //the case used in sql statement
                        $description = substr($row['description'], 0, 250);
                        $ISBN = $row['ISBN'];
                        echo "<a class='booktitle' href='ProductPage.php?ISBN=$row[ISBN]'> $row[title]</a> <br /><a href='ProductPage.php?ISBN=$row[ISBN]'>
                        <img class='Book' alt='$row[title]'
                            src='/sandvig/mis314/assignments/bookstore/bookimages/$row[ISBN].01.THUMBZZZ.jpg'>
                    </a>
                    <p>$description
                    <a href='ProductPage.php?ISBN=$row[ISBN]'>more...</a><br><br>";
                    }
            ?>
            </div>
        </div>
        <!-- end dynamic content -->

        <?php include 'footer.php';?>
    </div>

    <!-- Sample site uses a MasterPage-like template for page layout. -->
    <!-- This is not required. It may be used as an enhancement. -->
    <!-- Source: http://spinningtheweb.blogspot.com/2006/07/approximating-master-pages-in-php.html -->
</body>

</html>