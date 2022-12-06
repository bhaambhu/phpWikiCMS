<?php
if(!empty($_GET['id'])){
    $subject_id = $_GET['id'];
    $child_of = 0;
    if(!empty($_GET['child_of'])){
        $child_of = $_GET['child_of'];
    }
?>
<html>
    <head>
        <link href="styles/styles.css" type="text/css" rel="stylesheet">
        <script src="ckeditor/ckeditor.js"></script>
    </head>
    <body>
        <h1 class="text-primary">Add Topic</h1>
        <span class="text-secondary">Add a topic in the database.</span>
        <hr class="divider">
        <div class="margined">
            <form action="process-add-topic.php" method="post">
                <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                <input type="hidden" name="child_of" value="<?php echo $child_of; ?>">
                <input class="margined" type="submit" name="submit" value="Add"><br>
                <input type="text" class="margined" name="name" placeholder="Title"><br>
                <input type="text" class="margined" name="about" placeholder="This topic is about..."><br>
                <input type="checkbox" name="lastminute"> Last Minute
                <input type="checkbox" name="cramme"> Cramme
                <div class="margined">
                    <textarea name="explanation" id="explanation"></textarea>
                </div>
            </form>    
        </div>
    </body>
    <script>
        CKEDITOR.replace("explanation");
        CKEDITOR.config.width = '90%';
        CKEDITOR.config.height = '40%';
    </script>
</html>
<?php
} else{
    include("utils/ascii.php");
}
?>