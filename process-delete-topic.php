<?php
if(!empty($_GET['id'])){
    
    // Set variables received from source.
    $id = $_GET['id'];
    
    include("utils/db-functions.php");
    $db = new DB_FUNCTIONS();
    
    $result = $db->getTopicDetails($id);
    $topicDetails = mysqli_fetch_assoc($result);
    // Redirect to this page after success.
    $redirect_to = "subject.php?id=".$topicDetails['subject_id'];
    if($topicDetails['child_of'] > 0){
        $redirect_to = "topic.php?id=".$topicDetails['child_of'];
    }
    
    $result = $db->deleteTopic($id);
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
    include("utils/ascii.php");
}
?>