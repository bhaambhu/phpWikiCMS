<?php
include("utils/header.php");
echo "<div class='post'><pre>";
    echo $wikifilter->bracketsToLinks($db->getTopicTree("", $db), $db);
    //print_r($db->getTopicTree("", $db, true));
    echo "</pre></div>";
?>
