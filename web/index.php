<?php
/**
 * @author      Adrian Zurek <a.zurek@imerge.pl>
 * @copyright   Copyright (c) 2013 Imerge (http://www.imerge.pl)
 */

require_once '../vendor/autoload.php';
require_once '../FueroController.php';

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$request->overrideGlobals();

$controller = new \Nuvo\FueroController();

$response = $controller->handle($request);
$response->prepare($request);
$response->send();