<?php
include('utils/header.php');
// DEBUG
if(empty($_GET['id']))
$_GET['id'] = 12;
if(!empty($_GET['id'])){
  $id = $_GET['id'];
  $questionDetails = $db->getQuestionDetails($id);
  $questionDetails = mysqli_fetch_assoc($questionDetails);
  if(empty($questionDetails)){
    include("utils/ascii.php");
    exit;
  }
  ?>


  <!--        Static text-->
  <?php if(strlen($questionDetails['tags'])!=0){ ?>
    <span class="text-secondary"><i>A question related to <?php echo $wikifilter->bracketsToLinks($questionDetails['tags'], $db); ?></i></span>
  <?php } else {?>
    <span class="text-secondary"><i>This question is not tagged.</i></span>
  <?php } ?>
  <!--        Display Dot-->
  &#8226;

  <!--        "Edit the question" button-->
  <a class="button" href="edit-question.php?id=<?php echo $questionDetails['_id']; ?>">Edit This Question</a>

  <br>
  <!--        The Question Text-->
  <div class="question">
    <?php echo $questionDetails['question'] ?>
  </div>

  <br>

  <!--        The Answer Button-->
  <span class="button text-secondary">ANSWER</span>

  <!--        The Answer Itself-->
  <span class="text-secondary" style="color:black"><?php echo $questionDetails['answer'] ?></span>

  <br>

  <!--        Display explanation -->
  <?php if(strlen(trim($questionDetails['explanation']))>0){
    ?>
    <div class="explain-area">
      <?php echo $questionDetails['explanation']; ?>
    </div>
  <?php }

  //    Load Related Questions
  $result = $db->getRelatedQuestions($questionDetails['_id']);
  if(mysqli_num_rows($result)>0){
    echo "<span class='heading'>Related Questions:</span><br>";
    while($row = mysqli_fetch_assoc($result)){
      echo "<a class='button' href='topic.php?id=".$row['_id']."'>".$row['name']."</a><br>";
    }
  } else {
    // Do something when no related questions are found.
  }

  //    If this question type is common, load common explanation
  if($questionDetails['question_type_id'] != null && $questionDetails['question_type_id']!=0){
    ?>
    <div class="heading">Method:</div><br>
    <a href="question-type.php?id=<?php echo $questionDetails['question_type_id']; ?>" class="button"><?php echo $db->getQuestionTypeName($questionDetails['question_type_id']); ?></a></body>
    <?php
  }
} else {
  include("utils/ascii.php");
}
?>
</html>
