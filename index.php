<?php
date_default_timezone_set('Europe/Moscow');
$dir = __DIR__."/";
include_once $dir."config.php";
include_once $dir."class/Database.class.php";
$current_year = date("Y");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Фильмы</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="index.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/user-func.js"></script>
<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script language="javascript">
$(document).ready(function(){
	$.post("content.php",{type: 'films', id: <?php echo $current_year?>},
		function(data) {
			$('#'+<?php echo $current_year?>).append(data);
		}
	);
	return false;
});
</script>
</head>

<body>

<table width="100%" align="center">
	<tr>
		<td><a href="?p=films">Фильмы</a></td>
		<td><a href="?p=artists">Артисты</a></td>
	</tr>
</table>	

<div id="center">
<?php
if (@$_GET['p'] == 'films')
{
	$films = Database::getYear();
	if (is_array($films))
	{
		for ($i=0; $i<count($films); $i++)
		{
			if ($i != 0)
			{
				if ($films[$i-1]['year'] > $films[$i]['year'])
				{
				?>
		<div onclick="show('films', '<?php echo $films[$i]['year']?>')" class="cut"><?php echo $films[$i]['year']?></div>
				<?php
				if ($films[$i]['year'] == $current_year)
					$div_class = 'result_current';
				else
					$div_class = 'result';
				?>
		<div id="<?php echo $films[$i]['year']?>" class="<?php echo $div_class?>">
				<?php
				}
			}
			else
			{
			?>
		<div onclick="show('films', '<?php echo $films[$i]['year']?>')" class="cut"><?php echo $films[$i]['year']?></div>
		<div id="<?php echo $films[$i]['year']?>" class="result">
			<?php
			}
	
			if (isset($films[$i+1]['year']))
			{
				if ($films[$i+1]['year'] < $films[$i]['year'])
				{
			?>
		</div>
			<?php
				}
			}
		}
	}
}

if (@$_GET['p'] == 'artists')
{
?>
<form id="artist">
	<p>
	   <label>Имя</label>
	   <input type="text" name="artist-name">
	   <input type="submit" value="Найти">
    </p>
</form>
<div id="notice"></div>
<?php
	$artists = Database::getArtist();
	if ( ! empty($artists))
	{
		for($i=0; $i<count($artists); $i++)
		{
		?>
	<img src="img/add.png" onclick="show('artist', '<?php echo $artists[$i]['u_id']?>')">&nbsp;
	<img src="img/delete.png" onclick="del('<?php echo $artists[$i]['u_id']?>')">&nbsp;
	<img src="img/<?php echo $artists[$i]['type']?>.png">&nbsp;
	
	<a href="http://www.kinopoisk.ru/name/<?php echo $artists[$i]['u_id']?>/" target="_blank"><?php echo $artists[$i]['name']?></a>&nbsp;
	<br />
	<div id="<?php echo $artists[$i]['u_id']?>" class="result"></div>
		<?php
		}
	}
}
?>
</div>
<script src="js/user-func.js"></script>
</body>
</html>