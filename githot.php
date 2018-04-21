<?
date_default_timezone_set("Asia/Shanghai");
define("_DEFAULT_SRC_DATE_",date('Y-m-d H:i:s'));
ini_set("max_execution_time",60*60*60); 
ini_set("extension","php_pdo.dll"); 
ini_set("error_reporting","E_ALL ~E_NOTICE"); 
ini_set("extension","php_pdo_sqlite.dll"); 
ini_set("extension","php_sqlite.dll"); 
require_once("./lib/Snoopy.class.php");
require_once("./lib/func.general.php");
require_once("./lib/sqlite.lib.php");


/*
require_once("./caiji.weibo.cdn.db.inc.php");
echo 
$month=date("dHi");
exit;

https://m.weibo.cn/p/index?containerid=106003type%253D25%2526t%253D3%2526disable_hot%253D1%2526filter_type%253Drealtimehot&title=%25E5%25BE%25AE%25E5%258D%259A%25E7%2583%25AD%25E6%2590%259C%25E6%25A6%259C&extparam=filter_type%3Drealtimehot%26mi_cid%3D%26pos%3D9%26c_type%3D30%26source%3Dranklist%26flag%3D0%26display_time%3D1503051211&luicode=10000011&lfid=106003type%3D1&featurecode=20000320

https://m.weibo.cn/p/100103type%3D1%26q%3Dtest?type=all&queryVal=test&featurecode=20000320&luicode=10000011&lfid=106003type%3D1&title=test
*/
//echo 
$date=date("ymd");
$dbdir="";
$dbdir_hot=$dbdir."hot/";
if (!file_exists($dbdir_hot)) mkdir ($dbdir_hot);
$dbname_realtimehot=$dbdir_hot."wbc_t_realtimehot.$date.db3";
//unlink($dbname_realtimehot);
$DB_realtimehot=new SQLite($dbname_realtimehot); 

extract($_GET);
if(!empty($actfunc)){
	//echo $actfunc;
	$actfunc();
}


	exit;
function baidu_realtimehot_fetch(){
	global $DB_realtimehot;
	extract($_GET);
	tbl_create_realtimehot();
	global $snoopy,$ua;
	$snoopy=new snoopy;
	$cookie="";
	//$snoopy->agent="NOKIAE61i/UCBrowser7.7.0.81/28/999";
	//$snoopy->agent="Mozilla/5.0 (Linux; U; Android 1.0; en-us; dream) AppleWebKit/5.25.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2";
//echo
	$ua="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
	$url="http://top.baidu.com/buzz?b=1&fr=topcategory_c513";
	$str=fetchurl($url);
//echo
	$str=mb_convert_encoding($str, "UTF-8", "GBK");  
	$rule='<table class="list-table">[name]</tbody></table>';
	$rule='<table class="list-table">[name]</table>';
	$str=getpregmsg($str,$rule,1);
	//echo $str;
	//exit;
	//$rule='	<td class="keyword">[name]<tr class="hideline">';
	//$Arr1=getpregmsg($str,$rule,2);
	$Arr1=explode('<td class="keyword">',$str);
	//print_r($Arr1);
	//echo 
	$dhi=date("dHi");
	//showlink();
	$i=1;
	foreach($Arr1 as $str ){
		$rule='<span class="num-normal">[name]</span>';
		//$Arr2["seq"]=getpregmsg($str,$rule,1);
		$Arr2["seq"]=$i;
		$rule='<a class="list-title*>[name]</a>';
		$Arr2["word"]=getpregmsg($str,$rule,1);
		$rule='<span class="icon-*">[name]</span>';
		$Arr2["count"]=getpregmsg($str,$rule,1);
		$rule='<i class="icon [name]"></i>';
		$Arr2["icon"]=getpregmsg($str,$rule,1);
		$Arr2["dhi"]=$dhi;
		if(!empty($Arr2["word"])){
		//print_r($Arr2);
		$i++;
		//exit;
		realtimehot_amend($Arr2,$DB_realtimehot);
		//$Arr3[]=$Arr2;
		}
	}
}

function realtimehot_fetch(){
	baidu_realtimehot_fetch();
//return true;
	global $DB_realtimehot;
	extract($_GET);
	tbl_create_realtimehot();

	/*
	$urlencode=urlencode("金克深圳房产每日风云榜");
	$long_url="jinke.pe.hu/JINKE_".$urlencode."170712.html";
	$url=sinaShortenUrl($long_url);
	echo 
	sinaExpandUrl($url);
	exit;

	*/
	//exit;
	global $snoopy;
	$snoopy=new snoopy;
	$cookie="_T_WM=05fdab393ea646da27d202fd845c6a7c; SUB=_2A250Xcz_DeThGeRG7lsU-C3FyziIHXVXodS3rDV6PUJbkdBeLRGkkW1PLSLtf-_It3-XtzR-XhnrTYPaTg..; SUHB=0U1YJK0ysBgHnx; SCF=AqnyDg0JspDzECpgzPFvGqlRurgjqAq3mSSdoNiNGEvtn42MqOLoPWYlSJHMxNv1TrImncp6L-B1pgFH7dLpwRg.; SSOLoginState=1499053231";
	//$snoopy->agent="NOKIAE61i/UCBrowser7.7.0.81/28/999";
	$snoopy->agent="Mozilla/5.0 (Linux; U; Android 1.0; en-us; dream) AppleWebKit/5.25.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2";

	$url="http://s.weibo.com/top/summary?cate=realtimehot";
	$str=fetchurl($url);
	//echo $str;
	$rule='<ul class="list_a">[name]</ul>';
	$rule='<section class="list">[name]</section>';
	//$arr=getpregmsg($str,$rule,2);
	//echo $str;
	$arr=explode('<section class="list">',$str);
	//echo
	$str= $arr[1];
	$rule='<li>[name]</li>';
	$Arr1=getpregmsg($str,$rule,2);
	//print_r($Arr1);
	//echo 
	$dhi=date("dHi");
	//showlink();
	foreach($Arr1 as $str ){
	//echo $str;
		$rule="<strong*>[name]</strong>";
		$Arr2["seq"]=getpregmsg($str,$rule,1);
		$rule='<span>[name]<em>';
		$Arr2["word"]=getpregmsg($str,$rule,1);
		$rule='<em><em>[name]</em></em>';
		$Arr2["count"]=getpregmsg($str,$rule,1);
		$rule='<i class="icon [name]"></i>';
		$Arr2["icon"]=getpregmsg($str,$rule,1);
		$Arr2["dhi"]=$dhi;
if(!empty($Arr2["word"])){
//print_r($Arr2);		
realtimehot_amend($Arr2,$DB_realtimehot);
}
		//$Arr3[]=$Arr2;
		
	}
	//exit;
}

function realtimehot_listnew(){
	global $DB_realtimehot,$dbname_realtimehot;
	$DB_realtimehot=new SQLite($dbname_realtimehot); 
	extract($_GET);
	if($icon<>"all"){
		$where="icon='$icon'";
	}else{
		$where="1=1 ";
	}
	//echo
	//$sql="select dhi,word,sum(count) as totalcount,count(*) as total,sum(count)/count(*) as pingjun, count from (select * from realtimehot order by word,dhi  limit 0,5000) as aa where $where  group by word order by dhi desc";
	$sql1="select dhi ,word from (select * from realtimehot order by dhi  desc  limit 0,5000) as a1 where $where  group by word order by dhi ";
	$sql2="select dhi as dhi1,word,sum(count) as totalcount,count(*) as total,sum(count)/count(*) as pingjun,count from (select * from (select * from realtimehot order by dhi desc  limit 0,5000) order by dhi) as a2 where $where  group by word order by dhi desc";

	if(empty($dhiorder))$dhiorder="aa1.dhi";
	$sql="select * from ($sql1) as aa1 left join ($sql2) as aa2 on aa1.word = aa2.word where  aa1.word = aa2.word order by $dhiorder desc,count desc";
	//echo $sql;
	
/*
		echo "<pre>";
	$A_all=$DB_realtimehot->queryall($sql1);
	print_r($A_all);
	$A_all=$DB_realtimehot->queryall($sql2);
	print_r($A_all);
	*/
	$A_all=$DB_realtimehot->queryall($sql);
	showlink();
	realtimehot_fetch();
	echo "<br>";
	foreach($A_all as $A_row){
		extract($A_row);
		$dhi=substr($dhi,2);
		$dhi1=substr($dhi1,2);
		$word2=str_replace(" ","",$word);
		//$link1="https://m.weibo.cn/p/100103type%3D1%26q%3D$word?type=all&queryVal=$word&featurecode=20000320&luicode=10000011&lfid=106003type%3D1&title=$word";
		$link1="https://s.weibo.com/weibo/$word&Refer=top";
		$search_weibo="<A HREF=\"$link1\">$word</A>";
		//$search_weibo="<A HREF=\"http://s.weibo.com/weibo/$word&Refer=top\">#$word##金克热点文摘#</A>";
		$total="<A HREF=\"https://www.baidu.com/baidu?wd=$word&ie=utf-8\">$total</A>";
		$pingjun="<A HREF=\"https://weibo.cn/search/mblog/?keyword=$word&filter=hasori\">$pingjun</A>";
		//$total="<A HREF=\"https://m.toutiao.com/search/?keyword=$word&from=search_tab\">$total</A>";
		$count="<A HREF=\"caiji.toutiao.search.php?word=$word2\" >$count</A>";

		echo "$dhi-$dhi1 $total $count $pingjun $search_weibo  \n<br>";
		
	}
	return $A_all;

}

function realtimehot_listlast(){
	global $DB_realtimehot;
	extract($_GET);
	$sql="select * from realtimehot  order by dhi desc,icon desc,count desc limit 0,500 ";
	$A_all=$DB_realtimehot->queryall($sql);
	showlink();
	realtimehot_fetch();
	echo "<br>";
	foreach($A_all as $A_row){
		extract($A_row);
		$dhi=substr($dhi,2);
		$dhi1=substr($dhi1,2);
		$search_weibo="<A HREF=\"http://s.weibo.com/weibo/$word&Refer=top\">$word</A>";
		$search_baidu="<A HREF=\"https://www.baidu.com/baidu?wd=$word&ie=utf-8\">$count</A>";
		
		echo "$dhi $icon $search_baidu/$search_weibo  \n<br>";
		
	}
	return $A_all;

}
//
ob_start();
realtimehot_fetch();
$A_all=realtimehot_list();
//print_r($A_all);
//echo $str;
$content = ob_get_contents();
ob_end_clean();
$content=mb_convert_encoding($content, "GBK", "UTF-8");  
echo $content;
exit;







function realtimehot_amend($A_row,$DB){
	extract($A_row);
	if(empty($seq) && empty($word)){
		echo "fetch null/n<br>";
		//exit;
	}
	//echo $seq."$word\n";
	if(!empty($dhi)){
		$sql="select dhi from realtimehot where dhi =\"$dhi\" and seq=\"$seq\" ";
		$dhi=$DB->queryitem($sql);
		if(!empty($dhi)){
			$sql=make_sql_updatval($A_row);
			//echo "\n<br>";
			//echo 
			$sql="update \"realtimehot\" set ".$sql." where dhi =\"$dhi\"  and seq=\"$seq\"";
			//echo "\n<br>";
			$DB->query($sql);
		}else{
			$sql=make_sql_additem($A_row);
			//echo 
			$sql="insert into \"realtimehot\"  ".$sql;
			//echo "\n<br>";
			$DB->query($sql);
		}
		//echo $sql;
		//exit;
		//break;
	}else{
		echo "dhi empty";
	}
}


function tbl_create_realtimehot(){
	global $DB_realtimehot;
	//echo 
	$sql="
CREATE TABLE realtimehot
(
[ID] integer primary key autoincrement,
[dhi] char(6),
[icon] char(10) default '',
[seq]  tinyint(2) default 1,
[word]  varchar(200),
[count]  integer 

);
	";
	//var_dump($DB_realtimehot);

	$DB_realtimehot->query($sql);
	$sql="
CREATE UNIQUE INDEX 'dhiseq' ON 'realtimehot' ('dhi' ASC,seq ASC);
	";
	$DB_realtimehot->query($sql);



}

function showlink(){

?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0, maximum-scale=2.0" />
<meta name="MobileOptimized" content="240"/>
<base target='_blank'>
<title>热点采集<?=$actfunc;?></title>
</head>
<body style="white-space:nowrap;">
<a href="caiji.weibo.cn.realtimehot.php?actfunc=realtimehot_fetch">热门采集</a>
<a href="caiji.weibo.cn.realtimehot.php?actfunc=realtimehot_listnew&icon=new">新</a>
<a href="caiji.weibo.cn.realtimehot.php?actfunc=realtimehot_listnew&icon=hot">热</a>
<a href="caiji.weibo.cn.realtimehot.php?actfunc=realtimehot_listnew&icon=all&dhiorder=aa1.dhi">全1</a>
<a href="caiji.weibo.cn.realtimehot.php?actfunc=realtimehot_listnew&icon=all&dhiorder=aa2.dhi1">全2</a>
<a href="caiji.weibo.cn.realtimehot.php?actfunc=realtimehot_listlast">500</a>

<?}?>