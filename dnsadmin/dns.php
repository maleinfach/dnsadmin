<?php
class clSOA
{
  var $m_cNSMaster;
  var $m_cZoneAdmin;
  var $m_nSerial;
  var $m_nRefresh;
  var $m_nRetry;
  var $m_nExpire;
  var $m_nNegTTL;
}

class clRessource
{
  var $lcZone;
  var $lnTTl;
  var $lcType;
  var $loData;
}

function Comment(&$pcRecord) {
  $lcParse = $pcRecord;
  $lcComment = Rx($lcParse,";.*");
  if($lcComment)
  {
    $pcRecord = $lcParse;
    return true;
  }
  return false;
}

function Rx(&$pcParse,$pcPattern,$pbRemoveSpace=true) {
  $lnMatch = preg_match("/^$pcPattern/", $pcParse, $laResult );
  if($lnMatch>0)
  {
    $lcResult = $laResult[0];
    if($pbRemoveSpace)
    {
      $pcParse = substr($pcParse,strlen($lcResult));
      $lnMatch = preg_match("/^\s*/", $pcParse, $laResult);
      if($lnMatch>0)
      {
        $lcSpace = $laResult[0];
        $pcParse = substr($pcParse,strlen($lcSpace));
      }
    }
    return $lcResult;
  }
  return null;
}

function Type(&$pcParse) {
  $lcParse = $pcParse;
  $lcFamily = Rx($lcParse,"IN");
  $lcType = Rx($lcParse,"(SOA|AAAA|A|NS|MX|CNAME|TXT|PTR|SPF|SRV|NAPTR)");
  if($lcType)
  {
    $pcParse = $lcParse;
    return $lcType;
  }
  return false;
}

function Zone(&$pcParse) {
  $lcParse = $pcParse;
  $lcZone = Rx($lcParse, "[a-zA-Z0-9\\.\\-\\:\\_]+");
  if($lcZone!=null)
  {
    $pcParse = $lcParse;
    return $lcZone;
  }
  return null;
}

function TTL(&$pcParse) {
  $lcParse = $pcParse;
  $lcTTL = Rx($lcParse,"[0-9]+");
  if($lcTTL!=null)
  {
    $pcParse = $lcParse;
    return $lcTTL;
  }
  return false;
}

function SOA(&$pcParse) {
  $lcParse = $pcParse;
  $lcNSMaster = Zone($lcParse);
  if($lcNSMaster!=null)
  {
    $lcZoneAdmin = Zone($lcParse);
    if($lcZoneAdmin)
    {
      $lcSerial = Rx($lcParse,"[0-9]+");
      if($lcSerial!=null)
      {
        $lcRefresh = Rx($lcParse,"[0-9]+");
        if($lcRefresh!=null)
        {
          $lcRetry = Rx($lcParse,"[0-9]+");
          if($lcRetry!=null)
          {
            $lcExpire = Rx($lcParse,"[0-9]+");
            if($lcExpire!=null)
            {
              $lcNegTTL = Rx($lcParse,"[0-9]+");
              if($lcNegTTL!=null)
              {
                $pcParse = $lcParse;
                $loSoa = new clSOA();
                $loSoa->m_cNSMaster = $lcNSMaster;
                $loSoa->m_cZoneAdmin = $lcZoneAdmin;
                $loSoa->m_nSerial = $lcSerial*1;
                $loSoa->m_nRefresh = $lcRefresh*1;
                $loSoa->m_nRetry = $lcRetry*1;
                $loSoa->m_nExpire = $lcExpire*1;
                $loSoa->m_nNegTTL = $lcNegTTL*1;
                return $loSoa;
              }
            }
          }
        }
      }
    }
  }
  return false;
}

function NS(&$pcParse) {
  $lcParse = $pcParse;
  $lcHost = Zone($lcParse);
  if($lcHost!=null)
  {
    $pcParse = $lcParse;
    return $lcHost;
  }
  return false;
}

function A(&$pcParse) {
  $lcParse = $pcParse;
  $lcHost = Zone($lcParse);
  if($lcHost!=null)
  {
    $pcParse = $lcParse;
    return $lcHost;
  }
  return false;
}

function AAAA(&$pcParse) {
  $lcParse = $pcParse;
  $lcHost = Zone($lcParse);
  if($lcHost!=null)
  {
    $pcParse = $lcParse;
    return $lcHost;
  }
  return false;
}

function CNAME(&$pcParse) {
  $lcParse = $pcParse;
  $lcHost = Zone($lcParse);
  if($lcHost!=null)
  {
    $pcParse = $lcParse;
    return $lcHost;
  }
  return false;
}

function PTR(&$pcParse) {
  $lcParse = $pcParse;
  $lcHost = Zone($lcParse);
  if($lcHost!=null) {
    $pcParse = $lcParse;
    return $lcHost;
  }
  return false;
}

function TXT(&$pcParse) {
  $lcParse = $pcParse;
  $pcParse = "";
  return $lcParse;
}

function MX(&$pcParse) {
  $lcParse = $pcParse;
  $lcPrio = Rx($lcParse,"[0-9]+");
  if($lcPrio!=null)
  {
    $lcHost = Zone($lcParse);
    if($lcHost)
    {
      $pcParse = $lcParse;
      return $lcHost;
    }
  }
  return false;
}

function SPF(&$pcParse) {
  return TXT($pcParse);
}

function NAPTR(&$pcParse) {
  return TXT($pcParse);
}

function SRV(&$pcParse) {
 return TXT($pcParse);
}

function Ressource(&$pcParse) {
  $lcParse = $pcParse;
  $lcZone = Zone($lcParse);
  if($lcZone)
  {
    $lcTTL = TTL($lcParse);
    if($lcTTL)
    {
      $lcType = Type($lcParse);
      switch($lcType)
      {
        case "SOA":
        $loRessource = SOA($lcParse);
        break;
        case "NS":
        $loRessource = NS($lcParse);
        break;
        case "A":
        $loRessource = A($lcParse);
        break;
        case "AAAA":
        $loRessource = AAAA($lcParse);
        break;
        case "CNAME":
        $loRessource = CNAME($lcParse);
        break;
        case "PTR":
        $loRessource = PTR($lcParse);
        break;
        case "TXT":
        $loRessource = TXT($lcParse);
        break;
        case "MX":
        $loRessource = MX($lcParse);
        break;
        case "NAPTR":
        $loRessource = NAPTR($lcParse);
        break;
        case "SPF":
        $loRessource = SPF($lcParse);
        break;
        case "SRV":
        $loRessource = SRV($lcParse);
        break;
        default:
        return false;
      }
      $pcParse = $lcParse;
      $loRes = new clRessource();
      $loRes->m_oRessource = $loRessource;
      $loRes->m_cType = $lcType;
      $loRes->m_cTTL = $lcTTL;
      $loRes->m_cZone = $lcZone;
      return $loRes;
    }
  }
  return false;
}

class clRecord {
  var $m_nType;
  var $m_oRessource;
}

function Record(&$pcRecord) {
  $loRecord = new clRecord();
  if(!$pcRecord)
  {
    $loRecord->m_nType = 0;
    return $loRecord;
  }
  else
  {
    if($loComment = Comment($pcRecord))
    {
      $loRecord->m_nType = 1;
      return $loRecord;
    }
    else
    {
      if($loRessource = Ressource($pcRecord))
      {
        $loRecord->m_nType = 2;
        $loRecord->m_oRessource = $loRessource;
        return $loRecord;
      }
    }
  }
  return false;
}

function ParseRecords($paParse,&$paRecords) {
  $lnRecord = 0;
  $paRecords = array();
  for($i=0;$i<count($paParse);$i++)
  {
    $lcParse = $paParse[$i];
    $loRecord = Record($lcParse);
    if($lcParse)
    {
      echo "FEHLER AT $lcParse IN LINE ".$paParse[$i]."<br>\n";
      $lcParse = $paParse[$i];
      Record($lcParse);
    }
    else
    {
      $paRecords[$lnRecord] = $loRecord;
      $lnRecord++;
    }
  }
}

function LoadZone(
$pcZone,
$pcServer ) {
	//echo "Lade Zone $pcZone von Server $pcServer";
	$lbSoa = false;

	exec(GetVar("DIG")." @$pcServer $pcZone AXFR", $laResult);
	echo "<table style='width:600px;font-size:12px;'>";
	echo "<tr><td style='width:200px;'><b>Host</b></td><td style='width:30px;'><b>TTL</b></td><td style='width:50px;'><b>Typ</b></td><td><b>Ziel</b></td></tr>";
	ParseRecords($laResult,$laRecords);
	for($li=0;$li<count($laRecords);$li++) {
		if($li % 2 == 1) {
			$lcColor = "#CECEF6";
		} else {
			$lcColor = "#EFEFFB";
		}
		$loRecord = $laRecords[$li];
		if( $loRecord->m_nType == 2 ) {
			$loRess = $loRecord->m_oRessource;
			$lcHost = str_replace($pcZone, "", $loRess->m_cZone);
			if(!empty($lcHost)) {
				$lcHost = substr($lcHost,0,strlen($lcHost)-1);
				$lcHost = "<b><font color='red'>$lcHost</font></b>";
			}
			else  {
				//$lcHost = "$pcZone";
			}

			$lcTTL		= "";
			$lnSeconds	= (int)($loRess->m_cTTL*1);
			$lnMinutes	= (int)($lnSeconds / 60);
			if($lnMinutes>0) {
				$lnSeconds = $lnSeconds % 60;
				$lnHours = (int)($lnMinutes / 60);
				if($lnHours>0) {
					$lnMinutes = $lnMinutes % 60;
				}
			}

			if($lnHours>0)
			$lcTTL.=str_pad("$lnHours",2,"0",STR_PAD_LEFT)."h";
				
			if($lnMinutes>0)
			$lcTTL.=str_pad("$lnMinutes",2,"0",STR_PAD_LEFT)."m";

			if($lnSeconds>0)
			$lcTTL.=str_pad("$lnSeconds",2,"0",STR_PAD_LEFT)."s";

			switch($loRess->m_cType)
			{
				case "SOA":
				if(!$lbSoa) {
					echo "<tr><td style='background:#F5D0A9;' colspan='4'><b>SOA (Ressource Record) Serial: ".$loRess->m_oRessource->m_nSerial."</b><br><br></td></tr>";
						
					$lbSoa = true;
				}
				break;
				default:
				echo "<tr><td style='background:$lcColor'>";
				echo "<a href='?fn=del&host=$loRess->m_cZone&typ=$loRess->m_cType&value=".base64_encode($loRess->m_oRessource)."'>[del]</a>";
				echo "&nbsp;&nbsp;&nbsp;$lcHost</td><td style='background:$lcColor'>$lcTTL</td><td style='background:$lcColor'>$loRess->m_cType</td><td style='background:$lcColor'>";				
				$loRes = str_replace($pcZone, "", $loRess->m_oRessource);
				echo $loRess->m_oRessource;
				break;
			}
			echo "</td></tr>";
		}
	}
	echo "</table>";
}
?>
