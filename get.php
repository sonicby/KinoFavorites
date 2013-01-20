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
	@$page = file_get_contents($url, NULL, $settings);
	$page = iconv("windows-1251", "utf-8", $page);
	return $page;
}

if ($_POST['action'] == 'get_artist')
{
	$artist = str_replace(' ', '%20', iconv("utf-8", "windows-1251", $_POST['artist']));
	
	$url = "http://www.kinopoisk.ru/index.php?level=7&from=forma&result=adv&m_act%5Bfrom%5D=forma&m_act%5Bwhat%5D=actor&m_act%5Bfind%5D={$artist}";
	$page = getPage($url);
	preg_match_all("/<a href=\"http:\/\/www\.kinopoisk\.ru\/level\/(\d{1})\/people\/(\d{1,9})\/sr\/1\/\"><img src=\"http:\/\/st\.kinopoisk\.ru\/images\/sm_actor\/\d{1,9}\.jpg\" alt=\".*\" title=\"(.*)\" \/><\/a>/", $page, $array);
	
	if ( ! empty($array[2]))
	{
		for($i=0; $i<count($array[2]); $i++)
		{
			$url = "http://www.kinopoisk.ru/name/{$array[2][$i]}/";
			$page = getPage($url);
			if (preg_match_all("/<td class=\"type\">.*<\/td><td><a href=\".*\" >(.*)<\/a>/U", $page, $array2))
			{
				$typeWord = $array2[1][0];
				if (isset($typeWord))
				{
					if ($typeWord == 'Актер')
						$type = 1;
					elseif ($typeWord == 'Актриса')
						$type = 2;
					elseif ($typeWord == 'Режиссер' || $typeWord == 'Продюсер' || $typeWord == 'Оператор')
						$type = 3;
					else
						$type = 0;
				}
			}
			else
				$type = 0;

			if ($type == 0)
    			echo '-';
    		else
    		{	
    			if (Database::chechExistArtist($array[2][$i]) === NULL)
    				echo "<img src=\"img/add.png\" onclick=\"add_artist('{$array[2][$i]}', '{$type}', '{$array[3][$i]}')\">&nbsp;";
    			echo "<a href=\"{$url}\" target=\"_blank\">{$array[3][$i]}</a><br />";
    		}	
		}
	}
	else
		echo "Ничего не найдено.";
}
?>