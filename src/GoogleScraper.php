<?php
/**
@author : Samay Bhavsar <samay@samay.info>
@Version : 1.3
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Scraper;

class GoogleScraper
{
    var $keyword    = "testing";
    var $urlList    = array();
    var $time1      = 4000000;
    var $time2      = 8000000;
    var $proxy      = "";
    var $cookie     = "";
    var $header     = "";
    var $ei         = "";
    var $ved        = "";


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
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36');
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
        $data=$this->getpagedata('http://www.google.com');        //    Open google.com ( Might redirect to country specific site e.g. www.google.co.in)
        $this->pause();
        $data = $this->getpagedata('http://www.google.com/ncr');    //    Moves back to google.com
        preg_match('/type="submit" data-ved="(.*?)"/', $data, $matches);
        $this->ved = $matches[1];
    }

    function fetchUrlList()
    {
        for($i=0;$i<=250;$i=$i+10) {
            $data = $this->getpagedata('http://www.google.com/search?source=hp&q='.$this->keyword.'&ei='.$this->ei.'&btnK=Google+Search&ved='.$this->ved.'&start='.$i);

            preg_match('/;ei=(.*?)&amp;/', $data, $matches);
            if(empty($matches))
            {
                preg_match('/;sei=(.*?)"/', $data, $matches);
                $this->ei=urlencode($matches[1]);
                if(empty($matches))
                {
                    exit();
                }
            } else {
                $this->ei=urlencode($matches[1]);
            }
            echo $this->ei;
            if ($data) {
                if(preg_match("/sorry.google.com/", $data)) {
                    echo "You are blocked";
                    exit;
                } else {
                    preg_match_all('@<div class="r"><a href="(.+?)" ([^<>]*)?><br><h3 ([^<>]*)?>(.*?)</h3><div ([^<>]*)?>(<div ([^<>]*)?>)?<cite ([^<>]*)?>(.*?)<span ([^<>]*)?>(.*?)</span></cite></div></a>@si', $data, $matches);
                    print_r($matches);
                    for ($j = 0; $j < count($matches[1]); $j++) {
                        array_push($this->urlList, array('url' => $matches[1][$j], 'title' => $matches[4][$j]));
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

    function getUrlList($keyword,$proxy='') {
        $this->keyword=$keyword;
        $this->proxy=$proxy;
        $this->initGoogle();
        $this->pause();
        $this->fetchUrlList();
        return $this->urlList;
    }
}
?>
