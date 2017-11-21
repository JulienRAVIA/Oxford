<?php

namespace App\Utils;

/**
 * Utilitaire pour les cookies
 */
class Cookie {
    
    /**
     * Création d'une variable de session
     * @param string $key   Clé de la variable de session
     * @param string $value Valeur de la variable de session
     */
    public static function set($key, $value, $time = '') {
        if(empty($time)) {
            $time = time() + 3600 * 24 * 7;
        }
        setcookie($key, $value, $time);
    }
    
    /**
     * Récupération d'une variable de cookie à partir de sa clé
     * @param  string $key Clé du cookie à récupérer
     * @return string      Valeur du cookie
     */
    public static function get($key) {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        } else {
            return null;
        }
    }
    
    /**
     * Destruction d'un cookie utilisateur
     * @param  string $key Clé de la variable de cookie à détruire
     */
    public static function destroy($key) {
        if(isset($key)) { 
            /* si on ne renseigne pas de variable particulière à detruire 
            on détruit toute la session */
            setcookie($key, "", time() - 3600);
            unset($_COOKIE[$key]);
        }
    }

    /**
     * Fonction de vérification que la clé pâssée en paramètre ait pour valeur
     * la valeur attendue, si ce n'est pas le cas on redirige vers la route
     * passée en paramètre
     * @param  string $key   Clé du tableau de session à verifier
     * @param  string $value Valeur attendue
     * @param  string $route Route vers lequel rediriger l'utilisateur
     * @return view          Redirection si la clé attendue n'est pas correctement renseignée
     */
    public static function check($key, $value, $route = '/') {
        if(self::get($key) != $value or !isset($_COOKIE[$key])) {
            \App\View::redirect($route);
        }
    }
}