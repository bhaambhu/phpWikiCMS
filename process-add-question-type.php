<?php
if(!empty($_POST['subject_id']) && !empty($_POST['question_type'])){
    
    // Set variables received from source.
    $question_type = $_POST['question_type'];
    $method = $_POST['method'];
    $subject_id = $_POST['subject_id'];
    
    include("utils/db-functions.php");
    $db = new DB_FUNCTIONS();
    
    if(isset($_POST['edit-mode'])){
        // This means a topic was edited.
        // So we already have an ID.
        $id = $_POST['_id'];
        // Redirect to this page after success.
        $redirect_to = "subject.php?id=".$subject_id;
        
        $result = $db->editQuestionType($id, $subject_id, $question_type, $method);
		if($result){
            // Redirect with some positive response.
            header("Location: $redirect_to");
        } else {
            // Redirect with some error response.
            echo "Error occurred!";
            echo mysqli_error($db->link);
            exit();
            header("Location: $redirect_to");
        }
        
    } else {

        // Redirect to this page after success.
        $redirect_to = "subject.php?id=".$subject_id;
        
        $result = $db->addQuestionType($subject_id, $question_type, $method);
        if($result){
            // Redirect with some positive response.
            header("Location: $redirect_to");
        } else {
            // Redirect with some error response.
            echo "Error occurred!";
            echo mysqli_error($db->link);
            exit();
            header("Location: $redirect_to");
        }
        
    }
    
} else {
    include("utils/ascii.php");
}
?>