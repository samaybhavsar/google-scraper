<?php
/** 
@author : Samay Bhavsar <samay@samay.info> 
@Version : 1.1
*/ 
class GoogleScraper
{
	var $keyword	=	"testing";
	var $urlList	=	"";
	var $time1		=	4000000;
	var $time2		=	8000000;
	var $proxy		=	"";
	var $cookie		=	"";
	var $header		=	"";
	var $ei			=	"";

	
	function __construct() {
		$this->cookie = tempnam ("/tmp", "cookie");
		$this->headers[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
		$this->headers[] = "Connection: keep-alive"; 
		$this->headers[] = "Keep-Alive: 115"; 
		$this->headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; 
		$this->headers[] = "Accept-Language: en-us,en;q=0.5"; 
		$this->headers[] = "Pragma: "; 
	}

	function getpagedata($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers); 
		curl_setopt($ch, CURLOPT_COOKIEFILE,  $this->cookie); 
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->cookie);
		curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$data=curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function pause() {
		usleep(rand($this->time1,$this->time2));
	}

	function initGoogle() {
		$data=$this->getpagedata('http://www.google.com');		//	Open google.com ( Might redirect to country specific site e.g. www.google.co.in)
		$this->pause();
		$this->getpagedata('http://www.google.com/ncr');	//	Moves back to google.com
	}


	// This function opens the preference page and saves the count for "Results per page" to 100
	function setPreference() {
		$data=$this->getpagedata('http://www.google.com/preferences?hl=en');
		preg_match('/<input value="(.*?)" name="sig" type="hidden">/', $data, $matches);
		$this->pause();
		$this->getpagedata('http://www.google.com/setprefs?sig='.urlencode($matches[1]).'&hl=en&lr=lang_en&safeui=images&suggon=2&newwindow=0&num=100&q=&prev=http%3A%2F%2Fwww.google.com%2F&submit2=Save+Preferences+');
	}

	function fetchUrlList()
	{		
		for($i=0;$i<1001;$i=$i+100) {
			$data=$this->getpagedata('http://www.google.com/search?q='.$this->keyword.'&num=100&hl=en&biw=1280&bih=612&prmd=ivns&ei='.$this->ei.'&start='.$i.'&sa=N');
			preg_match('/;ei=(.*?)&amp;ved/', $data, $matches);
			$this->ei=urlencode($matches[1]);
			if ($data) {
				if(preg_match("/sorry.google.com/", $data)) {
					echo "You are blocked";
					exit;
				} else {
					preg_match_all('@<h3\s*class="r">\s*<a[^<>]*href="([^<>]*)"[^<>]*>(.*)</a>\s*</h3>@siU', $data, $matches);
					for ($j = 0; $j < count($matches[2]); $j++) {
						$this->urlList[] = $matches[1][$j];
					}
				}
			} 
			else 
			{
				echo "Problem fetching the data";
				exit;
			}
			$this->pause();
		}
	}

	function getUrlList($keyword,$proxy) {
		$this->keyword=$keyword;
		$this->proxy=$proxy;
		$this->initGoogle();
		$this->pause();
		$this->setPreference();
		$this->pause();
		$this->fetchUrlList();
		return $this->urlList;
	}
}	
?>
