<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
</head>
<body>
<?php require_once 'nav.php';?> 
	
<?php
if (isset($_POST["btnDispatch"]))
{
 require_once 'db_config.php';
	
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	
if ($mysqli->connect_errno)
{
	die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}
	
$patrolcarDispatched = $_POST["chkPatrolcar"]; 
$numOfPatrolcarDispatched = count($patrolcarDispatched);
	
$incidentStatus;
if ($numOfPatrolcarDispatched > 0) {
	$incidentStatus='2';
	} else {
	$incidentStatus='1';
}

$sql = "INSERT INTO incident (callerName, phoneNumber, incidentTypeId, incidentLocation, incidentDesc, incidentStatusId) VALUES (?, ?, ?, ?, ?, ?)";
	
if(!($stmt = $mysqli->prepare($sql)))
{
	die("Prepare failed: ".$mysqli->errno);
}
	
if(!$stmt->bind_param('ssssss', $_POST['qijieName'], $_POST['qijieContact'], $_POST['qijieType'], $_POST['qijielocation'], $_POST['qijieDesc'], $incidentStatus))
{
die("Binding parameters failed: ".$stmt->errno);
}
	
if (!$stmt->execute())
{
	die("Insert incident table failed: ".$stmt->errno);
}
	
// retrieve incident_id for the newly inserted incident
		$incidentId=mysqli_insert_id($mysqli);;
		
		//update patrolcar status table and add into dispatch table
		for($i=0; $i < $numOfPatrolcarDispatched; $i++)
			
	{
		// update patrol car status
		$sql = "Update patrolcar SET patrolcarStatusId='1' WHERE patrolcarId = ?";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('s', $patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if (!$stmt->execute())
		{
			die("Update patrolcar_status table failed: ".$stmt->errno);
		}
			
		//insert dispatch data
		$sql = "INSERT INTO dispatch (incidentId, patrolcarId, timeDispatched) VALUES (?, ?, NOW())";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
			
		if (!$stmt->bind_param('ss', $incidentId,
							  		$patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if(!$stmt->execute())
		{
			die("Insert dispatch table failed: ".$stmt->errno);
		}
	}
	$stmt->close();		
	$mysqli->close();	
} 
?>	
<br>
<fieldset>
<legend>Police Service System</legend>	
<form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> "><br>
<table>
<tr>
<td colspan="2">Incident Detail</td>	
</tr>	
<tr>
<td>Caller's Name :</td>
<td><?php echo $_POST['qijieName'] ?>
<input type="hidden" name="qijieName" id="qijieName"
value="<?php echo $_POST['qijieName'] ?>"></td>
</tr>
<tr>
<td>Contact No :</td>
<td><?php echo $_POST['qijieContact'] ?>
<input type="hidden" name="qijieContact" id="qijieContact"
value="<?php echo $_POST['qijieContact'] ?>"></td>	
</tr>
<tr>
<td>Location :</td>
<td><?php echo $_POST['qijielocation'] ?>
<input type="hidden" name="qijielocation" id="qijielocation"
value="<?php echo $_POST['qijielocation'] ?>"></td>    
</tr>
<tr>
<td>Incident Type :</td>
<td><?php echo $_POST['qijieType'] ?>
<input type="hidden" name="qijieType" id="qijieType"
value="<?php echo $_POST['qijieType'] ?>"></td>	
</tr>
<tr>
<td>Description :</td>
<td><textarea name="qijieDesc" cols="45"
rows="5" readonly id="qijieDesc"><?php echo $_POST['qijieDesc'] ?></textarea>
<input name="qijieDesc" type="hidden"
id="qijieDesc" value="<?php echo $_POST['qijieDesc'] ?>"</td>
</tr>
</fieldset>
</table
<?php 
// connect to a database
require_once'db_config.php';
    
// create database connection
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
// check connection
if($mysqli->connect_errno) 
{
    die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}

 

// retrieve from patrolcar table those patrol cars that are 2:Patrol or 3:Free
$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_status
ON patrolcar.patrolcarStatusId=patrolcar_status.StatusId
WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId='3'";

 

    if (!($stmt = $mysqli->prepare($sql)))
    {
        die("Prepare failed: ".$mysqli->errno);
    }
    if (!$stmt->execute())
    {
        die("Cannot run SQL command: ".$stmt->errno);
    }
    if(!($resultset = $stmt->get_result()))
    {
        die("No data in resultset: ".$stmt->errno);
    }
    
    $patrolcarArray; // an array variable
    
    while  ($row = $resultset->fetch_assoc()) 
    {
        $patrolcarArray[$row['patrolcarId']] = $row['statusDesc'];
    }
    
    $stmt->close();
    $resultset->close();
    $mysqli->close();
    ?>

<br><br><table border="1" align="center">
<tr>
<td colspan="3">Dispatch Patrolcar Panel</td>
</tr>
<?php
foreach($patrolcarArray as $key=>$value){
?>
<tr>
<td><input type="checkbox" name="chkPatrolcar[]" value="<?php echo $key?>"</td>
<td><?php echo $key ?></td>
<td><?php echo $value ?></td>
</tr>
<?php } ?>
<tr>
<td><input type="reset" name="btnCancel" id="btnCancel" value="Reset"></td>
<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnDispatch" id="btnDispatch" value="Dispatch"></td>
</tr>
</table>
</form>
</body>
</html>