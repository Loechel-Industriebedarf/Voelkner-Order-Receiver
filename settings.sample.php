<?php
	$api_url = "https://rein-prod.mirakl.net/api";
	$api_key = "";
	
	$csvPath = "..\voelknerOrder.csv";
	$last_execution = file_get_contents('last.txt');
	
	date_default_timezone_set("Europe/Berlin");