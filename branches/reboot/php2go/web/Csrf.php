<?php

abstract class Csrf
{
	const TOKEN_NAME = '__csrfToken';

	public static function getTokenName() {
		return self::TOKEN_NAME;
	}

	public static function getToken() {
		$session = Php2Go::app()->getSession();
		if (!$session->contains(self::TOKEN_NAME))
			$session->set(self::TOKEN_NAME, self::generateToken());
		return $session->get(self::TOKEN_NAME);
	}

	public static function validate() {
		$request = Php2Go::app()->getRequest();
		if ($request->isPost()) {
			$current = self::getToken();
			$provided = $request->getPost(self::TOKEN_NAME);
			if ($provided === null || $provided !== $current)
				throw new HttpException(403, __(PHP2GO_LANG_DOMAIN, 'The CSRF token could not be verified.'));
		}
	}

	private static function generateToken() {
		return sha1(uniqid(rand(),true));
	}
}