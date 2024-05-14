<?php
namespace app\helpers;

function redirect($response, $to) 
{
    return $response->withHeader('location', $to)->withStatus(302);
}