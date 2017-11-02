<?php

namespace App;
use \PDO as PDO;

/**
 * Classe de modification, lecture, insertion des données
 * Auteur : Julien RAVIA <jrgfawkes@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
 */
class Database
{
    private static $dbh; // Objet dbh

    private $_host = 'localhost';
    private $_database = 'oxford';
    private $_user = 'root';
    private $_password = '';
    private $_port = 3306;

    private static $instance;

    private function __construct()
    {
        $user = $this->_user;
        $password = $this->_password;
        $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";

        $dsn = 'mysql:host=' . $this->_host .
               ';dbname='    . $this->_database;
               // Au besoin :
               //';port='      . $this->_port .
               //';connect_timeout=15';

        // Création du pdo
        Database::$dbh = new PDO($dsn, $user, $password, $options);
        Database::$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    // Singleton
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

    // REQUETES

    /**
     * Récupération des utilisateurs, classés par id (desc)
     * @return array Résultat de la requête (utilisateurs)
     */
    public function getUsers()
    {
        $req = Database::$dbh->query('SELECT * FROM users ORDER BY id DESC');
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des infos d'un utilisateur
     * S'il n'existe pas on lance une exception
     * @param  int $user Utilisateur dont on va récupérer les infos
     * @return array     Résultat de la requête (infos utilisateur)
     */
    public function getUser(int $user, string $exception = 'Cet utilisateur n\'existe pas')
    {
        $req = Database::$dbh->prepare('SELECT users.id as id, nom, prenom, type, types.value, birth, sexe, status, email 
                                        FROM users INNER JOIN types ON users.type = types.id WHERE users.id = :user');
        $req->execute(array('user' => $user));
        $result = $req->fetch();
        if($result) {
            return $result;
        } else {
            throw new \Exception($exception);
        }
    }

    /**
     * Récupération de tous les événements, classés par date (desc)
     * @return array     Résultat de la requête (événements)
     */
    public function getEvents()
    {
        $req = Database::$dbh->query('SELECT events.id as id, categories.value as category, 
                                      users.id as user, nom, prenom, date, users.status,
                                      events.value as value
                                      FROM events 
                                      INNER JOIN users ON events.user = users.id 
                                      INNER JOIN categories ON events.category = categories.id
                                      ORDER BY date DESC');
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des types d'employés dans la table 'types'
     * @return array Résultat de la requête (types d'employés)
     */
    public function getTypes()
    {
        $req = Database::$dbh->query('SELECT id, value FROM types');
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des événements, classés par date (desc), 
     * entre minuit et 23h 59 de la date passée en paramètre
     * @param  int $dateB Début de la journée du jour choisi (00h)
     * @param  int $dateE Fin de la journée du jour choisi (23h59m59s)
     * @return array   Résultat de la requête (événements)
     */
    public function getEventsByDate(int $dateB, int $dateE)
    {
        $req = Database::$dbh->prepare('SELECT events.id as id, categories.value as category, 
                                        users.id as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        INNER JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE date BETWEEN :dateb AND :datee
                                        ORDER BY id, date DESC');
        $req->execute(array('dateb' => $dateB, 'datee' => $dateE));
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des événements, classés par date (desc), 
     * de l'utilisateur passé en paramètre
     * @param  int $user Id de l'utilisateur à filtrer
     * @return array   Résultat de la requête (événements)
     */
    public function getEventsByUser(int $user)
    {
        $req = Database::$dbh->prepare('SELECT events.id as id, categories.value as category, 
                                        users.id as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        INNER JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE user = :user
                                        ORDER BY id, date DESC');
        $req->execute(array('user' => $user));
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des événements, classés par date (desc), 
     * de l'utilisateur passé en paramètre
     * @param  int $user Id de l'utilisateur à filtrer
     * @return array   Résultat de la requête (événements)
     */
    public function getEventsByCategory(string $category)
    {
        $req = Database::$dbh->prepare('SELECT events.id as id, categories.value as category, 
                                        users.id as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        INNER JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE categories.value = :category
                                        ORDER BY id, date DESC');
        $req->execute(array('category' => strtolower($category)));
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Mise à jour des infos de l'utilisateur à partir d'un tableau de données
     * @param  array  $user Tableau des données à mettre à jour
     * @return bool         Résultat de la requête
     */
    public function updateUserInfos(array $user) {
        $req = Database::$dbh->prepare('UPDATE users SET nom = :nom, prenom = :prenom, birth = :birth, sexe = :sexe, email = :email WHERE id = :id');
        $req = $req->execute($user);
        if($req) {
            return $req;
        } else {
            throw new \Exception('L\'utilisateur n\'a pas pu être mis à jour');
        }
    }

    /**
     * Mise à jour du type d'employé pour un utilisateur 
     * à partir d'un tableau de données
     * @param  array  $infos Tableau des données à mettre à jour
     * @return bool         Résultat de la requête
     */
    public function updateUserJob(array $infos) {
        $poste = $this->userType($infos['id']); // on récupère le type actuel de l'employé
        $this->typeExist($infos['poste']); // on vérifie que les types d'emploi
        // On change son type
        $reqType = Database::$dbh->prepare('UPDATE users SET type = :type WHERE id = :id');
        $reqType = $reqType->execute(array('id' => $infos['id'], 'type' => $infos['poste']));
        
        if($infos['poste'] == 1 AND $poste != 1) { // Si on à choisi de le mettre RSSI on l'ajoute aux admins avec le mdp choisi
            $req = Database::$dbh->prepare('INSERT INTO admins(id, password, date) VALUES(:id, :password, :date)');
            $req->execute(array('id' => $infos['id'], 'password' => hash('sha256', $infos['password']), 'date' => time()));
        } elseif($infos['poste'] == 1 AND $poste == 1) { // Si il est déjà RSSI, on modifie son mdp admin
            $req = Database::$dbh->prepare('UPDATE admins SET password = :password WHERE id = :id');
            $req->execute(array('id' => $infos['id'], 'password' => hash('sha256', $infos['password'])));
        } elseif($infos['poste'] != 1 AND $poste == 1) { // Si on à choisi de changer son poste et qu'il était RSSI, on le supprime des admins
            $req = Database::$dbh->prepare('DELETE FROM admins WHERE id = :id');
            $req->execute(array('id' => $infos['id']));
        }
        if($reqType) {
            return true;
        } else {
            throw new \Exception('Le type de poste de l\'utilisateur n\'a pas pu être mis à jour');
        }
    }

    /**
     * Mise à jour du code d'accès d'un utilisateur
     * @param  array  $infos Tableau de données à mettre à jour
     * @return bool          Résultat de la requête
     */
    public function updateUserCode(array $infos)
    {
        $req = Database::$dbh->prepare('UPDATE users SET code = :code WHERE id = :id');
        $req = $req->execute(array('id' => $infos['id'], 'code' => $infos['code']));
        if($req) {
            return $req;
        } else {
            throw new \Exception('Le code de l\'utilisateur n\'a pas pu être changé.');
        }
    }

    /**
     * On vériie que le type passé en paramètre existe
     * @param  int    $type Type d'emploi (table types)
     * @return bool         Exception si 0 types, true si il y a un type avec cet id 
     */
    public function typeExist(int $type)
    {
        $req = Database::$dbh->prepare('SELECT COUNT(*) as count FROM types WHERE id = :id');
        $req->execute(array('id' => $type));
        $result = $req->fetch()['count'];
        if($result == 0) {
            throw new \Exception('Le type d\'emploi selectionné n\'existe pas');
        } else {
            return true;
        }
    }

    /**
     * Récupération du poste de l'utilisateur (visiteur, RSSI, etc)
     * @param  string $user Identifiant de l'utilisateur
     * @return string       Poste de l'utilisateur
     */
    public function userType(int $user)
    {
        $req = Database::$dbh->prepare('SELECT type FROM users WHERE id = :id');
        $req->execute(array('id' => $user));
        $result = $req->fetch()['type'];
        return $result;
    }

    /**
     * Ajout d'une photo à la table photos avec pour info les paramètres passés
     * @param  string $name Nom de la photo uploadée
     * @param  int $date    Date (format timestamp) de la photo
     * @return int          Identifiant de la photo inséré
     */
    public function insertPhoto(string $name, int $date) {
        $req = Database::$dbh->prepare('INSERT INTO photos(date, value) VALUES(:date, :value)');
        $req->execute(array('date' => $date, 'value' => $name));
        return Database::$dbh->lastInsertId();
    }

    /**
     * Récupération de l'id de la photo à partir du nom de fichier
     * @param  string $name Nom du fichier à rechercher
     * @return int          Identifiant de la photo recherchée
     * @return Exception    Exception
     */
    public function getPhotoId($name) {
        $req = Database::$dbh->prepare('SELECT id FROM photos WHERE value = :value');
        $req->execute(array('value' => $name));
        if($result = $req->fetch()) {
            return $result['id'];
        } else {
            throw new \Exception('Cette photo n\'existe pas');
        }
    }

    /**
     * Ajout d'un utilisateur dans la table users avec les infos qui sont dans le tableau
     * @param  array  $infos Données de l'utilisateur à insérer
     * @return int           Identifiant de l'utilisateur inséré
     */
    public function insertUser(array $infos)
    {
        $req = Database::$dbh->prepare('INSERT INTO users(nom, prenom, birth, sexe, email, photo, code, type, status) VALUES(:nom, :prenom, :birth, :sexe, :email, :photo, :code, :type, 1)');
        $array = ArrayUtils::copyAndExclude($infos, array('password')); // on copie le tableau en enlevant la clé photo
        if($req->execute($array)) {
            return Database::$dbh->lastInsertId();        
        } else {
            throw new \Exception('Impossible de créer l\'utilisateur');
        }
    }
}