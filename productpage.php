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
    <!-- lightbox -->
    <link rel="stylesheet" href="node_modules/baguettebox.js/dist/baguetteBox.css">
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
            $price = fCleanString($link, $_GET['price'], 15);
            $publisher = fCleanString($link, $_GET['publisher'], 15);
            $pubdate = fCleanString($link, $_GET['pubdate'], 15);
            $edition = fCleanString($link, $_GET['edition'], 15);
            $pages = fCleanString($link, $_GET['pages'], 15);

            //List records
            $sql = "SELECT GROUP_CONCAT(
                CONCAT(nameF, ' ', nameL) SEPARATOR ', '
            ) AS NAME, bookdescriptions.title, bookdescriptions.description, bookdescriptions.ISBN, bookdescriptions.price, bookdescriptions.publisher, bookdescriptions.pubdate, bookdescriptions.edition, bookdescriptions.pages 
            FROM bookdescriptions
            INNER JOIN bookauthorsbooks ON bookdescriptions.ISBN=bookauthorsbooks.ISBN
            INNER JOIN bookauthors ON bookauthorsbooks.AuthorID=bookauthors.AuthorID
            WHERE bookdescriptions.ISBN = bookauthorsbooks.ISBN
            AND bookdescriptions.ISBN = '$ISBN'
            GROUP BY bookdescriptions.ISBN
            order by bookdescriptions.title";
            
            //$result is an array containing query results
            $result = mysqli_query($link, $sql)
                    or die('SQL syntax error: ' . mysqli_error($link));

                    while ($row = mysqli_fetch_array($result)) {
                        //Field names are case sensitive and must match
                        //the case used in sql statement
                        $savings = $row['price'] * 20 / 100;
                        $ourprice = $row['price'] - $savings;
                        echo "<div id='pageContent'><div class='bookSimple'><div class='bookTitle'> $row[title]</div>

                        <div class='authors'>by <a href='SearchBrowse.php?search=$row[NAME]'>$row[NAME]</a></div>
                        <a class='baguetteBoxOne' href='/sandvig/mis314/assignments/bookstore/bookimages/$row[ISBN].01.LZZZZZZZ.jpg'>
                           <img class='Book' alt='$row[title]' title='$row[title]'
                                src='/sandvig/mis314/assignments/bookstore/bookimages/$row[ISBN].01.MZZZZZZZ.jpg'>
                        </a> <br />
                     
                        <div>
                           <span class='priceLabel'>List Price: </span>
                           <span class='bookPriceList'>
                              $$row[price]</span>
                        </div>
                     
                        <div>
                           <span class='priceLabel'>Our Price:</span>
                           <span class='bookPriceB'>
                              $".number_format($ourprice, 2)." </span>
                        </div>
                     
                        <div>
                           <span class='priceLabel'>You Save:</span>
                           <span class='bookPriceB'>
                              $". number_format($savings, 2)." (20%)</span><br />
                        </div>
                     
                        <div class='bookDetails'>
                           <div> <b>ISBN:</b> $row[ISBN] </div>
                           <div> <b>Publisher:</b> $row[publisher]</div>
                           <div>  <b>Pages:</b> $row[pages]</div>
                           <div> <b>Edition:</b> $row[edition]</div>
                        </div> 
                     
                        <a href='ShoppingCart.php?addISBN=$row[ISBN]'>
                           <img class='addToCart' src='/sandvig/mis314/assignments/bookstore/images/add-to-cart-small.gif' 
                                alt='Add to cart' title='Add to cart' ></a>
                     
                        <div class='bookDescription'>
                        <p><em>$row[description]</p>   </div>
                        <a href='ShoppingCart.php?addISBN=$row[ISBN]'>
                           <img class='addToCart'  src='/sandvig/mis314/assignments/bookstore/images/add-to-shopping-cart-blue.gif'  alt='Add to cart' title='Add to cart' >
                        </a></div></div>";
                    }
            ?>
            </div>
        </div>
        <!-- end dynamic content -->

        <?php include 'footer.php';?>
    </div>

    <!-- lightbox -->
    <script src="node_modules/jquery/dist/jquery.js"></script>
    <script src="node_modules/baguettebox.js/dist/baguetteBox.js"></script>
    <script src="js/lightbox.js"></script>

    <!-- Sample site uses a MasterPage-like template for page layout. -->
    <!-- This is not required. It may be used as an enhancement. -->
    <!-- Source: http://spinningtheweb.blogspot.com/2006/07/approximating-master-pages-in-php.html -->
</body>

</html>