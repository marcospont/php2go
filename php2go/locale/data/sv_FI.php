<?php
/**
 * Locale: sv_FI
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4599',
	'language' => 'sv',
	'territory' => 'FI',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'januari',
				2 => 'februari',
				3 => 'mars',
				4 => 'april',
				5 => 'maj',
				6 => 'juni',
				7 => 'juli',
				8 => 'augusti',
				9 => 'september',
				10 => 'oktober',
				11 => 'november',
				12 => 'december'
			),
			'narrow' => array(
				1 => 'J',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'M',
				6 => 'J',
				7 => 'J',
				8 => 'A',
				9 => 'S',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'jan',
				2 => 'feb',
				3 => 'mar',
				4 => 'apr',
				5 => 'maj',
				6 => 'jun',
				7 => 'jul',
				8 => 'aug',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'dec'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'söndag',
				1 => 'måndag',
				2 => 'tisdag',
				3 => 'onsdag',
				4 => 'torsdag',
				5 => 'fredag',
				6 => 'lördag'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'M',
				2 => 'T',
				3 => 'O',
				4 => 'T',
				5 => 'F',
				6 => 'L'
			),
			'abbreviated' => array(
				0 => 'sön',
				1 => 'mån',
				2 => 'tis',
				3 => 'ons',
				4 => 'tors',
				5 => 'fre',
				6 => 'lör'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1:a kvartalet',
				2 => '2:a kvartalet',
				3 => '3:e kvartalet',
				4 => '4:e kvartalet'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'K1',
				2 => 'K2',
				3 => 'K3',
				4 => 'K4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'förmiddag',
				'pm' => 'eftermiddag'
			),
			'abbreviated' => array(
				'am' => 'f.m.',
				'pm' => 'e.m.'
			),
			'narrow' => array(
				'am' => 'f.m.',
				'pm' => 'e.m.'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'f.Kr.',
				1 => 'e.Kr.'
			),
			'wide' => array(
				0 => 'före Kristus',
				1 => 'efter Kristus'
			),
			'narrow' => array(
				0 => 'f.Kr.',
				1 => 'e.Kr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE\'en\' \'den\' d:\'e\' MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM y',
			'short' => 'yyyy-MM-dd'
		),
		'timeFormats' => array(
			'full' => '\'kl\'. HH:mm:ss zzzz',
			'long' => 'HH:mm:ss z',
			'medium' => 'HH:mm:ss',
			'short' => 'HH:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'era'
			),
			'year' => array(
				'name' => 'år'
			),
			'month' => array(
				'name' => 'månad'
			),
			'week' => array(
				'name' => 'vecka'
			),
			'day' => array(
				'name' => 'dag',
				'relative' => array(
					-3 => 'i förrförrgår',
					-2 => 'i förrgår',
					-1 => 'i går',
					0 => 'i dag',
					1 => 'i morgon',
					2 => 'i övermorgon',
					3 => 'i överövermorgon'
				)
			),
			'weekday' => array(
				'name' => 'veckodag'
			),
			'dayperiod' => array(
				'name' => 'dagsperiod'
			),
			'hour' => array(
				'name' => 'timme'
			),
			'minute' => array(
				'name' => 'minut'
			),
			'second' => array(
				'name' => 'sekund'
			),
			'zone' => array(
				'name' => 'tidszon'
			)
		)
	),
	'numbers' => array(
		'defaultNumberingSystem' => 'latn',
		'symbols' => array(
			'decimal' => ',',
			'group' => ' ',
			'list' => ';',
			'percentSign' => '%',
			'nativeZeroDigit' => '0',
			'patternDigit' => '#',
			'plusSign' => '+',
			'minusSign' => '−',
			'exponential' => '×10^',
			'perMille' => '‰',
			'infinity' => '∞',
			'nan' => '¤¤¤'
		),
		'decimalFormat' => '#,##0.###',
		'scientificFormat' => '#E0',
		'percentFormat' => '#,##0 %',
		'currencyFormat' => '#,##0.00 ¤',
		'currencies' => array(
			'AFN' => 'AFN',
			'ANG' => 'ANG',
			'AOA' => 'AOA',
			'ARA' => 'ARA',
			'ARL' => 'ARL',
			'ARM' => 'ARM',
			'ARS' => 'ARS',
			'AUD' => 'AUD',
			'AWG' => 'AWG',
			'AZN' => 'AZN',
			'BAM' => 'BAM',
			'BBD' => 'BBD',
			'BDT' => 'BDT',
			'BEF' => 'BEF',
			'BHD' => 'BHD',
			'BIF' => 'BIF',
			'BMD' => 'BMD',
			'BND' => 'BND',
			'BOB' => 'BOB',
			'BOP' => 'BOP',
			'BRL' => 'BRL',
			'BSD' => 'BSD',
			'BTN' => 'BTN',
			'BWP' => 'BWP',
			'BZD' => 'BZD',
			'CAD' => 'CAD',
			'CDF' => 'CDF',
			'CLE' => 'CLE',
			'CLP' => 'CLP',
			'CNY' => 'CNY',
			'COP' => 'COP',
			'CRC' => 'CRC',
			'CUC' => 'CUC',
			'CUP' => 'CUP',
			'CVE' => 'CVE',
			'CYP' => 'CYP',
			'CZK' => 'CZK',
			'DEM' => 'DM',
			'DJF' => 'DJF',
			'DKK' => 'DKK',
			'DOP' => 'DOP',
			'DZD' => 'DZD',
			'EEK' => 'Ekr',
			'EGP' => 'EGP',
			'ERN' => 'ERN',
			'ESP' => 'ESP',
			'ETB' => 'ETB',
			'EUR' => '€',
			'FIM' => 'FIM',
			'FJD' => 'FJD',
			'FKP' => 'FKP',
			'FRF' => 'FRF',
			'GBP' => '£',
			'GHC' => 'GHC',
			'GHS' => 'GHS',
			'GIP' => 'GIP',
			'GMD' => 'GMD',
			'GNF' => 'GNF',
			'GRD' => 'GRD',
			'GTQ' => 'GTQ',
			'GYD' => 'GYD',
			'HKD' => 'HK$',
			'HNL' => 'HNL',
			'HRK' => 'HRK',
			'HTG' => 'HTG',
			'HUF' => 'HUF',
			'IDR' => 'IDR',
			'IEP' => 'IEP',
			'ILP' => 'ILP',
			'ILS' => 'ILS',
			'INR' => 'INR',
			'ISK' => 'ISK',
			'ITL' => 'ITL',
			'JMD' => 'JMD',
			'JOD' => 'JOD',
			'JPY' => 'JP¥',
			'KES' => 'KES',
			'KMF' => 'KMF',
			'KRW' => 'KRW',
			'KWD' => 'KWD',
			'KYD' => 'KYD',
			'LAK' => 'LAK',
			'LBP' => 'LBP',
			'LKR' => 'LKR',
			'LRD' => 'LRD',
			'LSL' => 'LSL',
			'LTL' => 'LTL',
			'LVL' => 'LVL',
			'LYD' => 'LYD',
			'MMK' => 'MMK',
			'MNT' => 'MNT',
			'MOP' => 'MOP',
			'MRO' => 'MRO',
			'MTL' => 'MTL',
			'MTP' => 'MTP',
			'MUR' => 'MUR',
			'MXN' => 'MX$',
			'MYR' => 'MYR',
			'MZM' => 'MZM',
			'MZN' => 'MZN',
			'NAD' => 'NAD',
			'NGN' => 'NGN',
			'NIO' => 'NIO',
			'NLG' => 'NLG',
			'NOK' => 'NKr',
			'NPR' => 'NPR',
			'NZD' => 'NZD',
			'PAB' => 'PAB',
			'PEI' => 'PEI',
			'PEN' => 'PEN',
			'PGK' => 'PGK',
			'PHP' => 'PHP',
			'PKR' => 'PKR',
			'PLN' => 'PLN',
			'PTE' => 'PTE',
			'PYG' => 'PYG',
			'QAR' => 'QAR',
			'RHD' => 'RHD',
			'RON' => 'RON',
			'RSD' => 'RSD',
			'SAR' => 'SAR',
			'SBD' => 'SBD',
			'SCR' => 'SCR',
			'SDD' => 'SDD',
			'SEK' => 'kr',
			'SGD' => 'SGD',
			'SHP' => 'SHP',
			'SKK' => 'SKK',
			'SLL' => 'SLL',
			'SOS' => 'SOS',
			'SRD' => 'SRD',
			'SRG' => 'SRG',
			'STD' => 'STD',
			'SVC' => 'SVC',
			'SYP' => 'SYP',
			'SZL' => 'SZL',
			'THB' => '฿',
			'TMM' => 'TMM',
			'TND' => 'TND',
			'TOP' => 'TOP',
			'TRL' => 'TRL',
			'TRY' => 'TRY',
			'TTD' => 'TTD',
			'TWD' => 'TWD',
			'TZS' => 'TZS',
			'UAH' => 'UAH',
			'UGX' => 'UGX',
			'USD' => 'US$',
			'UYU' => 'UYU',
			'VEF' => 'VEF',
			'VND' => 'VND',
			'VUV' => 'VUV',
			'WST' => 'WST',
			'XAF' => 'XAF',
			'XCD' => 'XCD',
			'XOF' => 'CFA',
			'XPF' => 'XPF',
			'YER' => 'YER',
			'ZAR' => 'ZAR',
			'ZMK' => 'ZMK',
			'ZRN' => 'ZRN',
			'ZRZ' => 'ZRZ',
			'ZWD' => 'ZWD',
			'BAD' => 'BAD',
			'BRB' => 'BRB',
			'BRC' => 'BRC',
			'BRE' => 'BRE',
			'BRN' => 'BRN',
			'BRR' => 'BRR',
			'BRZ' => 'BRZ',
			'CHF' => 'CHF',
			'MXP' => 'MXP',
			'VEB' => 'VEB'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} d',
				'short' => '{0} dag'
			),
			'other' => array(
				'normal' => '{0} d',
				'short' => '{0} dagar'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} h',
				'short' => '{0} tim.'
			),
			'other' => array(
				'normal' => '{0} h',
				'short' => '{0} tim.'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} min',
				'short' => '{0} min.'
			),
			'other' => array(
				'normal' => '{0} min',
				'short' => '{0} min.'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mån',
				'short' => '{0} mån.'
			),
			'other' => array(
				'normal' => '{0} mån',
				'short' => '{0} mån.'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} s',
				'short' => '{0} sek.'
			),
			'other' => array(
				'normal' => '{0} s',
				'short' => '{0} sek.'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} v',
				'short' => '{0} v.'
			),
			'other' => array(
				'normal' => '{0} v',
				'short' => '{0} v.'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} år',
				'short' => '{0} år'
			),
			'other' => array(
				'normal' => '{0} år',
				'short' => '{0} år'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja:j',
		'no' => 'nej:n'
	)
);