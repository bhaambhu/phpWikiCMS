<?php
if(!empty($_GET['id'])){
    $id = $_GET['id'];
    include("utils/db-functions.php");
    $db = new DB_FUNCTIONS();
	$questionTypeDetails = $db->getQuestionTypeDetails($id);
	$questionTypeDetails = mysqli_fetch_assoc($questionTypeDetails);
	$subject_id = $questionTypeDetails['subject_id'];
?>
<html>
    <head>
        <link href="styles/styles.css" type="text/css" rel="stylesheet">
        <script src="ckeditor/ckeditor.js"></script>
    </head>
    <body>
        <h1 class="text-primary">Edit Question Type</h1>
        <span class="text-secondary">Edit a question type in <?php echo $db->getSubjectName($subject_id); ?>.</span>
        <hr class="divider">
        <div class="margined">
            <form action="process-add-question-type.php" method="post">
            	<input type="hidden" name="edit-mode" value="1">
                <input type="hidden" name="_id" value="<?php echo $id; ?>">
                <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                <input class="margined" type="submit" name="submit" value="Save"><br>
                <input type="text" class="margined" name="question_type" value="<?php echo $questionTypeDetails['question_type']; ?>"><br>
                <span class="margined">
                How To Solve:
                </span>
                <div class="margined">
                    <textarea name="method" id="explanation">
                    <?php echo $questionTypeDetails['method']; ?>
                    </textarea>
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