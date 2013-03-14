<?php
	include dirname(__FILE__) . "/controller.stats.php";
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Stats &#124; Google Drive Time Tracker</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="./bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
	<style>body {padding-top:12px;}</style>
</head>
<body>
<div class="container">
	<div class="row-fluid">
		<div class="span12">
			<h1>Stats <small>Google Drive Time Tracker</small></h1>
			<hr />
		</div>
	</div>
	<div class="row-fluid">
		<div class="span9">
			<?php if(!is_null($file)) { ?>


				<form action="./stats.php" method="get" class="form">
					<input type="hidden" name="file" value="<?php echo $file; ?>" />

					<div class="row-fluid">
						<div class="span6">
							<div class="control-group">
								<label class="control-label" for="st">Tags</label>
								<div class="controls">
									<select id="st" name="st[]" multiple="multiple" style="width:100%;" size="<?php echo $selectBoxSize; ?>">
										<?php
											foreach($tags as $tagId=>$tag) {
												echo '<option value="'.$tagId.'"'.(in_array($tagId,$st) ? ' selected="selected"' : '').'>'.$tag.'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>

						<div class="span6">
							<div class="control-group">
								<label class="control-label" for="sc">Categories</label>
								<div class="controls">
									<select id="sc" name="sc[]" multiple="multiple" style="width:100%;" size="<?php echo $selectBoxSize; ?>">
										<?php
											foreach($categories as $categoryId=>$category) {
												echo '<option value="'.$categoryId.'"'.(in_array($categoryId,$sc) ? ' selected="selected"' : '').'>'.$category.'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>

					</div>
					
					<div class="row-fluid">
						 <div class="span12">
							<button type="submit" class="btn btn-primary">Apply selection</button>
						 </div>
					</div>



				</form>
				
				<hr />	
				<h1>Overview</h1>
				<div class="row-fluid">
					<div class="span6">
				
						<div class="well lead">
							<h3><?php echo date("d.m.Y", $jsonData['info']['start']); ?> &ndash; <?php echo date("d.m.Y", $jsonData['info']['end']); ?></h3>
							<h2><?php echo $stats['time']; ?></h2>
						</div>
						
						<table class="table" id="data-tags">
							<?php foreach($st as $selectTag) {
								echo '<tr>
										<td class="tagname">'.$tags[$selectTag].'</td>
										<td><span class="badge" style="background:'.$stats[$selectTag]['color'].'">&nbsp;</span> <span class="tagtime">'.$stats[$selectTag]['time'].'</span></td>
									</tr>
								';
							}
							?>
						</table>
					</div>
					
					<div class="span6"><div id="tag_chart" style="width: 100%; height: 300px;"></div></div>
				</div>
				
				<hr />
				<h2>Details</h2>
				<table class="table">
					<tr>
					<?php foreach($st as $selectTag) {
						echo '<th style="width:'.(100/count($st)).'%"">';
							echo $tags[$selectTag];
						echo '</th>';
					}?>
					<tr>

					<tr>
					<?php
					$i=0;
					foreach($st as $selectTag) {
						echo '<td>';

							echo '<div id="tag_chart['.$i.']" style="width: 100%; height: 300px;"></div>';
							
							echo '<table class="table" id="data-categories'.$i.'">';
								foreach($stats[$selectTag]['categories'] as $selectCategoryId=>$selectCategory) {
								echo '<tr>
										<td class="tagname">'.$categories[$selectCategoryId].'</td>
										<td><span class="badge" style="background:'.$selectCategory['color'].'">&nbsp;</span> <span class="tagtime">'.$selectCategory['time'].'</span></td>
									</tr>
								';

								}
							echo '</table>';
						echo '</td>';
						$i++;
					}
					?>
					<tr>
				</table>
			
			<?php } else { ?>
			
				<!-- NEW FILE -->
				<form action="./stats.php" method="post" class="form-horizontal" enctype="multipart/form-data">
					
					<div class="alert alert-info">
						Export your responses from Google Drive as a CSV file.
					</div>

					<div class="control-group">
						<label class="control-label" for="upload">Select CSV file</label>
						<div class="controls">
							<input type="file" id="upload" name="upload" />
						</div>
					</div>

					<div class="control-group">
						<div class="controls">
							<button type="submit" class="btn btn-primary" name="create">Create stats</button>
						</div>
					</div>
				</form>
						
			<?php } ?>
		</div>
		
		<div class="span3">
			<ul class="nav nav-tabs nav-stacked">
				<li><a href="./stats.php"><i class="icon-plus"></i> New stats</a></li>
<?php
$jsonFolder = "./json/";

if($fp = opendir($jsonFolder)) {
	while(false !== ($jsonFile = readdir($fp))) {
	if(preg_match("/.json$/", $jsonFile)){
		$jsonFile = str_replace(".json", "", $jsonFile);
		$jsonFileArr = explode("_", $jsonFile);

		

		if(isset($jsonFileArr[3]) && isValidMd5($jsonFileArr[3])) {
			$menuFile = str_replace("_".$jsonFileArr[3] , "", $jsonFile);
			$menuFile = str_replace("_", "&nbsp;", $menuFile);
			echo '<li><a href="./stats.php?file='.$jsonFile.'">'.$menuFile.'</a></li>';	
		}
	}
}
closedir($fp);
}
?>
			</ul>
		</div>
	</div>
</div>

	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="./bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		function drawChart() {

			var tagCount = $('#data-tags tr').size();
			var tagColors = new Array();

			var data = new google.visualization.DataTable();
				data.addColumn('string', 'Tag');
				data.addColumn('number', 'Hours');
				data.addRows(tagCount);
				// console.log(tagCount);
				for(i=0;i<tagCount;i++) {
					var tagname = $('#data-tags tr:eq('+i+') .tagname').text();
					var tagtime = $('#data-tags tr:eq('+i+') .tagtime').text()*1;
					var tagcolor = $('#data-tags tr:eq('+i+') .badge').attr("style").replace("background:","");
					// console.log("TN: "+tagname);
					// console.log("TT: "+tagtime);
					// console.log("TC: "+tagcolor);
					data.setCell(i, 0, tagname);
					data.setCell(i, 1, tagtime);

					tagColors.push(tagcolor);
				}
		
		var options = {
			chartArea: {
				width: "100%",
				height: "90%"
			},
			legend: {
				position: "none"
			},
			colors: tagColors
		};
		
		var chart = new google.visualization.PieChart(document.getElementById('tag_chart'));
		chart.draw(data, options);


		/**
		 * bar charts - categories
		 */
		var categoriesCount = $('table[id^="data-categories"]').size();;
		// console.log("CC:"+categoriesCount);

		for(i=0;i<categoriesCount;i++) {
			var categoryCount = $('#data-categories'+i+' tr').size();
			// console.log(i);
			// console.log("CC2:"+categoryCount);
			var data = new google.visualization.DataTable();
				data.addColumn('string', 'Category');
				data.addColumn('number', 'Hours');
				data.addRows(categoryCount);
				var categoryColors = new Array();
				for(y=0;y<categoryCount;y++) {
					var categoryname = $('#data-categories'+i+' tr:eq('+y+') .tagname').text();
					var categorytime = $('#data-categories'+i+' tr:eq('+y+') .tagtime').text()*1;
					var categorycolor = $('#data-categories'+i+' tr:eq('+y+') .badge').attr("style").replace("background:","");
					// console.log(categorycolor);
					// console.log("CN: "+tagname);
					// console.log("CT: "+tagtime);
					// console.log("CC: "+tagcolor);
					data.setValue(y, 0, categoryname);
					data.setValue(y, 1, categorytime);

					categoryColors.push(categorycolor);

				}
				// console.log(categoryColors);
				var options = {
					chartArea: {
						width: "100%",
						height: "90%"
					},
					legend: {
						position: "none"
					},
					colors: categoryColors
				};
				
				var chart = new google.visualization.PieChart(document.getElementById('tag_chart['+i+']'));
				chart.draw(data, options);

		}
      }
    </script>

</body>
</html>