<?php
class Database
{
    public $dbh;
    private static $instance;

    private function __construct()
    {
		$db_type = Config::read('db.type');
		
		switch ($db_type)
		{
			case 'mysql':
				$dsn = "mysql:host=".Config::read('db.host').";dbname=".Config::read('db.basename');
				$username = Config::read('db.user');
				$password = Config::read('db.password');
				$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".Config::read('db.charset'));
				break;
			case 'sqlite':
				$dsn = "sqlite:".Config::read('db.basename');
				$options = array(PDO::ATTR_PERSISTENT => true);
				break;
			case 'sqlite2':
				$dsn = "sqlite2:".Config::read('db.basename');
				$options = array(PDO::ATTR_PERSISTENT => true);
				break;
			case 'sqlsrv':
				$dsn = "sqlsrv:Server=".Config::read('db.host').",".Config::read('db.port').";Database=".Config::read('db.basename');
				$username = Config::read('db.user');
				$password = Config::read('db.password');
				break;
			case 'pgsql':
				$dsn = "pgsql:host=".Config::read('db.host').";".Config::read('db.port').";dbname=".Config::read('db.basename').";user=".Config::read('db.user').";password=".Config::read('db.password');
				break;
		}
		
		try {
        	$this->dbh = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}
    }

    public static function getInstance()
    {
        if ( ! isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
    
    public static function chechExistArtist($u_id)
    {
	    $stmt = Database::getInstance()->dbh->prepare("SELECT `id` FROM `artist` WHERE `u_id` = :u_id");
    	$stmt->bindParam(':u_id', $u_id);
		if ($stmt->execute())
		{
			foreach ($stmt as $row)
			{
				if ( ! empty($row['u_id']))
					return TRUE;
				else
					return FALSE;
			}
		}
		$stmt = NULL;
    }
    
    public static function addArtist($u_id, $type, $name)
    {
    	$stmt = Database::getInstance()->dbh->prepare("INSERT INTO `artist` (`u_id`, `type`, `name`) VALUES (:u_id, :type, :name)");
    	$stmt->bindParam(':u_id', $u_id);
    	$stmt->bindParam(':type', $type);
    	$stmt->bindParam(':name', $name);

		if ($stmt->execute())
			return TRUE;
		else
			return FALSE;
		$stmt = NULL;
    }
    
    public static function delArtist($u_id)
    {
    	$stmt = Database::getInstance()->dbh->prepare("DELETE FROM `artist` WHERE `u_id` = :u_id");
		$stmt->bindParam(':u_id', $u_id);
		if ($stmt->execute())
		{
    		$stmt2 = Database::getInstance()->dbh->prepare("SELECT `f_id` FROM `tagmap` WHERE `a_id` = :a_id");
        	$stmt2->bindParam(':a_id', $u_id);
    		if ($stmt2->execute())
    		{
    			foreach ($stmt2 as $row)
    			{
        			$stmt3 = Database::getInstance()->dbh->prepare("SELECT COUNT(*) AS `count` FROM `tagmap` WHERE `f_id` = :f_id");
        			$stmt3->bindParam(':f_id', $row['f_id']);
        			if ($stmt3->execute())
        			{
            			foreach ($stmt3 as $row2)
            			{
                			if ($row2['count'] == 1)
                			{
                			     Database::delFilm($row['f_id']);
                			     Database::delParticipant($row['f_id']);
                			}                			     
                			else
                			{
                			     Database::delParticipant($row['f_id']);
                			}
            			}
        			}
        			else
            			return FALSE;
            		$stmt3 = NULL;
    			}
    		}
    		else
    			return FALSE;
    		$stmt2 = NULL;
        }
		else
			return FALSE;
		$stmt = NULL;
    }
    
    public static function delFilm($id)
    {
    	$stmt = Database::getInstance()->dbh->prepare("DELETE FROM `films` WHERE `id` = :id");
		$stmt->bindParam(':id', $id);
		if ($stmt->execute())
		{
    		return TRUE;
        }
        else
            return FALSE;
        $stmt = NULL;
    }
    
    public static function delParticipant($id)
    {
        $stmt = Database::getInstance()->dbh->prepare("DELETE FROM `tagmap` WHERE `f_id` = :f_id");
        $stmt->bindParam(':f_id', $id);
        if ($stmt->execute())
        {
        	return TRUE;
        }
        else
            return FALSE;
        $stmt = NULL;
    }
    
    public static function getParticipant($f_id)
    {
	    $stmt = Database::getInstance()->dbh->prepare("SELECT `a_id` FROM `tagmap` WHERE `f_id` = :f_id");
    	$stmt->bindParam(':f_id', $f_id);
		if ($stmt->execute())
		{
			$i = 0;
			foreach ($stmt as $row)
			{
				$resultArray[$i]['a_id'] = $row['a_id'];
				$i++;
			}
			if ( ! empty($resultArray))
				return $resultArray;
		}
		$stmt = NULL;
    }
    
    public static function getArtist($id='all')
    {
    	if ($id == 'all')
    		$q = "SELECT `artist`.`u_id`, `artist`.`name`, `a_type`.`name` as `type` FROM `artist`, `a_type` WHERE `artist`.`type` = `a_type`.`id` ORDER BY `artist`.`name`";
    	else
    		$q = "SELECT `artist`.`u_id`, `artist`.`name`, `a_type`.`name` as `type` FROM `tagmap` LEFT JOIN `artist` ON `artist`.`u_id` = `tagmap`.`a_id` LEFT JOIN `a_type` ON `artist`.`type` = `a_type`.`id` WHERE `tagmap`.`f_id` = :u_id";
    	
	    $stmt = Database::getInstance()->dbh->prepare($q);
    	$stmt->bindParam(':u_id', $id);
		if ($stmt->execute())
		{
			$i = 0;
			foreach ($stmt as $row)
			{
				$resultArray[$i]['u_id'] = $row['u_id'];
				$resultArray[$i]['name'] = $row['name'];
				$resultArray[$i]['type'] = $row['type'];
				$i++;
			}
			if ( ! empty($resultArray))
				return $resultArray;
		}
		$stmt = NULL;
    }
    
    public static function checkExistFilm($f_id)
    {
	    $stmt = Database::getInstance()->dbh->prepare("SELECT `id` FROM `films` WHERE `id` = :f_id");
    	$stmt->bindParam(':id', $f_id);
		if ($stmt->execute())
		{
			foreach ($stmt as $row)
			{
				if ( ! empty($row['id']))
					return TRUE;
				else
					return FALSE;
			}
		}
		$stmt = NULL;
    }
    
    public static function addFilm($f_id, $f_name, $f_year)
    {
    	$stmt = Database::getInstance()->dbh->prepare("INSERT INTO `films` (`id`, `name`, `year`) VALUES (:id, :name, :year)");
    	$stmt->bindParam(':id', $f_id);
    	$stmt->bindParam(':name', $f_name);
    	$stmt->bindParam(':year', $f_year);

		if ($stmt->execute())
			return TRUE;
		else
			return FALSE;
		$stmt = NULL;
    }
    
    public static function getFilms($f_year)
    {
        $stmt = Database::getInstance()->dbh->prepare("SELECT `id`, `name`, `year`, `kp_rating`, `imdb_rating`, `overlooked` FROM `films` WHERE `year` = :year AND `show` = 1 ORDER BY `name`");
		$stmt->bindParam(':year', $f_year);
		if ($stmt->execute())
		{
			$i = 0;
			foreach ($stmt as $row)
			{
				$resultArray[$i]['id'] = $row['id'];
				$resultArray[$i]['name'] = $row['name'];
				$resultArray[$i]['year'] = $row['year'];
				$resultArray[$i]['kp_rating'] = $row['kp_rating'];
				$resultArray[$i]['imdb_rating'] = $row['imdb_rating'];
				$resultArray[$i]['overlooked'] = $row['overlooked'];
				$i++;
			}
			if ( ! empty($resultArray))
				return $resultArray;
		}
		$stmt = NULL;
		$resultArray = NULL;   
    }
    
    public static function getFilmsByArtist($a_id)
    {
        $stmt = Database::getInstance()->dbh->prepare("SELECT `films`.`id`, `films`.`name`, `films`.`year`, `films`.`kp_rating`, `films`.`imdb_rating`, `films`.`overlooked` FROM `tagmap` LEFT JOIN `films` ON `films`.`id` = `tagmap`.`f_id` WHERE `tagmap`.`a_id` = :a_id AND `films`.`show` = '1' ORDER BY `year` DESC");
		$stmt->bindParam(':a_id', $a_id);
		if ($stmt->execute())
		{
			$i = 0;
			foreach ($stmt as $row)
			{
				$resultArray[$i]['id'] = $row['id'];
				$resultArray[$i]['name'] = $row['name'];
				$resultArray[$i]['year'] = $row['year'];
				$resultArray[$i]['kp_rating'] = $row['kp_rating'];
				$resultArray[$i]['imdb_rating'] = $row['imdb_rating'];
				$resultArray[$i]['overlooked'] = $row['overlooked'];
				$i++;
			}
			if ( ! empty($resultArray))
				return $resultArray;
		}
		$stmt = NULL;
		$resultArray = NULL;   
    }    
    
    public static function checkExistTag($f_id, $a_id)
    {
		$stmt = Database::getInstance()->dbh->prepare("SELECT count(id) as `count` FROM `tagmap` WHERE `f_id` = :f_id AND `a_id` = :a_id");
    	$stmt->bindParam(':f_id', $f_id);
    	$stmt->bindParam(':a_id', $a_id);
		if ($stmt->execute())
			foreach ($stmt as $row)
			{
    			if ($row['count'] == 0)
    			    return FALSE;
                else
                    return TRUE;
			}
		$stmt = NULL; 
    }
    
    public static function addTag($f_id, $a_id)
    {
		$stmt = Database::getInstance()->dbh->prepare("INSERT INTO `tagmap` (`f_id`, `a_id`) VALUES (:f_id, :a_id)");
    	$stmt->bindParam(':f_id', $f_id);
    	$stmt->bindParam(':a_id', $a_id);
		if ($stmt->execute())
			return TRUE;
		else
			return FALSE;
		$stmt = NULL;   
    }
    
    public static function updateRating($f_id, $kp, $imdb)
    {
		$stmt = Database::getInstance()->dbh->prepare("UPDATE `films` SET `kp_rating` = :kp, `imdb_rating` = :imdb WHERE `id` = :f_id");
    	$stmt->bindParam(':kp', $kp);
    	$stmt->bindParam(':imdb', $imdb);
    	$stmt->bindParam(':f_id', $f_id);
		if ($stmt->execute())
			return TRUE;
		else
			return FALSE;
		$stmt = NULL;   
    }
    
    public static function getYear()
    {
        $stmt = Database::getInstance()->dbh->prepare("SELECT `year` FROM `films` ORDER BY `year` DESC");
		if ($stmt->execute())
		{
			$i = 0;
			foreach ($stmt as $row)
			{
			    $resultArray[$i]['year'] = $row['year'];
				$i++;
			}
			if ( ! empty($resultArray))
				return $resultArray;
		}
		$stmt = NULL;
		$resultArray = NULL;
    }    
    
    public static function overlooke($id)
    {
		$stmt = Database::getInstance()->dbh->prepare("UPDATE `films` SET `overlooked` = '1' WHERE `id` = :id");
    	$stmt->bindParam(':id', $id);
		if ($stmt->execute())
			return TRUE;
		else
			return FALSE;
		$stmt = NULL;
    }
    
    public static function hide($id)
    {
		$stmt = Database::getInstance()->dbh->prepare("UPDATE `films` SET `show` = '0' WHERE `id` = :id");
    	$stmt->bindParam(':id', $id);
		if ($stmt->execute())
			return TRUE;
		else
			return FALSE;
		$stmt = NULL;   
    }    
}
?>