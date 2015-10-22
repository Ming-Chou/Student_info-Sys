<?php //Reject SQL injection

function inject_check($sql_str) {
	return preg_match('select | insert | and | or | update | delete | \- | \" | \' | \/ | \* |\*| \.\.\/ | \.\/ | union | into | load_file | outfile', $sql_str);
} 

function inject_check_for_date($sql_str) {
	return preg_match('select|insert|and|or|update|delete|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
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
<title>接收訊息並變更</title>
</head>
<body>

<?php

require_once("server.php");
$id = $_POST['UID'];//從上頁得到ID
$name = $_POST['name'];//姓名
$birth = $_POST['bir'];//生日
$phone = $_POST['phone'];//手機號碼
$email = $_POST['email'];//電子郵件
$address=$_POST['add'];//地址
$company=$_POST['com'];//公司
$vrcd=$_POST['vrcd'];//Password
$info=$_POST['info'];//是否顯示資料
foreach ($id as $key => $value){
	if($info[$key]){//更新資料庫 將更改後的資料匯入
		$sqlcode = "update data set Name='" . $name[$key] . "',Birth='" . $birth[$key] . "',Phone='" . $phone[$key] . "',Email='" . $email[$key] . "',Address='" . $address[$key] . "',Company='" . $company[$key] . "',user_verification_code='" . $vrcd[$key] . "',info='" . $info[$key] . "' where UID=" . $value;
	}
	else{
		$sqlcode = "update data set Name='" . $name[$key] . "',Birth='" . $birth[$key] . "',Phone='" . $phone[$key] . "',Email='" . $email[$key] . "',Address='" . $address[$key] . "',Company='" . $company[$key] . "',user_verification_code='" . $vrcd[$key] . "',info='" . $info[$key] . "' where UID=" . $value;
	}

	$start = mysql_query($sqlcode);
	if($start)//確認是否正確更改的視窗
	{
		echo '<script language="javascript">window.alert("Success!");</script>';
		header("refresh:0;url=edit_profile.php");
	}
	else
		echo '<script language="javascript">window.alert("Fail!");</script>';
		header("refresh:0;url=edit_profile.php");
}

?>

</body>
</html>
