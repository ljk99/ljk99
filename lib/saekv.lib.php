<?php
/*
Git仓库信息
仓库地址	https://git.sinacloud.com/jkwz
用户名	bosska@sina.com
密码	您的安全密码 忘记密码?
Git代码部署说明
在你应用代码目录里，克隆git远程仓库
$ git clone https://git.sinacloud.com/jkwz
输入您的安全邮箱和密码。
$ cd jkwz

编辑代码并将代码部署到 `origin` 的版本1。
$ git add .
$ git commit -m 'Init my first app'
$ git push origin 1
52nmo5z2jz
zkwm352m1wx213l14k5xih3254y1xwjywxz5l4h5
*/
if(class_exists("SaeKV")){
$kv = new SaeKV();
// 初始化SaeKV对象
$ret = $kv->init("52nmo5z2jz"); //访问授权应用的数据
//var_dump($ret);
}
// 增加key-value
function kvadd($k,$v){
        global $kv;
        $ret = $kv->add($k, $v);
        //var_dump($ret);
}
function kvset($k,$v){
        // 更新key-value
        global $kv;
        $ret = $kv->set( $k, $v );
        //var_dump($ret);
}

function kvreplace ($k,$v){
        // 替换key-value
        global $kv;
        $ret = $kv->replace( $k, $v );
        //var_dump($ret);
}

function kvget($k){
        // 获得key-value
        global $kv;
        $ret = $kv->get($k);
    //echo $k."\\";
        //var_dump($ret);
         return $ret;
}

function kvdelete ($k){
        // 删除key-value
        global $kv;
        $ret = $kv->delete($k);
        //var_dump($ret);
}

/*
// 一次获取多个key-values
$keys = array();
array_push($keys, 'abc1');
array_push($keys, 'abc2');
array_push($keys, 'abc3');
$ret = $kv->mget($keys);
var_dump($ret);

// 前缀范围查找key-values
$ret = $kv->pkrget('abc', 3);
var_dump($ret);

// 循环获取所有key-values
$ret = $kv->pkrget('', 100);
while (true) {
   var_dump($ret);
   end($ret);
   $start_key = key($ret);
   $i = count($ret);
   if ($i < 100) break;
   $ret = $kv->pkrget('', 100, $start_key);
}

// 获取选项信息
$opts = $kv->get_options();
print_r($opts);

// 设置选项信息 (关闭默认urlencode key选项)
$opts = array('encodekey' => 0);
$ret = $kv->set_options($opts);
var_dump($ret);
*/
if(class_exists("SaeKV")){
	$kv = new SaeKV();

	// 初始化KVClient对象
	$ret = $kv->init("52nmo5z2jz");
}else{


	class esohoKV{
		var $arr= array();
		var $path="../sqlite/sqlite/hot/";
		function set($key,$val){
		$fn=$this->path . $key;
		file_put_contents($fn,$val);
			//$this->arr[$key]=$val;
		}
		function get($key){
		$fn=$this->path . $key;
		if(file_exists($fn)){
		$val=file_get_contents($fn);
			return $val;
		}else{
			return false;
		}
		}
		function delete($key){
			unset($this->arr[$key]);
		}
	}
	$kv= new esohoKV;
}

/*

var_dump($ret);

// 更新key-value
$ret = $kv->set('abc', 'aaaaaa');
var_dump($ret);

// 获得key-value
$ret = $kv->get('abc');
var_dump($ret);

// 删除key-value
$ret = $kv->delete('abc');
var_dump($ret);

$ret = $kv->get_info ('abc');
var_dump($ret);
// 一次获取多个key-values
$keys = array();
array_push($keys, 'abc1');
array_push($keys, 'abc2');
array_push($keys, 'abc3');
$ret = $kv->mget($keys);
var_dump($ret);

// 前缀范围查找key-values
$ret = $kv->pkrget('abc', 3);
var_dump($ret);

// 循环获取所有key-values
$ret = $kv->pkrget('', 100);
while (true) {
	var_dump($ret);
	end($ret);
	$start_key = key($ret);
	$i = count($ret);
	if ($i < 100) break;
	$ret = $kv->pkrget('', 100, $start_key);
}

//前缀总数
*/

function saekvset($key,$val){
	global $kv;
	$ret = $kv->set($key,$val);
	return $ret;
}
function saekvget($key){
	global $kv;
	$ret = $kv->get($key);
	return $ret;
}


function saekvdelete($key){
	global $kv;
	$ret = $kv->delete($key);
	return $ret;
}

function saekvtotalpage($prefix="",$size=100){
	global $kv;
	$ret = $kv->pkrget($prefix, $size);
	$total=0;
	while (true) {
		$total++;
		end($ret);
		$start_key = key($ret);
		$i = count($ret);
		if ($i < $size) break;
		$ret = $kv->pkrget($prefix, $size, $start_key);
	}
	return $total;
}

//前缀第几页
function saekvpagelist($prefix="",$page=1,$size=100){
	global $kv;
	$ret = $kv->pkrget($prefix, $size);
	$total=0;
	while (true) {
		$total++;
		if($page===$total)return $ret;
		end($ret);
		$start_key = key($ret);
		$i = count($ret);
		if ($i < $size) break;
		$ret[] = $kv->pkrget($prefix, $size, $start_key);
	}
	return $ret;
}


/*
$page=(empty($_GET["page"])?1:$_GET["page"]);
$arr=saekvpagelist("outlink_",$page);

foreach($arr as $key=>$val ){
	

}

*/
function saekvcleanall($prefix=""){
	global $kv;

	$ret = $kv->pkrget($prefix, 100);
	$i1=0;
	while (true) {
		$i1++;
		foreach($ret as $key=>$val ){
			saekvdelete($key);
		}
		var_dump($ret);
		$i = count($ret);
		if ($i < 1) break;
		$ret = $kv->pkrget($prefix, 100);
		if($i1>10)break;
	}

}
function array_msort($arr,$keys,$type='desc'){
//多维数组排序
$keysvalue = array();
$new_array = array();
 foreach ($arr as $k=>$v){
  $keysvalue[$k] = $v[$keys];
 }
 if($type == 'asc'){
  asort($keysvalue);
 }else{
  arsort($keysvalue);
 }
 reset($keysvalue);
 foreach ($keysvalue as $k=>$v){
  $new_array[$k] = $arr[$k];
 }
 return $new_array; 
}
?>