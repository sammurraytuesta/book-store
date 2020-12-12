<!--Begin menu include -->

<div class="menuContainer">
    <div class="menuSearch">
        <div class="menuHead">
            Search
        </div>

        <div class="menuBorder">
            <form action="searchbrowse.php">
                <input type="text" name="search" autofocus />
                <input type="submit" value="Search" class="button fullWidth" />
            </form>
        </div>

        <?php ?>
    </div>

    <nav class="IsDesktop">
        <div class="menuHead">
            Browse
        </div>

        <div class="menuBorder">
            <?php
                //List records
                $sql = 'SELECT DISTINCT c.CategoryName, c.CategoryID 
                FROM bookcategories c, bookcategoriesbooks cb 
                WHERE c.CategoryID = cb.CategoryID 
                order by c.CategoryName';

                //$result is an array containing query results
                $result = mysqli_query($link, $sql)
                    or die('SQL syntax error: ' . mysqli_error($link));

                    while ($row = mysqli_fetch_array($result)) {
                    $CategoryName = $row['CategoryName'];
                    $CategoryID = $row['CategoryID'];
                    echo "<a class='menuLink' href='searchbrowse.php?CategoryID=$CategoryID'>$CategoryName</a><br>";
                };
                ?>
        </div>

    </nav>
</div>

<!--End menu include -->