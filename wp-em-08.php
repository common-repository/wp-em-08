<?php
/*
Plugin Name: WP Euro 2008 
Plugin URI: http://www.svenkubiak.de/wordpress-em-2008/
Description: Displays information and results of the EURO 2008
Version: 1.16
Author: Sven Kubiak
Author URI: http://www.svenkubiak.de

Copyright 2008 Sven Kubiak

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
class EM08
{
	function em08()
	{	
		if (function_exists('load_plugin_textdomain'))
			load_plugin_textdomain('wp-em-08', PLUGINDIR.'/wp-em-08');
	
		if (phpversion() < 5){
			add_action('admin_notices', array(&$this, 'php5Required'));	
			return;		
		}
		
		if (!class_exists('SoapClient')){
			add_action('admin_notices', array(&$this, 'soapRequired'));	
			return;			
		}		

		add_option('em_lastchecked', 0, '', 'yes');
		add_option('em_nextmatch', 0, '', 'yes');
		add_option('em_currentmatch1', 0, '', 'yes');
		add_option('em_currentmatch2', 0, '', 'yes');
		add_option('em_currentmatch3', 0, '', 'yes');
		add_option('em_currentmatch4', 0, '', 'yes');								
		
		add_action('deactivate_wp-em-08/wp-em-08.php', array(&$this, 'deactivate'));
		add_action('wp_footer', array(&$this, 'checkUpdate'));		
	}
	
	function deactivate()
	{
		delete_option('em_lastchecked');
		delete_option('em_nextmatch');
		delete_option('em_currentmatch1');
		delete_option('em_currentmatch2');
		delete_option('em_currentmatch3');
		delete_option('em_currentmatch4');
	}

	function php5Required()
	{
		echo "<div class='plugin-update'>".__('WP Euro 2008 requires min. PHP 5. Your Webserver is running','wp-em-08')." ".phpversion().".</div>\n";
	}
	
	function soapRequired()
	{
		echo "<div class='plugin-update'>".__('WP Euro 2008 could not find the required PHP-SOAP-Extension','wp-em-08').".</div>\n";	
	}	
	
	function checkUpdate()
	{	
		$current = time();
			
		if ($current > 1215126000)
			return;
			 
		if ($current > (get_option('em_lastchecked') + 300)){
			try {
				$client = new SoapClient("http://www.OpenLigaDB.de/Webservices/Sportsdata.asmx?WSDL", array("connection_timeout" => 5));
				
				$params->leagueShortcut = 'fem08';
				$result = $client->GetNextMatch($params);				
				update_option('em_nextmatch',serialize($result));
				
				$params->groupOrderID = '1';
				$params->leagueSaison  = '2008';
				
				$result = $client->GetMatchdataByGroupLeagueSaison($params);	
				update_option('em_currentmatch1',serialize($result));
				
				$params->groupOrderID = '2';
				$result = $client->GetMatchdataByGroupLeagueSaison($params);	
				update_option('em_currentmatch2',serialize($result));
				
				$params->groupOrderID = '3';
				$result = $client->GetMatchdataByGroupLeagueSaison($params);	
				update_option('em_currentmatch3',serialize($result));
						
				$params->groupOrderID = '4';
				$result = $client->GetMatchdataByGroupLeagueSaison($params);	
				update_option('em_currentmatch4',serialize($result));	
				
				update_option('em_lastchecked',$current);
			}		
			catch (Exception $e) {return;}	
		}
	}	
	
	function currentMatch($round)
	{	
		switch($round)
		{
			case 1:
				$data = unserialize(get_option('em_currentmatch1'));
			break;
			case 2:
				$data = unserialize(get_option('em_currentmatch2'));
			break;
			case 3:
				$data = unserialize(get_option('em_currentmatch3'));
			break;	
			case 4:
				$data = unserialize(get_option('em_currentmatch4'));
			break;				
			default:
				$data = unserialize(get_option('em_currentmatch1'));					
		}
				
		$matchdata = $data->GetMatchdataByGroupLeagueSaisonResult;
		
		if(empty($matchdata))
			return;
			
		$first = true;			

		foreach ($matchdata as $matches){
			foreach ($matches as $match){
			
				$gmtdiff = get_option('gmt_offset');
		
				if ($gmtdiff < 0){
					$gmtdiff--;
					$matchdategmt = (strtotime($match->matchDateTime) - 7200) - (($gmtdiff*-1) * 60 * 60);
				}
				else if ($gmtdiff > 0){
					$gmtdiff++;
					$matchdategmt = (strtotime($match->matchDateTime) - 10800) + ($gmtdiff * 60 * 60);
				}
				else{
					$matchdategmt = strtotime($match->matchDateTime);	
				}
					
				if (time() >= $matchdategmt && (time() < ($matchdategmt + 10800))){
					if ($first === true){
						echo "<b>LIVE:</b>";
						$first = false;									
					}
					$team1icon 	= $match->iconUrlTeam1;
					$team2icon 	= $match->iconUrlTeam2;
					$team1 		= EM08::getTeamName(trim($match->nameTeam1));
					$team2 		= EM08::getTeamName(trim($match->nameTeam2));
					echo "<table cellpadding=0 cellspacing=2>";			
					echo "<tr>";
					echo "<td>";	
					echo "<img src=".$team1icon." alt=".$team1." /> ".$team1;
					echo " - ";
					echo "<img src=".$team2icon." alt=".$team2." /> ".$team2;
					echo "</td>";
					echo "<td>&nbsp;";
					if ($match->matchIsFinished == false){
						echo "<font color=red>";
					}
					if ($match->pointsTeam1 == -1){
						echo "0";
					}else{
						echo $match->pointsTeam1;
					}
					echo " : ";
					if ($match->pointsTeam2 == -1){
						echo "0";
					}else{
						echo $match->pointsTeam2;
					}
					if ($match->matchIsFinished == false){
						echo "</font>";
					}
					echo "</td>";
					echo "</tr>";
					echo "</table>";
				}
			}
		}
		if ($first === false){
			echo __('Last updated','wp-em-08').": ".gmdate("H:i:s",get_option('em_lastchecked')+(get_option('gmt_offset')*60*60));
		}
	}	

	function nextMatch()
	{	
		$nextmatch = unserialize(get_option('em_nextmatch'));
		
		if(empty($nextmatch))
			return;
		
		$gmtdiff = get_option('gmt_offset');

		if ($gmtdiff < 0){
			$gmtdiff--;
			$matchdategmt = (strtotime($nextmatch->GetNextMatchResult->matchDateTime) - 7200) - (($gmtdiff*-1) * 60 * 60);
		}
		else if ($gmtdiff > 0){
			$gmtdiff++;
			$matchdategmt = (strtotime($nextmatch->GetNextMatchResult->matchDateTime) - 10800) + ($gmtdiff * 60 * 60);
		}
		else{
			$matchdategmt = strtotime($nextmatch->GetNextMatchResult->matchDateTime);	
		}
					
		$matchdate 	= date("d.m.Y, H:i",$matchdategmt);
		$team1icon 	= $nextmatch->GetNextMatchResult->iconUrlTeam1;
		$team2icon 	= $nextmatch->GetNextMatchResult->iconUrlTeam2;
		$team1 		= EM08::getTeamName(trim($nextmatch->GetNextMatchResult->nameTeam1));
		$team2 		= EM08::getTeamName(trim($nextmatch->GetNextMatchResult->nameTeam2));

		echo __('Next game','wp-em-08').": ".$matchdate."<br />";
		echo "<img src=".$team1icon." alt=".$team1." /> ".$team1;
		echo " - ";
		echo "<img src=".$team2icon." alt=".$team2." /> ".$team2;		
	}	
	
	function getTeamName($team)
	{
		$team = md5($team);
		
		switch ($team)
		{
			case "3c2f8b8c8c43538b25a0327ae84c3f65":
				return __('Germany','wp-em-08');
			break;
			case "7a0be5a1ed9675975e678b517f07573c":
				return __('Switzerland','wp-em-08');
			break;
			case "8ce8318dedb8a09cd4009a4cd9774bfc":
				return __('Czech Republic','wp-em-08');
			break;
			case "ea71b362e3ea9969db085abfccdeb10d":
				return __('Portugal','wp-em-08');
			break;
			case "3795c072c57f91f60498888469461cc0":
				return __('Turkey','wp-em-08');
			break;
			case "b472f5868425ac2bc44e693fc3267e87":
				return __('Austria','wp-em-08');
			break;
			case "ee09f953e2cce4a1e7a2f5b288fee2f4":
				return __('Croatia','wp-em-08');
			break;
			case "c5d7f540c8bc591b128caf6c923206f8":
				return __('Poland','wp-em-08');
			break;
			case "e5e72ce64abf0b0be68d544840ab0cbf":
				return __('Romania','wp-em-08');
			break;
			case "8d865b652dc83da05aa34b499a9b01be":
				return __('France','wp-em-08');
			break;
			case "76ff6cc9752a8ba82a9a86d4ff9245a7":
				return __('Netherlands','wp-em-08');
			break;
			case "389962c9dec540ab28d04517e82c3996":
				return __('Italy','wp-em-08');
			break;
			case "5ea5f4002712b1a9a4e292d3c83736b9":
				return __('Spain','wp-em-08');
			break;
			case "65f7b8e17e93bc2a87843d99e82c08a5":
				return __('Russia','wp-em-08');
			break;
			case "ff8b6a7eb9b23e2fee8a59bad1a6164c":
				return __('Sweden','wp-em-08');
			break;
			case "9bbe5cef5dacc0b4666c2a1d1a8b5615":
				return __('Greece','wp-em-08');
			break;			
			default:
				return;			
		}
	}
}
//initalize class
if (class_exists('EM08'))
	$em08 = new EM08;	
?>