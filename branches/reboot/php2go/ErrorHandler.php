<?php

class ErrorHandler
{
	public $discardOutput = true;
	public $error = null;
	private static $errorTypes;

	public static function getErrorTrace($skip=0) {
		$result = '';
		$trace = debug_backtrace();
		$trace = array_slice($trace, $skip + 1);
		foreach ($trace as $i => $item) {
			if (!isset($item['file']))
				$item['file'] = 'unknown';
			if (!isset($item['line']))
				$item['line'] = 0;
			if (!isset($item['function']))
				$item['function'] = 'unknown';
			$result .= "#{$i} {$item['file']}({$item['line']}): ";
			if (isset($item['object']) && is_object($item['object']))
				$result .= get_class($item['object']) . "->";
			$result .= "{$item['function']}()\n";
		}
		return $result;
	}

	public function handle(Event $event) {
		if ($this->discardOutput)
			while(@ob_get_clean());
		if ($event instanceof ExceptionEvent) {
			$event->handled = true;
			$this->handleException($event);
		} elseif ($event instanceof ErrorEvent) {
			$event->handled = true;
			$this->handleError($event);
		}
	}

	protected function handleException(ExceptionEvent $event) {
		$app = Php2Go::app();
		$exception = $event->exception;
		if ($app instanceof WebApplication) {
			$trace = $exception->getTrace();
			for ($i=0, $j=null, $l=sizeof($trace); $i<$l; $i++) {
				if (isset($trace[$i]['file'])) {
					$j = $i;
					break;
				}
			}
			$this->error = $data = array(
				'code' => ($exception instanceof HttpException ? $exception->statusCode : 500),
				'type' => get_class($exception),
				'message' => $exception->getMessage(),
				'file' => ($j !== null ? $trace[$j]['file'] : null),
				'line' => ($j !== null ? $trace[$j]['line'] : null),
				'trace' => $exception->getTraceAsString(),
				'time' => $event->time
			);
			if ($exception instanceof HttpException || !PHP2GO_DEBUG_MODE)
				$this->render('error', $data);
			else
				$this->render('exception', $data);
		} else {
			$app->displayException($exception);
		}
	}

	protected function handleError(ErrorEvent $event) {
		$app = Php2Go::app();
		if ($app instanceof WebApplication) {
			$this->error = $data = array(
				'code' => 500,
				'type' => $this->getErrorType($event->code),
				'message' => $event->message,
				'file' => $event->file,
				'line' => $event->line,
				'trace' => ErrorHandler::getErrorTrace(3),
				'time' => $event->time
			);
			if (PHP2GO_DEBUG_MODE)
				$this->render('exception', $data);
			else
				$this->render('error', $data);
		} else {
			$app->displayError($event->code, $event->message, $event->file, $event->line);
		}
	}

	protected function render($view, $data) {
		ob_start();
		include $this->getView($view, $data['code']);
		$app = Php2Go::app();
		$app->getResponse()
				->setStatus(500)
				->setContentType('text/html; charset=' . $app->getCharset())
				->setBody(ob_get_clean())
				->sendResponse();
	}

	private function getView($view, $code) {
		$paths = array(
			Php2Go::app()->getSystemViewPath(),
			PHP2GO_PATH . DS . 'views'
		);
		$trials = ($view == 'error' ? array('%s%serror%s.php', '%s%serror.php') : array('%s%sexception.php'));
		foreach ($paths as $path) {
			for ($i=0; $i<sizeof($trials); $i++) {
				$args = array($path, DS);
				if (!$i)
					$args[] = $code;
				$file = vsprintf($trials[$i], $args);
				if (is_file($file))
					return $file;
			}
		}
		return null;
	}

	private function getErrorType($code) {
		if (!isset(self::$errorTypes)) {
			self::$errorTypes = array(
				1 => __(PHP2GO_LANG_DOMAIN, 'Fatal Error'),
				2 => __(PHP2GO_LANG_DOMAIN, 'Warning'),
				4 => __(PHP2GO_LANG_DOMAIN, 'Parse Error'),
				8 => __(PHP2GO_LANG_DOMAIN, 'Notice'),
				16 => __(PHP2GO_LANG_DOMAIN, 'Startup Error'),
				32 => __(PHP2GO_LANG_DOMAIN, 'Startup Warning'),
				64 => __(PHP2GO_LANG_DOMAIN, 'Compile Error'),
				128 => __(PHP2GO_LANG_DOMAIN, 'Compile Warning'),
				256 => __(PHP2GO_LANG_DOMAIN, 'Error'),
				512 => __(PHP2GO_LANG_DOMAIN, 'Warning'),
				1024 => __(PHP2GO_LANG_DOMAIN, 'Notice'),
				2048 => __(PHP2GO_LANG_DOMAIN, 'Strict Standards'),
				4096 => __(PHP2GO_LANG_DOMAIN, 'Error'),
				8192 => __(PHP2GO_LANG_DOMAIN, 'Notice'),
				16384 => __(PHP2GO_LANG_DOMAIN, 'Notice')
			);
		}
		return (isset(self::$errorTypes[$code]) ? self::$errorTypes[$code] : __(PHP2GO_LANG_DOMAIN, 'Error'));
	}
}