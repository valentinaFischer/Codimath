<?php
namespace app\classes;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TwigFilters extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('message', [$this, 'showMessage'])
        ];
    }

    public function showMessage($message, $alert)
    {
        if (is_string($message) && !empty($message))
        {
            return "<br><span class='alert alert-{$alert}'>{$message}</span><br>";
        }
    }
}