<?php

	session_start();

?>

<html>
<head>
<meta charset="utf-8">
<title>個人資料</title>
</head>
<body>

<form action="edit_profile_heal.php" method="post">
<?php
require_once("server.php");
if(htmlspecialchars($_SESSION["login"]==true)){
	$un = htmlspecialchars($_SESSION["ID"]);//從SESSION得到帳號
	$vrcd = htmlspecialchars($_SESSION["PW"]);//從SESSION得到認證碼

	$sqlcode_chge = "select * from data";//選擇資料庫
	$start_chge = mysql_query($sqlcode_chge);
	while($list = mysql_fetch_array($start_chge)){//將自己的資料列出 以ID 辨認
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

	<?php 
	echo "~~請公開個人資料以查看他人資料~~&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<button type='submit' name='s_year' onclick=location.href='edit_profile.php'>上一頁</button><br><br>"; 
	?>

	<table width="100%" border=1>
		<tr>
			<td width="8%">學號</td>
			<td width="7%">姓名</td>
			<td width="10%">生日</td>
			<td width="10%">手機號碼</td>
			<td width="20%">E-mail</td>
			<td width="*">地址</td>
			<td width="10%">公司</td>
		</tr>
	<?php
	$s_year = $_POST['s_year'];

	$boolen = 'y';

	$sqlcode_chge = "select * from data where UID='" . $un . "'";//選擇資料庫
	$start_chge = mysql_query($sqlcode_chge);

	$sqlcode_chg = "select * from data where year='" . $s_year . "'";//選擇資料庫
	$start_chg = mysql_query($sqlcode_chg);

	while($list1 = mysql_fetch_array($start_chge))//將自己的資料列出 以ID 辨認
	{
		if($list1['info']==$boolen)
		{
			$start_chg = mysql_query($sqlcode_chg);
			while($list = mysql_fetch_array($start_chg))//將自己的資料列出 以ID 辨認
			{
				echo "<tr>";
				echo "<td width='8%'>";
				echo $list['UID'] . "</a></td>";
				echo "<td width='7%'>";
				echo $list['Name']  . "</a></td>";
				if($list['info']=='y')//被隱藏看不到
				{
					echo "<td width='10%'>";
					echo $list['Birth']  . "</a></td>";
					echo "<td width='10%'>";
					echo $list['Phone']  . "</a></td>";
					echo "<td width='20%'>";
					echo $list['Email'] . "</a></td>";
					echo "<td width='*'>";
					echo $list['Address'] . "</a></td>";
					echo "<td width='10%'>";
					echo $list['Company'] . "</td>";
				}
				echo "</tr>";
			}
		}
		else
		{
			$start_chg = mysql_query($sqlcode_chg);
			while($list = mysql_fetch_array($start_chg))//將自己的資料列出 以ID 辨認
			{
				echo "<tr>";
				echo "<td width='8%'>";
				echo $list['UID'] . "</a></td>";
				echo "<td width='7%'>";
				echo $list['Name']  . "</a></td>";
				
				echo "</tr>";
			}
		}
	}
	?>
	</table>

<?php
}
else
{
	echo "登錄超時，請重新登錄。<br>";
	echo "2秒後返回登錄畫面。<br>";
	header("refresh:2;url=logforinf.php");
}
?>

</body>
</html>
