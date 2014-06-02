<?php
$db_host = 'localhost';
$db_user = 'erpy';
$db_passwd = 'erpy000';
$db_name = 'social';

$mysqli = new mysqli($db_host, $db_user, $db_passwd, $db_name);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/* set autocommit to off */
$mysqli->autocommit(FALSE);

/* Insert some values */
//$mysqli->query("INSERT INTO Language VALUES ('DEU', 'Bavarian', 'F', 11.2)");
//$mysqli->query("INSERT INTO Language VALUES ('DEU', 'Swabian', 'F', 9.4)");
$mysqli->query("INSERT INTO SOCIAL_SHOP_T (title, cmt1, price_org, link) VALUES ('등산화1', '좋아요111', '35000', 'www.daum.net')");
$mysqli->query("INSERT INTO SOCIAL_SHOP_T (title, cmt1, price_org, link) VALUES ('등산화2', '안좋아요1234', '24000', 'www.naver.com')");

/* commit transaction */
if (!$mysqli->commit()) {
    print("Transaction commit failed\n");
    exit();
}

/* close connection */
$mysqli->close();


/*
$con = mysql_connect($db_host,$db_user,$db_passwd) or die ("데이터베이스 연결에 실패하였습니다!");
mysql_select_db($db_name, $con);
mysql_query("INSERT INTO SOCIAL_SHOP_T (title, cmt1, price_org, link) VALUES ('등산화1', '좋아요111', '35000', 'www.daum.net')");
mysql_query("INSERT INTO SOCIAL_SHOP_T (title, cmt1, price_org, link) VALUES ('등산화2', '안좋아요1234', '24000', 'www.naver.com')");
mysql_close($con);
*/
echo "end\n";
?>
