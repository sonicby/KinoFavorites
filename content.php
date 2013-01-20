<?php
$dir = __DIR__."/";
include_once $dir."config.php";
include_once $dir."class/Database.class.php";

if ($_POST['type'] == 'films')
    $films = Database::getFilms($_POST['id']);
if ($_POST['type'] == 'artist')
    $films = Database::getFilmsByArtist($_POST['id']);

if (is_array($films))
{
?>
<table cellpadding="0" cellspacing="0">
	<tr class="header">
		<td width="550" colspan="2" rowspan="2">Название</td>
		<td width="31" rowspan="2">Год</td>
		<td width="100" colspan="2">Рейтинг</td>
		<?php
		if ($_POST['type'] == 'films')
		{
		?>
		<td width="305" rowspan="2">Участники</td>
		<?php
		}
		?>
		<td width="14" rowspan="2">&nbsp;</td>
	</tr>
	<tr class="header">
		<td>IMDB</td>
		<td>Кинопоиск</td>
	</tr>
<?php
	for ($i=0; $i<count($films); $i++)
	{
		if (($i % 2) == 0)
			$tr_class = 'light';
		else
			$tr_class = 'dark';
		?>
	<tr class="<?php echo $tr_class?>">
		<td width="14"><input type="checkbox" <?php if ($films[$i]['overlooked'] == 1) echo 'checked'?> onclick="overlooked(<?php echo $films[$i]['id']?>)"></td>
		<td width="586" class="text"><a href="http://www.kinopoisk.ru/level/1/film/<?php echo $films[$i]['id']?>/" target="_blank"><?php echo $films[$i]['name']?></a></td>
		<td class="num"><?php echo $films[$i]['year']?></td>
		<td class="num"><?php
		if ($films[$i]['imdb_rating'] != 0.00)
			echo $films[$i]['imdb_rating'];
		?></td>
		<td class="num"><?php
		if ($films[$i]['kp_rating'] != 0.00)
			echo $films[$i]['kp_rating'];
		?></td>
		<?php
		if ($_POST['type'] == 'films')
		{
		?>
		<td>
    		<?php
    		$artist = Database::getArtist($films[$i]['id']);
    		if (is_array($artist))
    		{
    			$stars = '';
    			for ($y=0; $y<count($artist); $y++)
    			{
    				$stars .= ', ';
    				$stars .= '<img src="img/'.$artist[$y]['type'].'.png">&nbsp;';
    				$stars .= '<a href="http://www.kinopoisk.ru/level/4/people/'.$artist[$y]['u_id'].'/" target="_blank">'.$artist[$y]['name'].'</a>';
    			}
    		}
    		echo substr($stars, 2);
    		?>
		</td>
		<?php
		}
		?>
		<td><input type="checkbox" onclick="hide('<?php echo $_POST['type']?>', '<?php echo $films[$i]['id']?>', '<?php echo $_POST['id']?>')"></td>
	</tr>
	<?php
	}
	?>
</table>
<?php	
}
?>