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
                $CategoryID = fCleanstring($link, $_GET["CategoryID"], 15);
                $search = fCleanstring($link, $_GET["search"], 15);
                $nameF = fCleanString($link, $_GET['nameF'], 15);
                $nameL = fCleanString($link, $_GET['nameL'], 15);
                $name = fCleanString($link, $_GET['NAME'], 15);
                $title = fCleanString($link, $_GET['title'], 15);
                $description = fCleanString($link, $_GET['description'], 15);
                $ISBN = fCleanString($link, $_GET['ISBN'], 15);
                $AuthorID = fCleanString($link, $_GET['AuthorID'], 15);
                

                if (strlen($CategoryID) == 0){
                    $sql = "SELECT DISTINCT GROUP_CONCAT(
                    CONCAT(nameF, ' ', nameL) SEPARATOR ', '
                ) AS NAME, d.ISBN, title, description, price
                    FROM bookauthors a, bookauthorsbooks ba, bookdescriptions d,
                    bookcategoriesbooks cb, bookcategories c
                    WHERE a.AuthorID = ba.AuthorID
                    AND ba.ISBN = d.ISBN
                    AND d.ISBN = cb.ISBN
                    AND c.CategoryID = cb.CategoryID
                    AND (CategoryName = '$search'
                    OR title LIKE '%$search%'
                    OR description LIKE '%$search%'
                    OR publisher LIKE '%$search%'
                    OR concat_ws(' ', nameF, nameL, nameF) LIKE '%$search%' )
                    GROUP BY d.ISBN
                    ORDER BY title";
                } else{

                $sql ="SELECT GROUP_CONCAT(
                    CONCAT(nameF, ' ', nameL) SEPARATOR ', '
                ) AS NAME, bookdescriptions.title, bookdescriptions.description, bookdescriptions.ISBN 
                FROM bookdescriptions
                INNER JOIN bookauthorsbooks ON bookdescriptions.ISBN=bookauthorsbooks.ISBN
                INNER JOIN bookauthors ON bookauthorsbooks.AuthorID=bookauthors.AuthorID
                INNER JOIN bookcategoriesbooks ON bookdescriptions.ISBN=bookcategoriesbooks.ISBN
                WHERE bookcategoriesbooks.ISBN = bookdescriptions.ISBN 
                AND bookcategoriesbooks.CategoryID = '$CategoryID'
                GROUP BY bookdescriptions.ISBN
                order by bookdescriptions.title";
                }

                //$result is an array containing query results
    $result = mysqli_query($link, $sql)
    or die('SQL syntax error: ' . mysqli_error($link));

                if (strlen($CategoryID) == 0 && strlen($search) > 0){
                    echo "<div class='pageTitle2'>" . mysqli_num_rows($result) . " books contain<font color='#CC0000'> '$search'</font></div><br />";
                }elseif(strlen($search) == 0 && strlen($CategoryID) == 0){
                    echo "<div class='pageTitle2'>" . mysqli_num_rows($result) . " books in database</div><br />";
                } else{echo "<div class='pageTitle2'>" . mysqli_num_rows($result) . " books in category</div>";
                };

                // iterate through the retrieved records
    while ($row = mysqli_fetch_array($result)) {
        //Field names are case sensitive and must match
        //the case used in sql statement
        $description = substr($row['description'], 0, 250);
        $title = $row['title'];
        echo "<div class='bookSimple'>                

        <a class='booktitle' href='ProductPage.php?ISBN=$row[ISBN]'>$row[title]</a>
        <br />
        <span class='authors'>by <a href='SearchBrowse.php?search=$row[NAME]'>$row[NAME]</a></span><br />
        <a href='ProductPage.php?ISBN=$row[ISBN]'>
            <img class='Book'
            src='/sandvig/mis314/assignments/bookstore/bookimages/$row[ISBN].01.THUMBZZZ.jpg'>
        </a>

            <p>$description<a href='ProductPage.php?ISBN=$row[ISBN]'>more...</a>
    </div>";
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