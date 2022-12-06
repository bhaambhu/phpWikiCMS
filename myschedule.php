<?php
include("utils/header.php");
?>
<div class='post'>
  <h2>Ongoing</h2><br>
  <i style="font-family:monospace;">
    Maths : Predicate Logic : Bring notebooks and stuff for predicate logic.
  </i><br><br>
  <h2>My Schedule</h2><br>
  <?php
  $mySchedule = array(
    // SUBJECT, READING, COSEN-ENTRY, Exercises, Previous Year, 1=DOING, 2=DONE
    array("C Programming", 2, 1, 1, 1),
    array("Algorithms", 2, 0, 2, 1),
    array("Data Structures", 0,0,0,0),
    array("Computer Networks", 0,0,0,0),
    array("Computer Organization", 0,0,0,0),
    array("Digital Logic", 0,0,0,0),
    array("Operating Systems", 2, 0, 0, 0),
    array("Theory of Computation", 0,0,0,0),
    array("Compiler Design", 0,0,0,0),
    array("Discrete Maths", 0,0,0,0),
    array("DBMS", 0,0,0,0)
  );
  ?>
  <table>
    <tr>
      <th>Subject</th>
      <th>Reading</th>
      <th>COSEN Entry</th>
      <th>Exercises</th>
      <th>Previous Year</th>
    </tr>
    <?php
    foreach($mySchedule as $oneRow){
      echo "<tr>";
      foreach($oneRow as $onecell){
        if(strlen($onecell)>1) // Means it's not a number
        echo "<td><b><i>".$onecell."</b></i></td>";
        else{ // means it's a number
          if($onecell==2) // means completed
          echo "<td bgcolor='#000000' style='color:#FFFF00'><b><i>COMPLETED!</i></b></td>";
          else if ($onecell == 0) // means not done yet
          echo "<td bgcolor='#000000' style='color:#FF0000'><b><i>INCOMPLETE!</i></b></td>";
          else if ($onecell == 1) // means not done yet
          echo "<td bgcolor='#000000' style='color:#00FF00'><b><i>IN PROGRESS...</i></b></td>";
        }
      }
      echo "<tr>";
    }
    ?>
  </table>
</div>

</body>
</html>
