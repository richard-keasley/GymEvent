<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('x/(:segment)', 'Links::view/$1');
$routes->get('about/(:segment)', 'About::view/$1');

$routes->get('sw.js', 'Home::js/sw');
$routes->get('pwa', 'Home::pwa/0');
$routes->get('pwa/(:segment)', 'Home::pwa/$1');
