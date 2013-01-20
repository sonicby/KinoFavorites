<?php
$dir = __DIR__."/";
include_once $dir."config.php";
include_once $dir."class/Database.class.php";

if ($_POST["action"] == "add_artist")
{
	if (Database::addArtist($_POST['id'], $_POST['type'], $_POST['name']))
		echo "Добавлено";
	else
		echo "Произошла ошибка, не добавлено.";
}

if ($_POST["action"] == "overlooked")
{
	$update = Database::overlooke($_POST['id']);

	if ($update)
	{
		$return["error"] = false;
	}
	else
	{
		$return["error"] = true;
		$return["msg"] = "Не удалось пометить!";
	}			
	echo json_encode($return);
}

if ($_POST["action"] == "hide")
{
	$update = Database::hide($_POST['id']);

	if ($update)
	{
		$return["error"] = false;
	}
	else
	{
		$return["error"] = true;
		$return["msg"] = "Не удалось пометить!";
	}			
	echo json_encode($return);
}

if ($_POST["action"] == "del")
{
	$del = Database::delArtist($_POST['id']);

	if ($del)
	{
		$return["error"] = false;
	}
	else
	{
		$return["error"] = true;
		$return["msg"] = "Не удалось пометить!";
	}			
	echo json_encode($return);
}
?>