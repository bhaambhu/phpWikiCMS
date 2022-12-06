<?php

// COSEN: Heavy modifications done to this file
if(!empty($_POST['question'])){

    // Set variables received from source.
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $question_type = $_POST['question_type'];
    $tags = $_POST['tags'];
    $exams = $_POST['exams'];
    $explanation = $_POST['explanation'];

    include("utils/db-functions.php");
    $db = new DB_FUNCTIONS();

    if(isset($_POST['edit-mode'])){

        // This means a question is being edited.
        // So we already have an ID.
        $id = $_POST['_id'];

        // Redirect to this page after success.
        $redirect_to = "question.php?id=".$id;

        $result = $db->editQuestion($id, $question, $answer, $question_type, $tags, $exams, $explanation);
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

    } else { // Means we are adding a new question

        // Redirect to this page after success.
        $redirect_to = "question.php?id=";

        $result = $db->addQuestion($question, $answer, $question_type, $tags, $exams, $explanation);
        if($result){
            // Redirect to newly added question.
            $redirect_to.=$result;
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
