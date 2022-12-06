<?php
if(!empty($_GET['subject_id'])){
    $subject_id = $_GET['subject_id'];
    include("utils/db-functions.php");
    $db = new DB_FUNCTIONS();
    $arr = $db->getChildTreeOfSubject($subject_id);
?>
<html>
    <head>
        <link href="styles/styles.css" type="text/css" rel="stylesheet">
        <script src="ckeditor/ckeditor.js"></script>
    </head>
    <body>
        <h1 class="text-primary">Add Question Type</h1>
        <span class="text-secondary">Add a question in <?php echo $db->getSubjectName($subject_id); ?>.</span>
        <hr class="divider">
        <div class="margined">
            <form action="process-add-question-type.php" method="post">
                <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                <input class="margined" type="submit" name="submit" value="Add"><br>
                <input type="text" class="margined" name="question_type" placeholder="Question Type"><br>
                <span class="margined">
                How To Solve:
                </span>
                <div class="margined">
                    <textarea name="method" id="explanation"></textarea>
                </div>
            </form>    
        </div>
    </body>
    <script>
        CKEDITOR.replace("explanation");
        CKEDITOR.config.width = '90%';
        CKEDITOR.config.height = '40%';
    </script>
    <script>
    function myFunction() {
        var tag = document.getElementById("tag");
        var tagClone = tag.cloneNode(false);
        var selectBox = document.getElementById("topicSelector");
        var currentID = selectBox.options[selectBox.selectedIndex].value;        
        var currName = selectBox.options[selectBox.selectedIndex].text;
        currName = currName.replace(/^-*/, '');
        tagClone.textContent = currName;
        
        var hiddenIDField = document.getElementById("hiddenIDField");
        var iDFieldClone = hiddenIDField.cloneNode(false);
        iDFieldClone.value = currentID;
        iDFieldClone.name = "topics[]";
        tagClone.appendChild(iDFieldClone);        document.getElementById("tags").appendChild(tagClone);
    }
    </script>

</html>
<?php
} else{
    include("utils/ascii.php");
}
?>