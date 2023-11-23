<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// obsolete... remove this after 28-Nov-2023
$routes->get('follow', 'Links::view/follow');

$routes->get('x/(:segment)', 'Links::view/$1');
