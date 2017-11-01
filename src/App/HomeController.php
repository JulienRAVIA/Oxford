<?php 

namespace App;
use App\View as View;
use App\Database as Database;

/**
 * Controlleur de la page d'accueil
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
 */
class HomeController
{
    /**
     * Affichage de la page d'accueil
     * @return twigView Vue de la page d'accueil
     */
    public function index()
    {
        View::make('index.twig');
    }
}

?>