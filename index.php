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
// broadcastId  Country  project  campaign  broadcastName  broadcastDate  sendCount

$table2="en_history_EaBroadcastData";
// id  date  broadcastId  openCount  clickCount  compCount  hardBounceCount  softbounceCount  unsubscribeCount  feedbackCount

$table3="en_history_project_list";
// ProjectName  Campaign  date_start  date_end  status


/*
	$qry="select * from $table1 where `broadcastId`=$broadcastId";
	$result = $conn->prepare($qry);
	$result->execute();
	if (mysql_error()){echo mysql_error();$error.="Select info ";}
	if ($result->rowCount()==0){
		$qry=" insert into $table1 (`broadcastId`,`Country`,`broadcastName`,`broadcastDate`,`sendCount`) values ($broadcastId,'$Country','$broadcastName','$broadcastDate',$sendCount) ";
		$result = $conn->prepare($qry);
		$result->execute();
		if (mysql_error()){echo mysql_error();$error.="Insert new info ";}
		if (isset($_REQUEST['debug'])){echo "$broadcastName added <br>";}
		}	
		
*/
$campaign=array('','C&E','Forest','SAGE','Toxic','Ocean','Nuclear','Peace','Polar','FR-TM','FR-Upgrade','FR-enews','FR-welcome','General / multi campaign','Non-project','Comm','TEST');

// Create option list for project name
$qry="select ProjectName from $table3 ";
$result=$conn->prepare($qry);
$result->execute();
if ($result!=false){
	$project_opt="<option value=\"\"></option>";
	foreach ($result as $row){
		$project_opt.="<option value=".$row['ProjectName'].">".$row['ProjectName']."</option>";
		}
	}

/*
Program flow
- Q's summary by country
- broadcast property edit
- Date range by country
- campaign by country
- project by country
- project summary


*/
// - Q's summary by country
if ($_REQUEST['q']==1){$q1=" selected=\"selected\"";}else if ($_REQUEST['q']==2){$q2=" selected=\"selected\"";}else if ($_REQUEST['q']==3){$q3="  selected=\"selected\"";}else if ($_REQUEST['q']==4){$q4="  selected=\"selected\"";}
if ($_REQUEST['year']==2014){$year2014=" selected=\"selected\"";}else if ($_REQUEST['year']==2015){$year2015=" selected=\"selected\"";}else if ($_REQUEST['year']==2016){$year2016="  selected=\"selected\"";}
$menu.="<form action=\"?\" method=\"post\"><input type=\"hidden\" name=\"m\" value=\"qinfo\">Q <select name=\"q\">";
$menu.="<option value=\"1\" $q1>1</option><option value=\"2\" $q2>2</option><option value=\"3\" $q3>3</option><option value=\"4\" $q4>4</option></select>";
$menu.="<select name=\"year\"><option value=\"2014\" $year2014>2014</option><option value=\"2015\" $year2015>2015</option><option value=\"2016\" $year2016>2016</option></select>";
$menu.="<input type=\"submit\" value=\"Get report\"></form>";

ob_start();
var_dump($_REQUEST);
$info = ob_get_clean();
$info.="Any bug or improvement please send to Noom<br>";




// broadcastId  Country  project  campaign  broadcastName  broadcastDate  sendCount
// id  date  broadcastId  openCount  clickCount  compCount  hardBounceCount  softbounceCount  unsubscribeCount  feedbackCount


if (!$_REQUEST['m']){

	}

elseif ($_REQUEST['m']=="project"){
	$body.="<div><a href=\"?m=project_add\">Add project</a></div>";
	$qry="select * from $table3 ";
	//$body.= $qry;echo $qry;
	$body.="<p><h3>IMPORTANT : Please add only approved project only</h3></p>";
	$result = $conn->prepare($qry);
	$result->execute();
	if ($result!==false){
		foreach ($result as $row){
			$body.=$row['ProjectName']." ".$row['Campaign']." ".$row['date_start']." ".$row['date_end']." ".$row['status']." <br>";
			}
		}
	else {
		echo "Error";
		}
	}

elseif ($_REQUEST['m']=="project_add"){
	$body.="<form action=\"?\" method=\"post\">";
	$body.="<div><input type=\"hidden\" name=\"m\" value=\"project_add_submit\"></div>";
	$body.="<div>Project name <input name=\"project_name\"></div>";
	$body.="<div>Start date <input name=\"project_start\" ></div>";
	$body.="<div>End date <input name=\"project_end\"></div>";
	$body.="<div>Campaign area<select name=\"project_campaign\"></div>";
	foreach ($campaign as $key=>$val){
		$body.="<option value=\"$val\">$val</option>";
		}
	
	$body.="</select>";
	$body.="<div><input type=\"submit\" value=\"Add Project\"></div>";
	$body.="</form>";
	
	
	}	

elseif ($_REQUEST['m']=="project_add_submit"){
	// ProjectName  Campaign  date_start  date_end  status
	$qry="insert ignore into $table3 (ProjectName,Campaign,date_start,date_end,status) values ('".$_REQUEST['project_name']."','".$_REQUEST['project_campaign']."','".$_REQUEST['project_start']."','".$_REQUEST['project_end']."','Normal')";
	$result = $conn->prepare($qry);
	$result->execute();
	if (mysql_error()){
		$body.= mysql_error();
		$body.= "fail";
		}
	else {
		$body.="Project added. <a href=\"?m=project\">Go back to project list</a>";
		
		}
	}

else if ($_REQUEST['m']=="binfo"){
	$qry="select * from $table1 where country='' or project='' or campaign='' ";
	$result = $conn->prepare($qry);
	$result->execute();
// Show remaining rows to fix
	$body.= "There are ".$result->rowCount()." Rows not yet complete";
// Show mysql error if any
	if (mysql_error()){echo mysql_error();}
// Show table list title
	$body.="<div class=\"row-fluid\" >";
	$body.="<div class=\"span1\">broadcastId</div>";
	$body.="<div class=\"span1\">Country</div>";
	$body.="<div class=\"span1\">project</div>";
	$body.="<div class=\"span1\">campaign</div>";
	$body.="<div class=\"span5\">broadcastName</div>";
	$body.="<div class=\"span2\">broadcastDate</div>";
	$body.="<div class=\"span1\">sendCount</div>";
	$body.="</div>";
	if ($result !== false) {
	  	foreach($result as $row) {	
	  		$body.="<div class=\"row-fluid\" >";
			$body.="<div class=\"span1\"><a onclick=\"window.open('?m=bedit&bid=".$row['broadcastId']."','editor','width=800,height=600');\"> ".$row['broadcastId']."</a></div>";
			$body.="<div class=\"span1\">".$row['Country']."</div>";
			$body.="<div class=\"span1\">".$row['project']."</div>";
			$body.="<div class=\"span1\">".$row['campaign']."</div>";
			$body.="<div class=\"span5\">".$row['broadcastName']."</div>";
			$body.="<div class=\"span2\">".$row['broadcastDate']."</div>";
			$body.="<div class=\"span1\">".$row['sendCount']."</div>";
			$body.="</div>";
			
			}	
		}
	}	

else if ($_REQUEST['m']=="bedit"){
	$qry="select * from $table1 where broadcastId=".$_REQUEST['bid'];
	$result=$conn->prepare($qry);
	$result->execute();
	if ($result!=false){
		foreach ($result as $row){
			//Array ( [broadcastId] => 82147 [0] => 82147 [Country] => [1] => [broadcastName] => FUpgrade20150717 [2] => FUpgrade20150717 [broadcastDate] => 2015-07-15 [3] => 2015-07-15 [sendCount] => 121 [4] => 121 [project] => [5] => [campaign] => [6] => )  
			$body.="<h3>ID :<em>".$row['broadcastId']."</em></h3>";
			$body.="<h4>Name : <em>".$row['broadcastName']." </em> Send date : <em>".$row['broadcastDate']." </em> Send to : <em>".$row['sendCount']."<em> supporters</h4>";
			$body.="<form action=\"?\" method=\"post\">";
			$body.="<input type=\"hidden\" name=\"m\" value=\"bsave\">";
			$body.="<input type=\"hidden\" name=\"bid\" value=\"".$_REQUEST['bid']."  \">";
// #######################################################		
//  This part not yet finish	
			$body.="Country <select name=\"country\">";
			$body.="<option value=\"\"></option>";
			$body.="<option value=\"ID\">Indonesia</option>";
			$body.="<option value=\"PH\">Philippines</option>";
			$body.="<option value=\"TH\">Thailand</option>";
			$body.="<option value=\"SEA\">Regional development</option>";
			$body.="<option value=\"OTHER\">Other / unknown</option>";
			$body.="</select><br>";
			$body.="Campaign area <select name=\"campaign\">";
			foreach ($campaign as $key=>$val){
				$body.="<option value=\"$val\">$val</option>";
				}
			$body.="</select><br>";
			$body.="Project <select name=\"project\">$project_opt</select>";
			$body.="<input type=\"submit\" value=\"SAVE\">";
			$body.="</form>";
			}
		}

	}
	
else if ($_REQUEST['m']=="bsave"){
	$bid=$_REQUEST['bid'];
	$country=$_REQUEST['country'];
	$campaign=$_REQUEST['campaign'];
	$project=$_REQUEST['project'];
	$qry="update $table1 set country='$country', campaign='$campaign', project='$project' where broadcastId=$bid ";
	$result=$conn->prepare($qry);
	$result->execute();
	if ($result!=false){
		$body.="Success<script>self.setTimeout(window.close(), 3000);</script>";
		}
	else {print_r($result->errorInfo());}
	

	}
	
else if ($_REQUEST['m']=="qinfo"){
	if ($_REQUEST['q']==1){$start=$_REQUEST['year']."-01-01";$stop=$_REQUEST['year']."-03-31";}
	elseif ($_REQUEST['q']==2){$start=$_REQUEST['year']."-04-01";$stop=$_REQUEST['year']."-06-30";}
	elseif ($_REQUEST['q']==3){$start=$_REQUEST['year']."-07-01";$stop=$_REQUEST['year']."-09-30";}
	elseif ($_REQUEST['q']==4){$start=$_REQUEST['year']."-10-01";$stop=$_REQUEST['year']."-12-31";}
	
	
	$qry="select * from $table1 where  broadcastDate between '$start' and  '$stop' ";
//	echo $qry;
	$result = $conn->prepare($qry);
	$result->execute();
	$count=$result->rowcount();
	$body.="<div class=\"row-fluid\" id=\"datainfo\"><div class=\"span12\"> Report for Q ".$_REQUEST['q']." of ".$_REQUEST['year']." Got $count results</div></div>";
	$body.="<div class=\"row-fluid\" id=\"dataheader\">";        
	$body.="<div class=\"span1\">#</div><div class=\"span1\">ID</div><div class=\"span1\">broadcastDate</div><div class=\"span1\">Country</div><div class=\"span1\">Sent</div><div class=\"span1\">Open</div><div class=\"span1\">Click</div><div class=\"span1\">compCount</div><div class=\"span1\">hardBounceCount</div><div class=\"span1\">softbounceCount</div><div class=\"span1\">unsubscribeCount</div><div class=\"span1\">feedbackCount</div></tr>";
	$body.="</div>";
	$body.= $th=$th_open=$th_click=$th_hard=$th_soft=$th_unsub=$th_feedback=0;
	$body.= $ph=$ph_open=$ph_click=$ph_hard=$ph_soft=$ph_unsub=$ph_feedback=0;
	$body.= $id=$id_open=$id_click=$id_hard=$id_soft=$id_unsub=$id_feedback=0;
	$body.= $sea=$sea_open=$sea_click=$sea_hard=$sea_soft=$sea_unsub=$sea_feedback=0;
	$body.= $other=$other_open=$other_click=$other_hard=$other_soft=$other_unsub=$other_feedback=0;
	$body.= $test=$test_open=$test_click=$test_hard=$test_soft=$test_unsub=$test_feedback=0;

	if ($result !== false) {
		$i=1;
		
	  	foreach($result as $row) {	
// SELECT * FROM `en_history_eabroadcastdata` as X where (select count(*) from `en_history_eabroadcastdata` as Y where X.`broadcastId` = Y.`broadcastId` AND X.`date` < Y.`date`  ) = 0 and X.`broadcastid`=115425
	  		$qry="SELECT * FROM `en_history_eabroadcastdata` as X where (select count(*) from `en_history_eabroadcastdata` as Y where X.`broadcastId` = Y.`broadcastId` AND X.`date` < Y.`date`  ) = 0 and X.`broadcastid`=".$row['broadcastId']." limit 1" ;
	  		//$body.= $qry;
			$result2 = $conn->prepare($qry);
			$result2->execute();
			$row2=$result2->fetch();

	  		$sendCount=$row['sendCount'];
	  		$openCount=$row2['openCount'];	
	  		$clickCount=$row2['clickCount'];
	  		$compCount=$row2['compCount'];
	  		$hardBounceCount=$row2['hardBounceCount'];
	  		$softbounceCount=$row2['softbounceCount'];
	  		$unsubscribeCount=$row2['unsubscribeCount'];
	  		$feedbackCount=$row2['feedbackCount'];

	  		$total+=$row['sendCount'];
	  		$total_click+=$clickCount;
	  		$total_open+=$openCount;
	  		$total_comp+=$compCount;
	  		$total_hard+=$hardBounceCount;
	  		$total_soft+=$softbounceCount;
	  		$total_feedback+=$feedbackCount;
	  		$total_unsub+=$unsubscribeCount;

	  																	 					        								
	  		$body.= "<div class=\"row-fluid \" id=\"data$i\">";
	  		$body.="<div class=\"span1\">$i</div>";
	  		$body.="<div class=\"span1\"><a href=\"#\" title=\"".$row['broadcastName']."\">".$row['broadcastId']."</a> </div>";
	  		$body.="<div class=\"span1\" title=\"broadcastDate\">".$row['broadcastDate']."</div>";
	  		$body.="<div class=\"span1\" title=\"Country\">".$row['Country']."</div>";
	  		$body.="<div class=\"span1\" title=\"sendCount\">".$row['sendCount']." </div>";
	  		$body.="<div class=\"span1\" title=\"openCount ". @round($openCount/$sendCount*100,2)."%\">".$row2['openCount']." </div>";
	  		$body.="<div class=\"span1\" title=\"clickCount VS sendCount ".@round($clickCount/$sendCount*100,2)."% \nclickCount VS openCount ".@round($clickCount/$openCount*100,2)."% \">".$row2['clickCount']." </div>";
	  		$body.="<div class=\"span1\" title=\"compCount ". @round($compCount/$sendCount*100,2)."%\">".$row2['compCount']." </div>";
	  		$body.="<div class=\"span1\" title=\"hardBounceCount ". @round($hardBounceCount/$sendCount*100,2)."%\">".$row2['hardBounceCount']." </div>";
	  		$body.="<div class=\"span1\" title=\"softbounceCount ". @round($softbounceCount/$sendCount*100,2)."%\">".$row2['softbounceCount']." </div>";
	  		$body.="<div class=\"span1\" title=\"unsubscribeCount ". @round($unsubscribeCount/$sendCount*100,2)."%\">".$row2['unsubscribeCount']." </div>";
	  		$body.="<div class=\"span1\" title=\"feedbackCount ". @round($feedbackCount/$sendCount*100,2)."%\">".$row2['feedbackCount']." </div></div>";
/*	  		$total+=$row['sendCount'];
	  		$total_click+=$clickCount;$info.=" $total_click ";
	  		$total_open+=$openCount;
	  		$total_comp+=$compCount;
	  		$total_hard+=$hardBounceCount;
	  		$total_soft+=$softbounceCount;
	  		$total_feedback+=$feedbackCount;
	  		$total_unsub+=$unsubscribeCount;
*/	  		if ($row['Country']=="TH"){$th+=$row['sendCount'];$th_open+=$openCount;$th_click+=$clickCount;$th_hard+=$hardBounceCount;$th_soft+=$softbounceCount;$th_unsub+=$unsubscribeCount;$th_feedback+=$feedbackCount;}
	  		elseif ($row['Country']=="PH"){$ph+=$row['sendCount'];$ph_open+=$openCount;$ph_click+=$clickCount;$ph_hard+=$hardBounceCount;$ph_soft+=$softbounceCount;$ph_unsub+=$unsubscribeCount;$ph_feedback+=$feedbackCount;}
	  		elseif ($row['Country']=="ID"){$id+=$row['sendCount'];$id_open+=$openCount;$id_click+=$clickCount;$id_hard+=$hardBounceCount;$id_soft+=$softbounceCount;$id_unsub+=$unsubscribeCount;$id_feedback+=$feedbackCount;}
	  		elseif ($row['Country']=="SEA"){$sea+=$row['sendCount'];$sea_open+=$openCount;$sea_click+=$clickCount;$sea_hard+=$hardBounceCount;$sea_soft+=$softbounceCount;$sea_unsub+=$unsubscribeCount;$sea_feedback+=$feedbackCount;}
	  		elseif ($row['Country']==(strtolower("test"))){$test+=$row['sendCount'];$test_open+=$openCount;$test_click+=$clickCount;$test_hard+=$hardBounceCount;$test_soft+=$softbounceCount;$test_unsub+=$unsubscribeCount;$test_feedback+=$feedbackCount;}
	  		else {$other+=$row['sendCount'];$other_open+=$openCount;$other_click+=$clickCount;$other_hard+=$hardBounceCount;$other_soft+=$softbounceCount;$other_unsub+=$unsubscribeCount;$other_feedback+=$feedbackCount;}
	  		$i++;
	  		}
		}
	$body.="</div>";
	$body.="<div class=\"row-fluid\" id=\"datainfo\"><div class=\"span12\"> TotalSent - $total <br>Open - $total_open(".@round($total_open/$total*100,2)."%) ; Click - $total_click(".@round($total_click/$total*100,2)."%/".@round($total_click/$total_open*100,2)."%) ; Complain - $total_comp(".@round($total_comp/$total*100,2)."%) <br>Hard - $total_hard(".@round($total_hard/$total*100,2)."%) ; Soft - $total_soft(".@round($total_soft/$total*100,2)."%) ; Feedback - $total_feedback(".@round($total_feedback/$total*100,2)."%) ; Unsub - $total_unsub(".@round($total_unsub/$total*100,2)."%) ;</div></div>";
       
	$body.= "<div class=\"row-fluid\" id=\"dataheader\" style=\"background-color:#ddddee;\"><div class=\"span1\">Country</div><div class=\"span1\">Sent</div><div class=\"span1\">Open</div><div class=\"span1\">Click</div><div class=\"span1\">Hard B</div><div class=\"span1\">Soft B</div><div class=\"span1\">Unsubscribe</div><div class=\"span1\">Feedback</div></div>";	
	$body.= "<div class=\"row-fluid\" id=\"dataheaderTH\"><div class=\"span1\" title=\"Country\">TH</div><div class=\"span1\" title=\"sendCount\">$th</div><div class=\"span1\" title=\"Open ".@round($th_open/$th*100,2)."%\">$th_open</div><div class=\"span1\" title=\"Click/Sentent ".@round($th_click/$th*100,2)."\nClick/open ".@round($th_click/$th_open*100,2)."%\">$th_click</div><div class=\"span1\" title=\"Hard bounce ".@round($th_hard/$th*100,2)."%\">$th_hard</div><div class=\"span1\" title=\"Soft bounce ".@round($th_soft/$th*100,2)."%\">$th_soft</div><div class=\"span1\" title=\"Unsub ".@round($th_unsub/$th*100,2)."%\">$th_unsub</div><div class=\"span1\" title=\"Feedback ".@round($th_feedback/$th*100,2)."%\">$th_feedback</div></div>";
	$body.= "<div class=\"row-fluid\" id=\"dataheaderTH\"><div class=\"span1\" title=\"Country\">PH</div><div class=\"span1\" title=\"sendCount\">$ph</div><div class=\"span1\" title=\"Open ".@round($ph_open/$ph*100,2)."%\">$ph_open</div><div class=\"span1\" title=\"Click/Sentent ".@round($ph_click/$ph*100,2)."\nClick/open ".@round($ph_click/$ph_open*100,2)."%\">$ph_click</div><div class=\"span1\" title=\"Hard bounce ".@round($ph_hard/$ph*100,2)."%\">$ph_hard</div><div class=\"span1\" title=\"Soft bounce ".@round($ph_soft/$ph*100,2)."%\">$ph_soft</div><div class=\"span1\" title=\"Unsub ".@round($ph_unsub/$ph*100,2)."%\">$ph_unsub</div><div class=\"span1\" title=\"Feedback ".@round($ph_feedback/$ph*100,2)."%\">$ph_feedback</div></div>";
	$body.= "<div class=\"row-fluid\" id=\"dataheaderTH\"><div class=\"span1\" title=\"Country\">ID</div><div class=\"span1\" title=\"sendCount\">$id</div><div class=\"span1\" title=\"Open ".@round($id_open/$id*100,2)."%\">$id_open</div><div class=\"span1\" title=\"Click/Sentent ".@round($id_click/$id*100,2)."\nClick/open ".@round($id_click/$id_open*100,2)."%\">$id_click</div><div class=\"span1\" title=\"Hard bounce ".@round($id_hard/$id*100,2)."%\">$id_hard</div><div class=\"span1\" title=\"Soft bounce ".@round($id_soft/$id*100,2)."%\">$id_soft</div><div class=\"span1\" title=\"Unsub ".@round($id_unsub/$id*100,2)."%\">$id_unsub</div><div class=\"span1\" title=\"Feedback ".@round($id_feedback/$id*100,2)."%\">$id_feedback</div></div>";
	$body.= "<div class=\"row-fluid\" id=\"dataheaderTH\"><div class=\"span1\" title=\"Country\">SEA</div><div class=\"span1\" title=\"sendCount\">$sea</div><div class=\"span1\" title=\"Open ".@round($sea_open/$sea*100,2)."%\">$sea_open</div><div class=\"span1\" title=\"Click/Sentent ".@round($sea_click/$sea*100,2)."\nClick/open ".@round($sea_click/$sea_open*100,2)."%\">$sea_click</div><div class=\"span1\" title=\"Hard bounce ".@round($sea_hard/$sea*100,2)."%\">$sea_hard</div><div class=\"span1\" title=\"Soft bounce ".@round($sea_soft/$sea*100,2)."%\">$sea_soft</div><div class=\"span1\" title=\"Unsub ".@round($sea_unsub/$sea*100,2)."%\">$sea_unsub</div><div class=\"span1\" title=\"Feedback ".@round($sea_feedback/$sea*100,2)."%\">$sea_feedback</div></div>";
	$body.= "<div class=\"row-fluid\" id=\"dataheaderTH\"><div class=\"span1\" title=\"Country\">Other</div><div class=\"span1\" title=\"sendCount\">$other</div><div class=\"span1\" title=\"Open ".@round($other_open/$other*100,2)."%\">$other_open</div><div class=\"span1\" title=\"Click/Sentent ".@round($other_click/$other*100,2)."\nClick/open ".@round($other_click/$other_open*100,2)."%\">$other_click</div><div class=\"span1\" title=\"Hard bounce ".@round($other_hard/$other*100,2)."%\">$other_hard</div><div class=\"span1\" title=\"Soft bounce ".@round($other_soft/$other*100,2)."%\">$other_soft</div><div class=\"span1\" title=\"Unsub ".@round($other_unsub/$other*100,2)."%\">$other_unsub</div><div class=\"span1\" title=\"Feedback ".@round($other_feedback/$other*100,2)."%\">$other_feedback</div></div>";
	$body.= "<div class=\"row-fluid\" id=\"dataheaderTH\"><div class=\"span1\" title=\"Country\">Test</div><div class=\"span1\" title=\"sendCount\">$test</div><div class=\"span1\" title=\"Open ".@round($test_open/$test*100,2)."%\">$test_open</div><div class=\"span1\" title=\"Click/Sentent ".@round($test_click/$test*100,2)."\nClick/open ".@round($test_click/$test_open*100,2)."%\">$test_click</div><div class=\"span1\" title=\"Hard bounce ".@round($test_hard/$test*100,2)."%\">$test_hard</div><div class=\"span1\" title=\"Soft bounce ".@round($test_soft/$test*100,2)."%\">$test_soft</div><div class=\"span1\" title=\"Unsub ".@round($test_unsub/$test*100,2)."%\">$test_unsub</div><div class=\"span1\" title=\"Feedback ".@round($test_feedback/$test*100,2)."%\">$test_feedback</div></div>";


/*
	$body.= "PH - $ph<br>";
	$body.= "ID - $id<br>";
	$body.= "SEA - $sea<br>";
	$body.= "TEST - $test<br>";
	$body.= "OTHER - $other<br>";
*/	
	}






$template=file("template/index.html");
$count=count($template);
for ($i=0;$i<$count;$i++){
	$outt.=$template[$i];
	}



$rpl1="<!--menu-->";
$rpl2="<!--body-->";
$rpl3="<!--footer-->";
$rpl4="<!--info-->";

$outt=ereg_replace($rpl1,$menu,$outt);
$outt=ereg_replace($rpl2,$body,$outt);
$outt=ereg_replace($rpl3,$footer,$outt);
$outt=ereg_replace($rpl4,$info,$outt);

echo $outt;






?>
