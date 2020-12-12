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
            <?php include 'encryption.php';?>
        </div>
        <!-- start dynamic content -->
        <div id='pageContent'>
            <div class='bookSimple'>
            <div class="pageTitle">Your Order History</div><br />
                <?php
                
                $CustIDe = $_POST["custIDe"];
                $CustID = decrypt($CustIDe);

                $sql ="SELECT GROUP_CONCAT(
                    CONCAT(nameF, ' ', nameL) SEPARATOR ', '
                ) AS NAME, bookdescriptions.title, bookdescriptions.ISBN, bookorderitems.orderID, bookorderitems.qty, bookorders.orderdate
                FROM bookdescriptions
                INNER JOIN bookauthorsbooks ON bookdescriptions.ISBN=bookauthorsbooks.ISBN
                INNER JOIN bookauthors ON bookauthorsbooks.AuthorID=bookauthors.AuthorID
                INNER JOIN bookcategoriesbooks ON bookdescriptions.ISBN=bookcategoriesbooks.ISBN
                INNER JOIN bookorderitems ON bookdescriptions.ISBN=bookorderitems.ISBN
                INNER JOIN bookorders ON bookorderitems.orderID=bookorders.orderID
                WHERE custID = '$CustID'
                GROUP BY bookdescriptions.ISBN
                order by bookorders.orderID";
                
                //$result is an array containing query results
    $result = mysqli_query($link, $sql)
    or die('SQL syntax error: ' . mysqli_error($link));
    
    if (mysqli_num_rows($result) != 1){
        echo "<div class='pageTitle2'>You have ordered " . mysqli_num_rows($result) . " books</div><br />";
    }else{
        echo "<div class='pageTitle2'>You have ordered " . mysqli_num_rows($result) . " book</div><br />";
    }

                // iterate through the retrieved records
    while ($row = mysqli_fetch_array($result)) {
        //Field names are case sensitive and must match
        //the case used in sql statement
        $orderID = $row['orderID'];
        echo "<div class='bookHistory'>
        <a href='ProductPage.php?isbn=$row[ISBN]'><img class='History' 
             src='/sandvig/mis314/assignments/bookstore//bookimages/$row[ISBN].01.THUMBZZZ.jpg' alt='$row[title]' />
        </a>                 
        <b>Order ID: $row[orderID]</b>&nbsp;&nbsp;
        $row[orderdate]
        <br /> 
        <a class='booktitle' href='ProductPage.php?isbn=$row[ISBN]'>$row[title]</a><br />
        <span class='authors'>by <a href='SearchBrowse.php?search=$row[NAME]'>$row[NAME]</a></span><br />
        Qty: $row[qty]
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