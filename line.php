<?php

include('./httpful.phar');
$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = 'AiEQ2tCxlf9hydDgOE7uHeeSomKFayk5LhW8cFNJZagT8saizPkZ//p5d20rBIkPjSW2o+OYskAZs2DePJb/3+NOSiUsbSpH3gCP5yUtKilE3z4gXSmETMmxdx6+gneZ17cdtTvv2eHtXsu+0K/2FAdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array

if ( sizeof($request_array['events']) > 0 )
{

 foreach ($request_array['events'] as $event)
 {
  $reply_message = '';
  $reply_token = $event['replyToken'];

  if ( $event['type'] == 'message' ) 
  {
   
   if( $event['message']['type'] == 'text' )
   {
		$text = $event['message']['text'];
		
		if(($text == "อยากทราบยอด COVID-19 ครับ")||($text == "ข้อ 1")||($text == "1")){
			$reply_message = 'รายงานสถานการณ์ ยอดผู้ติดเชื้อไวรัสโคโรนา 2019 (COVID-19) ในประเทศไทย ผู้ป่วยสะสม จำนวน 827 ราย ผู้เสียชีวิต จำนวน 4 ราย รักษาหาย จำนวน 57 ราย ผู้รายงานข้อมูล: นายจิรวัฒน์ บริบรรณ';
		}
		else if(($text== "ข้อมูลส่วนตัวของผู้พัฒนาระบบ")||($text== "ข้อมูลของคุณ")||($text== "ข้อมูลส่วนตัว")||($text== "หนักเท่าไหร่")){
			$reply_message = 'ชื่อนายจิรวัฒน์ บริบรรณ อายุ 23 ปี น้ำหนัก 64kg. สูง 175cm. ขนาดรองเท้าเบอร์ 8.5 ใช้หน่วย US';
		}else if(($text== "ราคาทอง")||($text== "ทองคำวันนี้")||($text== "ขอราคาทองคำหน่อย")){
			$buy ="";
			$sell ="";
			$uri = "https://thai-gold-api.herokuapp.com/latest";
			$response = \Httpful\Request::get($uri)->send();
			$buy = $response->price->gold->buy;
			$reply_message = 'ราคาทองวันนี้ ราคาซื้อ  '+buy+'บาท';
		}
   }
   else
    $reply_message = 'ระบบได้รับ '.ucfirst($event['message']['type']).' ของคุณแล้ว';
  
  }
  else
   $reply_message = 'ระบบได้รับ Event '.ucfirst($event['type']).' ของคุณแล้ว';
 
  if( strlen($reply_message) > 0 )
  {
   //$reply_message = iconv("tis-620","utf-8",$reply_message);
   $data = [
    'replyToken' => $reply_token,
    'messages' => [['type' => 'text', 'text' => $reply_message]]
   ];
   $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

   $send_result = send_reply_message($API_URL, $POST_HEADER, $post_body);
   echo "Result: ".$send_result."\r\n";
  }
 }
}

echo "OK";

function send_reply_message($url, $post_header, $post_body)
{
 $ch = curl_init($url);
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 $result = curl_exec($ch);
 curl_close($ch);

 return $result;
}

?>
