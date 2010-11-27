<?php

include 'bootstrap.php';

echo '<pre>';
$contents = file_get_contents(PHP2GO_PATH . '/library/jquery/jquery-ui/jquery-ui-i18n-datepicker.js', FILE_TEXT);
$locales = array(
		'af', 'ar', 'az', 'bg', 'bs', 'ca', 'cs', 'da', 'de_CH',
		'de', 'el', 'en_GB', 'eo', 'es', 'et', 'eu', 'fa',
		'fi', 'fo', 'fr_CH', 'fr', 'gl', 'he', 'hr', 'hu',
		'hy', 'id', 'is', 'it', 'ja', 'kk', 'ko', 'lt', 'lv',
		'ms', 'nl_BE', 'nl', 'no', 'pl', 'pt_BR', 'pt', 'ro',
		'ru', 'sk', 'sl', 'sq', 'sr_SR', 'sr', 'sv', 'ta',
		'th', 'tr', 'uk', 'vi', 'zh_CN', 'zh_HK', 'zh_TW'
);
foreach ($locales as $locale) {
	$locale = new Locale($locale);
	preg_match('/\[\'' . str_replace('_', '-', $locale) . '\'\]\s*=\s*{([^}]+)}/', $contents, $m1);
	preg_match('/dateFormat\s*\:\s*\'([^\']+)\'/', $m1[1], $m2);
	var_dump('==============' . $locale . '==============');
	var_dump($m2[1]);
	var_dump($locale->getDateInputFormat());
	var_dump('medium - ' . $locale->getDateFormat('medium'));
	var_dump('short - ' . $locale->getDateFormat('short'));
	echo "\n\n\n";
}
echo '</pre>';