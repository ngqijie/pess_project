<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link href="projectstyle.css" rel="stylesheet" type="text/css">
</head>
<body>
<script>
function validate()
{
	var x=document.forms["qijieLogCall"]["qijieCaller"].value;
	if (x==null || x=="")
	{
		alert("Caller Name is required.");
		return false;
	}
    }
	//may add code for validating other input
</script>
<?php require 'nav.php'?> 
<?php require 'db_config.php'; 
	
// create database connection	
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
//Check connection	
if ($mysqli->connect_errno)
{
	die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}
	
$sql = "SELECT * FROM incidenttype";
//Run sql command in sql, if error display error msg and exit	
if (!($stmt = $mysqli->prepare($sql)))
  {
	die("Prepare failed: ".$mysqli->errno);
  }
  //Check can run command?
	if (!$stmt->execute())
  {
	  die("Cannot run failed: ".$stmt->errno);
  }

if (!($resultset = $stmt->get_result())) {
	die("Error in getting resultset: ".$stmt->errno);
}
	
	$incidentType; //an array variable
	
while ($row = $resultset->fetch_assoc()) {
	// create an associative array of $incidentType [incident_type_id, incident_type_desc]
	$incidentType[$row['incidentTypeId']] = $row['incidentTypeDesc'];
}

$stmt->close();
	
$resultset->close();	
	
$mysqli->close();
	
?>
<br>
<fieldset>
<legend>Police Service System</legend>	
<form name="qijieLogcall" method="post" action="dispatch.php" onSubmit="Vaidate();">
	<table width="31% border="1" align="center" cellpadding="4" cellpadding="4">
	<tr>
	<td width="50%">Caller's Name :</td> 
	<td width="50%"><input type="text" name="qijieName" id="qijieName"</td>
	</tr>
	<tr>
	<td width="50">Contact No :</td>
<td width="50"><input type="text" name="qijieContact" id="qijieContact"></td>			
	</tr>
	<tr>
	<td width="50%">Location :</td>
	<td width="50%"><input type="text" name="qijielocation" id="qijielocation"</td>
	</tr>
	<tr>
	<td width="50%">Incident Type :</td>
	<td width="50%"><select name="qijieType" id="qijieType"
	 <?php foreach($incidentType as $key=> $value) {?>
	<option value="<?php echo $key ?> " > 
	 <?php echo $value ?> </option>					
	<?php } ?>									
	</select>									
	</td>										 
</tr>
   <tr>
	<td width="50%">Description:</td>
	<td width="50%"><textarea name="qijieDesc" id="qijieDesc" cols="45" rows="5"></textarea></td>
	</tr>
	<tr>									  
	<td> <input type="reset" name="cannelProcess" id="cannelProcess" value="Reset"</td><br>	
	<td><input type="submit" name="submitButton" id="submitButton" value="Process Call"validate();"></td>
     <tr>
</fieldset>
</table>
</form>
</body>
</html>