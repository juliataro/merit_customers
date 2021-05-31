<?php
/**
 * Merit API Plugin
 *
 * @package     merit_customers
 * @author      Julia Taro
 * @copyright   2021 Julia Taro
 * @license     GPL-2.0-or-later
 * @wordpress-plugin
 * Plugin Name: merit_customers
 * Description: This plugin prints "Merit Api Plugin" inside an admin page.
 * Version:     1.0.0
 * Author:      Julia Taro
 * Author URI:  https://http://yuliataro.ikt.khk.ee
 * Text Domain: merit_customers
 */


// Search Customer in Merit Api By Name------------------------
// Funktsioon allkirjastamiseks
function signURL($id, $key, $timestamp, $json){
 $signable = $id.$timestamp.$json;
 // NOTICE:  bool $raw_output = TRUE
 $rawSig = hash_hmac('sha256', $signable, $key, true); // key-hashed saade

// JSON binaarandmete kodeerimine vältib modifitseerimist transportimise kaudu
 $base64Sig = base64_encode($rawSig);
 return $base64Sig;
}

// Search for customer  in Merit API
function findCustomer($stuff, $endpoint) {
  $ch = curl_init();

    // eIEeasy ettevõtte andmed
    $APIID = "eb854b11-db9c-495f-a108-ce5fbcb59ccb";
    $APIKEY = "883GM0TSFxJqg/OANR5fgKi5U3FIHeEgICt4M7ZsAds=";
    $TIMESTAMP = date("YmdHis");

    $signature = signURL($APIID,$APIKEY, $TIMESTAMP,  json_encode($stuff));
    curl_setopt($ch, CURLOPT_URL, "https://aktiva.merit.ee/api/v1/".$endpoint."?ApiId=".$APIID.
        "&timestamp=".$TIMESTAMP."&signature=".$signature);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stuff));
    curl_exec($ch);
	if(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) != 200) {
	print("ERROR ".curl_getinfo($ch, CURLINFO_RESPONSE_CODE)."\r\n");
	print_r(curl_getinfo($ch));
 }
 curl_close($ch); // closing connection

 }

 // Andmete saatmine
    $findClient = array("Name" => "Julia Taro 2", "Email"=> "julia.taro@khk.ee");
    // Funktsiooni kutsumine
    findCustomer($findClient, "getcustomers");

    // Plugini lühikood
    add_shortcode('check','findCustomer');



//// _______________  Create customer and send to Merit API_________________________
//function sendCustomer($stuff, $endpoint) {
//    $ch = curl_init();
//
//    // random test company
//    $APIID = "eb854b11-db9c-495f-a108-ce5fbcb59ccb";
//    $APIKEY = "883GM0TSFxJqg/OANR5fgKi5U3FIHeEgICt4M7ZsAds=";
//    $TIMESTAMP = date("YmdHis");
//
//    $signature = signURL($APIID,$APIKEY, $TIMESTAMP,  json_encode($stuff));
//    curl_setopt($ch, CURLOPT_URL, "https://aktiva.merit.ee/api/v2/".$endpoint."?ApiId=".$APIID."&timestamp=".$TIMESTAMP."&signature=".$signature);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stuff));
//    curl_exec($ch);
//    if(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) != 200) {
//        print("ERROR ".curl_getinfo($ch, CURLINFO_RESPONSE_CODE)."\r\n");
//        print_r(curl_getinfo($ch));
//    }
//    curl_close($ch); // closing connection
//}
//
//$sendClient = (object)[
//    "Name"            => "Julia Taro 2",
//    "CountryCode"     => "EE",
//];
//sendCustomer($sendClient, "sendcustomer");
//// Plugin shortcode
//add_shortcode('send','sendCustomer');
//






/* ___________________Create Invoice___________________________________

function signURL($id, $key, $timestamp, $json) {
    $signable = $id.$timestamp.$json;
    // NOTICE:  bool $raw_output = TRUE
    $rawSig = hash_hmac('sha256', $signable, $key, true);
    $base64Sig = base64_encode($rawSig);
    return $base64Sig;

}

// curl that POST stuff to endpoints
function postStuff($stuff, $endpoint) {

    $responseString = "";

    $ch = curl_init();

    // random test company
    $APIID = "eb854b11-db9c-495f-a108-ce5fbcb59ccb";
    $APIKEY = "883GM0TSFxJqg/OANR5fgKi5U3FIHeEgICt4M7ZsAds=";
    $TIMESTAMP = date("YmdHis");

    $signature = signURL($APIID,$APIKEY, $TIMESTAMP,  json_encode($stuff));
    curl_setopt($ch, CURLOPT_URL, "https://aktiva.merit.ee/api/v1/".$endpoint."?ApiId=".$APIID."&timestamp=".$TIMESTAMP."&signature=".$signature);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stuff));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

   if(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) != 200) {

        callError("HTTP error ". curl_getinfo($ch, CURLINFO_RESPONSE_CODE), json_encode(curl_getinfo($ch)) . ", ". $body );
        exit;

    } else {

        $woSlashes = stripslashes($body);
        $strLen = strlen($woSlashes);
        // removing slashes and dashes
        $responseString = substr(substr($woSlashes, 1, $strLen), 0, $strLen-2);

    }
    curl_close($ch);
    return $responseString;
}

$CurTime = date("Ymd");
$sendInvoice = [
    "Customer" => [
        "Name" => "Julia Taro 2",
        "NotTDCustomer" => "true",

        "CountryCode" => "EE",


    ],
    "DocDate" => $CurTime,
    "DueDate" => $CurTime,
    "TransactionDate" => $CurTime,
    "InvoiceNo" => "TEST00001",
    "RefNo" => null,
    "CurrencyCode" => "EUR",
    "InvoiceRow" => [
        [
            "Item" => [
                "Code" => "",
                "Description" => "",
                "Type" => 2
            ],
            "Quantity" => "1.000",
            "Price" => "416.67",
            "DiscountPct" => "10.00",
            "DiscountAmount" => "41.67",
            "TaxId" => "b9b25735-6a15-4d4e-8720-25b254ae3d21"
        ]
    ],
    "RoundingAmount" => 5,
    "TotalAmount" => "375.00",

    "TaxAmount" => [
        [
            "TaxId" => "b9b25735-6a15-4d4e-8720-25b254ae3d21",
            "Amount" => "75.00"
        ]
    ],
    "HComment" => "",
    "FComment" => ""
];

// sending invoice to Merit
$sendingResponse = postStuff($sendInvoice, "sendinvoice");
add_shortcode('invoice', $sendingResponse);

$sendingResponseArray = json_decode($sendingResponse, true);

// was the response valid JSON?
if($sendingResponseArray == null) {
    callError("HTTP response was not JSON", $sendingResponse);
    exit;

}

// extract GUID
$guid = strtoupper($sendingResponseArray['InvoiceId']);

// valid guid? is it?
if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid)) {

    // GREAT SUCCESS!!!!!!
    print("InvoiceId guid OK, write it down somewhere \n\r");

    // can delete this invoice
    /*
    $deleteInvoice = [
           "Id"=>$guid
    ];
    $deletingResponse = postStuff($deleteInvoice, "deleteinvoice");
    print ("delete: " . $deletingResponse);
    */


/*

// code to find invoice in a propriate time______________________
function signURL($id, $key, $timestamp, $json)
{
    $signable = $id . $timestamp . $json;
    // NOTICE:  bool $raw_output = TRUE
    $rawSig = hash_hmac('sha256', $signable, $key, true);
    $base64Sig = base64_encode($rawSig);
    return $base64Sig;

}

function postStuff($stuff, $endpoint)
{
    $ch = curl_init();

    // random test company
    $APIID = "eb854b11-db9c-495f-a108-ce5fbcb59ccb";
    $APIKEY = "883GM0TSFxJqg/OANR5fgKi5U3FIHeEgICt4M7ZsAds=";
    $TIMESTAMP = date("YmdHis");

    $signature = signURL($APIID, $APIKEY, $TIMESTAMP, json_encode($stuff));
    curl_setopt($ch, CURLOPT_URL, "https://aktiva.merit.ee/api/v1/" . $endpoint . "?ApiId=" . $APIID . "&timestamp=" . $TIMESTAMP . "&signature=" . $signature);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stuff));
    curl_exec($ch);
    if (curl_getinfo($ch, CURLINFO_RESPONSE_CODE) != 200) {
        print("ERROR " . curl_getinfo($ch, CURLINFO_RESPONSE_CODE) . "\r\n");
        print_r(curl_getinfo($ch));
    }
    curl_close($ch);
}
$payloadGetInvoices = array("PeriodStart" => "20210101", "PeriodEnd" => "20212504");

postStuff($payloadGetInvoices, "getinvoices");
add_shortcode('inv','sendCustomer');
*/

//_________________DELETE CLIENT BY IDE______________________________

/* Delete Invoice From Merit
function signURL($id, $key, $timestamp, $json){
    $signable = $id.$timestamp.$json;
    // NOTICE:  bool $raw_output = TRUE
    $rawSig = hash_hmac('sha256', $signable, $key, true);
    // key-hashed message authentication code

    $base64Sig = base64_encode($rawSig); // encoding JSON binary data prevent modificcation through tronsporting
    return $base64Sig;
}

// Search for customer  to Merit API
function deleteInvoice($stuff, $endpoint) {
    $ch = curl_init();

    // random test company
    $APIID = "eb854b11-db9c-495f-a108-ce5fbcb59ccb";
    $APIKEY = "883GM0TSFxJqg/OANR5fgKi5U3FIHeEgICt4M7ZsAds=";
    $TIMESTAMP = date("YmdHis");

    $signature = signURL($APIID,$APIKEY, $TIMESTAMP,  json_encode($stuff));
    curl_setopt($ch, CURLOPT_URL, "https://aktiva.merit.ee/api/v1/".$endpoint."?ApiId=".$APIID."&timestamp=".$TIMESTAMP."&signature=".$signature);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stuff));
    curl_exec($ch);
    if(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) != 200) {
        print("ERROR ".curl_getinfo($ch, CURLINFO_RESPONSE_CODE)."\r\n");
        print_r(curl_getinfo($ch));
    }
    curl_close($ch); // closing connection
}
    $invoiceId = array("Id" => "78fb1185-0ca5-48a8-94c2-58b6a6e166e2");

    deleteInvoice($invoiceId, "deleteinvoice");

    // Plugin shortcode
    add_shortcode('delete','deleteInvoice');


*/