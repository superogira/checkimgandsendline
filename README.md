# checkimgandsendline
Check latest image in folder then send with Line Notify

ใช้ php เช็กว่าใน Folder ที่กำหนดไว้ มีรูปใหม่ล่าสุดเข้ามาไหม
ถ้ามีก็ให้ส่งข้อความพร้อมรูปเข้าไปในไลน์ ถ้ายังไม่มีก็ไม่ต้องส่งอะไร

ที่ผมใช้อยู่ คือ 
- ใช้ส่งรูปจากกล้อง IP ที่มัน Upload มาเก็บใน Ftp Server 
- กับตั้งเวลาให้โปรแกรม Capture รูปหน้าจอใส่ในโฟลเดอร์ แล้วให้ส่งมารายงานใน Line ทุก 1 ชั่วโมง

เผื่อใครอยากเอาไปประยุกต์ใช้กับอะไรครับ

การเรียกใช้
- ใช้การเรียกหน้าเว็บด้วยการตั้ง Auto Refresh 
- หรือเขียน cmd เรียกหน้าเว็บด้วย curl ตามตัวอย่างด้านล่างก็ได้ครับ

:restart
timeout /T 3600
echo (%time%) Check and send picture.
curl --silent http://localhost/test/checkandsend.php
goto restart

![alt text](https://raw.githubusercontent.com/superogira/checkimgandsendline/master/checkimgandsendline.jpg)
