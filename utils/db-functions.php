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
  public function getNamespaceAndIdentifier($namespaceWithIdentifier)
  {
    // Clean up the given namespace
    $namespaceWithIdentifier = $this->trimColons($namespaceWithIdentifier);
    $nsid_arr = array("namespace"=>"", "identifier"=>"");
    $last_colon_position = strrpos($namespaceWithIdentifier, ':');
    if($last_colon_position)
    {
      $namespace = substr($namespaceWithIdentifier, 0, $last_colon_position);
      $identifier = substr($namespaceWithIdentifier, $last_colon_position+1, strlen($namespaceWithIdentifier));
    } else {
      $namespace = "";
      if($namespaceWithIdentifier[0]==':')
      $identifier = substr($namespaceWithIdentifier, 1, strlen($namespaceWithIdentifier));
      else
      $identifier = $namespaceWithIdentifier;
    }
    $nsid_arr['namespace'] = $namespace;
    $nsid_arr['identifier'] = $identifier;
    return $nsid_arr;
  }
  public function TopicExists($namespaceWithIdentifier){
    $nsid_arr = $this->getNamespaceAndIdentifier($namespaceWithIdentifier);
    $namespace = $nsid_arr['namespace'];
    $identifier = $nsid_arr['identifier'];
    $query = "SELECT identifier FROM table_topics where identifier='$identifier' AND namespace='$namespace'";
    $result = mysqli_query($this->link, $query);
    if(mysqli_num_rows($result)>0){
      return true;
    } else
    return false;
  }

  public function TopicExists2($namespace, $identifier){
    $query = "SELECT identifier FROM table_topics where identifier='$identifier' AND namespace='$namespace'";
    $result = mysqli_query($this->link, $query);
    if(mysqli_num_rows($result)>0){
      return true;
    } else
    return false;
  }
  // Returns display_name if available, else returns identifier
  public function getTopicDisplayName($completenamespace){
    $nsid_arr = $this->getNamespaceAndIdentifier($completenamespace);
    $id = $nsid_arr['identifier'];
    $ns = $nsid_arr['namespace'];
    $query = "SELECT display_name FROM table_topics where identifier='$id' AND namespace='$ns'";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $name = $result['display_name'];
    if(strlen(trim($name))==0) return $id;
    return $name;
  }
  // Returns a mysqli_result object;
  public function getTopicData($namespaceWithIdentifier){
    $nsid_arr = $this->getNamespaceAndIdentifier($namespaceWithIdentifier);
    $query = "SELECT * FROM table_topics where identifier='".$nsid_arr['identifier']."' AND namespace='".$nsid_arr['namespace']."'";
    $result = mysqli_query($this->link, $query);
    return $result;
  }
  public function getStartingTopicIdentifier(){
    $query = "SELECT value FROM table_conf where field='starting_topic'";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $identifier = $result['value'];
    return $identifier;
  }
  public function trimColons($input){
    // Remove starting and ending colons
    while($input[0] == ':')
    {
      $input = substr($input, 1, strlen($input));
    }
    while(strrpos($input, ":") == (strlen($input)-1))
    {
      $input = substr($input, 0, (strlen($input)-1));
    }
    return $input;
  }
}
?>
