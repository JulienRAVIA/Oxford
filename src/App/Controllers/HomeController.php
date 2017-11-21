<?php 

namespace App\Controllers;
use App\View;
use App\Database;

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
        if(\App\Utils\Session::get('id')) {
        	View::make('index.twig');
        } else {
        	View::redirect('/connexion');
        }
    }
}