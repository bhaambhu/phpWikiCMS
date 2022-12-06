<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
class DB_Functions{

  public $link;
  public $workingArray = [];

  // Constructor.
  function __construct(){
    include('config.php');
    include('security.php');
    $this->link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    mysqli_set_charset($this->link, "utf8");
  }

  // Destructor.
  function __destruct(){

  }
  public function getStartingTopicIdentifier(){
    $query = "SELECT value FROM table_conf where field='starting_topic'";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $identifier = $result['value'];
    return $identifier;
  }
}
?>
