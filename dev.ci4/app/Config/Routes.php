<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('x/(:segment)', 'Links::view/$1');

$routes->get('about/(:segment)', 'About::view/$1');

