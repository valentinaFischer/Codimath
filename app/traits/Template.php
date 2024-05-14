<?php
namespace app\traits;

use Exception;
use Slim\Views\Twig;
use app\classes\TwigGlobal;
use app\classes\TwigFilters;

trait Template
{
    public function getTwig(){
        try {
            $twig = Twig::create(DIR_VIEWS); //, ['cache' => 'path/to/cache']);
            $twig->addExtension(new TwigFilters);
            TwigGlobal::load($twig);
            return $twig;
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function setView($name) {
        return $name . EXT_VIEWS;
    }
}