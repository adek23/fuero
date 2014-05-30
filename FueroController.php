<?php
/**
 * @author      Adrian Zurek <a.zurek@imerge.pl>
 * @copyright   Copyright (c) 2013 Imerge (http://www.imerge.pl)
 */

namespace Nuvo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FueroController {
	protected function render($template, $params) {
		$loader = new \Twig_Loader_Filesystem('../templates');
		$twig = new \Twig_Environment($loader);

		return new Response($twig->render($template, $params));
	}

	protected function getDb() {
		$db = new \PDO('sqlite:../test.db');
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$db->exec("CREATE TABLE IF NOT EXISTS urls (id INTEGER PRIMARY KEY, url TEXT, createdAt TEXT)");

		return $db;
	}

	public function handle(Request $request) {
		$data = array('error' => '', 'json' => '');

		if ($request->getMethod() == 'POST') {
			$url = $request->request->get('url');

			if (!preg_match('#^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$#', $url)) {
				$data['error'] = 'Niewłaściwy adres url';
			} else {
				$db = $this->getDb();
				$stmt = $db->prepare("INSERT INTO urls(url, createdAt) VALUES(:url, datetime('now'))");
				$stmt->bindParam(':url', $url);
				$stmt->execute();

				$json = file_get_contents($url);
				$data['json'] = print_r(json_decode($json, true), true);
			}
		}

		return $this->render('index.html.twig', $data);
	}
} 