<?php
//TODO: Better error/success messages

require 'vendor/autoload.php';
require_once 'settings.php';

use Mirakl\Core\Domain\Collection\DocumentCollection;
use Mirakl\Core\Domain\Document;
use Mirakl\MMP\Shop\Client\ShopApiClient;
use Mirakl\MMP\Shop\Request\Order\Document\UploadOrdersDocumentsRequest;

try {
	$directory = '../PDF-OrderNumber-Converter/pdf/';
	
	//Cycle throught files
	$filepaths = array();
	$filenames = array();
	$ordernumbers = array();
	foreach (scandir($directory) as $file) {
		if ($file !== '.' && $file !== '..') {
			$filepaths[] = $directory . $file;
			$filenames[] = $file;
			$ordernumbers[] = str_replace('.pdf', '', $file);
		}
	}
	/*
	echo "<pre>";
	var_dump($filepaths);
	var_dump($filenames);
	var_dump($ordernumbers);
	echo "</pre>";
	*/
	
	for($i = 0; $i < sizeof($filepaths); $i++){
		try{
			//Upload file to Völkner
			$api = new ShopApiClient($api_url, $api_key, null);
			$file = new \SplFileObject($filepaths[$i]);
			$docs = new DocumentCollection();
			$docs->add(new Document($file, $filenames[$i], 'CUSTOMER_INVOICE'));
			$request = new UploadOrdersDocumentsRequest($docs, $ordernumbers[$i]);
			$result = $api->uploadOrderDocuments($request);
		} catch (\Exception $e) {
			echo "[!] Unable to upload " . $filenames[$i] . " to order number " . $ordernumbers[$i] . "\r\n";
			continue;
		}
		
		try{
			//Delete file after upload
			unlink($filepaths[$i]);
			
			echo "Added invoice " . $filenames[$i] . " to order number " . $ordernumbers[$i] . "\r\n";
		} catch (\Exception $e) {
			echo "[!] Added invoice " . $filenames[$i] . " to order number " . $ordernumbers[$i] . ", but couldn't delete invoice pdf!\r\n";
		}
	}


} catch (\Exception $e) {
	// An exception is thrown if the requested object is not found or if an error occurs
	echo "[!] Unknown error! Try again with advanced errors.";
	/*
	echo "<pre>";
	var_dump($e);
	echo "</pre>";
	*/
}
