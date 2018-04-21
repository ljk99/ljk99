<?

//通用函数库，不跟某一个具体的程序相关

//截取子字符串，支持中文
//每个中文汉字看成一个字符
//echo string_substring("1A辽2B宁3C省4D抚5E顺6F市7G新8H宾9I县",4,5);
//会返回B宁3C省，位置从0开始
//http://www.szsm.net/list/wap02001l.php

/*
$filename = "http://www.chinasoftwareunion.com/index.asp";
echo strip_tags($str);
//strip_tags(

*/
function ccStrLen($str) #计算中英文混合字符串的长度 
{ 
	$ccLen=0; 
	$ascLen=strlen($str); 
	$ind=0; 
	$hasCC=ereg("[xA1-xFE]",$str); #判断是否有汉字 
	$hasAsc=ereg("[x01-xA0]",$str); #判断是否有ASCII字符 
	if($hasCC && !$hasAsc) #只有汉字的情况 
	return strlen($str)/2; 
	if(!$hasCC && $hasAsc) #只有Ascii字符的情况 
	return strlen($str); 
	for($ind=0;$ind<$ascLen;$ind++) 
	{ 
		if(ord(substr($str,$ind,1))>0xa0){
			$ccLen++; 
			$ind++;
		}else { 
			$ccLen++; 
		} 
	} 
	return $ccLen; 
} 
function msubstr($str, $start, $len){
	$tmpstr = "";
	$strlen = $start + $len;
	for($i = 0; $i < $strlen; $i++){
	if(ord(substr($str, $i, 1)) > 0xa0){
	$tmpstr .= substr($str, $i, 2);
	$i++;
	}else
	$tmpstr .= substr($str, $i, 1);
	}
	return $tmpstr;
}

function substr_utf8($str, $start=0, $length=-1, $return_ary=false) {
    $len = strlen($str);if ($length == -1) $length = $len;
    $r = array();
    $n = 0;
    $m = 0;
    
    for($i = 0; $i < $len; $i++) {
        $x = substr($str, $i, 1);
        $a = base_convert(ord($x), 10, 2);
        $a = substr('00000000'.$a, -8);
        if ($n < $start) {
            if (substr($a, 0, 1) == 0) {
            }elseif (substr($a, 0, 3) == 110) {
                $i += 1;
            }elseif (substr($a, 0, 4) == 1110) {
                $i += 2;
            }
            $n++;
        }else {
            if (substr($a, 0, 1) == 0) {
                $r[] = substr($str, $i, 1);
            }elseif (substr($a, 0, 3) == 110) {
                $r[] = substr($str, $i, 2);
                $i += 1;
            }elseif (substr($a, 0, 4) == 1110) {
                $r[] = substr($str, $i, 3);
                $i += 2;
            }else {
                $r[] = '';
            }
            if (++$m >= $length) {
                break;
            }
        }
    }
    
    return $return_ary ? $r : implode("",$r);
} 

function string_substring($string,$start,$length)
{
    $countstart=0;
    $countlength=0;
    $printstring="";
    for($i=0;$i<strlen($string);$i++)
    {
        while($countstart!=$start)
        {
            $countstart++;
            if(ord(substr($string,$i,1))>128)
            {
                $i+=2;
            }
            else
            {
                $i++;
            }
        }
        while($countlength!=$length)
        {
            $countlength++;
            if(ord(substr($string,$i,1))>128)
            {
                $printstring.=substr($string,$i,2);
                $i+=2;
            }
            else
            {
                $printstring.=substr($string,$i,1);
                $i++;
            }
        }
    }
    return $printstring;
}

function readfiles($file,$issnoopy=0){
	global $snoopy;
	//echo "dddd";
//$snoopy->proxy_host = "www.php100.com";   
//$snoopy->proxy_port = "8080"; //使用代理   
	if(empty($snoopy->proxy_host) && $issnoopy==0){
		if ($fp = @fopen($file,"r")) {
//	echo "222";
			while($data = fread($fp, 32768)) {
				$file_string .= $data;
			}
			fclose($fp);
			//echo $file_string;
			return $file_string;
		} else {
			return false;
		}
	}else{
		//echo "5555";
		$snoopy->referer = $file; //伪装来源页地址 http_referer   
		$snoopy->fetch($file);
		//echo 
		$str=$snoopy->results;
		return $str;
	}
}
//读文件
function sreadfile($filename, $mode='r', $remote=0, $maxsize=0, $jumpnum=0) {
	if($jumpnum > 5) return '';
	$contents = '';

	if($remote) {
		$httpstas = '';
		$urls = initurl($filename);
		if(empty($urls['url'])) return '';

		$fp = @fsockopen($urls['host'], $urls['port'], $errno, $errstr, 20);
		if($fp) {
			if(!empty($urls['query'])) {
				fputs($fp, "GET $urls[path]?$urls[query] HTTP/1.1\r\n");
			} else {
				fputs($fp, "GET $urls[path] HTTP/1.1\r\n");
			}
			fputs($fp, "Host: $urls[host]\r\n");
			fputs($fp, "Accept: */*\r\n");
			fputs($fp, "Referer: $urls[url]\r\n");
			fputs($fp, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.00; Windows 98)\r\n");
			fputs($fp, "Pragma: no-cache\r\n");
			fputs($fp, "Cache-Control: no-cache\r\n");
			fputs($fp, "Connection: Close\r\n\r\n");

			$httpstas = explode(" ", fgets($fp, 128));
			if($httpstas[1] == 302 || $httpstas[1] == 302) {
				$jumpurl = explode(" ", fgets($fp, 128));
				return sreadfile(trim($jumpurl[1]), 'r', 1, 0, ++$jumpnum);
			} elseif($httpstas[1] != 200) {
				fclose($fp);
				return '';
			}

			$length = 0;
			$size = 1024;
			while (!feof($fp)) {
				$line = trim(fgets($fp, 128));
				$size = $size + 128;
				if(empty($line)) break;
				if(strexists($line, 'Content-Length')) {
					$length = intval(trim(str_replace('Content-Length:', '', $line)));
					if(!empty($maxsize) && $length > $maxsize) {
						fclose($fp);
						return '';
					}
				}
				if(!empty($maxsize) && $size > $maxsize) {
					fclose($fp);
					return '';
				}
			}
			fclose($fp);

			if(@$handle = fopen($urls['url'], $mode)) {
				if(function_exists('stream_get_contents')) {
					$contents = stream_get_contents($handle);
				} else {
					$contents = '';
					while (!feof($handle)) {
						$contents .= fread($handle, 8192);
					}
				}
				fclose($handle);
			} elseif(@$ch = curl_init()) {
				curl_setopt($ch, CURLOPT_URL, $urls['url']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);//timeout
				$contents = curl_exec($ch);
				curl_close($ch);
			} else {
				//无法远程上传
			}
		}
	} else {
		if(@$handle = fopen($filename, $mode)) {
			$contents = fread($handle, filesize($filename));
			fclose($handle);
		}
	}

	return $contents;
}

function writefile($file,$str){
	$fp = fopen ($file, "w");
	fwrite($fp,$str);
	fclose($fp);
	return true;
}
function add2file($str,$file,$pos=0){
	$str_file=readfiles($file);
	if($pos==0){
		$str=$str."\n".$str_file;
	}else{
		if(!empty($str_file))$str=$str_file."\n".$str;
	}
	writefile($file,$str);
}

function getmid($str,$from,$to,$delTags=false){
	$A_from=explode($from,$str);
	$n=count($A_from);
	$A_result=array();
	for($i=1;$i<$n;$i++){
		$str_1=$A_from[$i];
		//print_r($A_from);
		$A_to=explode($to,$str_1);
		//print_r($A_to);
		$str_2=$A_to[0];
		if ($delTags)$str_2=strip_tags($str_2);
		$A_result[$i]=$str_2;
	}
	return $A_result;
}
function getmid_str($str,$from,$to,$i=1,$delTags=false){
	$A_all=getmid($str,$from,$to,$delTags);
	//print_r($A_all);
	$str=$A_all[$i];
	return $str;
}
function getmid_str2($str,$fr,$to){
	$pfr = strpos($str, $fr);
	$pto = strpos($str, $to);
	$lfr = strlen($fr);
	$pos = $pfr+ $lfr ;
	$len = $pto - $pos;
	$str = substr($str,$pos,$len);
	return $str;
}
function getArrFrFile($db,$id){
	$Arr1=file($db);
	$row=$Arr1[$id];
	if (trim($row)!=""){
		$Arr2=explode("|",$row);
	}
	return $Arr2;
}

function file2array($fn){
	$Arr=file($fn);
	if(empty($Arr))return false;
	foreach($Arr as $str ){
		$str=str_replace("\n","",$str);
		$str=str_replace("\r","",$str);
		if ($str<>""){
			$Arr1[]=$str;
		}else{
			break;
		}
	}
	return $Arr1;
}


function row2col($A_array){
	$n=(count($A_array)-1);
	for ($row=0;$row<=$n;$row++){
		for ($col=0;$col<=(count($A_array[$row])-1);$col++){
			$A_array_1[$col][$row]=$A_array[$row][$col];
		}
	}
	return $A_array_1;
}



function list_array($array){
//list_array($test->A_data);
	echo "<br>start-----------<BR>";
	for ($col=0;$col<=(count($array)-1);$col++){
		echo printf("%s:%s#",($col+1),($array[$col]?$array[$col]:"null"));
	}
}


function DateNull($Date){
	if ($Date=="0000-00-00")$Date="";
	return $Date;
}
function TimeNull($Time){
	if ($Time=="00:00:00")$Time="";
	return $Time;
}

function isAdmin(){
	global $ssn_isAdmin;
	//$isAdmin=true;
	if ($ssn_isAdmin){
		return true;
	}else{
		return false;
	}
}

function set_val($C_sql){
	global $O_db;
	$A_row=$O_db->queryrow($C_sql);
	if ($A_row){
		$i=0;
		while ( list( $key, $val ) = each( $A_row ) ) {
			if ($key<>($i/2)){
				global $$key;
				$$key = $val;
				//echo $key.":".$$key."<br>";
			}
			$i=$i+1;
		}
		return true;
	}else return false;
}

function make_sql_additem($A_field){
	if(!$A_field)return false;
	$C_sql="";
	foreach($A_field as $nam=>$val ){
		//$val=addslashes($val);
		if(!is_numeric($nam)){
			$val=str_replace("\"","\"\"",$val);
			$nams.="\"$nam\",";
			$vals.="\"$val\",";
		}
	}
	$nams=substr($nams,0,-1);
	$vals=substr($vals,0,-1);
	$C_sql=" ($nams) values ($vals)";
	return $C_sql; 
}
function make_sql_updatval($A_field){
	if(!$A_field)return false;
	$C_sql="";
	foreach($A_field as $nam=>$val ){
		//$val=addslashes($val);
		$val=str_replace("\"","\"\"",$val);
		$C_sql.="\"$nam\" =\"$val\",";
	}
	$C_sql=substr($C_sql,0,-1);
	return $C_sql; 
}

function make_sql_setval($A_field=false){
	if(!$A_field)global $A_field;
	$n=count($A_field["name"]);
	$C_sql="";
	for	($i=0;$i<$n;$i++){
		$nam=$A_field["name"][$i];
		global $$nam;
		$val=$$nam;
		if ($val){
			if ($val==" ")$val="";
			$C_sql.=" $nam='$val' ,";
		}
	}
	$C_sql=substr($C_sql,0,-1);
	return $C_sql; 
}

function gotourl($adr,$method=1){
//function goto($adr,$method=1){
		$str= "<script language=\"JavaScript\">
		<!--
		location=\"$adr\";
		//-->
		</script>";
		echo $str;
}

function GetCliCookie($name){
	global $_COOKIE;
	return $_COOKIE[$name];
}

function DelCliCookie($name){
//	setcookie($name, "", time(), "/", "list.wonder", 0);
	setcookie($name, "", time());
}

function SetCliCookie($name, $value,$time=false){
//	echo $value;
	if ($time==false)$time=365*30*24*3600;
//	setcookie($name,$value,time()+$time,"/","list.wonder",0);
	setcookie($name,$value,time()+$time);
	if(empty($value)){
		setcookie($name, "", time());
	}
}

function replace_fields($Template,$Arr){
    $Text  = $Template;
	$Keys  = array_keys($Arr);
	//print_r($Keys);
    $nKeys = count($Keys);
    for($i = 0;$i < $nKeys;$i++)				// get each key in turn
    {
        //echo
		$Key   = $Keys[$i];
        $Value = $Arr[$Key];
        //$Text  = ereg_replace("%%" . $Key . "%%",$Value,$Text); // replace with value
		//if(!$Value)$Value="";
        $Text  = str_replace("[[" . $Key . "]]",$Value,$Text); // replace with value
    }
    $Text  = ereg_replace("\[\[[^<]*\]\]", "-",$Text); // replace with value
    return $Text;
}

function list_table($sql,$tpl_itm,$tpl_tbl,$A_title=false){
	if (!$A_title)$A_title=array();
	$A_title["list_item"]=list_item($sql,$tpl_itm);
	$str=replace_fields($tpl_tbl,$A_title);
	return $str;
}

function list_item($sql,$template){
	$A_col=queryall($sql);
	//print_r($A_Key);
	//print_r($A_col);
	if ($A_col){
		foreach ($A_col as $A_row){
			//echo 
			$str.=replace_fields($template,$A_row);
		}
	}
	return $str;
}

//2010-8-28
//PHP中文标点全角转半角
function make_semiangle($str)  
{  
    $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',  
                 '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',  
                 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',  
                 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',  
                 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',  
                 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',  
                 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',  
                 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',  
                 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',  
                 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',  
                 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',  
                 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',  
                 'ｙ' => 'y', 'ｚ' => 'z',  
                 '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',  
                 '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',  
                 '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',  
                 '》' => '>',  
                 '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',  
                 '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',  
                 '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',  
                 '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',  
                 '　' => ' ', '『' => ' ', '』' => ' ');  
 
    return strtr($str, $arr);  
}





function searchweb($url,$rule,$utf8=1,$mod=1){//搜索原创主题微博
	global $snoopy;
	if(empty($snoopy)){
		$str=readfiles($url);
	}else{
		if(empty($snoopy->cookies)){
			global $cookie;
			$Arr1=makecookie($cookie);
			$snoopy->cookies=$Arr1;
		}
		//$snoopy->cookies["SessionID"] = "238472834723489l";
		//print_r($Arr1);
		//exit;
		$snoopy->fetch($url);
		//echo 
		$str=$snoopy->results;
	}
	if($utf8==1)$str=mb_convert_encoding($str, "GBK", "UTF-8");  
	//echo $str;
	if($mod==1){
		$A_content=pregmessage($str, $rule, "name",0);
		//var_dump($A_content);
		if(!empty($A_content[0])){
			return $A_content;
		}else{
			return false;
		}
	}else{
		$A_content=pregmessage($str, $rule, "name",1);
		//var_dump($A_content);
		return $A_content[0];
	}
	
}

function pregmessage($message, $rule, $getstr, $limit=1) {
	$result = array('0'=>'');
	$rule = convertrule($rule);		//转义正则表达式特殊字符串
	$rule = str_replace('\['.$getstr.'\]', '\s*(.+?)\s*', $rule);	//解析为正则表达式
	if($limit == 1) {
		preg_match("/$rule/is", $message, $rarr);
		if(!empty($rarr[1])) {
			$result[0] = $rarr[1];
		}
	} else {
		preg_match_all("/$rule/is", $message, $rarr);
		if(!empty($rarr[1])) {
			$result = $rarr[1];
		}
	}
	return $result;
}

/**
 * 正则规则
 */
function getregularstring($rule, $getstr) {
	$rule = convertrule($rule);		//转义正则表达式特殊字符串
	$rule = str_replace('\['.$getstr.'\]', '\s*(.+?)\s*', $rule);	//解析为正则表达式
	return $rule;
}



/**
 * 转义正则表达式字符串
 */
function convertrule($rule) {
	$rule = preg_quote($rule, "/");		//转义正则表达式
	$rule = str_replace('\*', '.*?', $rule);
	$rule = str_replace('\|', '|', $rule);
	return $rule;
}
function getpregmsg($str, $rule,$limit=1){
	//$rule='<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="[name]" />';
	$A_name=pregmessage($str, $rule, "name",$limit);
	if($limit===1){
		$res=$A_name[0];
		return $res;
	}else{
		return $A_name;
	}
}




function getrobotmessage($sourcehtml, $robotlevel=1) {
	//提取正文内容 
	echo $sourcehtml;
	$sourcehtml="";
	$searchcursory = array(
		"/\<(script|style|textarea)[^\>]*?\>.*?\<\/(\\1)\>/si",
		"/\<!*(--|doctype|html|head|meta|link|body)[^\>]*?\>/si",
		"/<\/(html|head|meta|link|body)\>/si",
		"/([\r\n])\s+/",
		"/\<(table|div)[^\>]*?\>/si",
		"/\<\/(table|div)\>/si"
	);
	$replacecursory = array(
		"",
		"",
		"",
		 "\\1",
		"\n\n###table div explode###\n\n",
		"\n\n###table div explode###\n\n"
	);
	$searchaborative = array(
		"/\<(iframe)[^\>]*?\>.*?\<\/(\\1)\>/si",
		"/\<[\/\!]*?[^\<\>]*?\>/si",
		"/\t/",
		"/[\r\n]+/",
		"/(^[\r\n]|[\r\n]$)+/",
		"/&(quot|#34);/i",
		"/&(amp|#38);/i",
		"/&(lt|#60);/i",
		"/&(gt|#62);/i",
		"/&(nbsp|#160|\t);/i",
		"/&(iexcl|#161);/i",
		"/&(cent|#162);/i",
		"/&(pound|#163);/i",
		"/&(copy|#169);/i",
		"/&#(\d+);/e"
	);
	$replaceaborative = array(
		"",
		"",
		"",
		"\n",
		"",
		"\"",
		"&",
		"<",
		">",
		" ",
		chr(161),
		chr(162),
		chr(163),
		chr(169),
		"chr(\\1)"
	);
	$arrayrobotmeg = array();
	//$sourcetext = replaceimageurl($referurl, preg_replace($searchcursory, $replacecursory, $sourcehtml));
	$sourcetext = $sourcehtml;

	$arraysource = explode("\n\n###table div explode###\n\n", $sourcetext);
	$arraycell = array();
	foreach($arraysource as $value) {
		$cell = array(
			'code'	=>	$value,
			'text'	=>	preg_replace("/[\n\r\s]*?/is", "", preg_replace ($searchaborative, $replaceaborative, $value)),
			'pr'	=>	0,
			'title'	=>	'',
			'process'	=>''
		);
		if($cell['text'] != '') {
			if($robotlevel == 2) {
				$arraycell[] = getpr($cell, $searchaborative, $replaceaborative);
			} else {
				$arraycell[] = $cell;
			}
		}
	}

	$arraysubject = $arraymessage = array();
	$leachsubject = $leachmessage = '';
	foreach($arraycell as $value) {
		if($value['title'] == 'title') {
			$arraysubject[] = $value;
		} elseif($value['pr'] >= 0) {
			$arraymessage[] = $value['code'];
		}
	}

	$pr = '';
	foreach($arraysubject as $value) {
		if($pr < $value['pr'] || empty($pr)) {
			$leachsubject = $value['text'];
		}
		$pr = $value['pr'];
	}
	$leachmessage = preg_replace("/\<(p|br)[^\>]*?\>/si", "\n", implode("\n", $arraymessage));
	$arraymessage = explode("\n", preg_replace($searchaborative, $replaceaborative, $leachmessage));
	$leachmessage = '';
	foreach($arraymessage as $value) {
		if(trim($value) != '') {
			$leachmessage .= "<p>" . trim($value) . "</p>\n";
		}
	}

	$arrayrobotmeg['leachsubject'] = $leachsubject;
	$arrayrobotmeg['leachmessage'] = $leachmessage;
	return $arrayrobotmeg;
}

function getpr($arraycell, $searchaborative, $replaceaborative) {
	$htmltags = array(
		array('title', 5),
		array('a', -1),
		array('iframe', -2),
		array('p', 1),
		array('li', -1),
		array('input', -0.1),
		array('select', -3),
		array('form', -0.1)
	);

	if(strlen($arraycell['text']) > 10) {
		if(strlen($arraycell['text']) > 200) {
			$arraycell['pr'] += 2;
		}

		foreach($htmltags as $tagsvalue) {
			$temp = array();
			preg_match_all("/\<$tagsvalue[0][^\>]*?\>/is", $arraycell['code'], $temp, PREG_SET_ORDER);
			$tagsnum = count($temp);

			$temp = array();
			if($tagsvalue[0] == 'title' && $tagsnum > 0) {
				$arraycell['title'] = 'title';
			} elseif($tagsvalue[0] == 'a' && $tagsnum > 0) {
				preg_match_all("/\<a[^\>]*?\>(.*?)\<\/a>/is", $arraycell['code'], $temp);
				$temp[2] = preg_replace("/[\n\r\s]*?/is", '', preg_replace ($searchaborative, $replaceaborative, implode('', $temp[1])));
				$ahretnum = strlen($temp[2]) / strlen($arraycell['text']);
				$tagsnum *= $ahretnum * 10;
			}

			$arraycell['pr'] += $tagsnum * $tagsvalue[1];
		}
	} else {
		$arraycell['pr'] -= 10;
	}

	if($arraycell['pr'] >= 0) {
		$g1 = preg_replace("/\<(p|br)[^\>]*?\>/si", "\n\n###p br explode###\n\n", $arraycell['code']);
		$arrayg1 = explode("\n\n###p br explode###\n\n", $g1);

		preg_match_all("/\n\n###p br explode###\n\n/is", $g1, $g4, PREG_SET_ORDER);

		if(count($g4) > 2) {
			$g3 = 0;
			foreach($arrayg1 as $value) {
				$g2 = preg_replace("/[\n\r\s]*?/is", "", preg_replace ($searchaborative, $replaceaborative, $value));

				if($g2 != '') {
					$g2num = strlen($g2);
					if($g2num <= 25) {
						$g3--;
					} elseif($g2num > 70 ) {
						$g3 = 10;
						continue;
					}
					else {
						$g3++;
					}
				}
			}
			
			if($g3 < 0) {
				$arraycell['pr'] += $g3;
			}
		}
	}

	return $arraycell;
}

function maketimesecond($n=0,$today=""){
	if(empty($today))$today=date('Y-m-d H:i:s');
	//echo 
	$date=date('Y-m-d H:i:s',strtotime($today.'+'.$n.' second'));
	return $date;
}

function changeip(){
	echo "\n";echo "change ip===================================\n";
	$snoopy = new Snoopy;
	$url_disconnect="http://192.168.2.1/userRpm/PPPoECfgRpm.htm?wantype=2&acc=sz5708646%40163.gd&psw=37119189&VnetPap=0&linktype=2&Disconnect=%B6%CF+%CF%DF";

	$url_connect="http://192.168.2.1/userRpm/PPPoECfgRpm.htm?wantype=2&acc=sz5708646%40163.gd&psw=37119189&VnetPap=0&linktype=2&Connect=%C1%AC+%BD%D3";

	$snoopy->rawheaders["Authorization"] = "Basic YWRtaW46cmlnaHQ="; //Authorization  

	echo
	$ip1 = getip();
		$snoopy->fetch($url_disconnect);
		$snoopy->fetch($url_connect);
		echo "\n";
		sleep(20);
		echo
	$ip2 = getip();
	if($ip1==$ip2 )changeip();
	
}
function getip(){
	$snoopy = new Snoopy;
	//echo 
	$url="http://192.168.2.1/userRpm/StatusRpm.htm";
	$snoopy->rawheaders["Authorization"] = "Basic YWRtaW46cmlnaHQ="; //Authorization  
	$snoopy->fetch($url);
	//echo 
	$str=$snoopy->results;
//exit;
	$rule='00-1D-0F-2C-BB-65", "[name]",';
	//echo 
	$ip=getpregmsg($str, $rule);
	//$ip=readfiles($url);
	if($ip =="0.0.0.0")getip();
		else return $ip;
}

 
function checkgbk($s){         
     return preg_match('/[\x80-\xff]./', $s);         
}         
       
function   diffdate($d1,$d2=""){
	//diffdate("2011-10-1 00:00:00");
	//exit;
	$t1   =   strtotime($d1); 
	if(empty($d2))$t2   = time();else $t2   =   strtotime($d2); 
	$t=$t1-$t2; 
	if($t <0) 
	$t=$t*(-1); 
	$day=round($t/3600/24); 
	return   $day; 
} 


function convertToUTF8($str) {
		$charset = mb_detect_encoding($str, array('ASCII','UTF-8','GB2312','GBK','BIG5','ISO-8859-1'));
		if (strcasecmp($charset,'UTF-8') != 0) {
				$str = mb_convert_encoding($str,'UTF-8',$charset);
		}
		return $str;
}


function convertToGBK($str) {
		$charset = mb_detect_encoding($str, array('ASCII','UTF-8','GB2312','GBK','BIG5','ISO-8859-1'));
		if (strcasecmp($charset,'GBK') != 0) {
				$str = mb_convert_encoding($str,'GBK',$charset);
		}
		return $str;
}


?>