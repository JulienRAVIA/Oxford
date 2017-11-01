<?php 

namespace App;

/**
 * Utilitaire pour les formulaires
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation interdite sauf autorisation
 */
class Form
{
	/**
	 * On vérifie que la valeur passée soit une valeur numérique	
	 * @param  int  $value    Valeur numérique renseignée
	 * @return int            Valeur numérique renseignée
	 * @return Exception      Exception
	 */
	static function isInt($value) {
		if(is_numeric($value)) {
			return $value;
		} else {
			throw new \Exception('La valeur '.$value.' n\'est pas le type de donnée attendu (int)');
		}
	}

	/**
	 * On vérifie que la valeur passée soit une chaine de caractères, de plus de x caractères
	 * @param  string  $value  Chaine de caractère renseignée
	 * @param  integer $length Longueur minimale
	 * @return string          Chaine de caractère renseignée
	 * @return Exception       Exception
	 */
	static function isString($value, $length = 5) {
		if(is_string($value)) {
			if(strlen($value) >= $length)  {
				return $value;
			} else {
				throw new \Exception('La valeur '.$value.' ne respecte pas la longueur attendue ('.$length.')');
			}
		} else {
			throw new \Exception('La valeur '.$value.' n\'est pas le type de donnée attendu (string)');
		}
	}

	/**
	 * On vérifie que l'adresse email renseignée soit correcte
	 * @param  string  $value Adresse email renseignée
	 * @return string         Adresse email renseignée
	 * @return Exception      Exception
	 */
	static function isMail($value) {
		if(filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return $value;
		} else {
			throw new \Exception('La valeur '.$value.' n\'est pas le type de donnée attendu (email)');
		}
	}

	/**
	 * On vérifie que la date attendue soit au bon format
	 * @param  string  $value Date renseignée
	 * @return int        	  Timestamp de la date renseignée
	 * @return exception        Exception
	 */
	static function isDate($value) {
		if (preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $value, $matches)) {
		    if (!checkdate($matches[2], $matches[1], $matches[3])) {
				throw new \Exception('La valeur '.$value.' n\'est pas le type de donnée attendu (date). Elle doit-être au format dd/mm/yyyy');
		    } else {
        		$date = mktime(0, 0, 0, $matches[2], $matches[1], $matches[3]);
        		return $date;
		    }
		} else {
		    throw new \Exception('La valeur '.$value.' n\'est pas le type de donnée attendu (date). Elle doit-être au format dd/mm/yyyy');
		}
	}

	/**
	 * On vérifie que le sexe renseigné soit M ou F
	 * @param  string  $value Sexe renseigné
	 * @return string         Sexe renseigné
	 * @return exception        Exception
	 */
	static function isSex($value) {
		if($value == 'M' OR $value == 'F') {
			return $value;
		} else {
			throw new \Exception('La valeur '.$value.' n\'est pas le type de donnée attendu (Sexe de type M ou F)');
		}
	}

	/**
	 * On vérifie que les données d'un tableau correspondent
	 * au données attendues, à partir d'un tableau définissant
	 * les champs attendus
	 * @param  array  $array  Données envoyées
	 * @param  [type]  $fields Champs/clés du tableau attendues
	 * @return boolean         On renvoie true, ou une exception
	 */
	static function isNotEmpty($array, $fields) {
		if(isset($array)) {
			foreach ($fields as $field) {
				if(!isset($array[$field])) {
					throw new \Exception('Un des champs n\'est pas renseigné');
				}
				if(empty($array[$field])) {
					throw new \Exception('Le champ '.$field.' est vide');
				}
			}
			return true;
		} else {
			throw new \Exception('Toutes les données ne sont pas renseignées'.var_dump($array));
		}
	}

	/**
	 * Vérification que le mot de passe à plus de 8 caractères, un caractère spécial, un chiffre, et une libxml_set_streams_context(streams_context)
	 * @param  string  $value  Mot de passe à verifier		
	 * @return string 	Mot de passe conforme
	 * @return exception Exception
	 */
	static function isPassword($value) {
		if(!empty($value)) {
			if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/", $value, $matches)) {
				return $value;
			} else {
		    	throw new \Exception('Le mot de passe renseigné n\'est pas un mot de passe valide, il doit y avoir 8 caractères, un caractère spécial et un chiffre.');
			}
		}
	}

	static function upload($file, $name, $folder = 'photos')
	{	
		$extension = explode('.', basename($_FILES[$file]["name"]));
		$fileName = $name.'.'.$extension[1];
		$target_file = $folder . $fileName;
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// On vérifie si le fichier est une image
		$check = getimagesize($_FILES[$file]["tmp_name"]);

		if($check !== false) {
		    $uploadOk = 1;
		} else {
		    throw new \Exception('Le fichier n\'est pas une image');
		}
		// Check if file already exists
		if (file_exists($target_file)) {
		    throw new \Exception('Le fichier existe déjà');
		}

		// On autorise les images
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		    throw new \Exception('Le fichier n\'est pas une image');
		} else {
			$uploadOk = 1;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    throw new \Exception('Le fichier n\'a pas été uploadé');
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
		        return $fileName;
		    } else {
		        return false;
		    }
		}
	}
}
