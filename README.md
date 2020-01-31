# checkimgandsendline
Check latest image in folder then send with Line Notify

ใช้ php เช็กว่าใน Folder ที่กำหนดไว้ มีรูปใหม่ล่าสุดเข้ามาไหม<br/>
ถ้ามีก็ให้ส่งข้อความพร้อมรูปเข้าไปในไลน์ ถ้ายังไม่มีก็ไม่ต้องส่งอะไร<br/>
เก็บค่าตัวแปรด้วยไฟล์ txt ไม่จำเป็นต้องใช้ฐานข้อมูลอะไร<br/>

ที่ผมใช้อยู่ คือ 
- ใช้ส่งรูปจากกล้อง IP ที่มัน Upload มาเก็บใน Ftp Server 
- กับตั้งเวลาให้โปรแกรม Capture รูปหน้าจอใส่ในโฟลเดอร์ แล้วให้ส่งมารายงานใน Line ทุก 1 ชั่วโมง

เผื่อใครอยากเอาไปประยุกต์ใช้กับอะไรครับ

การเรียกใช้
- ใช้การเรียกหน้าเว็บด้วยการตั้ง Auto Refresh 
- หรือเขียน cmd เรียกหน้าเว็บด้วย curl ตามตัวอย่างด้านล่างก็ได้ครับ

:restart<br/>
timeout /T 3600<br/>
echo (%time%) Check and send picture.<br/>
curl --silent http://localhost/test/checkandsend.php<br/>
goto restart<br/>

![alt text](https://raw.githubusercontent.com/superogira/checkimgandsendline/master/checkimgandsendline.jpg)
