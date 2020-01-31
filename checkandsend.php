<?php

//ปรับโซนเวลาเป็นของประเทศไทย
date_default_timezone_set("Asia/Bangkok");

//Scan ไฟล์ในโฟลเดอร์ที่ไฟล์ php นี้วางอยู่ โดยเรียงจากไฟล์ล่าสุดขึ้นมาก่อน
$files = scandir("./", SCANDIR_SORT_DESCENDING);

//ไฟล์ที่ให้ข้ามไป ไม่ต้องเอาไปรวมในตัวแปร
unset($files[array_search('checkandsend.php', $files, true)]);
unset($files[array_search('linenoti.jpg', $files, true)]);
unset($files[array_search('array.txt', $files, true)]);

//print_r ($files); //ถ้ามันส่งไฟล์ไปไม่ถูกต้อง ให้ลองเปิดบรรทัดนี้ เพื่อ Print ดูค่าลำดับของตัวแปร แล้วเอาไปแก้ตัวเลขใน [] บรรทัดด้างล่าง
$newest_file = $files[3];

//ดึงตัวแปร ไฟล์ล่าสุด จาก txt มาแสดงผลเทียบกัน 
$latest_file = file_get_contents('./array.txt', true);
echo "Last - $latest_file"; 
echo "<br>";
echo "Now - $newest_file"; 
echo "<br>";

//แสดงผลว่าไฟล์ล่าสุดนั้น เป็นไฟล์เมื่อ วัน เวลา เมื่อใด
$file_date = "./".$newest_file;
if (file_exists($file_date)) {
    echo "<br>";
    echo "$file_date - was last modified - " . date ("d F Y - H:i:s.", filemtime($file_date));
    echo "<br>";
    echo "<br>";
}

//ถ้ายังเป็นไฟล็นเดิมอยู่ก็ให้แสดงผลว่ายังเป็นไฟล์เดิม
if($newest_file == $latest_file) {
            exit('Still same file.');
}

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

$datetime = date("Y-n-j H:i:s", filemtime($file_date));
$strDate = $datetime;
        
//เก็บตัวแปรไฟล์ล่าสุดลง txt
file_put_contents('./array.txt', $newest_file);

$base_url = "http://localhost/test/"; //url โฟลเดอร์ที่เก็บไฟล์รูป
$photo="$base_url$newest_file";

sleep(3); //หน่วงเวลา เผื่อรูปเพิ่งถูกเขียนลง Disk เวลาส่งไปภาพจะได้ไม่ดำ

print_r("sending {$photo}");

//Url ของไฟล์รูป
$url = $photo;
//Download ไฟล์จาก url
$downloadedFileContents = file_get_contents($url);
//ตรวจสอบว่า Download ได้สำเร็จไหม
if($downloadedFileContents === false){
    throw new Exception('Failed to download file at: ' . $url);
}
//ชื่อไฟล์ที่ต้องการเก็บ
$fileName = 'linenoti.jpg';
//เซฟไฟล์
$save = file_put_contents($fileName, $downloadedFileContents);
//ตรวจสอบว่า Save สำเร็จไหม
if($save === false){
    throw new Exception('Failed to save file to: ' , $fileName);
}

/*-------------line noti----------------------*/
	$line_api = 'https://notify-api.line.me/api/notify';
	$access_token = 'Line Notify Access Token'; //เอา Line Notify Access Token มาใส่ในนี้
    
	$message = 'ข้อความที่จะส่งไปพร้อมกับรูปภาพ '.DateThai($strDate); //ข้อความที่จะส่งไปพร้อมกับรูปภาพ + วัน เวลา ของไฟล์รูป
	$imageFile = new CurlFile('D:\xampp\htdocs\test\linenoti.jpg', 'image/jpg', 'linetemp.jpg'); //Windows Path ของไฟล์ linenoti.jpg

    $message_data = array(
	'message' => $message,
	'imageFile' => $imageFile
    );

    $result = send_notify_message($line_api, $access_token, $message_data);

	echo '<pre>';
    print_r($result);
    echo '</pre>';
/*-------------line noti----------------------*/

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

?>
