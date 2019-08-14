<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('simple_html_dom.php');

$cache_file = 'boyotour.html';
if(!file_exists($cache_file)){
    $ch = curl_init('https://boyotour.com/tour/');
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:36.0) Gecko/20100101 Firefox/36.0");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    // echo $result;
    curl_close ($ch);

    file_put_contents($cache_file, $result, LOCK_EX);
}

// print_r($cache_file);
header('Access-Control-Allow-Origin: *');

$html = @str_get_html(file_get_contents($cache_file));
if($html){
    $i=0;
    foreach($html->find('div.gridpad') as $e){
        $i++;
        $data['productId'] = $i;
        $data['categoryId'] = $e->find('div.destirasi', 0)->plaintext;
        $data['productName'] = $e->find('div.areatitle h3', 0)->plaintext;
        $data['quantityPerUnit'] = 1;
        // $data['prd_url'] = $e->find('div.smart_pdtitle a', 0)->href;
        $data['productImg'] = $e->find('div.vimg img', 0)->src;
        $data['unitPrice'] = preg_replace("/[^0-9]/", "", $e->find('strong.harga', 0)->plaintext);
        $data['unitsInStock'] = 0;

        if($data) $results[] = $data;
    }
	echo json_encode($results); die();
}