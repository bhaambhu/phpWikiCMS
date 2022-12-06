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

  // Returns a mysqli_result object.
  public function getSubjectsAll(){
    $query = "SELECT * FROM table_subjects";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // Returns a mysqli_result_object;
  public function getSubjectDetails($id){
    $query = "SELECT * FROM table_subjects where _id=$id";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // Returns a mysqli_result object.
  public function getExams(){
    $query = "SELECT * FROM table_exams";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  public function getSubjectName($id){
    $query = "SELECT name FROM table_subjects where _id=$id";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $name = $result['name'];
    return $name;
  }

  public function getStartingTopicIdentifier(){
    $query = "SELECT value FROM table_conf where field='starting_topic'";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $identifier = $result['value'];
    return $identifier;
  }

  public $count = 0;

  public function getAllTopicNamespaces()
  {
    $query = "SELECT namespace, identifier FROM table_topics";
    $result = mysqli_query($this->link, $query);
    while($row = mysqli_fetch_assoc($result))
    {
      if(strlen(trim($row['namespace'])) == 0)
      $ns = "";
      else $ns = ":".$row['namespace'];
      $result_array[] = ":root".$ns.":".$row['identifier'];
    }
    return $result_array;
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

  // COSEN
  public function getChildTree($namespace){
    // First collect the base topics that have no parent, then for each one of these topics we'll find things recursively

    if($namespace == "")
    // Collecting base topics that have no parent
    $baseTopics = $this->getOrphanTopics();
    // Else collect children who have this namespace as their immediate parent
    else $baseTopics = $this->getChildTopics($namespace);
    while($oneBaseTopic = mysqli_fetch_assoc($baseTopics)){

      $this->getChildTreeArray($oneBaseTopic['identifier']);

      $childArray = $this->childArr;
    }
    return $childArray;
  }


  // COSEN
  // Recursive function that collects a sting array in form of a tree of the children of any given namespace.
  public $childArr = [];
  public function getChildTreeArray($namespace){
    $i = 0;
    $currentName = "";
    while($i < $this->count){
      $currentName.= "----";
      $i += 1;
    }

    $currentName.= $this->getTopicDisplayName($namespace);

    $currentArr['nsid'] = $namespace;
    $currentArr['display_name'] = $currentName;

    $this->childArr[] = $currentArr;

    $this->count += 1;
    if($children = $this->getChildTopics($namespace)){
      while($row = mysqli_fetch_assoc($children)){
        $this->getChildTreeArray($namespace.":".$row['identifier']);
      }
      $this->count -= 1;
    } else {
      return;
    }

  }

  // Returns a mysqli_result object;
  public function getTopicData($namespaceWithIdentifier){
    $nsid_arr = $this->getNamespaceAndIdentifier($namespaceWithIdentifier);
    $query = "SELECT * FROM table_topics where identifier='".$nsid_arr['identifier']."' AND namespace='".$nsid_arr['namespace']."'";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // Returns a mysqli_result object;
  public function getQuestionDetails($id){
    $query = "SELECT * FROM table_questions where _id=$id";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // Returns a mysqli_result object;
  public function getQuestionsByType($question_type){
    $query = "SELECT _id, question FROM table_questions where question_type_id=$question_type";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // Returns a mysqli_result object;
  public function getQuestionTypeDetails($id){
    $query = "SELECT * FROM table_question_type where _id=$id";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  public function getRelatedQuestions(){
    return mysqli_query($this->link, "SELECT * FROM table_questions_topics WHERE topic_id > 9999");
  }

  // Returns child topics as a mysqli_result object.
  public function getTopicChildren($id){
    $query = "SELECT * FROM table_topics where child_of=$id";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // COSEN
  public function getOrphanTopics(){
    $query = "SELECT `identifier`, `display_name` FROM table_topics where `namespace`=''";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // COSEN
  public function getChildTopics($namespace){
    $query = "SELECT `identifier`, `display_name` FROM table_topics where `namespace`='$namespace'";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // COSEN
  // Returns child topics as a mysqli_result object.
  public function getQuestionsByTopic($nsid){
    //TODO
    $query = "SELECT `_id`, `question` FROM `table_questions` WHERE `tags` LIKE '%[".$nsid."|%'";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // COSEN
  // Returns child topics as a mysqli_result object.
  public function getTopicsByQuestion($question_id){
    $query = "SELECT * FROM table_questions_topics where question_id=$question_id";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  public function getQuestionTypesOfSubject($id){
    $query = "SELECT * FROM table_question_type where subject_id=$id";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  // COSEN
  // Get title of question by supplying _id of question in database.
  public function getQuestionTitle($id){
    $query = "SELECT question FROM table_questions where _id=$id";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $name = $result['question'];
    return $name;
  }

  public function getTopicNamespaceArray($id){
    $query = "SELECT namespace FROM table_topics where identifier='$id'";
    $result = mysqli_query($this->link, $query);
    if(mysqli_num_rows($result)>0){
      $result = mysqli_fetch_assoc($result);
      $namespace = $result['namespace'];
      $namespace = explode(':', $namespace);
      return $namespace;
    } else
    return false;
  }

  public function getTopicParentID($id){
    $query = "SELECT child_of FROM table_topics where _id='$id'";
    $result = mysqli_query($this->link, $query);
    if(mysqli_num_rows($result)>0){
      $result = mysqli_fetch_assoc($result);
      $parentID = $result['child_of'];
      return $parentID;
    } else
    return false;
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

  // COSEN
  // Returns display_name if available, else returns identifier
  public function getTopicDisplayName($completenamespace){
    $nsid_arr = $this->getNamespaceAndIdentifier($completenamespace);
    $id = $nsid_arr['identifier'];
    $ns = $nsid_arr['namespace'];
    $query = "SELECT display_name FROM table_topics where identifier='$id' AND namespace='$ns'";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $name = $result['display_name'];
    if(strlen(trim($name))==0)
    return $id;
    return $name;
  }

  public function getQuestionTypeName($id){
    $query = "SELECT question_type FROM table_question_type where _id=$id";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $name = $result['question_type'];
    return $name;
  }

  public function addQuestionType($subject_id, $question_type, $method){
    $method = sanitize($this->link, $method);
    $question_type = sanitize($this->link, $question_type);
    $query = "INSERT INTO table_question_type (subject_id, question_type, method) values('$subject_id', '$question_type', '$method')";
    return mysqli_query($this->link, $query);
  }

  public function editQuestionType($id, $subject_id, $question_type, $method){
    $method = sanitize($this->link, $method);
    $question_type = sanitize($this->link, $question_type);
    $query = "UPDATE table_question_type SET subject_id='$subject_id', question_type='$question_type', method='$method' WHERE _id=$id";
    return mysqli_query($this->link, $query);
  }

  // COSEN
  public function editQuestion($id, $question, $answer, $question_type, $tags, $exams, $explanation){

    // Sanitize stuff
    $question = sanitize($this->link, $question);
    $answer = sanitize($this->link, $answer);
    $question_type = sanitize($this->link, $question_type);
    $tags = sanitize($this->link, $tags);
    $exams = sanitize($this->link, $exams);
    $explanation = sanitize($this->link, $explanation);

    // Query String
    $query = "UPDATE table_questions SET `question` = '$question', `answer` = '$answer', `question_type` = '$question_type', `tags` = '$tags', `exams` = '$exams', `explanation` = '$explanation' WHERE `_id` = $id";

    // Run Query
    if(mysqli_query($this->link, $query)){
      return true;
    } else {
      return false;
    }
  }

  public function addQuestion($question, $answer, $question_type, $tags, $exams, $explanation){

    // Sanitize stuff
    $question = sanitize($this->link, $question);
    $answer = sanitize($this->link, $answer);
    $question_type = sanitize($this->link, $question_type);
    $tags = sanitize($this->link, $tags);
    $exams = sanitize($this->link, $exams);
    $explanation = sanitize($this->link, $explanation);

    // Query String
    $query = "INSERT INTO table_questions(question, answer, question_type, tags, exams, explanation) values('$question', '$answer', '$question_type', '$tags', '$exams', '$explanation')";

    if(mysqli_query($this->link, $query)){
      $question_id = mysqli_insert_id($this->link);
      return $question_id;
    } else {
      return false;
    }
  }

  public function getQuestionsNotHavingTopics($subject_id){
    $query = "select * from table_questions where subject_id='$subject_id' and _id not in (select question_id from table_questions_topics)";
    return mysqli_query($this->link, $query);
  }

  public function getQuestionsThatAppearedInExams($subject_id){
    $query = "select * from table_questions where subject_id='$subject_id' and _id in (select question_id from table_questions_exams)";
    return mysqli_query($this->link, $query);
  }

  public function addQuestionTopic($question_id, $topic_id){
    $query = "INSERT INTO table_questions_topics (question_id, topic_id) values($question_id, $topic_id)";
    return mysqli_query($this->link, $query);
  }

  public function addQuestionExam($question_id, $exam_id){
    $query = "INSERT INTO table_questions_exams (question_id, exam_id) values($question_id, $exam_id)";
    return mysqli_query($this->link, $query);
  }

  public function addTopic($identifier, $display_name, $about, $namespace, $body){
    $display_name = sanitize($this->link, $display_name);
    $about = sanitize($this->link, $about);
    $body = sanitize($this->link, $body);
    $identifier = sanitize($this->link, $identifier);
    $namespace = sanitize($this->link, $namespace);

    // Check if topic already exists
    if($this->TopicExists2($namespace, $identifier))
    {
      return $this->editTopic($identifier, $display_name, $about, $namespace, $body);
    }
    $query = "INSERT INTO table_topics(`identifier`, `about`, `display_name`, `namespace`, `body`) values ('$identifier','$about', '$display_name', '$namespace', '$body')";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  public function editTopic($identifier, $display_name, $about, $namespace, $body){
    $display_name = sanitize($this->link, $display_name);
    $about = sanitize($this->link, $about);
    $body = sanitize($this->link, $body);
    $identifier = sanitize($this->link, $identifier);
    $namespace = sanitize($this->link, $namespace);
    $query = "UPDATE table_topics SET `display_name` = '$display_name', `about` = \"".$about."\", `body` = \"".$body."\" WHERE `namespace` = '$namespace' AND `identifier` = '$identifier'";
    $result = mysqli_query($this->link, $query);
    return $result;
  }

  public function deleteTopic($id){
    // Get parent ID first.
    $query = "SELECT child_of FROM table_topics where _id=$id";
    $result = mysqli_query($this->link, $query);
    $result = mysqli_fetch_assoc($result);
    $parentID = $result['child_of'];

    // Set this topic's parent as the parent of its children.
    $query = "UPDATE table_topics SET `child_of` = $parentID WHERE `child_of` = $id";
    mysqli_query($this->link, $query);

    return mysqli_query($this->link, "DELETE FROM table_topics WHERE `_id` = $id");
  }

  public function deleteQuestion($id){
    mysqli_query($this->link, "DELETE FROM table_questions WHERE `_id` = $id");
    mysqli_query($this->link, "DELETE FROM table_questions_topics WHERE `question_id` = $id");
    mysqli_query($this->link, "DELETE FROM table_questions_exams WHERE `question_id` = $id");
    return true;
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

  // COSEN
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

  // COSEN
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

}
?>
