<?php
$dir = __DIR__."/";
include_once $dir."config.php";
include_once $dir."class/Database.class.php";

function getPage($url)
{
	$setting = array('http' => array('method' => 'GET', 'header' => 
            	"User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; ru; rv:1.9.2.4) Gecko/20100611 Firefox/3.6.4\r\n".
            	"Accept: text/xml, application/xml, application/xhtml+xml;q=0.5/r/n".
            	"Accept-Language: us-en,en;q=0.5/r/n".
            	"Accept-Encoding: gzip,deflate\r\n".
            	"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"
            ));
	$settings = stream_context_create($setting);	
	$page = file_get_contents($url, NULL, $settings);
	$page = iconv("windows-1251", "utf-8", $page);
	return $page;
}

$artists = Database::getArtist();
if ( ! empty($artists))
{
    for ($i=0; $i<count($artists); $i++)
    {
    	$url = "http://www.kinopoisk.ru/name/{$artists[$i]['u_id']}/";
    	$page = getPage($url);
    	preg_match_all("/<a href=\"\/film\/(\d+)\/\" (class=\"gray\")?>(.*) \(.*?(\d{4}).*?\)<\/a>/", $page, $array);
    	
    	for ($y=0; $y<count($array[1]); $y++)
    	{
    		$f_id = $array[1][$y];
    		$f_name = str_replace("'", "`", $array[3][$y]);
    		$f_year = $array[4][$y];
    		$a_id = $artists[$i]['u_id'];
    		
    		if (Database::chechExistArtist($f_id) === NULL)
    			Database::addFilm($f_id, $f_name, $f_year);

    		if ( ! Database::checkExistTag($f_id, $a_id))
    			Database::addTag($f_id, $a_id);

    		$xml_str = file_get_contents("http://www.kinopoisk.ru/rating/{$f_id}.xml");
    		$xml = simplexml_load_string($xml_str);
    		
    		$kp = (isset($xml->kp_rating)) ? $xml->kp_rating : '0.00';
    		$imdb = (isset($xml->imdb_rating)) ? $xml->imdb_rating : '0.00';
    
    		Database::updateRating($f_id, $kp, $imdb);
    	}
    }
}
?>