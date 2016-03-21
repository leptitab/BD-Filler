<?php
	require_once "bdService.php";

	/*
	foreach ($structure as $key => $value) {
		echo '<pre>'.var_dump($value).'</pre>';
	}
	*/
	
	if(isset($_POST['action']) && !empty($_POST['action'])) {
		$action = $_POST['action'];
		
		$action();
	}
	
	function getTable(){
		
		$bd = new bdService();
		$strSQL="
		SELECT TABLE_NAME 
		FROM INFORMATION_SCHEMA.TABLES
		WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='pokemonchecklist' 
		";
		try
		{
			$content=$bd->Selection($strSQL);
		}
		
		catch(Exception $e)
		{
			die($e->getmessage());
		}
		
		echo(json_encode($content));
	}

	function addNewContent(){
		
		if(isset($_POST['table']) && !empty($_POST['table'])) 
		{
			$table = $_POST['table'];
			$bd = new bdService();
			$strSQL="
			DESCRIBE ".$table;
			try
			{
				$structure=$bd->Selection($strSQL);
			}
			
			catch(Exception $e)
			{
				die($e->getmessage());
			}

			$newContentStructure = array();
			foreach ($structure as $key => $value) {
				if($value["Key"]=="MUL" && $value["Extra"]!="auto_increment")
				{
					$bd = new bdService();
					$strSQL="
					SELECT NAME FROM information_schema.INNODB_SYS_TABLES WHERE TABLE_ID = (SELECT TABLE_ID FROM information_schema.INNODB_SYS_COLUMNS WHERE NAME='".$value['Field']."' AND POS=0)
					";
					try
					{
						$fkTable=$bd->Selection($strSQL);
					}
					
					catch(Exception $e)
					{
						die($e->getmessage());
					}

					$fkTable=substr($fkTable[0]["NAME"],strpos($fkTable[0]["NAME"],"/")+1);

					$strSQL="
					SELECT * FROM ".$fkTable;
					try
					{
						$content=$bd->Selection($strSQL);
					}
					
					catch(Exception $e)
					{
						die($e->getmessage());
					}
					$newContentStructure[$value['Field']]=$content;
				}
				elseif($value["Extra"]=="auto_increment")
				{

				}
				else
				{
					$newContentStructure[$value['Field']]=$value['Type'];
				}
			}
			
			echo(json_encode($newContentStructure));
		}
	}
	
	function getTableContent(){
		
		if(isset($_POST['table']) && !empty($_POST['table'])) 
		{
			$table = $_POST['table'];
			$bd = new bdService();
			$strSQL="
			SELECT *
			FROM ".$table;
			try
			{
				$content=$bd->Selection($strSQL);
			}
			
			catch(Exception $e)
			{
				die($e->getmessage());
			}

			foreach ($content as $key => $value) {
				foreach ($value as $cle => $valeur) {
					$content[$key][$cle]=utf8_encode($valeur);
				}
			}
			
			echo(json_encode($content));
		}
	}
?>