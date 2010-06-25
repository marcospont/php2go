<?php

Php2Go::import('php2go.auth.adapter.*');

abstract class AuthenticatorAdapter extends Component
{
	private static $adapters = array(
		Authenticator::ADAPTER_DB
	);

	public static function factory($options) {
		if (is_array($options)) {
			$type = Util::consumeArray($options, 'type', Authenticator::ADAPTER_DB);
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid authenticator adapter configuration.'));
		}
		$config = array('options' => $options);
		if (in_array($type, self::$adapters)) {
			$config['class'] = 'AuthenticatorAdapter' . ucfirst($type);
		} else {
			$config['class'] = $type;
			$config['parent'] = 'AuthenticatorAdapter';
		}
		return Php2Go::newInstance($config);
	}
}