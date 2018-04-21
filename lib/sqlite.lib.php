<?php  

/*** 
$conn = new PDO('sqlite:SpiderResult.db3');
if ($conn){
  echo 'connect ok';
}
else{
  echo 'connect bad';
}
foreach ($conn->query("SELECT ID FROM Content;") as $row)
{
  echo "$row[0]";
}
//应用举例 
//require_once('cls_sqlite.php'); 
//创建实例 
$DB=new SQLite('blog.db3'); //这个数据库文件名字任意 
//创建数据库表。 
$DB->query("create table test(id integer primary key,title varchar(50))"); 
//接下来添加数据 
$DB->query("insert into test(title) values('泡菜')"); 
$DB->query("insert into test(title) values('蓝雨')"); 
$DB->query("insert into test(title) values('Ajan')"); 
$DB->query("insert into test(title) values('傲雪蓝天')"); 
//读取数据 
print_r($DB->getlist('select * from test order by id desc')); 
//更新数据 
$DB->query('update test set title = "三大" where id = 9'); 
 
$DB=new SQLite('blog.db3'); //这个数据库文件名字任意 
print_r($DB->getlist('select * from test order by id desc')); 
var_dump($DB);


$conn = new PDO('sqlite:blog.db3');
$conn  =  sqlite_open('blog.db3');  

$DB=new SQLite('SpiderResult.db3'); //这个数据库文件名字任意 
print_r($DB->getlist('SELECT  c2.连接, c2.专辑名称, c2.个数 FROM  content c2 limit 0,1')); 
var_dump($DB);
***/   

/*

$arr[]=array("/33159791/album/3875050","怎样买意外险","317");
$arr[]=array("/33159791/album/3651021","保险资讯","308");
$arr[]=array("/33159791/album/3751244","退休理财与养老保险","275");
$arr[]=array("/33159791/album/4015714","保险保单合同","172");
$arr[]=array("/33159791/album/3753519","专家谈保险理财","151");
$arr[]=array("/33159791/album/3862457","理财规划学习","149");
$arr[]=array("/33159791/album/3525350","少儿保险","139");
$arr[]=array("/33159791/album/3886788","重疾保险","101");
$arr[]=array("/33159791/album/3937140","如何购买分红险","86");
$arr[]=array("/33159791/album/3725170","认识医疗保险","86");
$arr[]=array("/33159791/album/3710512","白领保险理财","82");
$arr[]=array("/33159791/album/3975987","保险理赔","74");
$arr[]=array("/33159791/album/3713859","保险理财知识大全","65");
$arr[]=array("/33159791/album/3759645","财产保险那些事儿","61");
$arr[]=array("/33159791/album/3724658","2016保险理财新知","54");
$arr[]=array("/33159791/album/4048393","保险与投资","53");
$arr[]=array("/33159791/album/3708960","80后保险理财","50");
$arr[]=array("/33159791/album/3751681","女人保险理财技巧","50");
$arr[]=array("/33159791/album/4048409","保险基础之 健康险知识","48");
$arr[]=array("/33159791/album/3416559","为什么买保险","48");
$arr[]=array("/33159791/album/3546426","WTO 时代的保险营销变革","44");
$arr[]=array("/33159791/album/3949094","万能险知识","43");
$arr[]=array("/33159791/album/3752959","单身贵族如何保险理财","40");
$arr[]=array("/33159791/album/4051612","保险常识之家财险","26");
$arr[]=array("/33159791/album/3405402","保险常识2","23");
$arr[]=array("/33159791/album/3961577","爱情保险","12");
makelist_jinke($arr);


$DB=new SQLite('..\\Data\\47\\SpiderResult.db3'); //这个数据库文件名字任意 
$A_content=($DB->getlist('SELECT  c2.连接, c2.专辑名称, c2.个数 FROM  content c2 limit 0,100')); 
makelist_jinke($A_content);
function makelist_jinke($A_content){
	//生成喜马拉雅专辑列表，文本打印导出具体采集的地址然后放到采集器批量采集
	foreach($A_content as $A_row){
		//$geshu=$A_row["个数"];
		//$lianjie=$A_row["连接"];
		//$mingcheng=$A_row["专辑名称"];
		$lianjie=$A_row[0];
		$mingcheng=$A_row[1];
		$geshu=$A_row[2];
		makepage_jinke($geshu,$lianjie,$url);
	}

}
function makepage_jinke($geshu,$lianjie,$url=""){
	//生成喜马拉雅专辑页面链接
	$yeshu=ceil($geshu/100);
	$i=1;
	$url="http://www.ximalaya.com%s?page=%s";
	while($i<=$yeshu){
		echo 
		//$link=$url.$lianjie.$i;
		$link=sprintf($url,$lianjie,$i);

		echo "\n";
		$i++;
	}

}




*/

//======right====================================

class SQLite  
{  
	function __construct($file)  
	{  
		try  
		{  
			$this->connection=new PDO('sqlite:'.$file);  
		}  
		catch(PDOException $e)  
		{  
			try  
			{  
				$this->connection=new PDO('sqlite2:'.$file);  
			}  
			catch(PDOException $e)  
			{  
				exit($file.$e.'<br> sqlite connect error!');  
			}  
		}  
	}  
  
	function __destruct()  
	{  
		$this->connection=null;  
	}  
  
	function query($sql) //直接运行SQL，可用于更新、删除数据  
	{  
		try  
		{  
			return $this->connection->query($sql);  
		}  
		catch(PDOException $e)  
		{  
			exit('error!<br>'.$e);  
		}  
		
	}  
  
	function getlist($sql) //取得记录列表  
	{  
		$recordlist=array();  

		try  
		{  
			foreach($this->query($sql) as $rstmp)  
			{  
				$recordlist[]=$rstmp;  
			}  
		}  
		catch(PDOException $e)  
		{  
			exit('error!<br>'.$e);  
		}  
		return $recordlist;  
	}  
	function queryall($sql) //取得多行记录列表  
	{  
		$A_all=$this->getlist($sql);
		return $A_all;  
	}  
	function queryrow($sql) //取得单行记录列表  
	{  
		$A_all=$this->getlist($sql);
		$A_row=$A_all[0];
		return $A_row;  
	}  
	function queryitem($sql) //取得单个记录  
	{  
		//echo $sql;
		$A_all=$this->getlist($sql);
		//print_r($A_all);
		$item=$A_all[0][0];
		return $item;
	}  
	function querycol($sql) //取得单个记录  
	{  
		$A_all=$this->getlist($sql);
		$n=count($A_all);
		if($n>0){
			foreach ($A_all as $A_row){
				$A_col[]=$A_row[0];
			}
			return $A_col;
		}else{
			return false;
		}
	}  
  
  
	function Execute($sql)  
	{  
		return $this->query($sql)->fetch();  
	}  
  
	function RecordArray($sql)  
	{  
		return $this->query($sql)->fetchAll();  
	}  
  
	function RecordCount($sql)  
	{  
		return count($this->RecordArray($sql));  
	}  
  
	function RecordLastID()  
	{  
		return $this->connection->lastInsertId();  
	}  
}  
?>  