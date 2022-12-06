<?php
include("utils/header.php");
?>
<div class='post'>
  <h2>Remarks:</h2><br>
  <i style="font-family:monospace;">
    We must fill those:</h2><br>
  </i><br><br>
  <?php
  $mySchedule = array(
    // JOBNAME, LASTDATE, REMARKS
    array("PATENT OFFICER", "6 August-4 September", "Prelims is more important, Do test series daily." ),
    array("KVS", "somedate", "someremarks" ),
    array("HTET", "somedate", "someremarks" ),
    array("SBI SO", "somedate", "someremarks" ),
    array("IBPS SO", "somedate", "someremarks" ),
    array("NIELIT", "somedate", "someremarks" ),

  );
  ?>
  <table>
    <tr>
      <th>JOBNAME</th>
      <th>LASTDATE</th>
      <th>REMARKS</th>
    </tr>
    <?php
    foreach($mySchedule as $oneRow){
      echo "<tr>";
      foreach($oneRow as $onecell){
        echo "<td><b><i>$onecell</i></b></td>";
      }
      echo "<tr>";
    }
    ?>
  </table>
</div>
</body>
</html>
