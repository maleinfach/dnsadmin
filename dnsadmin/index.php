<?php
include "fn.php";
include "dns.php";

if(!isset($_SESSION["nzone"]))
$_SESSION["nzone"]=-1;

$lnZone = $_SESSION["nzone"];

if($lnZone>=0) 
{
	$lcZone 	= $GLOBALS["azonen"][$lnZone]["czone"];
	$lcServer = $GLOBALS["azonen"][$lnZone]["cserver"];
	$lcTyp	= $GLOBALS["azonen"][$lnZone]["ctyp"];
}
	
if(isset($_GET["fn"])) 
{
	$lcFn = strtolower($_GET["fn"]);
	switch($lcFn) 
	{
		case "setzone":
			$lnZone = $_POST["nzone"];
			break;

		case "del":
			$lcMessage  = "; DO NSUPDATE\n";
			$lcMessage .= "SERVER $lcServer\n";
			
			$lcRes = base64_decode($_GET["value"]);
			
			$lcMessage .= "UPDATE DELETE ".$_GET["host"]. " IN ".$_GET["typ"]." $lcRes\n";
			$lcMessage .= "SEND\n";

			$lhFile = fopen(GetVar("TEMP")."query.txt", "w+");
			fwrite($lhFile, $lcMessage);
			fclose($lhFile);

			exec(GetVar("NSUPDATE")." ".GetVar("TEMP")."query.txt", $laResult, $lnReturn);

			if($lnReturn>0)
			$lcError = "Der DNS Server war nicht glücklich. DNS Server Logfile prüfen.";
			else
			$lcErfolg = "Vorgang erfolgreich!";

			$lcMessage = str_replace("\n", "<br>", $lcMessage);
			break;

		case "add":
			$lcMessage  = "; DO NSUPDATE\n";
			$lcMessage .= "SERVER $lcServer\n";

			if($_GET["typ"]=="A")
			$lcMessage .= "UPDATE ADD ".$_POST["host"].".$lcZone 3600 IN A ".$_POST["adresse"]."\n";
				
			if($_GET["typ"]=="PTR")
			$lcMessage .= "UPDATE ADD ".$_POST["host"].".$lcZone 3600 PTR ".$_POST["adresse"]."\n";
				
			$lcMessage .= "SEND\n";

			$lhFile = fopen(GetVar("TEMP")."query.txt", "w+");
			fwrite($lhFile, $lcMessage);
			fclose($lhFile);

			exec(GetVar("NSUPDATE")." ".GetVar("TEMP")."query.txt", $laResult, $lnReturn);

			if($lnReturn>0)
			$lcError = "Der DNS Server war nicht glücklich. DNS Server Logfile prüfen.";
			else
			$lcErfolg = "Vorgang erfolgreich!";

			$lcMessage = str_replace("\n", "<br>", $lcMessage);
			break;
	}
}

if($lnZone>=0) {
	$lcZone 	= $GLOBALS["azonen"][$lnZone]["czone"];
	$lcServer = $GLOBALS["azonen"][$lnZone]["cserver"];
	$lcTyp	= $GLOBALS["azonen"][$lnZone]["ctyp"];
}
?>
<html>
<head>
<style type="text/css">
table {
	font-size: 16px;
	font-family: arial;
}

;
body {
	font-size: 18px;
	font-family: arial;
}
;
</style>
</head>
<body>
	<table align="center">
		<tr>
			<td valign='top'>DNS Zone Verwaltung -||-</td>
			<td valign='top'>Zone:</td>
			<td>
				<form method="post" action="?fn=setzone">
					<select style="width: 300px" name='nzone'>
					<?php
					for($li=0;$li<count($GLOBALS["azonen"]);$li++) {
						echo "<option ".($lnZone==$li?"selected":"")." value='$li'>".$GLOBALS["azonen"][$li]["czone"]."</option>;";
					}
					?>
					</select> <input type="submit" value="Zone laden">
				</form>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">Bitte immer drann denken auch Reverse
				DNS Einträge zu erstellen</td>
		</tr>
		<tr>
			<td align="center" colspan="3">
			<?php
			if($lnZone>=0) {
				echo "<b>--- ZONE ".$GLOBALS["azonen"][$lnZone]["czone"]." (NS: ".$GLOBALS["azonen"][$lnZone]["cserver"]." ) ---</b>";
			}
			?>
			</td>
		</tr>
	</table>
	<hr>
	<table>
		<tr>
			<td style="width: 240px;" valign="top">
			<?php
			if($lcTyp=="FW") {
				?> Neuer A Record</b><br>
				<form method="post" action="?fn=add&typ=A">
					<table>
						<tr>
							<td valign="top" style='width: 80px;'>Hostname:</td>
							<td><input name="host" value="" style='width: 130px;' type="text">
							</td>
						</tr>
						<tr>
							<td>Adresse:</td>
							<td><input name="adresse" value="" style='width: 130px;'
								type="text"></td>
						</tr>
						<tr>
							<td colspan="2"><input style='width: 180px;' type="submit"
								value="A Record erstellen"></td>
						</tr>
					</table>
				</form> <br>
			
			
			
			
<?php
 }
 
 if($lcTyp=="RR") { 
?>
Neuer PTR Record</b><br>
<form method="post" action="?fn=add&typ=PTR">
<table>
<tr><td valign="top" style='width:80px;'>Adresse:</td><td><input name="host" style='width:130px;' type="text"></td></tr>
<tr><td>FQDN:</td><td><input name="adresse" style='width:130px;' type="text"></td></tr>
<tr><td colspan="2"><input  style='width:180px;' type="submit" value="PTR Record erstellen"></td></tr>
</table>
</form>
<?php
 } 
?>
</td>
			<td>&nbsp;</td>
			<td valign="top">
			<?php
			if(isset($lcMessage)) {
				?>
				<table
					style='width: 600px; height: 50px; border-style: solid; border-width: 2px; border-color: #9999FF'>
					<tr>
						<td><?php echo $lcMessage; ?></td>
					</tr>
				</table> <br>
			
			
			
			
 	<?php
 }
 if(isset($lcError)) {
 	?>
 	<table style='width:600px;height:50px;border-style:solid;border-width:2px;border-color:#FFAAAA'><tr><td align="center"><b><?php echo $lcError; ?></b></td></tr></table><br>
 	<?php
 }
 if(isset($lcErfolg)) {
 	?>
 	<table style='width:600px;height:50px;border-style:solid;border-width:2px;border-color:#006600;background:#EEFFEE;'><tr><td align="center"><b><?php echo $lcErfolg; ?></b></td></tr></table><br>
 	<?php
 }
 
 if($lnZone>=0) {
 	LoadZone( $GLOBALS["azonen"][$lnZone]["czone"], $GLOBALS["azonen"][$lnZone]["cserver"] );
 } 
?>
</td>
		</tr>
	</table>
</body>
</html>

<?php
  $_SESSION["nzone"]=$lnZone; 
?>
