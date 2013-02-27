<?php
$taskId = "entry_299071204";
$categoryId = "entry_920947763";
$tagId = "entry_662564008";
$timeId = "entry_64794649";


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

	$time = str_replace(".", ",", $time);

	$postData = array(
			$taskId => $task,
			$categoryId => $category,
			$tagId => $tag,
			$timeId => $time,
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