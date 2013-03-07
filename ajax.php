<?php
$taskId = "entry_299071204";
$categoryId = "entry_920947763";
$tagId = "entry_662564008";
$timeId = "entry_64794649";

/**
 * Time Converter
 */
function timeConvert($time) {
	$time = str_replace(" ", "", $time);
	$time = str_replace(".", ",", $time);
	$time = trim($time);

	if(preg_match("/:/", $time)) {
		// return "je tam";

		if(substr($time, 0, 1)==":") {
			$minutes = substr($time, 1);
		} else {
			$timeArr = explode(":", $time);
			$minutes = ($timeArr[0]*60)+(isset($timeArr[1]) ? $timeArr[1] : 0);
		}
		$time = str_replace(".", ",", round($minutes/60,2));
	}

	return $time;
}

/**
 * Data
 */
if(isset($_GET['get_data'])) {
	$categories = array();
	$tags = array();
	/**
	 * HTML of Form
	 */
	$ch = curl_init();
	$googleForm = "https://docs.google.com/forms/d/".$_GET['form_id']."/viewform";
	curl_setopt($ch, CURLOPT_URL, $googleForm);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$htmlForm = curl_exec($ch);
	curl_close($ch);


	/**
	 * Get <select> box options
	 */
	$dom = new DOMDocument();
	$dom->loadHTML($htmlForm);
	$xpath = new DOMXPath($dom);
	$htmlCategories = $xpath->evaluate("//select[@id='$categoryId']//option");
	foreach($htmlCategories as $category) {
		if($category->nodeValue!="") $categories[] = $category->nodeValue;
	}
	$htmlTags = $xpath->evaluate("//select[@id='$tagId']//option");
	foreach($htmlTags as $tag) {
		if($tag->nodeValue!="") $tags[] = $tag->nodeValue;
	}

	sort($categories);
	sort($tags);

	$return = array(
			"categories" => $categories,
			"tags" => $tags,
		);

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');

	echo json_encode($return);
}



/**
 * Data sending
 */
if(isset($_POST['send_data'])) {

	$googleFormResponse = $googleForm = "https://docs.google.com/forms/d/".$_POST['form_id']."/formResponse";

	$task = isset($_POST['task']) ? $_POST['task'] : "";
	$category = isset($_POST['category']) ? $_POST['category'] : "";
	$tag = isset($_POST['tag']) ? $_POST['tag'] : "";
	$time = isset($_POST['time']) ? $_POST['time'] : "";

	$taskId = str_replace("_", ".", $taskId);
	$categoryId = str_replace("_", ".", $categoryId);
	$tagId = str_replace("_", ".", $tagId);
	$timeId = str_replace("_", ".", $timeId);

	$postData = array(
			$taskId => $task,
			$categoryId => $category,
			$tagId => $tag,
			$timeId => timeConvert($time),
			"submit" => true
		);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $googleFormResponse);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	$result = curl_exec($ch);
}

/**
 * Time Format
 */
if(isset($_GET['time_format'])) {
	$time = isset($_GET['time_format']) ? $_GET['time_format'] : 0;
	$time = str_replace(",", ".", $time);
	$time = ($time*60*60)-3600;
	$return = array();
	$return['time'] = $time;
	$return['time_format'] = date("H:i", $time);

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');

	echo json_encode($return);
}