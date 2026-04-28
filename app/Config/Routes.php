<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('home', 'Home::index');
$routes->get('/', 'Login::showLogin');
$routes->post('/login-check', 'Login::doLogin');
$routes->get('/logout', 'Login::logout');
$routes->get('/register', 'Register::showRegister');
$routes->post('/register-check', 'Register::doRegister');
