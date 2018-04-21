<?php

/*************************************************
 *
 * Snoopy - the PHP net client
 * Author: Monte Ohrt <monte@ohrt.com>
 * Copyright (c): 1999-2014, all rights reserved
 * Version: 2.0.0
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * You may contact the author of Snoopy by e-mail at:
 * monte@ohrt.com
 *
 * The latest version of Snoopy can be obtained from:
 * http://snoopy.sourceforge.net/
 *************************************************/
class Snoopy
{
    /**** Public variables ****/

    /* user definable vars */

    var $scheme = 'http'; // http or https
    var $host = "www.php.net"; // host name we are connecting to
    var $port = 80; // port we are connecting to
    var $proxy_host = ""; // proxy host to use
    var $proxy_port = ""; // proxy port to use
    var $proxy_user = ""; // proxy user to use
    var $proxy_pass = ""; // proxy password to use

	var $agent			=	"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";	// agent we masquerade as
    var $referer = ""; // referer info to pass
    var $cookies = array(); // array of cookies to pass
    // $cookies["username"]="joe";
    var $rawheaders = array(); // array of raw headers to send
    // $rawheaders["Content-type"]="text/html";

    var $maxredirs = 5; // http redirection depth maximum. 0 = disallow
    var $lastredirectaddr = ""; // contains address of last redirected address
    var $offsiteok = true; // allows redirection off-site
    var $maxframes = 0; // frame content depth maximum. 0 = disallow
    var $expandlinks = true; // expand links to fully qualified URLs.
    // this only applies to fetchlinks()
    // submitlinks(), and submittext()
    var $passcookies = true; // pass set cookies back through redirects
    // NOTE: this currently does not respect
    // dates, domains or paths.

    var $user = ""; // user for http authentication
    var $pass = ""; // password for http authentication

    // http accept types
    var $accept = "image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, */*";

    var $results = ""; // where the content is put

    var $error = ""; // error messages sent here
    var $response_code = ""; // response code returned from server
    var $headers = array(); // headers returned from server sent here
    var $maxlength = 500000; // max return data length (body)
    var $read_timeout = 0; // timeout on read operations, in seconds
    // supported only since PHP 4 Beta 4
    // set to 0 to disallow timeouts
    var $timed_out = false; // if a read operation timed out
    var $status = 0; // http request status

    var $temp_dir = "/tmp"; // temporary directory that the webserver
    // has permission to write to.
    // under Windows, this should be C:\temp

    var $curl_path = false;
    // deprecated, snoopy no longer uses curl for https requests,
    // but instead requires the openssl extension.

    // send Accept-encoding: gzip?
    var $use_gzip = true;

    // file or directory with CA certificates to verify remote host with
    var $cafile;
    var $capath;

    /**** Private variables ****/

    var $_maxlinelen = 4096; // max line length (headers)

    var $_httpmethod = "GET"; // default http request method
    var $_httpversion = "HTTP/1.0"; // default http request version
    var $_submit_method = "POST"; // default submit method
    var $_submit_type = "application/x-www-form-urlencoded"; // default submit type
    var $_mime_boundary = ""; // MIME boundary for multipart/form-data submit type
    var $_redirectaddr = false; // will be set if page fetched is a redirect
    var $_redirectdepth = 0; // increments on an http redirect
    var $_frameurls = array(); // frame src urls
    var $_framedepth = 0; // increments on frame depth

    var $_isproxy = false; // set if using a proxy server
    var $_fp_timeout = 30; // timeout for socket connection

    /*======================================================================*\
        Function:	fetch
        Purpose:	fetch the contents of a web page
                    (and possibly other protocols in the
                    future like ftp, nntp, gopher, etc.)
        Input:		$URI	the location of the page to fetch
        Output:		$this->results	the output text from the fetch
    \*======================================================================*/
}

/*
if(file_exists("./CurlAutoLogin.php")){
	require_once("./CurlAutoLogin.php");
}else{
	if(file_exists("../lib/CurlAutoLogin.php")){
		require_once("'./lib/CurlAutoLogin.php");
	}else{
		echo "<br>\ncan not load 'CurlAutoLogin.php'\n<br>";
	}
}

*/
function makecookie($str){
	$Arr1=explode(";",$str);
	foreach ($Arr1 as $str1 ){
		$Arr2=explode("=",$str1);
		$key=trim($Arr2[0]);
		$val=trim($Arr2[1]);
		$Arr3[$key]=$val;
	}
	return $Arr3;
}

/*
function fetchurl($url){
	global $snoopy;
	$snoopy->fetch($url);
	$str=$snoopy->results;
	return $str;
}
*/
function gzdecode2($data){
//echo
$bin=substr($data,0,2);
$strInfo = @unpack("C2chars", $bin);
//echo
$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
//exit;
$isGzip = 0;
switch ($typeCode)
{
	case 31139:
		//网站开启了gzip?
		$isGzip = 1;
		break;
	default:
		$isGzip = 0;
}
if($isGzip){
	$d=gzdecode($data);
	return $d;
	/*
	$g=tempnam("/tmp","ff");
	@file_put_contents($g,$data);
	ob_start();
	readgzfile($g);
	$d=ob_get_clean();
	return $d;
	*/
}else{
	return $data;
}
}
function fetch2url($url){
	$ch = curl_init(); 
	global $cookie,$ua;
	if(empty($ua))
	//echo
		$ua="Mozilla/5.0 (Linux; Android 7.0; MHA-AL00 Build/HUAWEIMHA-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/64.0.3282.137 Mobile Safari/537.36";
	//$ua=$_SERVER["HTTP_USER_AGENT"];
	curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	if(!empty($cookie)){
		curl_setopt($ch, CURLOPT_COOKIESESSION, true); 
		curl_setopt($ch, CURLOPT_COOKIE, $cookie); 
	}
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_VERBOSE, true); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
//	 curl_setopt($ch,CURLOPT_ENCODING,'gzip');
	
	curl_setopt($ch, CURLOPT_AUTOREFERER, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	$ret = curl_exec($ch); 
	$info = curl_getinfo($ch); 
	curl_close($ch);
	$ret=gzdecode2($ret);
//echo $ret;exit;
	//echo
	$isutf8=strpos($ret,"set=utf-8\">");
	$isgbk=strpos($ret,"set=gb");
//echo "---$isutf8 --$isgbk ----$ret -";exit;


	if($isgbk>1){
		$ret=convertToUTF8($ret);
	}else{
	if($isutf8==0){
		$rule="<title>[name]</title>";
		$title=getpregmsg($ret,$rule,1);
		$charset = mb_detect_encoding($title, array('ASCII','UTF-8','GB2312','GBK','BIG5','ISO-8859-1'));
		if (strcasecmp($charset,'UTF-8') != 0) {
			$ret=convertToUTF8($ret);
		}
	}}
	return $ret;
}
function fetchurl($url){//获取微博页面
	global $snoopy;
		$str=curlPost($url);
		return $str;
		/*彻底放弃snoopy
    $SSL = substr($url, 0, 8) == "https://" ? true : false; 
	if ($SSL){
		$str=curlPost($url);
		return $str;
	}
	if(empty($snoopy->cookies)){
		global $cookie;
		$Arr1=makecookie($cookie);
		$snoopy->cookies=$Arr1;
	} 
	$snoopy->fetch($url);
//var_dump($snoopy);
	$str=$snoopy->results;
	return $str;
	*/
}
function fetchurl_cookies($url){
	global $snoopy;
	if(empty($snoopy->cookies)){
		global $cookie;
		$Arr1=makecookie($cookie);
		$snoopy->cookies=$Arr1;
	} 
	$snoopy->fetch($url);
	$str=$snoopy->results;
	return $str;
}


function postform($url,$formvars){//获取微博页面
	global $snoopy;
	if(empty($snoopy->cookies)){
		global $cookie;
		$Arr1=makecookie($cookie);
		$snoopy->cookies=$Arr1;
	} 
	$snoopy->set_submit_normal();
	$formvars["uname"]="$uname";
	$formvars["step"]="submit";
	$snoopy->submit($url, $formvars);
	$str=$snoopy->results;
	return $str;
}







function curlPost($url, $A_data=false, $timeout = 30, $CA = false){   
    $cacert = getcwd() . '/cacert.pem'; //CA根证书 
    $SSL = substr($url, 0, 8) == "https://" ? true : false; 
	global $snoopy;
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2); 
    if(!empty($snoopy->referer))curl_setopt($ch, CURLOPT_REFERER, $snoopy->referer); 
    if(!empty($snoopy->agent))curl_setopt($ch, CURLOPT_USERAGENT,$snoopy->agent);//$_SERVER['HTTP_USER_AGENT']
    if ($SSL && $CA) { 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书 
        curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布） 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配 
    } else if ($SSL && !$CA) { 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名 
    } 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免A_data数据过长问题 
	if($A_data<>false){
	    curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $A_data); 
    }
    //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($A_data)); //data with URLEncode 
	global $cookie,$ua;
if(!empty($ua))curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	if(!empty($cookie)){
		curl_setopt($ch, CURLOPT_COOKIESESSION, true); 
		curl_setopt($ch, CURLOPT_COOKIE, $cookie); 
	}
    $ret = curl_exec($ch); 
   // var_dump(curl_error($ch));  //查看报错信息 

    curl_close($ch); 
    return $ret;   
}    

function http_request($url,$timeout=30,$header=array()){ 
        if (!function_exists('curl_init')) { 
            throw new Exception('server not install curl'); 
        } 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HEADER, true); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	echo "222222222";
        if (!empty($header)) { 
	echo "3333333333";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
	echo "5555555";
        } 
	echo "444444444444";
        $data = curl_exec($ch); 
		echo $data;
        list($header, $data) = explode("\r\n\r\n", $data); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        if ($http_code == 301 || $http_code == 302) { 
            $matches = array(); 
            preg_match('/Location:(.*?)\n/', $header, $matches); 
            $url = trim(array_pop($matches)); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HEADER, false); 
            $data = curl_exec($ch); 
        } 

        if ($data == false) { 
	echo "1111111111";
            curl_close($ch); 
        } 
        @curl_close($ch); 
        return $data; 
}  

/**
 * 模拟登录
 */
/*
  
//初始化变量
$cookie_file = "tmp.cookie";
$login_url = "http://xxx.com/logon.php";
$verify_code_url = "http://xxx.com/verifyCode.php";
 
echo "正在获取COOKIE...\n";
$curlj = curl_init();
$timeout = 5;
curl_setopt($curl, CURLOPT_URL, $login_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($curl,CURLOPT_COOKIEJAR,$cookie_file); //获取COOKIE并存储
$contents = curl_exec($curl);
curl_close($curl);
 
echo "COOKIE获取完成，正在取验证码...\n";
//取出验证码
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $verify_code_url);
curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$img = curl_exec($curl);
curl_close($curl);
 
$fp = fopen("verifyCode.jpg","w");
fwrite($fp,$img);
fclose($fp);
echo "验证码取出完成，正在休眠，20秒内请把验证码填入code.txt并保存\n";
//停止运行20秒
sleep(20);
 
echo "休眠完成，开始取验证码...\n";
$code = file_get_contents("code.txt");
echo "验证码成功取出：$code\n";
echo "正在准备模拟登录...\n";
 
$post = "username=maben&pwd=hahahaha&verifycode=$code";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
$result=curl_exec($curl);
curl_close($curl);
 
//这一块根据自己抓包获取到的网站上的数据来做判断
if(substr_count($result,"登录成功")){
 echo "登录成功\n";
}else{
 echo "登录失败\n";
 exit;
}
 
//OK，开始做你想做的事吧。。。。。
 
 */

?>