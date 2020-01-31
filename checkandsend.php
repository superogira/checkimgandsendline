<?php
date_default_timezone_set("Asia/Bangkok");
$files = scandir("./", SCANDIR_SORT_DESCENDING);
unset($files[array_search('checkandsend2.php', $files, true)]);
unset($files[array_search('linenoti.jpg', $files, true)]);
unset($files[array_search('array.txt', $files, true)]);
$newest_file = $files[3];

$last_file = file_get_contents('./array.txt', true); //ดึงตัวแปร ไฟล์ล่าสุด จาก txt
echo "Last - $last_file"; 
echo "<br>";
echo "Now - $newest_file"; 
echo "<br>";

$file_date = "./".$newest_file;
if (file_exists($file_date)) {
    echo "<br>";
    echo "$file_date - was last modified - " . date ("d F Y - H:i:s.", filemtime($file_date));
    echo "<br>";
    echo "<br>";
}

if($newest_file == $last_file) {
            exit('Still same file.');
}

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
        
file_put_contents('./array.txt', $newest_file);

$base_url = "http://localhost/test/test/"; //url โฟลเดอร์ที่เก็บไฟล์รูป
$photo="$base_url$newest_file";

sleep(3);

print_r("sending {$photo}");

//The URL of the file that you want to download.
$url = $photo;
//Download the file using file_get_contents.
$downloadedFileContents = file_get_contents($url);
//Check to see if file_get_contents failed.
if($downloadedFileContents === false){
    throw new Exception('Failed to download file at: ' . $url);
}
//The path and filename that you want to save the file to.
$fileName = 'linenoti.jpg';
//Save the data using file_put_contents.
$save = file_put_contents($fileName, $downloadedFileContents);
//Check to see if it failed to save or not.
if($save === false){
    throw new Exception('Failed to save file to: ' , $fileName);
}

/*-------------line noti----------------------*/
	$line_api = 'https://notify-api.line.me/api/notify';
    //$access_token = 'Line Notify Access Token'; //เอา Line Notify Access Token มาใส่ในนี้
	$access_token = 'yhHwRKfP5QnRZqmom6kyRMFKDWeyyF3rXQjGpCzmHBr'; //Test Token
    
	$message = 'ข้อความที่จะส่งไปพร้อมกับรูปภาพ '.DateThai($strDate); //ข้อความที่จะส่งไปพร้อมกับรูปภาพ + วัน เวลา ของไฟล์รูป
	$imageFile = new CurlFile('D:\xampp\htdocs\test\test\linenoti.jpg', 'image/jpg', 'linetemp.jpg'); //Windows Path ของไฟล์ linenoti.jpg

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
