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
        View::make('index.twig');
    }
}

?>