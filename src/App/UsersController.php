<?php 

namespace App;
use App\View as View;
use App\Database as Database;

/**
 * summary
 */
class UsersController
{
    /**
     * summary
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }

    public function index() {
    	$users = $this->_db->getUsers();
        View::make('users.twig', array('users' => $users));
    }
}
