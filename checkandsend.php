<?php

/*--------------------------------------- แก้ไขการตั้งค่าแค่ตรงนี้ ก็ใช้งานได้แล้ว ---------------------------------------*/
//ไฟล์ checkandsend.php ต้องอยู่ใน Directory เดียวกันกับโฟล์เดอร์ที่เก็บรูป (ไม่ใช่ในโฟลเดอร์รูป)

//Windows Path ของที่อยู่ของไฟล์ php
$win_dir = 'D:\xampp\htdocs\checkimgandsendline';

//Url Directory ที่ไฟล์ php และโฟลเดอร์เก็บรูปอยู่
$base_url = "http://localhost/checkimgandsendline/";

//ตำแหน่งของ Directory หรือ Folder ที่เก็บรูป 
$Imagedir = "Images/";

//Line Notify access token สร้างได้ที่ https://notify-bot.line.me/my/
$linenotifytoken = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

//ข้อความที่จะส่งเข้า Line ไปพร้อมกับรูป
$strMessage = "ข้อความที่จะส่งไปพร้อมกับรูปภาพ ";
/*--------------------------------------- แก้ไขการตั้งค่าแค่ตรงนี้ ก็ใช้งานได้แล้ว ---------------------------------------*/


//ปรับโซนเวลาเป็นของประเทศไทย
date_default_timezone_set("Asia/Bangkok");

//ระบบเดือนภาษาไทยตัวย่อ
function DateThai($strDate)
	{
		$strYear = date("Y",strtotime($strDate))+543;
		$strMonth= date("n",strtotime($strDate));
		$strDay= date("j",strtotime($strDate));
		$strHour= date("H",strtotime($strDate));
		$strMinute= date("i",strtotime($strDate));
		$strSeconds= date("s",strtotime($strDate));
		$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
		$strMonthThai=$strMonthCut[$strMonth];
		return "$strDay $strMonthThai $strYear เวลา $strHour:$strMinute:$strSeconds";
	}

//ย้ายตำแหน่งไปยังโฟลเดอร์เก็บรูปที่กำหนดไว้ แล้วทำการจัดเรียงรายชื่อไฟล์ที่สร้างหรือเปลี่ยนแปลงล่าสุดขึ้นมาก่อน
chdir($Imagedir);
array_multisort(array_map('filemtime', ($files = glob("*.*"))), SORT_DESC, $files);

//เลือกไฟล์อันดับแรกสุด หรือก็คือไฟล์ล่าสุด
$newest_file = $files[0];

//ย้ายตำแหน่งกลับขึ้นไปที่ไฟล์ php อยู่
chdir ('../');

//ดึงตัวแปร ไฟล์ล่าสุด จาก txt มาแสดงผลเทียบกับไฟล์ล่าสุด
$latest_file = file_get_contents('./array.txt', true);

//ตรวจสอบว่า Save สำเร็จไหม
if($latest_file === false){
	echo "<br>";
    echo "ยังไม่มีไฟล์ array.txt เนื่องด้วยเป็นการ Run ครั้งแรก เริ่มทำการตรวจสอบและสร้างไฟล์...";
	echo "<br>";
	echo "<br>";
}

//แสดงผลเปรียบเทียบไฟล์ว่าเปลี่ยนหรือแปลงหรือคงเดิม
echo "========== เปรียบเทียบไฟล์ =========="; 
echo "<br>";
echo "ที่ผ่านมา - $latest_file"; 
echo "<br>";
echo "ปัจจุบัน - $newest_file"; 
echo "<br>";
echo "================================="; 

//ตำแหน่งของไฟล์ล่าสุด
$current_file = $Imagedir.$newest_file;

//ตรวจสอบว่าไฟล์ถูกสร้างหรือแก้ไขล่าสุดเมื่อใด
$filedatetime = date("Y-n-j H:i:s", filemtime($current_file));
$strDate = DateThai($filedatetime);

//แสดงผลวันเวลาของไฟล์
if (file_exists($current_file)) {
    echo "<br>";
    echo "$newest_file - ถูกสร้างหรือเปลี่ยนแปลงเมื่อ - $strDate";
    echo "<br>";
    echo "<br>";
}

//ถ้ายังเป็นไฟล์เดิมอยู่ก็ให้แสดงผลว่ายังเป็นไฟล์เดิม และจบการทำงาน
if($newest_file == $latest_file) {
            exit('*** ยังคงเป็นไฟล์เดิมอยู่ ***');
}
        
//ถ้าเป็นไฟล์ใหม่ ก็เก็บตัวแปรไฟล์ลง txt
file_put_contents('./array.txt', $newest_file);

//หน่วงเวลา เผื่อรูปเพิ่งถูกเขียนลง Disk เวลาส่งไปภาพจะได้ไม่ดำ
sleep(3); 

//แสดงผลว่าเริ่มกระบวนการส่งไฟล์แล้ว
print_r("=== ทำการส่งไฟล์ {$newest_file} ===");

//เอาตัวแปรมารวมกัน เพื่อทำเป็น URL ไว้ดึงไฟล์รูป
$imgurl="$base_url$Imagedir$newest_file";

//Download ไฟล์จาก url
$downloadedFileContents = file_get_contents($imgurl);

//ตรวจสอบว่า Download ได้สำเร็จไหม
if($downloadedFileContents === false){
    throw new Exception('Failed to download file at: ' . $imgurl);
}

//ชื่อไฟล์ที่ Save
$fileName = 'linenoti.jpg';

//เซฟไฟล์
$save = file_put_contents($fileName, $downloadedFileContents);

//ตรวจสอบว่า Save สำเร็จไหม
if($save === false){
    throw new Exception('Failed to save file to: ' , $fileName);
}

/*---------------------- ระบบส่งข้อความและรูป Line Notify ----------------------*/
function send_notify_message($line_api, $access_token, $message_data){
   $headers = array('Method: POST', 'Content-type: multipart/form-data', 'Authorization: Bearer '.$access_token );

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $line_api);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $result = curl_exec($ch);
   // Check Error
   if(curl_error($ch))
   {
      $return_array = array( 'status' => '000: send fail', 'message' => curl_error($ch) );
   }
   else
   {
      $return_array = json_decode($result, true);
   }
   curl_close($ch);
return $return_array;
}
/*---------------------- ระบบส่งข้อความและรูป Line Notify ----------------------*/

/*---------------------- เรียกตัวแปรกับระบบส่งข้อความและรูป Line Notify ----------------------*/
$line_api = 'https://notify-api.line.me/api/notify';
$access_token = $linenotifytoken;
    
$message = "$strMessage$strDate";
$imageFile = new CurlFile($win_dir.'\linenoti.jpg', 'image/jpg', 'linetemp.jpg');

$message_data = array(
	'message' => $message,
	'imageFile' => $imageFile
);

$result = send_notify_message($line_api, $access_token, $message_data);

echo '<pre>';
print_r($result);
echo '</pre>';
/*---------------------- เรียกตัวแปรกับระบบส่งข้อความและรูป Line Notify ----------------------*/

?>
