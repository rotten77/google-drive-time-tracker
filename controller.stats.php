<?php
$file = isset($_GET['file']) && trim($_GET['file']!="") ? trim($_GET['file']) : null;

function text2url($text) {
	static $znaky = array (
		'á' => 'a', 'Á' => 'A', 'ä' => 'a', 'Ä' => 'A', 'č' => 'c',
		'Č' => 'C', 'ď' => 'd', 'Ď' => 'D', 'é' => 'e', 'É' => 'E',
		'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'í' => 'i',
		'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ľ' => 'l', 'Ľ' => 'L',
		'ĺ' => 'l', 'Ĺ' => 'L', 'ň' => 'n', 'Ň' => 'N', 'ń' => 'n',
		'Ń' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O',
		'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R', 'š' => 's',
		'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ť' => 't', 'Ť' => 'T',
		'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u',
		'Ü' => 'U', 'ý' => 'y', 'Ý' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y',
		'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z',
	);
	$text = strtolower(strtr($text, $znaky));
	$text = preg_replace('/[^a-zA-Z0-9]+/u', '-', $text);
	$text = str_replace('--', '-', $text);
	$text = trim($text, '-');
	return $text;
}
function isValidMd5($md5) {
	return !empty($md5) && preg_match('/^[a-f0-9]{32}$/', $md5);
}
if(!function_exists("rgb2hex")) {
	function rgb2hex($rgb) {
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
		return $hex; // returns the hex value including the number sign (#)
	}
}
function randomColor() {
	return rgb2hex(array(rand(30,200),rand(30,200),rand(30,200)));
}

/**
 * Upload CSV and create JSON
 */
$tmpFile = "tmp.csv";;
if(isset($_POST['create'])) {
	@unlink($tmpFile);
	if($_FILES["upload"]["error"]==0) {
		if(move_uploaded_file($_FILES['upload']['tmp_name'], "tmp.csv")) {


			/**
			 * Read CSV and create JSOn data
			 */
			$jsonData = array();
			$minTimeStamp = 0;
			$maxTimeStamp = 0;
			$row = 0;
			if (($handle = fopen($tmpFile, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
						if($row>0) {

							$taskTimeStamp = strtotime($data[0]);

							$task = trim($data[1]);

							$category = trim($data[2]);
							$categoryId = text2url($category);

							$tag = trim($data[3]);
							$tagId = text2url($tag);
							
							$time = str_replace(",", ".", $data[4]);

							if($row==1) $minTimeStamp = $taskTimeStamp;
							$maxTimeStamp = $taskTimeStamp;

							if(!isset($jsonData['categories'][$categoryId])) {
								$jsonData['categories'][$categoryId] = $category;
							}

							if(!isset($jsonData['tags'][$tagId])) {
								$jsonData['tags'][$tagId] = $tag;
							}

							$jsonData['tasks'][$taskTimeStamp]['category'] = $categoryId;
							$jsonData['tasks'][$taskTimeStamp]['tag'] = $tagId;
							$jsonData['tasks'][$taskTimeStamp]['task'] = $task;
							$jsonData['tasks'][$taskTimeStamp]['time'] = $time;
						}

					$row++;
				}
			 	fclose($handle);
			}

			$jsonData['info']['start'] = $minTimeStamp;
			$jsonData['info']['end'] = $maxTimeStamp;

			$statsFilename = date("Y-m-d", $minTimeStamp)."_-_".date("Y-m-d", $maxTimeStamp)."_".md5(rand(10000,99999));
			$fp = fopen("./json/$statsFilename.json", "w+");
			fwrite($fp, json_encode($jsonData));
			fclose($fp);

			@unlink("tmp.csv");
			header("Location: ./stats.php?file=$statsFilename");
		}
	}
}

/**
 * Get Stats
 */
$categories = array();
$tags = array();
unset($jsonData);
if(!is_null($file)) {
	$json = file_get_contents("./json/$file.json");
	$jsonData = json_decode($json, true);

	$categories = $jsonData['categories'];
	$tags = $jsonData['tags'];

	$st = isset($_GET['st']) ? $_GET['st'] : array();
		if(count($st)==0) foreach($tags as $id=>$name) $st[] = $id;

	$sc = isset($_GET['sc']) ? $_GET['sc'] : array();
		if(count($sc)==0) foreach($categories as $id=>$name) $sc[] = $id;
}


$selectBoxSize = 5;

if(round(count($categories)/3)>$selectBoxSize) $selectBoxSize = round(count($categories)/3);
if(round(count($tags)/3)>$selectBoxSize) $selectBoxSize = round(count($tags)/3);


/**
 * Data for statistics
 */
$stats = array();
$stats['time'] = 0;
if(isset($jsonData)) {
	foreach($jsonData['tasks'] as $timestamp=>$task) {
		foreach($task as $key=>$val) $$key=$val;

			if(in_array($tag, $st) && in_array($category, $sc)) {
				// all time
				$stats['time']+=$time;
			}
				//
				if(!isset($stats[$tag]['color'])) $stats[$tag]['color'] = randomColor();
				if(!isset($stats[$tag]['categories'][$category]['color'])) $stats[$tag]['categories'][$category]['color'] = randomColor();
				
				$stats[$tag]['time'] = isset($stats[$tag]['time']) ? $stats[$tag]['time']+$time : $time;
				$stats[$tag]['categories'][$category]['time'] = isset($stats[$tag]['categories'][$category]['time']) ? $stats[$tag]['categories'][$category]['time']+$time : $time;

				if(isset($stats[$tag]['categories'][$category]['tasks'])) $stats[$tag]['categories'][$category]['tasks'] = array();
				$stats[$tag]['categories'][$category]['tasks'][$timestamp]['task'] = $task;
				$stats[$tag]['categories'][$category]['tasks'][$timestamp]['time'] = $time;

	}
}
// echo '<pre>';
// print_r($stats);