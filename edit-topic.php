<?php
include('utils/header.php');
if(!empty($_GET['id'])){
    $id = $_GET['id'];

    // Get namespace and identifier the from nsid in URL
    $namespace = $db->getNamespaceAndIdentifier($id)['namespace'];
    $identifier = $db->getNamespaceAndIdentifier($id)['identifier'];

    // Get topic data from the database
    $topicData = $db->getTopicData($id);
    $topicData = mysqli_fetch_assoc($topicData);

    // If topic data found it's edit mode else create mode
    $editmode = 1;
    if(empty($topicData))
        $editmode = 0;
?>
    <div class="page-subtitle" style="display:block;">
        <?php

    // Displaying beautiful headline
    if($editmode)
    {
        $displayName = $topicData['display_name'];
        echo "You're here to edit the topic \"".$displayName."\".";
    }
    else
    {
        $displayName = $identifier;
        echo "You're here to create a topic \"".$displayName."\".";
    }
    ?>
    </div>
    <?php

    // Store given nsid in temporary variable.
    $temp = $id;
    $tempAdder = "";

    // Loop for creating buttons for visiting heirarchical parents of this topic which we're editing.
    while(strlen($temp)!=0){
        // Find position of first colon
        $firstColonPosition = strpos($temp, ':');
        // If no colon exists, nsid is identifier
        if($firstColonPosition == FALSE)
            break; // Break exit, we found identifier

        // If we collected some words already, add colon
        if(strlen($tempAdder)>0)
            $tempAdder .= ':';

        // Get first "word" from nsid (from 0 to first colon)
        $tempAdder = $tempAdder.substr($temp, 0, $firstColonPosition);

        // Trim the temp nsid to remove first word
        $temp = substr($temp, $firstColonPosition+1, strlen($temp));

        // Add a button for visiting the topic
        echo "<a class='button' href='topic.php?id=".$tempAdder."'>".$db->getTopicDisplayName($tempAdder)."</a>&rarr;";
    }
?>
        <div class="margined">
            <form action="process-add-topic.php" method="post">
                <input type="hidden" name="edit-mode" value="<?php echo $editmode; ?>">
                <input type="hidden" name="namespace" value="<?php echo $namespace; ?>">
                <input type="hidden" name="identifier" value="<?php echo $identifier; ?>">

                <i>Name</i>
                <input type="text" class="margined" name="name" value="<?php echo $topicData['display_name']; ?>"><br>
                <i>About</i>
                <input type="text" class="margined" name="about" value="<?php echo $topicData['about']; ?>"><br>
                <div class="margined">
                    <textarea name="explanation" id="explanation">
                <?php echo $topicData['body']; ?>
            </textarea>
                </div>
                <!--        DELETE THIS TOPIC button-->
                <a class="button" style="background-color: #f44336;color:white; border-color:black;" href="process-delete-topic.php?id=<?php echo $id; ?>">Delete This Topic</a> &#8226;
                <!--    Create a button for visiting this topic's reading page.-->
                <a class='button' href='topic.php?id=<?php echo $id; ?>'>Cancel Editing</a>
                <!--        Print Dot-->
                &#8226;
                <input class="button" style="background-color: #4CAF50; color:white; border-color:black;" type="submit" name="submit" value="Save">
            </form>
        </div>
        </body>
        <script>
        CKEDITOR.replace('explanation');
            CKEDITOR.config.extraPlugins = 'eqneditor';
            CKEDITOR.config.width = '90%';
            CKEDITOR.config.height = '40%';

        </script>

        </html>
        <?php
} else{
    include("utils/ascii.php");
}
?>
