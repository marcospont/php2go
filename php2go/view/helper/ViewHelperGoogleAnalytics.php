<?php

class ViewHelperGoogleAnalytics extends ViewHelper
{
	protected $profiles = array();

	public function setProfiles(array $profiles) {
		$this->profiles = $profiles;
	}

	public function googleAnalytics() {
		if (!empty($this->profiles)) {
			$buf = array();
			$buf[] = 'var _gaq = _gaq || [];';
			$i = 0;
			foreach ($this->profiles as $id => $options) {
				++$i;
				// tracker instance name
				$inst = Util::consumeArray($options, 'jsId');
				if ($inst !== null)
					$inst .= '.';
				elseif (sizeof($this->profiles) > 1)
					$inst = 'gaTracker' . $i . '.';
				else
					$inst = '';
				$buf[] = '_gaq.push(["' . $inst . '_setAccount", "' . $id . '"]);';
				foreach ($options as $method => $value) {
					if (is_numeric($method)) {
						$method = $value;
						unset($value);
					}
					if ($method == 'setAccount' || $method == 'trackPageview')
						continue;
					if (isset($value)) {
						if (!is_array($value))
							$value = array($value);
						foreach ($value as $item) {
							if (is_array($item)) {
								foreach ($item as &$entry)
									$entry = Json::encode(utf8_encode($entry));
								$buf[] = '_gaq.push(["' . $inst . '_' . $method . '", ' . join(',', $item) . ']);';
							} else {
								$buf[] = '_gaq.push(["' . $inst . '_' . $method . '", ' . Json::encode(utf8_encode($item)) . ']);';
							}
						}
					} else {
						$buf[] = '_gaq.push(["' . $inst . '_' . $method . '"]);';
					}
				}
				$buf[] = '_gaq.push(["' . $inst . '_trackPageview"]);';
			}
			$buf[] = '(function() {';
			$buf[] = 'var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;';
			$buf[] = 'ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";';
			$buf[] = 'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);';
			$buf[] = '})();';
			$this->view->scriptBuffer()->add(join("\n", $buf));
		}
	}
}