<?php
include('utils/header.php');
// This php file gets nsid from URL and works on that
if(empty($_GET['id'])){
  $nsid = $db->getStartingTopicIdentifier();
} else {
  $nsid = $_GET['id'];
}
  // The question will save it in the tags field.
  ?>
  <!--    Beautiful Heading-->
  <div class="text-secondary" style="display:block;">
    You're here to add a new question:
  </div>

  <div class="margined">

    <div class="margined">

      <!-- Form for adding question -->
      <form action="process-add-question.php" method="post">

        <!--Fill the Question edit box-->
        <div class="margined">
          <textarea name="question" id="question" required>
          </textarea>
        </div><br>

        <!--The Answer edit box-->
        <i class="button">Answer:</i>
        <input type="text" class="margined" name="answer" required>
        <br>

        <!--            Question Type edit box-->
        <!--            COSEN: Code Changed for question type, to support namespaces-->
        <i class="button">Question Type:</i> <input type="text" class="margined" name="question_type"><br>


        <!--            Tags edit box-->
        <!--Topics are now entered as tags-->
        <!--            COSEN: Code Changed for adding tags, tags are now just namespace links.-->
        <script src="scripts/question-tags.js"></script>
        <i class="button">Wiki Tags: eg [[programming:datatypes|Data Types]], ...</i>
        <div class="button">
        <select id="topicSelector" class="margined">
            <?php
            $largeAssArray = $db->getTopicTree("", $db, true);
            $ns_array = $largeAssArray['namespacearray'];
            $display_array = $largeAssArray['displayarray'];
            foreach(array_keys($ns_array) as $key){
              echo "<option value='".$ns_array[$key]."'>   ".$display_array[$key]."</option>";
            }
            ?>
        </select>
        <button type="button" onclick="addTag()">Add Tag</button>
      </div><br>
        <textarea name="tags" class="margined" style="width:100%" id="tags">[[<?php
          echo $nsid;
          echo "|";
          $displayName = $db->getTopicDisplayName($nsid);
          echo $displayName;
          ?>]]</textarea><br>

        <!-- Exam Tags -->
        <i class="button">Exam Tags: eg NETJUNE2012, GATE2012, ...</i><br> <textarea name="exams" class="margined" id="exams">
        </textarea><br>

        <!--            COSEN: removed code for selecting difficulty-->

        <!--Explanation edit box-->
        <i class="button">Explanation:</i>
        <div class="margined">
          <textarea name="explanation" id="explanation">
          </textarea>
        </div>
        <input class="margined" type="submit" name="submit" value="Add"><br>
      </form>
    </div>
    <script>
    CKEDITOR.replace('question');
    CKEDITOR.replace('explanation');
    CKEDITOR.config.width = '90%';
    CKEDITOR.config.height = '40%';
    $("form").submit( function(e) {
      var messageLength = CKEDITOR.instances['question'].getData().replace(/<[^>]*>/gi, '').length;
      if( !messageLength ) {
        alert( 'Please enter a question atleast : |' );
        e.preventDefault();
      }
    });
  </script>
  </body>
</html>
