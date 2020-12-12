<?php
include_once("databaseConnection.php");
$link = fConnectToDatabase();

//Shopping cart uses cookies to store cart items.
//PHP script uses an array for adding, removing and displaying the cart items.
//Cookies can contain only string data so array must be serialized.

$cookieName = "myCart2";
// retrieve cookie and unserialize into $bookArray
if (isset($_COOKIE[$cookieName])) {
   $bookArray = unserialize($_COOKIE[$cookieName]);
}
// Add items to cart
$addISBN = fCleanString($link, $_GET['addISBN'], 10);
if (strlen($addISBN) > 0) {
   if (isset($addISBN, $bookArray)) {
      // Increment by +1
      $bookArray[$addISBN] += 1;
   } else {
      // Add new item to cart
      $bookArray[$addISBN] = 1;
   }
}
// Remove items from cart
$deleteISBN = fCleanString($link, $_GET['deleteISBN'], 10);
if (strlen($deleteISBN) > 0) {
   if (isset($bookArray[$deleteISBN])) {
      // Deincrement by 1
      $bookArray[$deleteISBN] -= 1;
      // remove ISBN from array if qty==0
      if ($bookArray[$deleteISBN] == 0) {
         unset($bookArray[$deleteISBN]);
      }
   }
}   

$totalbooks = 0;
if (isset($bookArray)) {
   // Write cookie
   setcookie($cookieName, serialize($bookArray), time() + 60 * 60 * 24 * 180);

   //Count total books in cart
   foreach ($bookArray as $isbn => $qty) {
      $totalbooks += $qty;
   }
   setCookie('BookCount', $totalbooks, time() + 60 * 60 * 24 * 180);
}
//***************************************************
//You do not need to modify any code above this point
//***************************************************
?>
<!DOCTYPE html>
<html>

<head>
    <title>Basic Shopping Cart -- GeekBooks.com</title>
    <link rel="stylesheet" href="/sandvig/mis314/assignments/bookstore/styleSheet.css" type="text/css">
</head>

<body>

    <?php
      include_once("header.php");
      ?>

    <div id="pageContainer">
        <div id="leftColumn">
            <?php include "menu.php" ?>
        </div>
        <div id="pageContent">
            <p class="centeredText">
                <?php
               echo $totalbooks . " item";
               if ($totalbooks != 1)
                  echo 's';
               echo ' in your cart'
               ?>
            </p>

            <?php
               //To do:
               // 1. Build sql statement containing ISBNs. Use foreach loop.
               // 2. Execute sql and display book titles, prices, qty, etc.
               if (isset($bookArray) && count($bookArray) > 0) {

                  $sql = 'SELECT ISBN, title, price  
                  FROM bookdescriptions
                  where ';

                  foreach ($bookArray as $isbn => $qty) {
                     $sql .= " isbn = '$isbn' or ";
                     
                  }
                  $sql = substr($sql, 0, strlen($sql) - 3);

                  //echo "sql: $sql <br>";

                  //$result is an array containing query results
                  $result = mysqli_query($link, $sql)
                  or die('SQL syntax error: ' . mysqli_error($link));

                  $subtotal = 0;
                  echo "<table id='cart'>"
                  . "<tr><th>Title</th>
                     <th>Qty</th>
                     <th>Price</th>
                     <th>Total</th>
                     <th></th></tr>"; 
                  while ($row = mysqli_fetch_array($result)) {
                        //Field names are case sensitive and must match
                        //the case used in sql statement
                        $isbn = $row['ISBN'];
                        $price = $row['price'];
                        $qty = $bookArray[$isbn];
                        $total = $qty * $price;
                        $subtotal += $total;
                        echo "
                        <tr>
                        <td>
                           <a class='booktitle' href='ProductPage.php?isbn=$isbn'>$row[title]</a> </td>
                        <td>$qty</td>
                        <td>$" . number_format($price, 2) . "</td>
                        <td>$" . number_format($total, 2) . "</td>
                        <td>
                           <a href='?addISBN=$isbn'>Add</a><br>
                           <a href='?deleteISBN=$isbn'>Remove</a>
                        </td>
                     </tr>";
               }
               echo"</table>";
               $baseshipping = 3.49;
               $unitshipping = .99;
               $shipping = $baseshipping + (($totalbooks-1) * $unitshipping);
   
               echo " <br />

               <table class='cartTotal'>
                 <tr>
                   <td> Sub-Total:</td>
                   <td align='right'>$" . number_format($subtotal, 2) . "</td>
                 </tr>
                     <tr>
                   <td> Shipping:*</td>
                   <td align='right'>$" . number_format($shipping, 2) . "</td>
                 </tr>
                 <tr>
                   <td><b>Total:</b></td>
                   <td align='right'><b>$" . number_format($subtotal + $shipping, 2) . "</b></td>
                 </tr>
               </table>
       
           
               
       ";
            }
               ?>

            <div class='cartIcons'>
                   <a href='index.php'> <img border='0' src='/sandvig/mis314/assignments/bookstore/images/continue-shopping.gif' width='121' height='19' alt='Continue shopping' /></a>&nbsp;&nbsp;&nbsp;&nbsp;
                   <a href='checkout01.php'> <img border='0' src='/sandvig/mis314/assignments/bookstore/images/proceed-to-checkout.gif' width='183' height='31' alt='Proceed to checkout'  ></a>
                  </div>
       
       
               <p id='shipping'>* Shipping is $3.49 for the first book and $.99 for each additional book. To assure
               reliable delivery and to keep your costs low we send all books via UPS ground. </p>
        </div>


        <?php include "footer.php"; ?>
    </div>