<!DOCTYPE html>
<?php include "Header.php";?>
<?php
$Title = (string)"";
$Active = 2; /* Show Webpage Top Menu */
$Team1 = (integer)0;
$Team2 = (integer)0;
$Team1Player = Null;
$Team2Player = Null;
$Team1Prospect = Null;
$Team2Prospect = Null;
$Team1DraftPick = Null;
$Team2DraftPick = Null;		

If (file_exists($DatabaseFile) == false){
	$LeagueName = $DatabaseNotFound;
	$LeagueOutputOption = Null;
	echo "<title>" . $DatabaseNotFound . "</title>";
	$Title = $DatabaseNotFound;
}else{
	$LeagueName = (string)"";
	if(isset($_GET['Team1'])){$Team1 = filter_var($_GET['Team1'], FILTER_SANITIZE_NUMBER_INT);}
	if(isset($_GET['Team2'])){$Team2 = filter_var($_GET['Team2'], FILTER_SANITIZE_NUMBER_INT);}

	$db = new SQLite3($DatabaseFile);
	
	$Query = "Select Name, TradeDeadLine from LeagueGeneral";
	$LeagueGeneral = $db->querySingle($Query,true);		
	$LeagueName = $LeagueGeneral['Name'];
	$Title = $TradeLang['Trade'];
	
	If ($Team1 == 0 or $Team2 == 0 or $Team1 == $Team2){
		echo "<style>#Trade{display:none}</style>";
	}else{
		$Query = "SELECT Number, Name FROM TeamProInfo Where Number = " . $Team1;
		$Team1Info =  $db->querySingle($Query,true);	
		$Query = "SELECT Number, Name FROM TeamProInfo Where Number = " . $Team2;
		$Team2Info =  $db->querySingle($Query,true);			
		
		$Query = "SELECT MainTable.* FROM (SELECT PlayerInfo.Number, PlayerInfo.Name,PlayerInfo.AvailableForTrade FROM PlayerInfo WHERE Team = " . $Team1 . " AND Number > 0 UNION ALL SELECT (GoalerInfo.Number + 10000), GoalerInfo.Name, GoalerInfo.AvailableForTrade FROM GoalerInfo WHERE Team = " . $Team1 . " AND Number > 0) AS MainTable ORDER BY MainTable.Name ASC";
		$Team1Player = $db->query($Query);
		$Query = "SELECT MainTable.* FROM (SELECT PlayerInfo.Number, PlayerInfo.Name,PlayerInfo.AvailableForTrade FROM PlayerInfo WHERE Team = " . $Team2 . " AND Number > 0 UNION ALL SELECT (GoalerInfo.Number + 10000), GoalerInfo.Name, GoalerInfo.AvailableForTrade FROM GoalerInfo WHERE Team = " . $Team2 . " AND Number > 0) AS MainTable ORDER BY MainTable.Name ASC";
		$Team2Player = $db->query($Query);	
		
		$Query = "SELECT Prospects.* FROM Prospects WHERE TeamNumber = " . $Team1 . " ORDER By Name ASC";
		$Team1Prospect = $db->query($Query);
		$Query = "SELECT Prospects.* FROM Prospects WHERE TeamNumber = " . $Team2 . " ORDER By Name ASC";
		$Team2Prospect = $db->query($Query);		
		
		/* Look at Condition Trade */
		$Query = "SELECT * FROM DraftPick WHERE ConditionalTrade = '' AND TeamNumber = " . $Team1 . " ORDER BY Year, Round, FromTeamAbbre";
		$Team1DraftPick = $db->query($Query);
		$Query = "SELECT * FROM DraftPick WHERE ConditionalTrade = '' AND TeamNumber = " . $Team2 . " ORDER BY Year, Round, FromTeamAbbre";
		$Team2DraftPick = $db->query($Query);		
	}

	echo "<title>" . $LeagueName . " - " . $TradeLang['Trade']  . "</title>";
}?>
</head><body>
<?php include "Menu.php";?>
<?php echo "<h1>" . $Title . "</h1>"; ?>
<br />
<div style="width:99%;margin:auto;">

<form id="Trade" name="Trade" method="post" action="TradeConfirm.php<?php If ($lang == "fr" ){echo "?Lang=fr";}?>">
	<input type="hidden" id="Team1" name="Team1" value="<?php echo $Team1;?>">
	<input type="hidden" id="Team2" name="Team2" value="<?php echo $Team2;?>">
	<input type="hidden" id="Confirm" name="Confirm" value="NO">
	<table class="STHSTableFullW">
	<tr>
		<td class="STHSPHPTradeTeamName"><?php echo $Team1Info['Name']?></td>
		<td class="STHSPHPTradeTeamName"><?php echo $Team2Info['Name']?></td>
	</tr>
	
	
	<tr><td colspan="2" class="STHSPHPTradeType"><hr /><?php echo $TradeLang['Players']?></td></tr>
	<tr>
	<td><select id="Team1Player" name="Team1Player[]"  multiple="multiple">
	<?php
	if (empty($Team1Player) == false){while ($Row = $Team1Player ->fetchArray()) { 
		echo "<option value=\"" . $Row['Number'] . "\">" . $Row['Name'] . "</option>";
	}}?>
	</select></td>
	<td><select id="Team2Player" name="Team2Player[]" multiple="multiple">
	<?php
	if (empty($Team2Player) == false){while ($Row = $Team2Player ->fetchArray()) { 
		echo "<option value=\"" . $Row['Number'] . "\">" . $Row['Name'] . "</option>";
	}}?>
	</select></td>
	</tr>
	
	<tr><td colspan="2" class="STHSPHPTradeType"><hr /><?php echo $TradeLang['Prospects']?></td></tr>
	<tr>
	<td><select id="Team1Prospect" name="Team1Prospect[]"  multiple="multiple">
	<?php
	if (empty($Team1Prospect) == false){while ($Row = $Team1Prospect ->fetchArray()) { 
		echo "<option value=\"" . $Row['Number'] . "\">" . $Row['Name'] . "</option>";
	}}?>
	</select></td>
	<td><select id="Team2Prospect" name="Team2Prospect[]" multiple="multiple">
	<?php
	if (empty($Team2Prospect) == false){while ($Row = $Team2Prospect ->fetchArray()) { 
		echo "<option value=\"" . $Row['Number'] . "\">" . $Row['Name'] . "</option>";
	}}?>
	</select></td>
	</tr>
	
	<tr><td colspan="2" class="STHSPHPTradeType"><hr /><?php echo $TradeLang['DraftPicks']?></td></tr>
	<tr>
	<td><select id="Team1DraftPick" name="Team1DraftPick[]"  multiple="multiple">
	<?php
	if (empty($Team1DraftPick) == false){while ($Row = $Team1DraftPick ->fetchArray()) { 
		echo "<option value=\"" . $Row['InternalNumber'] . "\">Y:" . $Row['Year'] . "-RND:" . $Row['Round'] . "-" . $Row['FromTeamAbbre'] . "</option>";
	}}?>
	</select></td>
	<td><select id="Team2DraftPick" name="Team2DraftPick[]" multiple="multiple">
	<?php
	if (empty($Team2DraftPick) == false){while ($Row = $Team2DraftPick ->fetchArray()) { 
		echo "<option value=\"" . $Row['InternalNumber'] . "\">Y:" . $Row['Year'] . "-RND:" . $Row['Round'] . "-" . $Row['FromTeamAbbre'] . "</option>";
	}}?>
	</select></td>
	</tr>	
	
	<tr><td colspan="2" class="STHSPHPTradeType"><hr /><?php echo $TradeLang['Money']?></td></tr>
	<tr>
	<td class="STHSPHPTradeType"><input type="number" name="Team1Money" size="20" value="0"></td>
	<td class="STHSPHPTradeType"><input type="number" name="Team2Money" size="20" value="0"></td>
	</tr>
	
	<tr><td colspan="2" class="STHSPHPTradeType"><hr /><?php echo $TradeLang['SalaryCap']?></td></tr>
	<tr>
	<td class="STHSPHPTradeType"><input type="number" name="Team1SalaryCap" size="20" value="0"></td>
	<td class="STHSPHPTradeType"><input type="number" name="Team2SalaryCap" size="20" value="0"></td>
	</tr>
	
	<tr>
      <td colspan="2" class="STHSPHPTradeType"><input class="SubmitButton" type="submit" name="Submit" value="Submit" /></td>
    </tr>
	</table>
</form>
<br />


<?php
If ($Team1 == 0 or $Team2 == 0 or $Team1 == $Team2){
	echo "<div class=\"STHSCenter\">";
	echo "<form action=\"Trade.php\" method=\"get\">";
	If ($lang == "fr"){echo "<input type=\"hidden\" name=\"Lang\" value=\"fr\">";}
	echo "<table class=\"STHSTableFullW\"><tr>";
	echo "<th class=\"STHSPHPTradeType STHSW250\">" . $TradeLang['Team1'] . "</th><th class=\"STHSPHPTradeType STHSW250\">" . $TradeLang['Team2'] . "</th></tr><tr>";
	echo "<td><select name=\"Team1\" class=\"STHSW250\"><option selected value=\"\"></option>";
	$Query = "SELECT Number, Name FROM TeamProInfo Order By Name";
	$TeamName = $db->query($Query);	
	if (empty($TeamName) == false){while ($Row = $TeamName ->fetchArray()) {
		echo "<option value=\"" . $Row['Number'] . "\">" . $Row['Name'] . "</option>"; 
	}}
	echo "</select></td><td>";
	
	echo "<select name=\"Team2\" class=\"STHSW250\"><option selected value=\"\"></option>";
	$Query = "SELECT Number, Name FROM TeamProInfo Order By Name";
	$TeamName = $db->query($Query);	
	if (empty($TeamName) == false){while ($Row = $TeamName ->fetchArray()) {
		echo "<option value=\"" . $Row['Number'] . "\">" . $Row['Name'] . "</option>"; 
	}}
	echo "</select></td></tr>";
	echo "<tr><td colspan=\"2\" class=\"STHSCenter\"><br /><input class=\"SubmitButton\" type=\"submit\" value=\"" . $SearchLang['Submit'] . "\"></td></tr>";
	echo "<tr><td colspan=\"2\" class=\"STHSPHPTradeType \"><a href=\"TradeOtherTeam.php\">" . $TradeLang['ConfirmTradeAlreadyEnter'] . "</a></td></tr>";
	echo "</table></form></div>";
	
	
	
}else{
	echo "<script type=\"text/javascript\">";
	echo "$('#Team1Player').multiSelect();";
	echo "$('#Team2Player').multiSelect();";
	echo "$('#Team1Prospect').multiSelect();";
	echo "$('#Team2Prospect').multiSelect();";	
	echo "$('#Team1DraftPick').multiSelect();";
	echo "$('#Team2DraftPick').multiSelect();";		
	echo "</script>";
}
?>

</div>

<?php include "Footer.php";?>
