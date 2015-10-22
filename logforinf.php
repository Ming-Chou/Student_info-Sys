<?php

	session_start();
	//$_SESSION['loginwrang'] = 0;

?>

<html>
<head>
<meta charset="utf-8">
<title>登入</title>
</head>
<body>

<?php

if(htmlspecialchars($_SESSION["login"])==true)
{
	header("refresh:1;url=edit_profile.php");
}
else if(htmlspecialchars($_SESSION["loginwrang"]>4))
{
	echo "錯誤次數過多\r\n 您被鎖定10小時。";
	die();
}

?>

<form action="edit_profile.php" method="post">
	　學號：　<input type="text" name="us_id" maxlength='8'><br><br>
	　密碼：　<input type="password" name="vrcd" maxlength='12'><br><br>
	　　<input type="submit" value="送出">
   	　　<input type="reset" value="清除">
</form>

</body>
</html>
