<?php
include("local.php");
include("../proto/protocolbuffers.inc.php");
include("../proto/market.proto.php");
include("../Market/MarketSession.php");

$session = new MarketSession(true);
$session->login(GOOGLE_EMAIL, GOOGLE_PASSWD);
$session->setAndroidId(ANDROID_DEVICEID);

$assetRequest = new GetAssetRequest();
$assetRequest->setAssetId("lammar.quotes");

$reqGroup = new Request_RequestGroup();
$reqGroup->setGetAssetRequest($assetRequest);

$request = new Request();
$request->setContext($session->context);
$request->addRequestGroup($reqGroup);

$response = $session->executeProtobuf($request);
var_dump($response);
$assetResponse = $response->getResponseGroup(0)->getGetAssetResponse();

$ia = $assetResponse->getInstallAsset(0);


$cookieName = $ia->getDownloadAuthCookieName();
$cookieValue = $ia->getDownloadAuthCookieValue();
$url = $ia->getBlobUrl();

$fp = fopen("com.google.android.keep", 'w');

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, "Android-Finsky/3.7.13 (api=3,versionCode=8013013,sdk=15,device=crespo,hardware=herring,product=soju)"); 
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch, CURLOPT_COOKIE, $cookieName . '=' . $cookieValue);
curl_setopt($ch, CURLOPT_FILE, $fp);


$data = curl_exec($ch);
curl_close($ch);
fclose($fp);