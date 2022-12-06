<html>
    <head>
        <link href="styles/styles.css" type="text/css" rel="stylesheet">
    </head>
    <body>
        <h1 class="text-primary">Subjects</h1>
        <span class="text-secondary">All the subjects.</span>
        <hr class="divider" />
        <?php
        include("utils/db-functions.php");
        $db = new DB_FUNCTIONS();
        $result = $db->getSubjectsAll();
        while($row = mysqli_fetch_assoc($result)){
            echo "<a class='button' href='subject.php?id=".$row['_id']."'>".$row['name']."</a>";
        }
        ?>
    </body>
</html>