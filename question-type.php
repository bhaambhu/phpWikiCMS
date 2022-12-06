<?php
include("utils/db-functions.php");
$db = new DB_FUNCTIONS();
if(!empty($_GET['id'])){
  $id = $_GET['id'];
  $questionTypeDetails = $db->getQuestionTypeDetails($id);
  $questionTypeDetails = mysqli_fetch_assoc($questionTypeDetails);
  if(empty($questionTypeDetails)){
    include("utils/ascii.php");
    exit;
  }
  ?>
  <html>
  <head>
    <link href="styles/styles.css" type="text/css" rel="stylesheet">
  </head>
  <body>
    <h1 class="text-primary"><?php echo $questionTypeDetails['question_type'] ?></h1>
    <div class="margined text-secondary">
      <?php
      echo "in ";
      echo "<a class='button' href='subject.php?id=".$questionTypeDetails['subject_id']."'>".$db->getSubjectName($questionTypeDetails['subject_id'])."</a>";
      ?>
      &#8226;
      <a class="button" href="edit-question-type.php?id=<?php echo $questionTypeDetails['_id']; ?>">Edit This Question Type</a>
    </div>
    <hr class="divider" />
    <?php if(strlen(trim($questionTypeDetails['method']))>0){ ?>
      <div class="explain-area">
        <?php echo $questionTypeDetails['method']; ?>
      </div>
    <?php } ?>
    <?php
    $result = $db->getQuestionsByType($questionTypeDetails['_id']);
    if(mysqli_num_rows($result)>0){
      echo "<span class='heading'>Related Questions:</span><br>";
      while($row = mysqli_fetch_assoc($result)){
        echo "<a class='button' href='question.php?id=".$row['_id']."'>".$row['question']."</a><br>";
      }
    } else {
      // Do something when no related questions are found.
    }
    ?>
  </body>
  </html>
  <?php
} else {
  include("utils/ascii.php");
}
?>
