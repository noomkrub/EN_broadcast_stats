<?php
// EN call
// http://e-activist.com/ea-dataservice/data.service?service=EaBroadcastInfo&token=TOKEN&contentType=json&startRow=0&endRow=100
/*
name		value					type		format
clientId	1827					xs:int	
broadcastId	104117					xs:int
broadcastName	TH.20160204.Nogmo.PRevent.Esup B	xs:string
exportName	TH.20160204.Nogmo.PRevent.Esup B	xs:string
broadcastDate	04/02/2016				xs:date
sendCount	1827					xs:int
openCount	144					xs:int
clickCount	1					xs:int
compCount	0					xs:int
hardBounceCount	0					xs:int
softbounceCount	0					xs:int
unsubscribeCount	1				xs:int
feedbackCount	0					xs:int
optOutQuestionId	7905				xs:int
optOutQuestionName	opt_in_status			xs:string
optOutQuestionExportName	opt_in_status		xs:string
*/

$time_start = microtime(true);
########## MySql details (Replace with yours) #############
$db_username = ""; //Database Username
$db_password = ""; //Database Password
$hostname = "127.0.0.1"; //Mysql Hostname
$db_name = ''; //Database Name
###################################################################
/*
$db_username = "ilovemyo_ilmo"; //Database Username
$db_password = "shark!attack1"; //Database Password
$hostname = "127.0.0.1"; //Mysql Hostname
$db_name = 'ilovemyo_ilmo'; //Database Name
*/
###################################################################
// connect db
try {
	$conn = new PDO("mysql:host=$hostname; dbname=$db_name", $db_username, $db_password);
 	$conn->exec("set names utf8");//    echo "Connected to database";
}
catch (PDOException $e) {
    echo $e->getMessage();
}

$table1="en_history_EaBroadcastInfo";
$table2="en_history_EaBroadcastData";
$en_token="TOKEN";

$datasource="https://e-activist.com/ea-dataservice/data.service";

$settings="service=EaBroadcastInfo&token=$entoken&contentType=json&startRow=0&endRow=100";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $datasource);
//curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $settings);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$output = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (isset($_REQUEST['debug'])){
	echo "<pre>";echo $output;echo $status;echo $ch;echo "</pre>";print curl_error($ch);
	}
curl_close($ch);


// Determine success or fail
if(stripos($status, '200') !== false){echo 'Success retreive campaign list<br>';}
else{echo 'failure retreive list';exit;}

$data=json_decode($output,TRUE);

$rows=$data['rows'];
$count=count($rows);
if (isset($_REQUEST['debug'])){
	echo "<table border='1'><tr><td>broadcastId</td>";
	echo "<td>Country</td>";
	echo "<td>broadcastName</td>";
	echo "<td>broadcastDate</td>";
	echo "<td>sendCount</td>";
	echo "<td>openCount</td>";
	echo "<td>clickCount</td>";
	echo "<td>compCount</td>";
	echo "<td>hardBounceCount</td>";
	echo "<td>softbounceCount</td>";
	echo "<td>unsubscribeCount</td>";
	echo "<td>feedbackCount</td>";
	echo "</tr>";
	}
for ($i=0;$i<$count;$i++){
	$broadcastId=$rows[$i]['columns'][1]['value'];
	$broadcastName=$rows[$i]['columns'][2]['value'];
	$broadcastDate=$rows[$i]['columns'][4]['value'];
	$sendCount=$rows[$i]['columns'][5]['value']; //sendCount
	$openCount=$rows[$i]['columns'][6]['value']; //openCount
	$clickCount=$rows[$i]['columns'][7]['value']; //clickCount
	$compCount=$rows[$i]['columns'][8]['value']; //compCount
	$hardBounceCount=$rows[$i]['columns'][9]['value']; //hardBounceCount
	$softbounceCount=$rows[$i]['columns'][10]['value']; //softbounceCount
	$unsubscribeCount=$rows[$i]['columns'][11]['value']; //unsubscribeCount
	$feedbackCount=$rows[$i]['columns'][12]['value']; //feedbackCount
	if (isset($_REQUEST['debug'])){
		echo "<tr><td title='broadcastId'>$broadcastId</td>"; //broadcastId
		}
	if (preg_match ( "/^\s*th./ix" , $broadcastName  )) {
		$country="TH";
		}
	else if (preg_match ( "/^\s*ph./ix" , $broadcastName  )) {
		$country="PH";
		}
	else if (preg_match ( "/^\s*id./ix" , $broadcastName  )) {
		$country="ID";
		}
	else if (preg_match ( "/^\s*sea./ix" , $broadcastName  )) {
		$country="SEA";
		}
	else if (preg_match ( "/^\s*test./ix" , $broadcastName  )) {
		$country="test";
		}
	else {$country="";}
	if (isset($_REQUEST['debug'])){
		echo "<td>$country</td>";
		echo "<td>".$broadcastName."</td>"; //broadcastName	
		}
	$bdate = str_replace('/', '-', $rows[$i]['columns'][4]['value']);
	$broadcastDate=date('Y-m-d', strtotime($bdate));	
	if (isset($_REQUEST['debug'])){
		echo "<td>".$broadcastDate."</td>"; //broadcastDate
		echo "<td>".$sendCount."</td>"; //sendCount
		echo "<td>".$openCount."</td>"; //openCount
		echo "<td>".$clickCount."</td>"; //clickCount
		echo "<td>".$compCount."</td>"; //compCount
		echo "<td>".$hardBounceCount."</td>"; //hardBounceCount
		echo "<td>".$softbounceCount."</td>"; //softbounceCount
		echo "<td>".$unsubscribeCount."</td>"; //unsubscribeCount
		echo "<td>".$feedbackCount."</td>"; //feedbackCount
		}
		
// check if there is any new email campaign and add to db
//	broadcastId	Country	broadcastName	broadcastDate	sendCount	
	$qry="select * from $table1 where `broadcastId`=$broadcastId";
	$result = $conn->prepare($qry);
	$result->execute();
	if (mysql_error()){echo mysql_error();$error.="Select info ";}
	if ($result->rowCount()==0){
		$qry=" insert into $table1 (`broadcastId`,`Country`,`broadcastName`,`broadcastDate`,`sendCount`) values ($broadcastId,'$country','$broadcastName','$broadcastDate',$sendCount) ";
		$result = $conn->prepare($qry);
		$result->execute();
		if (mysql_error()){echo mysql_error();$error.="Insert new info ";}
		if (isset($_REQUEST['debug'])){echo "$broadcastName added <br>";}
		}	
		
// Proceed add daily stat info to data table
	$date=date('Y-m-d',time());
	$qry="select * from $table2 where `date`='$date' and `broadcastId`=$broadcastId ";
	$result = $conn->prepare($qry);
	$result->execute();
	if (mysql_error()){echo mysql_error();$error.="Check exist data ";}
	if ($result->rowCount()==0){ //insert
		$qry=" insert into $table2 (`date`,`broadcastId`,`openCount`,`clickCount`,`compCount`,`hardBounceCount`,`softbounceCount`,`unsubscribeCount`,`feedbackCount`) values ('$date',$broadcastId,$openCount,$clickCount,$compCount,$hardBounceCount,$softbounceCount,$unsubscribeCount,$feedbackCount) ";
		$result = $conn->prepare($qry);
		$result->execute();
		if (mysql_error()){echo mysql_error();$error.="Insert new data ";}
		if (isset($_REQUEST['debug'])){echo "insert `date`='$date' and `broadcastId`=$broadcastId<br>";}
		}
	else {	//update 
		$qry="update  $table2 set `openCount`= $openCount,`clickCount`=$clickCount,`compCount`=$compCount,`hardBounceCount`=$hardBounceCount,`softbounceCount`=$softbounceCount,`unsubscribeCount`=$unsubscribeCount,`feedbackCount`=$feedbackCount  where  `date`='$date' and `broadcastId`=$broadcastId ";
		$result = $conn->prepare($qry);
		$result->execute();
		if (mysql_error()){echo mysql_error();$error.="Update data ";}
		if (isset($_REQUEST['debug'])){echo "update `date`='$date' and `broadcastId`=$broadcastId<br>";}
		}
	
/*
	echo $rows[$i]['columns'][1]['name']." = ".$rows[$i]['columns'][1]['value']."<br>";
	echo $rows[$i]['columns'][2]['name']." = ".$rows[$i]['columns'][2]['value']."<br>";
	echo $rows[$i]['columns'][4]['name']." = ".$rows[$i]['columns'][4]['value']."<br>";
	echo $rows[$i]['columns'][5]['name']." = ".$rows[$i]['columns'][5]['value']."<br>";
	echo $rows[$i]['columns'][6]['name']." = ".$rows[$i]['columns'][6]['value']."<br>";
	echo $rows[$i]['columns'][7]['name']." = ".$rows[$i]['columns'][7]['value']."<br>";
	echo $rows[$i]['columns'][8]['name']." = ".$rows[$i]['columns'][8]['value']."<br>";
	echo $rows[$i]['columns'][9]['name']." = ".$rows[$i]['columns'][9]['value']."<br>";
	echo $rows[$i]['columns'][10]['name']." = ".$rows[$i]['columns'][10]['value']."<br>";
	echo $rows[$i]['columns'][11]['name']." = ".$rows[$i]['columns'][11]['value']."<br>";
	echo $rows[$i]['columns'][12]['name']." = ".$rows[$i]['columns'][12]['value']."<br>";
*/	
	if (isset($_REQUEST['debug'])){echo "</tr>";}



	}
if (isset($_REQUEST['debug'])){echo "</table>";}







if (isset($error)){echo "<br>$error<br>";} 
else {echo "All done<br>";}


	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start);
	echo '<b>Total Execution Time:</b> '.$execution_time.' sec';

//var_dump($output);

?>
