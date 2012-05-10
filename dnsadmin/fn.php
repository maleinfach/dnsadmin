<?php
	/*
	 * DNS Admin Config Datei
	 */
	$GLOBALS["azonen"] = array();
	
	function AddZone( $pcTyp, $pcZone, $pcDnsAddr = "127.0.0.1" ) {
		$laZonen = &$GLOBALS["azonen"];
		$lnZonen = count($laZonen);
		$laZonen[$lnZonen]["czone"] 	= $pcZone;
		$laZonen[$lnZonen]["cserver"]	= $pcDnsAddr;
		$laZonen[$lnZonen]["ctyp"]		= $pcTyp;
	}
	
	function SetVar( $pcVar, $pcValue ) {
		$GLOBALS[$pcVar]=$pcValue;
	}
	
	function GetVar( $pcVar ) {
		return $GLOBALS[$pcVar];
	}	
	
	include "config_inc.php";

	/*
	 * Globale Variablen
	 */
	session_start();
	
?>