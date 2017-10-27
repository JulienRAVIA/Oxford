<?php 

namespace App;
use App\View as View;
use App\Database as Database;

class HomeController
{
    private $_db;

    public function __construct()
    {
        $this->_db = Database::getInstance();
    }

    public function index()
    {
        $categories = $this->_db->getUsers();
    	View::make('index.html', array('name' => 'ALBERT'));
    }

    public function test()
    {
    	echo 'jpp';
    }
}

?>