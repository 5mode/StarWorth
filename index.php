<?php

/**
 * Copyright 2021, 2024 5 Mode
 *
 * This file is part of StarWorth.
 *
 * StarWorth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * StarWorth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with StarWorth. If not, see <https://www.gnu.org/licenses/>.
 *
 * index.php
 * 
 * StarWorth home page.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode      
 */
 
 require "init.inc";
 
// echo("&nbsp;&nbsp;&nbsp;&nbsp;" . time());
 
 $contextType = PUBLIC_CONTEXT_TYPE;
  
 $signHistory = [];
 $cmd = PHP_STR;
 $opt = PHP_STR;
 $param1 = PHP_STR;
 $param2 = PHP_STR;
 $param3 = PHP_STR;
   
 $curLocale = APP_LOCALE;


 function doAging($param1) {
   global $signHistory;
   global $curPath;
   
   $mysign= trim($param1,"'");
   
   $ret = $mysign;
   
   if ($signHistory) {
     
     $ipos = stripos($mysign, PHP_PIPE);
     $oldTimestamp = (int)left($mysign, $ipos);
     
     if ((time() -$oldTimestamp) > APP_MAX_AGE) {
        $newval = str_repeat("0",strlen(time())).PHP_PIPE.substr($mysign,$ipos+1); 
        //echo("**$newval**"); 
        $ret = $newval;
        
        $key = array_search($mysign."\n", $signHistory);
        if ($key !== false) { 
          $signHistory[$key] = $newval . "\n"; 

          // Writing out $signHistory on disk..
          $filepath = $curPath . DIRECTORY_SEPARATOR . ".SW_history";
          file_put_contents($filepath, implode('', $signHistory));	        
        }
     }  
   }  
   return $ret;
 }


 function showHistory() {
   global $signHistory;
   global $curPath;
   global $CONFIG;
   global $curLocale;
   global $LOCALE;
   global $password;
   global $contextType;
   
   $signHistoryCopy = $signHistory;
   
   rsort($signHistoryCopy);

   echo("<div id='events'>");
   
   $m = 1;
   foreach($signHistoryCopy as $val) {
     
     $val = rtrim($val, "\n");
     $val = doAging($val);     
     
     $mytag = PHP_STR;
     $myccounter = PHP_STR;     
     $mydate = PHP_STR; 
     $mytime = PHP_STR;
     $mydesc = PHP_STR;
     $myflag = PHP_STR;
     
     $aFields = explode(PHP_PIPE, $val);
     
     $mytag = $aFields[0]??"0000";
     $myccounter = (int)$aFields[1]??PHP_STR;
     $mydate = $aFields[2]??PHP_STR;
     $mytime = $aFields[3]??PHP_STR;
     $mydesc = $aFields[4]??PHP_STR;
     $myflag = $aFields[5]??PHP_STR;
     
     if ($mydate==PHP_STR && $mydesc==PHP_STR) {
       continue;
     }
     
     //$mytag = date("Hi", $mytag); //right($mytag??"0000", 4);
     //$mytag = left($mytag, 2).":".right($mytag, 2);
     $myoritag = right($mytag??"0000", 4);
     $mytag = date("Hi", $mytag);
     $mytag = left($mytag, 2).":".right($mytag, 2);     
     $yesStar = ($myoritag==="0000")?false:true;
     $myccounter = left($myccounter,6);
     
     $mydesc = enableLinks($mydesc);
     
     // If I'm in admin
     if ($contextType === PERSONAL_CONTEXT_TYPE) {

       $star= PHP_STR;
        if ($yesStar) {
           $starFnc= "&nbsp;&nbsp;&nbsp;<a href='#' onclick=\"setStar('" . $val . "')\"><img src='/SW_res/star.png' style='width:26px;'></a>";
        } else {
           $starFnc = "&nbsp;&nbsp;&nbsp;<a href='#' onclick=\"setStar('" . $val . "')\"><img src='/SW_res/setstar.png' style='width:26px;'></a>";
        }    
                     
       $adminFnc = PHP_STR;
       if ($myflag === "u") {
         $adminFnc = "<a href='#' onclick=\"confSign('" . $val . "')\"><img src='/SW_res/confirm.png' style='width:26px;'></a>";
       } else {
         $adminFnc = "<a href='#' onclick=\"delSign('" . $val . "')\"><img src='/SW_res/del.png' style='width:26px;'></a>";
       }    
     
       // Display event/calendar list   
       
       echo("<table class='table-event' align='center'>");
       echo("<tr>");
       echo("<td class='td-data-date'>");
       echo("<span class='data-date' style='font-family:".DISPLAY_DATE_FONT.";'>".$mydate."</span>");
       echo("</td>");
       echo("<td class='td-data-time'>");
       echo("<span class='data-time' style='font-family:".DISPLAY_DATE_FONT.";'>".$mytime."</span>");
       echo("</td>");
       echo("<td class='td-data-desc'>");
       echo("<span class='data-desc'>".$mydesc."</span>");
       echo("</td>");
       echo("<td class='td-star'>");
       echo($starFnc);
       echo("</td>");
       echo("<td class='td-admin'>");
       echo($adminFnc);
       echo("</td>");
       echo("</tr>");   
       echo("</table>");
       
     // If I'm not in admin
     } else {   
       
       if ($myflag !== "u") {

       $star= PHP_STR;
        if ($yesStar) {
           $star= "<img src='/SW_res/star.png' style='width:26px;'>&nbsp;(".$mytag.")";
        } else {
           $star = "<a href='#' onclick='showSideBar();'><img src='/SW_res/setstar.png' style='width:26px;'></a>";
        }    

         // Display event list
         echo("<table class='table-event' align='center'>");
         echo("<tr>");
         echo("<td class='td-data-star'>");
         echo("$star");
         echo("</td>");
         echo("<td class='td-data-date'>");
         echo("<span class='data-date' style='font-family:".DISPLAY_DATE_FONT.";'>".$mydate."</span>");
         echo("</td>");
         echo("<td class='td-data-time'>");
         echo("<span class='data-time' style='font-family:".DISPLAY_DATE_FONT.";'>".$mytime."</span>");
         echo("</td>");
         echo("<td class='td-data-desc' style='width:76%;'>");
         echo("<span class='data-desc'>".$mydesc."&nbsp;(".$myccounter.")</span>");
         echo("</td>");
         echo("</tr>");   
         echo("</table>");

       }  
     }
     
     $m++;
   }
   
   echo("</div>");
 }

 function updateHistory(&$update, $maxItems) {
   global $signHistory;
   global $curPath;
   
   // Making enough space in $signHistory for the update..
   $shift = (count($signHistory) + count($update)) - $maxItems;
   if ($shift > 0) {
     $signHistory = array_slice($signHistory, $shift, $maxItems); 
   }		  
   // Adding $signHistory update..
   if (count($update) > $maxItems) {
     $beginUpd = count($update) - ($maxItems-1);
   } else {
	   $beginUpd = 0;
   }	        
   $update = array_slice($update, $beginUpd, $maxItems); 
   foreach($update as $val) {  
	   $signHistory[] = $val;   
   }
 
   // Writing out $signHistory on disk..
   $filepath = $curPath . DIRECTORY_SEPARATOR . ".SW_history";
   file_put_contents($filepath, implode('', $signHistory));	 
 }


 function updatecaptchaHistory(&$update) {
   global $captchaHistory;
   global $curPath;
   	        
   foreach($update as $val) {  
     $captchaHistory[] = $val;     
   }
 
   // Writing out $captchaHistory on disk..
   $filepath = $curPath . DIRECTORY_SEPARATOR . ".SW_captchahistory";
   file_put_contents($filepath, implode('', $captchaHistory));	 
 }


 function parseCommand() {
   global $command;
   global $cmd;
   global $opt;
   global $param1;
   global $param2;
   global $param3;
   
   $str = trim($command);
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $cmd = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	   $cmd = $str;
	   return;
   }	     
   
   if (left($str, 1) === "-") {
	 $ipos = stripos($str, PHP_SPACE);
	 if ($ipos > 0) {
	   $opt = left($str, $ipos);
	   $str = substr($str, $ipos+1);
	 } else {
	   $opt = $str;
	   return;
	 }	     
   }
   
   if (left($str, 1) === "'") {
     $ipos = stripos($str, "'", 1);
     if ($ipos > 0) {
       $param1 = substr($str, 0, $ipos+1);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }  
   } else {   
     $ipos = stripos($str, PHP_SPACE);
     if ($ipos > 0) {
       $param1 = left($str, $ipos);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }	     
   }
     
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param2 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param2 = $str;
	 return;
   }
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param3 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param3 = $str;
	 return;
   }	     
 	     
 }

 function signParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $date;
  global $hour;
  global $min;
  global $desc;
  global $captchacount; 
  global $captchasign;
  global $captchaHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	//param1==""  
	if ($param1!==PHP_STR) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }
	//param2==""
	if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }

  //date!=""
  if ($date===PHP_STR || strlen($date)<4) {
    //echo("WARNING: invalid date<br>");
    return false;
  }  

  if ($hour===PHP_STR || strlen($hour)>2) {
    //echo("WARNING: invalid hour<br>");
    return false;
  }  
  if ($min===PHP_STR || strlen($min)>2) {
    //echo("WARNING: invalid min<br>");
    return false;
  }  
  
  //place!=""
  if ($desc===PHP_STR || strlen($desc)<4) {
    //echo("WARNING: invalid desc<br>");
    return false;
  }  
  
  $rescaptcha1=$captchacount>=4;
  $rescaptcha2=count(array_filter($captchaHistory, "odd")) > (APP_MAX_FROM_IP - 1);
  //if ($rescaptcha1) {
  //  echo("WARNING: captcha expired #1<br>");
  //}  
  
  //if ($rescaptcha2) {
  //  echo("WARNING: captcha expired #2<br>");
  //}  
  
  ///if ($rescaptcha1 || $rescaptcha2) {
  
  //if ($rescaptcha1) {
  //  return false;
  //}  
  
  return true;
 } 


 function odd($val) {
   
   global $captchasign;
   
   return rtrim($val,"\n") == $captchasign;   
 }   
 
  
 function myExecSignCommand() {
   
   global $date;
   global $hour;
   global $min;
   global $desc;
   global $curPath;
   global $lastMessage;
   global $captchacount;
   global $captchasign;
   global $captchaHistory;
   
   $newSign = time() . PHP_PIPE . "000001X" . PHP_PIPE . HTMLencodeF($date,false) . PHP_PIPE . HTMLencodeF($hour.":".((strlen($min)==1)?"0".$min:$min)) . PHP_PIPE . HTMLencodeF($desc,false) . "|v";
   $newSignChk = HTMLencodeF($date,false) . PHP_PIPE . HTMLencodeF($hour.":".((strlen($min)==1)?"0".$min:$min)) . PHP_PIPE . HTMLencodeF($desc,false) . "|v";
       
   $signHChk = file($curPath . DIRECTORY_SEPARATOR . ".SW_history");
   $lastMsgChk = $signHChk[count($signHChk)-1];
   $doubleChk = (stripos($lastMsgChk,"|$desc|")===false)?false:true;
   
   if (hash("sha256", $newSignChk . APP_SALT, false) !== $lastMessage && !$doubleChk) {

     // Updating message history..
     $output = [];
     $output[] = $newSign . "\n";
     updateHistory($output, HISTORY_MAX_ITEMS);

     // Updating captcha history..
     $output = [];
     $output[] = $captchasign . "\n";
     updatecaptchaHistory($output);

     $lastMessage = hash("sha256", $newSignChk . APP_SALT, false);
   }
   
 }  


 function confParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $signHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	
  $myval = trim($param1,"'");
  
  //param1!=""  
	if ($myval===PHP_STR) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }
	//param1 in $signHistory  
	if (!in_array($myval."\n",$signHistory)) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }  
  
	//param2==""
	if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  
  return true;

 } 


 function myExecConfSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecConfSignCommand()");
     
     $newval = left($mysign, strlen($mysign)-2) . "|v"; 
     
     $key = array_search($mysign."\n", $signHistory);
     if ($key !== false) { 
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".SW_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }

 function delParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $signHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	
  $myval = trim($param1,"'");
  
  //param1!=""  
	if ($myval===PHP_STR) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }
	//param1 in $signHistory
	if (!in_array($myval."\n",$signHistory)) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }  
  
	//param2==""
	if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  
  return true;

 } 


 function myExecDelSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecDelSignCommand()");
     
     $newval = left($mysign, strlen($mysign)-2) . "|u"; 
     
     $key = array_search($mysign."\n", $signHistory);
     if ($key !== false) { 
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".SW_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }


 function myExecSetStarCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   
   $val = trim($param1,"\n'");
   
   $mytag = PHP_STR;
   $myccounter = PHP_STR;     
   $mydate = PHP_STR; 
   $mytime = PHP_STR;
   $mydesc = PHP_STR;
   $myflag = PHP_STR;
     
   $aFields = explode(PHP_PIPE, $val);
 
   $mytag = $aFields[0]??"0000";
   $myccounter = $aFields[1]??PHP_STR;
   $mydate = $aFields[2]??PHP_STR;
   $mytime = $aFields[3]??PHP_STR;
   $mydesc = $aFields[4]??PHP_STR;
   $myflag = $aFields[5]??PHP_STR;

   $mytagnew = time();
   $myccounter = (int)left($myccounter,6);
   $myccounter = (string)($myccounter+1);
   $myccounternew = str_repeat("0", 6-strlen($myccounter)).$myccounter."X";

   if ($signHistory) {
     
     //echo("inside myExecConfSignCommand()");
     
     $newval = $mytagnew.PHP_PIPE.$myccounternew.PHP_PIPE.$mydate.PHP_PIPE.$mytime.PHP_PIPE.$mydesc.PHP_PIPE."v"; 
     
     $key = array_search($val."\n", $signHistory);
     if ($key !== false) { 
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".SW_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }

 function setParamValidation() {
   
  global $opt;
  global $param1;
  global $param2; 
  global $param3; 
  global $signHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
     echo("WARNING: invalid options<br>");	
     return false;
  }	
	
  $myval = trim($param1,"'");
  
  //param1!=""  
  if ($myval===PHP_STR) {
     echo("WARNING: invalid parameters<br>");	
     return false;
  }
  
  //param1 in $signHistory  
  if (!in_array($myval."\n",$signHistory)) {
    echo("WARNING: invalid parameters<br>");	
    return false;
  }  
  
  //param2==""
  if ($param2!==PHP_STR) {
      echo("WARNING: invalid parameters<br>");
      return false;
  }
  
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  
  return true;

 } 

 $curPath = APP_DATA_PATH;
 chdir($curPath);

 $signHistory = file($curPath . DIRECTORY_SEPARATOR . ".SW_history");
 $captchaHistory = file($curPath . DIRECTORY_SEPARATOR . ".SW_captchahistory");

 $password = filter_input(INPUT_POST, "Password")??"";
 $password = strip_tags($password);
 if ($password==PHP_STR) {
   $password = filter_input(INPUT_POST, "Password2")??"";
   $password = strip_tags($password);
 }  
 $command = filter_input(INPUT_POST, "CommandLine")??"";
 $command = strip_tags($command);
 
 //$pwd = filter_input(INPUT_POST, "pwd"); 
 $hideSplash = filter_input(INPUT_POST, "hideSplash")??"";
 $hideSplash = strip_tags($hideSplash);
 $hideHCSplash = filter_input(INPUT_POST, "hideHCSplash")??"";
 $hideHCSplash = strip_tags($hideHCSplash);

 $date = filter_input(INPUT_POST, "date")??"";
 $date = strip_tags($date);
 $hour = filter_input(INPUT_POST, "hour")??"";
 $hour = strip_tags($hour);
 $min = filter_input(INPUT_POST, "min")??"";
 $min = strip_tags($min);
 $desc = filter_input(INPUT_POST, "desc")??"";
 $desc = strip_tags($desc);

 $captchasign = hash("sha256", $_SERVER["REMOTE_ADDR"] . date("Y") . APP_SALT, false);
 
 $lastMessage = filter_input(INPUT_POST, "last_message")??"";
 $lastMessage = strip_tags($lastMessage);
 $totsigns = count($signHistory);
 //print_r($totsigns);
 //exit(0);
 if ($totsigns > 0) {
   $lastMessage = hash("sha256", rtrim($signHistory[$totsigns-1],"\n") . APP_SALT, false);
 }   

 $captchacount = (int)filter_input(INPUT_POST, "captcha_count")??"";
 $captchacount = strip_tags($captchacount);
 //if ($captchacount === 0) {
 //  $captchacount = 1;
 //}  

 if ($password !== PHP_STR) {	
	$hash = hash("sha256", $password . APP_SALT, false);

	if ($hash !== APP_HASH) {
	  $password=PHP_STR;	
    }	 
 } 
  
 parseCommand($command);
 //echo("cmd=" . $cmd . "<br>");
 //echo("opt=" . $opt . "<br>");
 //echo("param1=" . $param1 . "<br>");
 //echo("param2=" . $param2 . "<br>");
  
 if ($password !== PHP_STR) {
   
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
 
     if ($cmd === "sign") {
       $captchacount = $captchacount + 1;
       if (signParamValidation()) {
         myExecSignCommand();
       }	     	     
     } else if ($command === "refresh") {
       // refreshing Msg Board..
     }
 
   } else if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $cmd . "|")) {
     
     if ($cmd === "del") {
       if (delParamValidation()) {
         myExecDelSignCommand();
       }	     
     } else if ($cmd === "conf") {
       if (confParamValidation()) {
         myExecConfSignCommand();
       }	     	     
     } else if ($cmd === "set") {
       if (setParamValidation()) {
         myExecSetStarCommand();
       }
     }       
   } else {
     
   }
   
   $contextType = PERSONAL_CONTEXT_TYPE;
      
 } else {
 
  /*
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
     if ($cmd === "sign") {
       $captchacount = $captchacount + 1;
       if (signParamValidation()) {
         myExecSignCommand();
       }	
     }   
   }*/
 }
 
?>

<!DOCTYPE html>
<head>
	
  <meta charset="UTF-8"/>
  
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  
<!--
    Copyright 2021, 2024 5 Mode

    This file is part of StarWorth.

    StarWorth is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    StarWorth is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with StarWorth. If not, see <https://www.gnu.org/licenses/>.
 -->
  
    
  <title><?php echo(APP_TITLE); ?></title>
	
  <link rel="shortcut icon" href="/favicon.ico?v=<?php echo(time()); ?>>" />
    
  <meta name="description" content="<?php echo(APP_DESCRIPTION); ?>"/>
  <meta name="keywords" content="<?php echo(APP_KEYWORDS); ?>"/>
  <meta name="author" content="5 Mode"/> 
  <meta name="robots" content="index,follow"/>
  
  <script src="/SW_js/jquery-3.6.0.min.js" type="text/javascript"></script>
  <script src="/SW_js/sha.js" type="text/javascript"></script>
  <script src="/SW_js/common.js" type="text/javascript"></script>
  <script src="/SW_js/bootstrap.min.js" type="text/javascript"></script>
  
  <script src="/SW_js/index.js" type="text/javascript" defer></script>
  
  <link href="/SW_css/bootstrap.min.css" type="text/css" rel="stylesheet">
  <link href="/SW_css/style.css?r=<?PHP echo(time());?>" type="text/css" rel="stylesheet">
  
<style>
@import url('https://fonts.googleapis.com/css2?family=<?php echo(str_ireplace(" ","+",DISPLAY_DATE_FONT));?>');
</style>
     
</head>
<body>

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "jscheck.html")): ?>
<?php include("jscheck.html"); ?> 
<?php endif; ?>

<form id="frmHC" method="POST" action="/" target="_self" enctype="multipart/form-data">

<?php if(APP_USE === "PRIVATE"): ?>
<div class="header">
   <a id="burger-menu" href="#" style="display:none;"><img src="/SW_res/burger-menu2.png" style="width:58px;"></a><a id="ahome" href="http://homolog.5mode-foss.eu" target="_blank"><img id="logo-hmm" src="/SW_res/SWlogo.png" style="width:28px;">&nbsp;StarWorth</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="agithub" href="https://github.com/par7133/StarWorth" style="color:#000000"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="afeedback" href="mailto:posta@elettronica.lol" style="color:#000000"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="asupport" href="tel:+39-378-0812391" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:black;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a><div id="pwd2" style="float:right;position:relative;top:+13px;display:none"><input type="password" id="Password2" name="Password2" placeholder="password" style="font-size:13px; background:#393939; color:#ffffff; width: 125px; border-radius:3px;" value="" autocomplete="off"></div>
</div>
<?php else: ?>
<div class="header2">
   <?php echo(APP_CUSTOM_HEADER); ?>
</div>  
<?php endif; ?>

<div style="clear:both;margin:auto">&nbsp;</div>

<?php
  $callSideBarTOP = 1; 
  if(APP_USE === "PRIVATE") {
    $callSideBarTOP = 65;   
  }    
?>

<div id="call-sidebar" style="top:<?php echo($callSideBarTOP);?>px;">
    &nbsp;
</div>

<div id="sidebar">
    
    <button id="sidebar-close" type="button" class="close" aria-label="Close" onclick="closeSideBar();">
      <span aria-hidden="true">&times;</span>
    </button>
    
    <br><br>
    <img id="genius" src="/SW_res/SWgenius.png" alt="HL Genius" title="HL Genius">
    &nbsp;<br><br>
    <div style="text-align:left;white-space:nowrap;">
    &nbsp;<input id="Password" name="Password" class="sidebarcontrol" type="password" placeholder="password" value="<?php echo($password);?>" autocomplete="off">&nbsp;<input type="submit" class="sidebarcontrol" value="<?php echo(getResource("Go", $curLocale));?>" style="width:24%; height: 25px;background-color:lightgray;color:#000000;"><br>
    &nbsp;<input id="Salt"  class="sidebarcontrol" type="text" placeholder="salt" autocomplete="off"><br>
    <div style="text-align:center;">
    <a id="butHashMe" href="#" onclick="showEncodedPassword();"><?php echo(getResource("Hash Me", $curLocale));?>!</a>     
    
    <br><br><br>

    </div>
    </div>
</div>

<div id="content-bar">

	<?php if (APP_SPLASH): ?>
	<?php if ($hideSplash !== PHP_STR): ?>
	<div id="splash">	
	
	  <button id="butCloseSplash" type="button" class="close" aria-label="Close" onclick="closeSplash();">
           <span aria-hidden="true">&times;</span>
        </button>
	
	   Hello and welcome to StarWorth!<br><br>
	   
	   StarWorth is a light and simple software on premise to log calendar and events.<br><br>
	   
	   StarWorth is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
                   StarWorth name comes from a prank on "Star War" meaning our intention to make stars/projects "worth".<br><br>
     
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to run StarWorth in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:<br>
	   <ol>
	   <li>Check the permissions of your "data" folder in your web app private path; and set its path in the config file.</li>
	   <li>In the data path create a ".SW_history" and ".SW_captchahistory" files and give them the write permission.</li>
     <li>Finish to setup the configuration file apporpriately, in the specific:</li>
     <ul>
       <li>Configure the APP_USE appropriately.</li>
       <li>Configure the DISPLAY attributes as required.</li>
       <li>Configure the max history items as required (default: 1000).</li>	      
	   </ul>
     </ol>
	   
	   <br>	
     
	   Hope you can enjoy it and let us know about any feedback: <a href="mailto:posta@elettronica.lol" style="color:#e6d236;">posta@elettronica.lol</a>
	   
	</div>	
	<?php endif; ?>
	<?php endif; ?>

  <div style="width:100%; padding: 0px; text-align:center; font-size:12px; border:0px solid red;">
   
    <?php if (APP_DEFAULT_CONTEXT === "PRIVATE"): ?>
     
     <div id="content-header">
    
      <?php if ($contextType === PUBLIC_CONTEXT_TYPE): ?>
     
        <div id="guest-msg"><h1><?php echo(APP_GUEST_MSG??"&nbsp;"); ?></h1></div>
      
      <?php else: ?>
      
        <div id="welcome-msg"><h1><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></h1></div>
        
        <br>
        
        <div style="clear:both;margin:auto;"><br><br></div>
        
        <hr>
        
        <br>
        
        </div>

        <div id="insertBar">
        
            <input id="date" name="date" type="text" class="standardfield standardcontrol" placeholder="Date" value="<?php echo(date("Y-m-d"));?>">&nbsp;
            <select id="hour" name="hour" class="standardfield standardcontrol">
            <?PHP for($i=0;$i<=24;$i++):?>
              <option value="<?PHP echo($i);?>" <?PHP echo(($i==date("G"))?"selected":"")?>><?PHP echo((strlen($i)===1)?"0".$i:$i);?></option>
            <?PHP endfor; ?>
            </select>:<select id="min" name="min" class="standardfield standardcontrol">
            <?PHP for($i=0;$i<=59;$i++):?>
              <option value="<?PHP echo($i);?>" <?PHP echo(($i==ltrim(date("i"),'0'))?"selected":"");?>><?PHP echo((strlen($i)===1)?"0".$i:$i);?></option>
            <?PHP endfor; ?>
            </select>&nbsp;
            <input id="desc" name="desc" type="text"  class="standardfield standardcontrol" placeholder="Description" maxlength="300"><br>

            <input id="send" name="send" type="text"  value="&nbsp;<?php echo(DISPLAY_SUBMIT_BUTTON);?>&nbsp;" title="<?php echo(DISPLAY_SUBMIT_BUTTON);?>">

        </div>
        
        <div style="clear:both;margin:auto;"><br><br><br></div>
                        
        <?php showHistory(); ?>
   
      <?php endif; ?>
    
    <?php else: ?>
    
      <div id="content-header">  
        
      <?php if ($contextType === PUBLIC_CONTEXT_TYPE): ?>
    
        <div id="welcome-msg"><h1><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></h1></div>
        
        <br>
        
        <hr>

        <br>
        
        <?php showHistory(); ?>
        
      <?php else: ?>

        <div id="welcome-msg"><h1><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></h1></div>
        
        <br>

       <div style="clear:both;margin:auto;"><br><br></div>
        
        <hr>
        
        <br>
        
        </div>

        <div id="insertBar">
                
            <input id="date" name="date" type="text" class="standardfield standardcontrol" placeholder="Date" value="<?php echo(date("Y-m-d"));?>">&nbsp;
            <select id="hour" name="hour" class="standardfield standardcontrol" style="background-color:#FFFFFF;">
            <?PHP for($i=0;$i<=24;$i++):?>
              <option value="<?PHP echo($i);?>" <?PHP echo(($i==date("G"))?"selected":"")?>><?PHP echo((strlen($i)===1)?"0".$i:$i);?></option>
            <?PHP endfor; ?>
            </select>:<select id="min" name="min" class="standardfield standardcontrol">
            <?PHP for($i=0;$i<=59;$i++):?>
              <option value="<?PHP echo($i);?>" <?PHP echo(($i==ltrim(date("i"),'0'))?"selected":"")?>><?PHP echo((strlen($i)===1)?"0".$i:$i);?></option>
            <?PHP endfor; ?>
            </select> &nbsp;
            <input id="desc" name="desc" type="text" class="standardfield standardcontrol" placeholder="Description" maxlength="300"> 

            <input id="send" name="send" type="button" value="&nbsp;<?php echo(DISPLAY_SUBMIT_BUTTON);?>&nbsp;" title="<?php echo(DISPLAY_SUBMIT_BUTTON);?>">

        </div>
        
        <div style="clear:both;margin:auto;"><br><br><br></div>
                        
        <?php showHistory(); ?>

      <?php endif; ?>
    
    <?php endif; ?>
    
    <br><br><br><br><br><br><br>

    <?php if(APP_USE === "BUSINESS"): ?>    
    <div id="footer2">
      <a id="ahome" href="http://starworth.5mode-foss.eu" target="_blank"><img id="logo-hl" src="/SW_res/SWlogo.png">Powered by StarWorth</a>
    </div>
    <?php endif; ?>&nbsp;
       
  </div>     

</div>

<input type="hidden" id="CommandLine" name="CommandLine">
<input type="hidden" name="hideSplash" value="<?php echo($hideSplash); ?>">
<input type="hidden" name="hideHCSplash" value="1">
<input type="hidden" name="captcha_count" value="<?php echo($captchacount); ?>">
<input type="hidden" name="last_message" value="<?php echo($lastMessage); ?>">

</form>

<div class="footer">
<div id="footerCont">&nbsp;</div>
<div id="footer"><span style="background:#FFFFFF;opacity:1.0;margin-right:10px;">&nbsp;&nbsp;A <a href="http://5mode.com">5 Mode</a> project <span class="no-sm">and <a href="http://wysiwyg.systems">WYSIWYG</a> system</span>. Some rights reserved.</span></div>	
</div>

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "skinner.html")): ?>
<?php include("skinner.html"); ?> 
<?php endif; ?>

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "metrics.html")): ?>
<?php include("metrics.html"); ?> 
<?php endif; ?>

<script>
/*
<?PHP if ($password !== PHP_STR):  ?>
$(".td-data-time")
   .css("display", "none")
<?PHP endif; ?>
*/
</script>

</body>
</html>
