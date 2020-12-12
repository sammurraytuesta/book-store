<?php
   /*List Authors Function:
    This function uses ISBN as an input parameter and returns a string of author names formatted as
    hyperlinks. To use the function:
    1. Copy this code into a file in your bookstore directory named "ListAuthors.php".
    2. Include this file in your page using: include_once("ListAuthors.php")
    3. To list authors, call the function and pass in the ISBN of the book using:
       echo ListAuthors($ISBN);
       where $ISBN is a variable containing the book's ISBN.
    This function requires that you have an open database connection to your database.
    You are welcome to modify this script.
   */
   function fListAuthors($link, $ISBN) {

        $sql = "SELECT nameF, nameL
                FROM bookauthors, bookauthorsbooks
                WHERE bookauthorsbooks.ISBN = '$ISBN'
                AND bookauthors.AuthorID = bookauthorsbooks.AuthorID
                ORDER BY nameL";

        $result = $result = mysqli_query($link, $sql)
              or die('SQL syntax error: ' . mysqli_error($link));

        while ($row = mysqli_fetch_array($result)) {
            $nameF = $row['nameF'];
            $nameL = $row['nameL'];
            $AuthorList .= "<a href='SearchBrowse.php?search=".
                           "$nameL'>$nameF $nameL</a>, ";
}

        //remove the last comma
        return substr_replace($AuthorList, "",-2);
   }
?>