<?php $origId = "1Bsv2Yb93JYs7gfGr_QZ3UE54syOrB6OGylmEamA_s7U";
?><!DOCTYPE html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<title>Google Drive Time Tracker</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="./bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
	<style>form {margin-top:12px;}</style>
</head>
<body>
<div class="navbar navbar-inverse navbar-static-top">
	<div class="navbar-inner">
		
		<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>


			<span class="brand">Time Tracker</span>
			
			<div class="nav-collapse collapse">
				<ul class="nav">
					<li><a href="./" id="change_id">Change form ID</a></li>
					<li><a href="https://github.com/rotten77/google-drive-time-tracker" target="_blank">GitHub</a></li>
				</ul>
			</div>
		</div>

	</div>
</div>
<div class="container" id="timetracker">
	<div class="row">
		<form class="form-horizontal" method="post" id="form" action="./">
			<div class="control-group" id="task_group">
				<label class="control-label" for="task">Task</label>
				<div class="controls">
					<input type="text" id="task" name="task" placeholder="What are you working on?" class="input-xxlarge" />
					<span class="help-inline" id="task_err">Enter task description</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="category">Category &#47; project</label>
				<div class="controls">
					<select name="category" id="category"></select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="tag">Tag</label>
				<div class="controls">
					<select name="tag" id="tag"></select>
				</div>
			</div>

			<div class="control-group" id="time_spend">
				<label class="control-label" for="time">Time spent</label>
				<div class="controls">
					<input type="text" id="time" name="time" class="input-small" />
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">


			<div class="alert alert-info" id="working_from">
				Working from: <strong id="working_from_value">#</strong>
			</div>

			<!--<div class="alert alert-info" id="time_spend">
				Time spent: <strong id="time_spend_value">#</strong>
				<input name="time" id="time" type="hidden" value="">
			</div>-->
			
					<button type="submit" name="send_data" id="send_data" class="btn btn-primary">Send</button>
					<button class="btn btn-primary" id="start">Start</button>
					<button class="btn btn-primary" id="stop">Stop</button>
					<button class="btn btn-warning" id="cancel">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="container" id="settings">
		<h2>Step 1: Make a form</h2>
		<p>Open <a href="https://docs.google.com/forms/d/<?php echo $origId; ?>/edit?usp=sharing" target="_blank">this form</a> and make a copy (<strong>File &raquo; Make a copy</strong>, you must be logged in).</p>
		
		<div class="alert alert-warning">Please <strong>do not change this form</strong> so other users can use it. <strong>Make your copy and close original form</strong>.</div>
		
		<p>Now you can edit your Category and Task values.</p>
		
		<p>There is only simple summary of responses in Google Drive. 
		So you can make your own better summary with data from new table of responses (<strong>Choose response</strong> &raquo; <strong>New spreadsheet</strong>).
		</p>
		
		<p>Enter your form ID to next input field. Form ID you can find in address bar:</p>
		
		<div class="alert alert-info">
			Example: https://docs.google.com/forms/d/ <span class="label label-info"><?php echo $origId; ?></span> /viewform
		</div>
		
		<hr />
		<h2>Step 2: Enter your form ID</h2>
		<form class="form-horizontal" method="post" id="settings" action="./">
			<div class="control-group" id="settings_group">
				<label class="control-label" for="form_id">Form ID</label>
				<div class="controls">
					<input type="text" id="form_id" name="form_id" placeholder="Enter your Google Drive form ID" class="input-xxlarge" />
					<span class="help-inline" id="form_id_err">Enter your Google Drive form ID</span>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button type="submit" name="send_data" id="save" class="btn btn-primary">Save</button>
				</div>
			</div>
		</form>
		
		<hr />
		<div class="alert alert-info">
			<strong>Tip for Chrome users:</strong> You can use this app as a &quot;desktop app&quot;. Simply make a shortcut (<strong>Tools &raquo; Create application shortcuts</strong>) to your desktop.
		</div>

</div>

	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="./bootstrap/js/bootstrap.min.js"></script>
	<script>
		$(function(){

			var form_id = localStorage.getItem("form_id");

			if(form_id=="" || form_id==null) {
				$('#timetracker').hide();
				$('#settings').show();
			} else {
				$('#timetracker').show();
				$('#settings').hide();

					$('#task_err, #cancel').hide();

					$('#task').keydown(function(){
						$('#task_group').removeClass("error");
						$('#task_err').hide();
					});

					$.getJSON('./ajax.php?get_data=1&form_id='+form_id, function(data) {

						$.each(data['categories'], function(key, val) {
							var lastCategory = localStorage.getItem("lastCategory");
							$("#category").append('<option value="'+val+'"'+(lastCategory==val ? ' selected="selected"' : '')+'>'+val+'</option>');
						});

						$.each(data['tags'], function(key, val) {
							var lastCategory = localStorage.getItem("lastTag");
							$("#tag").append('<option value="'+val+'"'+(lastCategory==val ? ' selected="selected"' : '')+'>'+val+'</option>');
						});
						
		  			
					});

					$('#task').val(localStorage.getItem("lastTask"));

					$('#tag, #category').change(function(){
						localStorage.setItem("lastTag", $('#tag option:selected').val());
						localStorage.setItem("lastCategory", $('#category option:selected').val());
					});
					

					if(localStorage.getItem("lastTag")!="") {
						//$('#tag option').removeAttr("selected");
						//$('#tag option[value="'+localStorage.getItem("lastTag")+'"]').attr("selected", "selected");
					}

					if(localStorage.getItem("lastCategory")!="") {
						//$('#category option').removeAttr("selected");
						//$('#category option[value="'+localStorage.getItem("lastCategory")+'"]').attr("selected", "selected");
					}

					//$('#tag').change();

					function returnTime() {
						var d = new Date();
						var n = d.getTime();

						return n;
					}

					function formatDate(d) {
						return (d.getHours()<10 ? '0' : '')+d.getHours()+":"+(d.getMinutes()<10 ? '0' : '')+d.getMinutes();
					}

					$('#form').submit(function(){
						return false;
					});
					$('#send_data').click(function(){
						if($('#task').val()=="") {
							$('#task_group').addClass("error");
							$('#task_err').show();
							return false;
						}
						$.post("ajax.php", {
								task: $('#task').val(),
								category: $('#category option:selected').val(),
								tag: $('#tag option:selected').val(),
								time: $('#time').val(),
								send_data: true,
								form_id: localStorage.getItem("form_id")
							});

									$('#task').val("");
									$('#stop, #send_data, #working_from, #time_spend, #cancel').hide();
									$('#start').show();
									localStorage.setItem("lastTask", "");
							
						return false;
					});


					
					var lastTime = localStorage.getItem("lastTime")*1;
					if(lastTime>0) {
						$('#start, #send_data,#time_spend').hide();
						$('#stop, #working_from').show();

						var d = new Date(lastTime);
						$('#working_from_value').text(formatDate(d));
					} else {
						$('#stop, #send_data, #working_from,#time_spend').hide();
						$('#start').show();
					}

					$('#start').click(function(){
						var actualTime = returnTime();
						var d = new Date(actualTime);

						localStorage.setItem("lastTime", actualTime);
						localStorage.setItem("lastTask", $('#task').val());
						$('#start, #send_data,#time_spend,#cancel').hide();
						$('#stop, #working_from').show();
						$('#working_from_value').text(formatDate(d));
					});
					$('#stop').click(function(){
						var lastTime = localStorage.getItem("lastTime")*1;
						// console.log(returnTime());
						// console.log(lastTime);

						var timeSpend = (returnTime()-lastTime)/60/60/1000;

						timeSpend = Math.round(timeSpend*100)/100;
						//timeSpend.replace(".", ",");
						localStorage.setItem("lastTime", 0);

						
						$('#time').val(0);

							$.getJSON('./ajax.php?time_format='+timeSpend, function(data) {
								$('#time').val(data['time_format']);
							});

						//$('#time_spend_value').text(timeSpend+" hrs.");

						$('#start, #stop, #working_from').hide();
						$('#send_data,#time_spend,#cancel').show();
					});
					$('#cancel').click(function(){
						$('#stop, #time_spend, #start, #send_data, #cancel').hide();
						$('#start').show();
						localStorage.setItem("lastTime", 0);
					});
			}


			$('#form_id_err').hide();
			$('#settings').submit(function(){
				if($('#form_id').val()=="") {
					$('#settings_group').addClass("error");
					$('#form_id_err').show();
					return false;
				} else {
					localStorage.setItem("form_id", $('#form_id').val());
				}
			});

			$('#form_id').keydown(function(){
				$('#settings_group').removeClass("error");
				$('#form_id_err').hide();
			});

			$('#change_id').click(function(){
				localStorage.setItem("form_id", "");
			});

			$('#task').keyup(function(){
				localStorage.setItem("lastTask", $('#task').val());
			});
					
		});
	</script>
</body>
</html>