<?php

namespace App;
use \PDO as PDO;
use App\Utils\ArrayUtils;

/**
 * Classe de modification, lecture, insertion des données
 * Auteur : Julien RAVIA <jrgfawkes@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
 */
class Database
{
    private static $dbh; // Objet dbh

    // private $_host = 'localhost';
    // private $_database = 'oxford';
    // private $_user = 'root';
    // private $_password = '';
    // private $_port = 3306;

    private $_host = 'mysql-simubac.alwaysdata.net';
    private $_database = 'simubac_oxford';
    private $_user = 'simubac';
    private $_password = 'aDemantA';
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

    public function lastInsertId()
    {
        return Database::$dbh->lastInsertId();
    }

    // REQUETES

    /**
     * Récupération des utilisateurs, classés par id (desc)
     * @return array Résultat de la requête (utilisateurs)
     */
    public function getUsers()
    {
        $req = Database::$dbh->query('SELECT users.id, nom, prenom, status, photos.value as photo, type, types.icon, types.filter FROM users 
                                      LEFT JOIN photos ON users.photo = photos.id
                                      INNER JOIN types ON users.type = types.id
                                      WHERE status IS NOT NULL
                                      ORDER BY users.id DESC');
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des utilisateurs par type classés par id (desc)
     * @return array Résultat de la requête (utilisateurs)
     */
    public function getUsersByType($type)
    {
        if($type == 'deleted') {
            $req = Database::$dbh->query('SELECT users.id, nom, prenom, status, photos.value as photo, type, types.icon, types.filter FROM users 
                                          INNER JOIN photos ON users.photo = photos.id
                                          INNER JOIN types ON users.type = types.id
                                          WHERE status IS NULL
                                          ORDER BY users.id DESC');
        } else {
            $req = Database::$dbh->prepare('SELECT users.id, nom, prenom, status, photos.value as photo, type, types.icon, types.filter FROM users 
                                            INNER JOIN photos ON users.photo = photos.id
                                            INNER JOIN types ON users.type = types.id
                                            WHERE types.filter = :type AND status IS NOT NULL
                                            ORDER BY users.id DESC');
            $req->execute(array('type' => strtolower($type)));
        }
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
        $req = Database::$dbh->prepare('SELECT users.id as id, nom, prenom, type, types.value, birth, sexe, status, email, photos.value as photo 
                                        FROM users 
                                        LEFT JOIN types ON users.type = types.id 
                                        LEFT JOIN photos ON users.photo = photos.id
                                        WHERE users.id = :user');
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
                                      events.user as user, nom, prenom, date, users.status,
                                      events.value as value
                                      FROM events 
                                      LEFT JOIN users ON events.user = users.id 
                                      INNER JOIN categories ON events.category = categories.id
                                      ORDER BY date DESC, id DESC');
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des types d'employés dans la table 'types'
     * @return array Résultat de la requête (types d'employés)
     */
    public function getTypes()
    {
        $req = Database::$dbh->query('SELECT id, value, filter, icon FROM types ORDER BY id');
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des types d'employés dans la table 'types'
     * @return array Résultat de la requête (types d'employés)
     */
    public function getTypesWithValue()
    {
        $req = Database::$dbh->query('SELECT id, value FROM types ORDER BY id');
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
                                        events.user as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        LEFT JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE date BETWEEN :dateb AND :datee
                                        ORDER BY date DESC, id DESC');
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
                                        events.user as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        LEFT JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE user = :user
                                        ORDER BY date DESC, id DESC');
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
                                        events.user as user, nom, prenom, date, users.status,
                                        events.value as value
                                        FROM events 
                                        LEFT JOIN users ON events.user = users.id 
                                        INNER JOIN categories ON events.category = categories.id
                                        WHERE categories.value = :category
                                        ORDER BY date DESC, id ASC');
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
        $this->typeExist($infos['poste']); // on vérifie que les types d'emploi existent
        // On change son type
        $reqType = Database::$dbh->prepare('UPDATE users SET type = :type WHERE id = :id');
        $reqType = $reqType->execute(array('id' => $infos['id'], 'type' => $infos['poste']));
        
        if($infos['poste'] == 1 AND $poste != 1) { // Si on à choisi de le mettre RSSI on l'ajoute aux admins avec le mdp choisi
            $this->addAdmin($infos['id'], $infos['password']);
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
     * On vériie que le type passé en paramètre existe
     * @param  int    $type Type d'emploi (table types)
     * @return bool         Exception si 0 types, true si il y a un type avec cet id 
     */
    public function typeExistWithFilter(string $type)
    {
        $req = Database::$dbh->prepare('SELECT COUNT(*) as count, value FROM types WHERE filter = :filter GROUP BY value');
        $req->execute(array('filter' => $type));
        $result = $req->fetch();
        if($result['count'] == 0) {
            throw new \Exception('Le type d\'emploi selectionné n\'existe pas');
        } else {
            return $result['value'];
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

    /**
     * Suppression d'un utilisateur à partir de son id
     * @param  int    $user Utilisateur à supprimer
     * @return boolean      Résultat de la requête
     * @return Exception    Exception
     */
    public function deleteUser(int $user) {
        $req = Database::$dbh->prepare('UPDATE users SET status = NULL WHERE id = :id');
        if($req->execute(array('id' => $user))) {
            return true;      
        } else {
            throw new \Exception('Impossible de supprimer l\'utilisateur');
        }
    }

    /**
     * Révocation (suppression d'accès) d'un utilisateur à partir de son id
     * @param  int    $user Utilisateur à révoquer
     * @return boolean      Résultat de la requête
     * @return Exception    Exception
     */
    public function revokeUser(int $user) {
        $req = Database::$dbh->prepare('UPDATE users SET status = 0 WHERE id = :id');
        if($req->execute(array('id' => $user))) {
            return true;      
        } else {
            throw new \Exception('Impossible de révoquer l\'utilisateur');
        }
    }

    /**
     * Autorisation (attribution d'accès) d'un utilisateur à partir de son id
     * @param  int    $user Utilisateur à autoriser
     * @return boolean      Résultat de la requête
     * @return Exception    Exception
     */
    public function autorizeUser(int $user) {
        $req = Database::$dbh->prepare('UPDATE users SET status = 1 WHERE id = :id');
        if($req->execute(array('id' => $user))) {
            return true;      
        } else {
            throw new \Exception('Impossible d\'autoriser l\'utilisateur');
        }
    }

    /**
     * Autorisation (attribution d'accès) d'un utilisateur à partir de son id
     * @param  int    $user Utilisateur à autoriser
     * @return boolean      Résultat de la requête
     * @return Exception    Exception
     */
    public function restoreUser(int $user) {
        $req = Database::$dbh->prepare('UPDATE users SET status = 1 WHERE id = :id');
        if($req->execute(array('id' => $user))) {
            return true;      
        } else {
            throw new \Exception('Impossible de restaurer l\'utilisateur');
        }
    }

    /**
     * Ajout d'un administrateur
     * @param int $user        Identifiant de l'utilisateur à ajouter aux administrateurs
     * @param string $password Mot de passe de l'utilisateur pour accéder à l'administration
     */
    public function addAdmin($user, $password)
    {
        $req = Database::$dbh->prepare('INSERT INTO admins(id, password, date) VALUES(:id, :password, :date)');
        if($req->execute(array('id' => $user, 'password' => hash('sha256', $password), 'date' => time()))) {
            return true;
        } else {
            throw new \Exception('L\'administrateur n\'à pas pu être ajouté');
        }
    }

    public function getTickets()
    {
        $req = Database::$dbh->query('SELECT tickets.id AS id, events.id as event, subjects.value as subject, events.value as eventvalue,
                                      users.id as user, users.nom as nom, users.prenom as prenom, tickets.date, statut FROM tickets 
                                      LEFT JOIN subjects ON tickets.subject = subjects.id
                                      LEFT JOIN users ON tickets.user = users.id
                                      LEFT JOIN events ON tickets.event = events.id
                                      ORDER BY date DESC');
        $req->execute();
        $result = $req->fetchAll();
        return $result;
    }

    public function getTicket($id) {
        $req = Database::$dbh->prepare('SELECT tickets.id AS id, events.id as event, subjects.value as subject, events.value as eventvalue,
                                      users.id as user, COALESCE(users.nom, "Utilisateur supprimé") as nom, users.prenom as prenom, tickets.date, statut, token FROM tickets 
                                      LEFT JOIN users ON tickets.user = users.id
                                      LEFT JOIN subjects ON tickets.subject = subjects.id
                                      LEFT JOIN events ON tickets.event = events.id
                                      WHERE tickets.id = :id
                                      ORDER BY date DESC');
        $req->execute(array('id' => $id));
        $result = $req->fetch();
        if($result) {
            return $result;
        } else {
            throw new \Exception('Le ticket auquel vous essayez d\'accéder n\'existe pas');
        }
    }

    public function getTicketReplies($id) {
        $req = Database::$dbh->prepare('SELECT r.date, r.id, r.value, COALESCE(users.nom, "Utilisateur supprimé") as nom, 
                                        users.prenom, r.user FROM tickets_replies AS r 
                                        LEFT JOIN users on r.user = users.id
                                        WHERE ticket = :id
                                        ORDER BY date');
        $req->execute(array('id' => $id));
        $result = $req->fetchAll();
        return $result;
    }

    public function addTicketReply($array) {
        $req = Database::$dbh->prepare('INSERT INTO tickets_replies(user, value, date, ticket) VALUES(:user, :value, :date, :ticket)');
        $req->execute($array);
        return Database::$dbh->lastInsertId();
    }

    public function updateTicketStatus($ticket, $statut, $date = '') {
        if (empty($date)) {
            $req = Database::$dbh->prepare('UPDATE tickets SET statut = :statut WHERE id = :ticket');
            $req->execute(array('statut' => $statut, 'ticket' => $ticket));
        } else {
            $req = Database::$dbh->prepare('UPDATE tickets SET statut = :statut, date = :date WHERE id = :ticket');
            $req->execute(array('statut' => $statut, 'ticket' => $ticket, 'date' => $date));
        }
    }

    public function getTicketsByStatut($statut)
    {
        $statusList = array('opens' => 1, 'closed' => 4, 'replied' => 3, 'newreply' => 2);
        $req = Database::$dbh->prepare('SELECT tickets.id AS id, events.id as event, subjects.value as subject, events.value as eventvalue,
                                      users.id as user, users.nom as nom, users.prenom as prenom, tickets.date, statut FROM tickets 
                                      LEFT JOIN subjects ON tickets.subject = subjects.id
                                      LEFT JOIN users ON tickets.user = users.id
                                      LEFT JOIN events ON tickets.event = events.id
                                      WHERE statut = :statut
                                      ORDER BY date DESC');
        $req->execute(array('statut' => $statusList[$statut]));
        $result = $req->fetchAll();
        return $result;
    }

    public function getTicketsByUser($user)
    {
        $req = Database::$dbh->prepare('SELECT tickets.id AS id, events.id as event, subjects.value as subject, events.value as eventvalue,
                                      users.id as user, users.nom as nom, users.prenom as prenom, tickets.date, statut FROM tickets 
                                      LEFT JOIN users ON tickets.user = users.id
                                      LEFT JOIN subjects ON tickets.subject = subjects.id
                                      LEFT JOIN events ON tickets.event = events.id
                                      WHERE tickets.user = :user
                                      ORDER BY date DESC');
        $req->execute(array('user' => $user));
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Affichage des tickets par date
     * @param  int $dateB Date de début
     * @param  int $dateE Date de fin
     * @return array        Résultats
     */
    public function getTicketsByDate($dateB, $dateE)
    {
        $req = Database::$dbh->prepare('SELECT tickets.id AS id, events.id as event, subjects.value as subject, events.value as eventvalue,
                                      users.id as user, users.nom as nom, users.prenom as prenom, tickets.date, statut FROM tickets 
                                      LEFT JOIN users ON tickets.user = users.id
                                      LEFT JOIN subjects ON tickets.subject = subjects.id
                                      LEFT JOIN events ON tickets.event = events.id
                                      WHERE tickets.date BETWEEN :dateB AND :dateE
                                      ORDER BY date DESC');
        $req->execute(array('dateE' => $dateE, 'dateB' => $dateB));
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Récupération des infos d'un utilisateur à partir d'un login et d'un mdp      
     * @param  string $login    Adresse mail
     * @param  string $password Mot de passe (hashé en sha256)
     * @return array            Résultat de la requête (administrateur)
     */
    public function getAdmin($login, $password) {
        $req = Database::$dbh->prepare('SELECT users.id, users.nom, users.prenom, types.filter as type FROM `admins` 
                                        INNER JOIN users ON admins.id = users.id 
                                        INNER JOIN types ON users.type = types.id
                                        WHERE users.email = :login AND admins.password = :password');
        $req->execute(array('login' => $login, 'password' => $password));
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Suppression d'un ticket
     * @param  int $id Identifiant du ticket à supprimer
     * @return boolean     True or false
     */
    public function deleteTicket($id)
    {
        $req = Database::$dbh->prepare('DELETE FROM tickets WHERE id = :id');
        $req->execute(array('id' => $id));
        $req = Database::$dbh->prepare('DELETE FROM tickets_replies WHERE ticket = :ticket');
        $req->execute(array('ticket' => $id));
    }

    /**
     * Création d'un évent
     * @param  int $id Type d'événement
     * @return boolean     True or false
     */
    public function newEvent($user, $type, $value)
    {
        $req = Database::$dbh->prepare('INSERT INTO events(user, category, date, value) VALUES(:user, :category, :date, :value)');
        $result = $req->execute(array('user' => $user,
                                      'category' => $type,
                                      'date' => time(),
                                      'value' => $value));
    }

    /**
     * Récupération des sujets tickets par défaut
     * @return boolean     Sujets de tickets
     */
    public function getDefaultSubjects()
    {
        $req = Database::$dbh->query('SELECT * FROM subjects WHERE created = 0 ORDER BY id');
        return $req->fetchAll();
    }

    /**
     * Récupération des sujets tickets par défaut
     * @return boolean     Sujets de tickets
     */
    public function getAdmins()
    {
        $req = Database::$dbh->query('SELECT admins.id, users.id as user, users.nom, users.prenom, users.status FROM admins LEFT JOIN users ON admins.id = users.id ORDER BY id');
        return $req->fetchAll();
    }

    /**
     * Suppression d'un type d'employés
     * @return boolean     True ou false
     */
    public function deleteType($id)
    {
        $req = Database::$dbh->prepare('DELETE FROM types WHERE id = :id');
        $req->execute(array('id' => $id));
        $req = Database::$dbh->prepare('UPDATE users SET status = NULL WHERE type = :id');
        $req->execute(array('id' => $id));
    }

    /**
     * Suppression d'un sujet de ticket
     * @return boolean     True ou false
     */
    public function deleteSubject($id)
    {
        $req = Database::$dbh->prepare('DELETE FROM subjects WHERE id = :id');
        $req->execute(array('id' => $id));
        return $req;
    }

    /**
     * Suppression d'un accès administrateur
     * @return boolean     True ou false
     */
    public function deleteAccess($id)
    {
        $req = Database::$dbh->prepare('DELETE FROM admins WHERE id = :id');
        $req->execute(array('id' => $id));
        $req = Database::$dbh->prepare('UPDATE users SET type = 6 WHERE id = :id');
        $req->execute(array('id' => $id));
        return $req;
    }

    /**
     * Ajout d'un type d'employé
     * @param array $array Données du type d'employé à ajouter à la table
     */
    public function addType($array)
    {
        $req = Database::$dbh->prepare('INSERT INTO types(value, filter, icon) VALUES(:type, :filter, :icon)');
        $req->execute($array);
        return Database::$dbh->lastInsertId();
    }

    /**
     * Ajout d'un sujet par défaut
     * @param array $array Sujet à ajouter aux sujets par défaut
     */
    public function addSubject($subject)
    {
        $req = Database::$dbh->prepare('INSERT INTO subjects(value, created) VALUES(:sujet, 0)');
        $req->execute(array('sujet' => $subject));
        return Database::$dbh->lastInsertId();
    }
}