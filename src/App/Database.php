<?php

namespace App;
use \PDO as PDO;

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
     * @return [type] [description]
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
    public function getUser($user, $exception = 'Cet utilisateur n\'existe pas')
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
     * @param  int $date Timestamp de la date à filtrer
     * @return array   Résultat de la requête (événements)
     */
    public function getEventsByDate($date)
    {
        $date = date('m/d/Y', $date);
        $date = explode('/', $date);
        $dateb = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        $datee = mktime(23, 59, 59, $date[0], $date[1], $date[2]);

        $req = Database::$dbh->prepare('SELECT events.id as id, categories.value as category, 
                                        users.id as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        INNER JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE date BETWEEN :dateb AND :datee
                                        ORDER BY date DESC');
        $req->execute(array('dateb' => $dateb, 'datee' => $datee));
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des événements, classés par date (desc), 
     * entre minuit et 23h 59 de la date passée en paramètre
     * @param  int $date Timestamp de la date à filtrer
     * @return array   Résultat de la requête (événements)
     */
    public function getEventsByUser($user)
    {
        $checkUserExist = $this->getUser($user, 'L\'utilisateur dont vous souhaitez filtrer les événements n\'existe pas');
        $req = Database::$dbh->prepare('SELECT events.id as id, categories.value as category, 
                                        users.id as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        INNER JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE user = :user
                                        ORDER BY date DESC');
        $req->execute(array('user' => $user));
        $result = $req->fetchAll();
        return $result;
    }
}