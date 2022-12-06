<?php
include("utils/header.php");
include("title-bar.php");
// For debugging this page
if(empty($_GET['id'])) // If no url supplied, open home page.
$_GET['id'] = $db->getStartingTopicIdentifier();

// Get identifier from url
if(!empty($_GET['id'])){
  $nsid = $_GET['id'];

  // Remove starting and ending colons
  $nsid = $db->trimColons($nsid);


  // Get topic data using given namespace
  $topicData = $db->getTopicData($nsid);
  $topicData = mysqli_fetch_assoc($topicData);
  $nsid_arr = $db->getNamespaceAndIdentifier($nsid);
  // If topic empty
  if(empty($topicData)){
    $displayName = $nsid_arr['identifier'];
  }
  else $displayName = $topicData['display_name'];

  // Handle Trace
  session_start();
  $trace_limit = 6;
  if(!empty($_SESSION['trace_ns'])) {
    if(end($_SESSION['trace_ns']) !== $nsid)
    {
      if(array_push($_SESSION['trace_ns'], $nsid) > $trace_limit) array_shift($_SESSION['trace_ns']);
    }
  }else {
    $_SESSION['trace_ns'][] = $nsid;
  }

    echo "<div class='trace-section'>Trace: ";
    // Print Trace
    foreach($_SESSION['trace_ns'] as $key=>$value)
    {
      if($key>0) echo ">>";
      echo "<a class='trace-section-button' href='topic.php?id=".$value."'>".$db->getTopicDisplayName($value)."</a>";
    }
    echo "</div>";
    echo "<hr class='divider'/>";
    ?>

  <div class="buttons-section">
    <?php
    // Print Topic Related Buttons
    $temp = $nsid;
    $tempAdder = "";
    while(strlen($temp)!=0){
      $firstColonPosition = strpos($temp, ':');
      if($firstColonPosition == FALSE)
      break;
      if(strlen($tempAdder)>0)
      $tempAdder .= ':';
      $tempAdder = $tempAdder.substr($temp, 0, $firstColonPosition);
      $temp = substr($temp, $firstColonPosition+1, strlen($temp));
      echo "<a class='buttons-section-button' href='topic.php?id=".$tempAdder."'>".$db->getTopicDisplayName($tempAdder)."</a>&rarr;";
    }
    echo "<a class='buttons-section-button' href='topic.php?id=$nsid'>".$displayName."</a>";
    ?>

    &#8226;
    <!--            The Edit this topic button-->
    <a class="edit-topic-button" href="edit-topic.php?id=<?php echo $nsid; ?>">Edit This Topic</a>

    &#8226;
    <!--            All Topics button-->
    <a class="all-topics-button" href="treeview.php">All Topics</a>

    &#8226;
    <!--            My Schedule button-->
    <a class="buttons-section-button" href="myschedule.php">My Schedule</a>

    &#8226;
    <!--            Opportunities button-->
    <a class="buttons-section-button" href="opportunities.php">Opportunities</a>
  </div>
  <hr class="divider" />
<div class="topic-body-section">
  <div class="post">
    <h1 class="page-title"><?php
    echo $displayName;
    ?></h1>
    <span class="page-subtitle"><?php echo $topicData['about']; ?></span><br>
  </div>
  <div class="post">
    <?php
    if(empty($topicData))
    echo "This topic is empty.";
    echo $wikifilter->bracketsToLinks($topicData['body'], $db);
    ?>
  </div>
</div>

  <!-- COSEN: Related topics section removed -->
  <?php

  // Show questions related to this topic
  $result = $db->getQuestionsByTopic($nsid);
  if(mysqli_num_rows($result)>0){
    echo "<span class='button'>Questions on ".$displayName.":</span><br>";
    while($row = mysqli_fetch_assoc($result)){
      ?>
      <a class='button' href='question.php?id=".$row['_id']."'><?php echo $row['question']?></a><br>
      <?php
    }
  } else {
    // Do something when no child topics are found.
  }
  ?>
</body>
</html>
<?php
} else {
  include("utils/ascii.php");
}
?>
