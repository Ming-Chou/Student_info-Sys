<?php

	session_start();

?>

<html>
<head>
<meta charset="utf-8">
<title>登出</title>
</head>
<body>

<?php

if(htmlspecialchars($_SESSION["login"])==true)
{
	session_destroy();
	header("refresh:0;url=logforinf.php");
}
header("refresh:0;url=logforinf.php");

?>

</body>
</html>
