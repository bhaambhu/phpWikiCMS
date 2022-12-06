
function addTag() {
  var tag = document.getElementById("tag");
  var selectBox = document.getElementById("topicSelector");
  var currentID = selectBox.options[selectBox.selectedIndex].value;
  if(document.getElementById("tags").value == "")
  document.getElementById("tags").value += currentID;
  else if (!document.getElementById("tags").value.includes(currentID))
  document.getElementById("tags").value += ", "+currentID;
  else alert("Tag already added!");
}

// function removeTag(e){
// 		var itm = document.getElementById(e);
// 		var tagContainer = document.getElementById("tags");
// 		if(e != "tag"){
// 		tagContainer.removeChild(itm);
// 		} else {
// 		alert("The subject cannot be removed!");
// 		}
// }
//
// function removeExamTag(e){
// 		var itm = document.getElementById(e);
// 		var tagContainer = document.getElementById("exam_tags");
// 		if(e != "exam_tag"){
// 		tagContainer.removeChild(itm);
// 		} else {
// 		alert("The default tag cannot be removed!");
// 		}
// }
