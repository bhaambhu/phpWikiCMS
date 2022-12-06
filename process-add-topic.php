<?php
if(!empty($_POST['identifier'])){
    // Set variables received from source.
    $display_name = $_POST['name'];
    $about = $_POST['about'];
    $body = $_POST['explanation'];

    include("utils/db-functions.php");
    $db = new DB_FUNCTIONS();
    $identifier = $_POST['identifier'];
    $namespace = $_POST['namespace'];
    if($_POST['edit-mode'] == 1){
        // This means a topic was edited.
        // So we already have an ID.
        // Redirect to this page after success.
        $redirect_to = "topic.php?id=".$namespace.":".$identifier;

        $result = $db->editTopic($identifier, $display_name, $about, $namespace, $body);
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
        $redirect_to = "topic.php?id=".$namespace.":".$identifier;
        $result = $db->addTopic($identifier, $display_name, $about, $namespace, $body);
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
