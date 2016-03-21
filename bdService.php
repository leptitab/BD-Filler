<?php
	//Fichier: bdService.php
	//Auteur: William Themens
	//Date: 27 Février
	//But: Fichier qui détiendra la classe bdService
	
	class bdService
	{
		private $bdInterne;
		
		function __construct()
		{
			$this->bdInterne = new mysqli('localhost','root','','pokemonchecklist');
			
			if(mysqli_connect_errno())
			{
				throw new Exception("Impossible de se connecter: ".mysqli_connect_error());
			}
		}
		
		function selection($Requete)
		{
				$Tableau = array();
				$Resultat = $this->bdInterne->query($Requete);
				if(!$Resultat)
				{
					throw new Exception("Erreur SQL dans le SELECT: $Requete(".$this->bdInterne->error.")");
				}
				
				while($Ligne=$Resultat->fetch_array(MYSQLI_ASSOC))
				{
					$Tableau[]=$Ligne;
				}
				return $Tableau;
		}
		
		function maj($req)
		{
			$resultat = $this->bdInterne->query($req);
			if(!stripos($req,'WHERE'))
			{
				throw new Exception ('Danger!!! pas de WHERE dans l\'énoncé UPDATE');
			}
			if(!$resultat)
			{
				throw new Exception("Erreur SQL dans le UPDATE: $req(".$this->bdInterne->error.")");
			}
			return $this->bdInterne->affected_rows;
		}
		
		function supprimer($req)
		{
			$resultat = $this->bdInterne->query($req);
			if(!stripos($req,'WHERE'))
			{
				throw new Exception ('Danger!!! pas de WHERE dans l\'énoncé DELETE');
			}
			if(!$resultat)
			{
				throw new Exception("Erreur SQL dans le DELETE: $req(".$this->bdInterne->error.")");
			}
		}
		
		function insertion($req)
		{
			$resultat = $this->bdInterne->query($req);
				if(!$resultat)
				{
					throw new Exception(mysqli_errno($this->bdInterne));
				}
		}
		
		function neutralise($str)
		{
			return $this->bdInterne->real_escape_string($str);
		}
	}
?>