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

    public function getUsers()
    {
        $req = Database::$dbh->query('SELECT * FROM users ORDER BY iduser');
        $result = $req->fetchAll();
        return $result;
    }

    public function getUser($user)
    {
        $req = Database::$dbh->prepare('SELECT * FROM users WHERE iduser = :user');
        $req->execute(array('user' => $user));
        $result = $req->fetch();
        if($result) {
            return $result;
        } else {
            throw new \Exception('Cet utilisateur n\'existe pas');
        }
    }

}