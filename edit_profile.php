<?php

	session_start();

?>

<?php //Reject SQL injection

function inject_check($sql_str) {
	return eregi('select|insert|and|or|update|delete|\-|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
} 

function str_check( $str ) {
	if(!get_magic_quotes_gpc()) {
		$str = addslashes($str); // 进行过滤
	}
	$str = str_replace("_", "\_", $str);
	$str = str_replace("%", "\%", $str);
	return $str;
}
 
function post_check($post) {
	if(!get_magic_quotes_gpc()) {
		$post = addslashes($post);
	}
	$post = str_replace("_", "\_", $post);
	$post = str_replace("%", "\%", $post);
	$post = nl2br($post);
	$post = htmlspecialchars($post);
	return $post;
}

?>

<html>
<head>
<meta charset="utf-8">
<title>帳密比對後修改個人資料</title>
</head>
<body>
<?php
require_once("server.php");//
if(htmlspecialchars($_SESSION["login"]==true)){
	$un = htmlspecialchars($_SESSION["ID"]);//從SESSION得到帳號
	$vrcd = htmlspecialchars($_SESSION["PW"]);//從SESSION得到認證碼
}
else if(htmlspecialchars($_SESSION["loginwrang"]>4)){
	echo "錯誤次數過多\r\n 您被鎖定10小時。";
	$_SESSION['loginwrang'] = $_SESSION['loginwrang']+1;
	header("refresh:1;url=logforinf.php");
	die();
}
else{
	$un = $_POST['us_id'];//從上個頁面得到帳號
	$vrcd = $_POST['vrcd'];//上頁的認證碼
	if(!is_numeric($un)){
		echo "非法帳號";
		header("refresh:1;url=logforinf.php");
		$_SESSION['loginwrang'] = $_SESSION['loginwrang']+1;
		die();
	}
	if(inject_check($vrcd)){
		echo "非法密碼";
		header("refresh:1;url=logforinf.php");
		$_SESSION['loginwrang'] = $_SESSION['loginwrang']+1;
		die();
	}
	$vrcd = post_check($vrcd);
	$vrcd = str_check($vrcd);
}

if(strlen($un) == 7) {
	$un_email = "u" . $un . "@ms" . substr($un,0,2) . ".nttu.edu.tw";
}
if(strlen($un) == 8) {
	$un_email = "u" . $un . "@ms" . substr($un,0,3) . ".nttu.edu.tw";
}

$radius = radius_auth_open();
$ip_address = "IP";
$port = "1812";
$shared_secret = "PW";
radius_add_server($radius, $ip_address, $port, $shared_secret, 5, 3);
radius_create_request($radius, RADIUS_ACCESS_REQUEST);
radius_put_attr($radius, RADIUS_USER_NAME, $un_email);
radius_put_attr($radius, RADIUS_USER_PASSWORD, $vrcd);

$radius_result = radius_send_request($radius);

switch ($radius_result) {
case RADIUS_ACCESS_ACCEPT:
	// An Access-Accept response to an Access-Request indicating that the RADIUS server authenticated the user successfully.
	if(htmlspecialchars($_SESSION["login"]!=true)){
		$_SESSION["login"] = "true";
		$_SESSION["ID"] = $un;
		$_SESSION["PW"] = $vrcd;
	}
	break;
case RADIUS_ACCESS_REJECT:
	// An Access-Reject response to an Access-Request indicating that the RADIUS server could not authenticate the user.
	echo '帳號或密碼錯誤';
	echo $un_email;
	$_SESSION['loginwrang'] = $_SESSION['loginwrang']+1;
	header("refresh:1;url=logforinf.php");
	die();
	break;
case RADIUS_ACCESS_CHALLENGE:
	// An Access-Challenge response to an Access-Request indicating that the RADIUS server requires further information in another Access-Request before authenticating the user.
	echo '帳號或密碼錯誤';
	$_SESSION['loginwrang'] = $_SESSION['loginwrang']+1;
	header("refresh:1;url=logforinf.php");
	die();
	break;
default:
	die('A RADIUS error has occurred: ' . radius_strerror($radius));
}

$sqlcode_chg = "select * from data";//選擇資料庫
$sqlcode_cked = "select * from data where UID='" 
				. $un . "' and user_verification_code='" . $vrcd . "'"; //列出帳號、及認證碼都符合的帳號
$start_cked = mysql_query($sqlcode_cked);

/*
if(mysql_num_rows($start_cked)==0 && htmlspecialchars($_SESSION["login"]!=true)){//從資料庫裡比對帳密
	echo "帳號或密碼錯誤";
	$_SESSION['loginwrang'] = $_SESSION['loginwrang']+1;
	header("refresh:1;url=logforinf.php");
	die();
}
*/

/*
if(htmlspecialchars($_SESSION["login"]!=true)){
	setcookie("login", "true", time()+3600);
	setcookie("ID", $un, time()+3600);
	setcookie("PW", $vrcd, time()+3600);
}
*/
/*
if(htmlspecialchars($_SESSION["login"]!=true)){
	$_SESSION["login"] = "true";
	$_SESSION["ID"] = $un;
	$_SESSION["PW"] = $vrcd;
}
*/
?>

<?//-----------------------------------------------------------------------------------------------------------//?>

<form action="edit_profile_heal.php" method="post">
<?php
$start_chg = mysql_query($sqlcode_chg);
while($list = mysql_fetch_array($start_chg)){//將自己的資料列出 以ID 辨認
	if($list['UID']==$un){
		echo "學號&nbsp;:&nbsp;<input type='text' readonly=true name='UID[]' value=" . $list['UID'] . "></a><br><br>";
		echo "姓名&nbsp;:&nbsp;<input type='text' name='name[]' size='7' maxlength='5' value='" . $list['Name']  . "'></a>&nbsp;&nbsp;";
		echo "生日&nbsp;:&nbsp;<input type='text' name='bir[]' size='7' maxlength='10' value='" . $list['Birth']  . "'></a><br><br>";
		echo "手機&nbsp;:&nbsp;<input type='text' name='phone[]' size='7' maxlength='10' value='" . $list['Phone']  . "'></a>&nbsp;&nbsp;";
		echo "E-mail&nbsp;:&nbsp;<input type='text' name='email[]' size='30' value='" . $list['Email']  . "'></a><br><br>";
		echo "地址&nbsp;:&nbsp;<input type='text' name='add[]' size='50' value='" . $list['Address']  . "'></a>&nbsp;&nbsp;<br><br>";
		echo "公司&nbsp;:&nbsp;<input type='text' name='com[]' value='" . $list['Company']  . "'></a><br><br><br>";
		//echo "密碼&nbsp;:&nbsp;<input type='password' size='12' maxlength='12' name='vrcd[]' value='" . $list['user_verification_code']  . "'></a>密碼只能為英文或數字,最多12個字<br><br>";
		echo "是否選擇公開:<input type='radio' name='info[]' value='y' checked='true'>是";
		echo "<input type='radio' name='info[]' value='n'></a>否&nbsp;&nbsp;&nbsp;&nbsp;目前設定&nbsp;:&nbsp;" . $list['info'];
	}
}
?>
<br>
<tr>
	<td colspan="5" align='center'>
	<input type="submit" value="修改">
</tr>
若不修改的欄位，則不用動作。
</form>
<button type='submit' name='s_year' onclick=location.href='logout.php'>登出</button>
<br><br>

<?//-----------------------------------------------------------------------------------------------------------//?>
<hr />
<?//-----------------------------------------------------------------------------------------------------------//?>

<br><br>
<?php echo "~~請公開個人資料以查看他人資料~~<br><br><br>"; ?>

<?php echo "請點選按鈕查看該年度之通訊錄<br><br>"; ?>
<form action="show_profile.php" method="post">
<table width="70%">
	<tr>

<?php

date_default_timezone_set("Asia/Taipei");
$count = 1;
$start_year_chg = 97;
$new_year = date("Y") - 1911;
while($start_year_chg <= $new_year)
{	
	
	if($start_year_chg == $new_year)
	{
		if(date("n")>8)
		{
			echo "<td align='center'>";
			echo "<button type='submit' name='s_year' value='" . $start_year_chg . "'><br>&nbsp;&nbsp;&nbsp;&nbsp;" . $start_year_chg . "&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;</button><br>";
			echo "</td>";
		}
	}
	else
	{
		echo "<td align='center'>";
		echo "<button type='submit' name='s_year' value='" . $start_year_chg . "'><br>&nbsp;&nbsp;&nbsp;&nbsp;" . $start_year_chg . "&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;</button><br>";
		echo "</td>";
	}
	$start_year_chg = $start_year_chg + 1;
	if($count == 5)
	{
		echo "</tr>";
		echo "<tr>";
		$count = 1;
	}
	$count = $count + 1;

}

?>

	</tr>
</table>
</form>

</body>
</html>
