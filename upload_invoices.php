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
	$filename = '2021-03-25_190116_dafha_Invoice_a3de8f2970144500a42f014f1db2c721.PDF';
	$ordernumber = '180416221-A';
	
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
		//Upload file to Völkner
		$api = new ShopApiClient($api_url, $api_key, null);
		$file = new \SplFileObject($filepaths[$i]);
		$docs = new DocumentCollection();
		$docs->add(new Document($file, $filenames[$i], 'CUSTOMER_INVOICE'));
		$request = new UploadOrdersDocumentsRequest($docs, $ordernumbers[$i]);
		$result = $api->uploadOrderDocuments($request);
		//Delete file after upload
		unlink($filepaths[$i]);
		
		echo "Added invoice " . $filenames[$i] . " to order number " . $ordernumbers[$i] . "<br>";
	}


} catch (\Exception $e) {
	// An exception is thrown if the requested object is not found or if an error occurs
	echo "Exception:<br><br> ";
	echo "<pre>";
	var_dump($e);
	echo "</pre>";
}
