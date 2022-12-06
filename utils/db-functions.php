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
  public function getTreeForm($arr, $ns_dn_arr, $indent=0, $mother_run=true){
    if ($mother_run) {
      // the beginning of plotTree. We're at rootlevel
      //$this->workingArray[]="start\n";
      $this->workingArray = [];
    }

    foreach ($arr as $k=>$v){
      // skip the baseval thingy. Not a real node.
      if ($k == "__base_val") continue;
      // determine the real value of this node.
      $show_val = (is_array($v) ? $v['__base_val'] : $v);
      // show the indents
      $repStr = str_repeat("&nbsp;&nbsp;", $indent);
      $this->workingArray[]=$repStr;
      if ($indent == 0) {
        // this is a root node. no parents
        // COSEN : Dont add this node
        //$this->workingArray[]="+ ";
      } elseif (is_array($v)){
        // this is a normal node. parents and children
        $this->workingArray[]="+ ";
      } else {
        // this is a leaf node. no children
        $this->workingArray[]=">> ";
      }

      // show the actual node
      //echo $k . " (" . $show_val. ")" . "\n";
      $ns = $show_val;
      if(array_key_exists($ns, $ns_dn_arr))
      $display_name = $ns_dn_arr[$ns];
      else $display_name = $k;
      $show_val = substr($show_val, 6, strlen($show_val));
      if($display_name == "root")
      // COSEN : Remove this node lateron but we need to add now for sakeof the procedure:
      $this->workingArray[]="Root\n";
      else
      $this->workingArray[]="[[$show_val|$display_name]]\n";
      if (is_array($v)) {
        // this is what makes it recursive, rerun for childs
        $this->getTreeForm($v, $ns_dn_arr, ($indent+1), false);
      }
    }

    if ($mother_run) {
      //$this->workingArray[]="end\n";
      //return $db->$treeString;
      return $this->workingArray;
    }
  }
  public function explodeTree($array, $delimiter = '_', $baseval = false)
  {
    if(!is_array($array)) return false;
    $splitRE   = '/' . preg_quote($delimiter, '/') . '/';
    $returnArr = array();
    foreach ($array as $key => $val) {
      // Get parent parts and the current leaf
      $parts	= preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
      $leafPart = array_pop($parts);
      // Build parent structure
      // Might be slow for really deep and large structures
      $parentArr = &$returnArr;
      $partCollector = "";
      foreach ($parts as $part) {
        if (!isset($parentArr[$part])) {
          $parentArr[$part] = array();
        } elseif (!is_array($parentArr[$part])) {
          if ($baseval) {
            $parentArr[$part] = array('__base_val' => $parentArr[$part]);
          } else {
            $parentArr[$part] = array();
          }
        }
        $partCollector .=":".$part;
        $parentArr = &$parentArr[$part];
        $parentArr['__base_val'] = $partCollector;
      }

      // Add the final part to the structure
      if (empty($parentArr[$leafPart])) {
        $parentArr[$leafPart] = $val;
      } elseif ($baseval && is_array($parentArr[$leafPart])) {
        $parentArr[$leafPart]['__base_val'] = $val;
      }
    }
    return $returnArr;
  }
  public function getAllTopicNamespacesAndDisplayNames()
  {
    $query = "SELECT namespace, identifier, display_name FROM table_topics";
    $result = mysqli_query($this->link, $query);
    while($row = mysqli_fetch_assoc($result))
    {
      if(strlen(trim($row['namespace'])) == 0)
      $ns = "";
      else $ns = ":".$row['namespace'];
      $namespace_array[] = ":root".$ns.":".$row['identifier'];

      $display_name = $row['display_name'];
      if(strlen(trim($display_name))==0)
      $display_name = $row['identifier'];
      $display_name_array[] = $display_name;

    }
    return array($namespace_array, $display_name_array);
  }
  public function getTopicTree($root_namespace, $db, $getSeparateArrays = false){
    $ns_dn_arr = $db->getAllTopicNamespacesAndDisplayNames();

    // Prepare an array for our explodeTree() function to use
    $key_files = array_combine(array_values($ns_dn_arr[0]), array_values($ns_dn_arr[0]));

    // Prepare an array for getting display names from namespace to prevent multiple database accesses
    $ns_dn_arr_combined = array_combine(array_values($ns_dn_arr[0]), array_values($ns_dn_arr[1]));

    // Get a tree in the form of an array of arrays
    $tree = $db->explodeTree($key_files, ":", true);

    // Get tree-structure in form of text
    $tree_text_array = $db->getTreeForm($tree, $ns_dn_arr_combined);
    $tree_text = "";

    // Function by default returns a string of text (representing the tree) with newlines and stuff, but we can also configure it to return 2 arrays, one of namespaces and other of words (for showing them in topic selection options)
    $separateNamespaceArray = [];
    $separateDisplayArray = [];

    //Do we want separate arrays or do we want a single string
    if($getSeparateArrays){
      $lineString = "";
      foreach($tree_text_array as $oneWord){
        // If word contains newline, means after this word we insert a new element in array.
        if(strpos($oneWord, "\n")!==false){
          // Process this line before adding in array.
          // Find position of first double brackets
          $firstDoubleBrackets = strpos($oneWord, "[[");
          if($firstDoubleBrackets !== false){

            $danda = strpos($oneWord, "|");
            $lastClosingBrackets = strpos($oneWord, "]]");

            // In namespacearray, we gotta insert the link like [[abc:cde|CDE]]
            $ns = substr($oneWord, $firstDoubleBrackets, $lastClosingBrackets+2);
            // In display array we gotta change [[abc:cde|CDE]] to CDE
            $dn = substr($oneWord, $danda+1, $lastClosingBrackets); // The display name i.e. CDE
            $oneWord = str_replace($ns."\n", $dn, $oneWord);
            $oneWord = str_replace("]]\n", "", $oneWord);

          }else{
            $ns = "";
          }
          $lineString.=$oneWord;  // Insert word in line
          // Add things to arrays
          $separateNamespaceArray[] = $ns;
          $separateDisplayArray[] = $lineString;

          // Clear linestring to collect next line
          $lineString = "";

        }else{ // means still current row is being processed, keep populating the linestring
          $lineString.=$oneWord;
        }
      }
      // Remove first elements from arrays cuz it's the useless "ROOT" element.
      array_shift($separateNamespaceArray);
      array_shift($separateDisplayArray);
      return array('namespacearray'=>$separateNamespaceArray, 'displayarray'=>$separateDisplayArray);
    }else{
      foreach($tree_text_array as $oneWord){
        $tree_text.=$oneWord;
      }
      return $tree_text;
    }
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
