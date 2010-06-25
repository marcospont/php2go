<?php
/**
 * Locale: nn_NO
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'nn',
	'territory' => 'NO',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'januar',
				2 => 'februar',
				3 => 'mars',
				4 => 'april',
				5 => 'mai',
				6 => 'juni',
				7 => 'juli',
				8 => 'august',
				9 => 'september',
				10 => 'oktober',
				11 => 'november',
				12 => 'desember'
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
				5 => 'mai',
				6 => 'jun',
				7 => 'jul',
				8 => 'aug',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'des'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'søndag',
				1 => 'måndag',
				2 => 'tysdag',
				3 => 'onsdag',
				4 => 'torsdag',
				5 => 'fredag',
				6 => 'laurdag'
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
				0 => 'søn',
				1 => 'mån',
				2 => 'tys',
				3 => 'ons',
				4 => 'tor',
				5 => 'fre',
				6 => 'lau'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. kvartal',
				2 => '2. kvartal',
				3 => '3. kvartal',
				4 => '4. kvartal'
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
				'am' => 'på formiddagen',
				'pm' => 'på ettermiddagen'
			),
			'abbreviated' => array(
				'am' => 'f.m.',
				'pm' => 'e.m.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'f.Kr.',
				1 => 'e.Kr.'
			),
			'wide' => array(
				0 => 'før Kristus',
				1 => 'etter Kristus'
			),
			'narrow' => array(
				0 => 'f.Kr.',
				1 => 'e.Kr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'd. MMM. y',
			'short' => 'dd.MM.yy'
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
				'name' => 'æra'
			),
			'year' => array(
				'name' => 'år'
			),
			'month' => array(
				'name' => 'månad'
			),
			'week' => array(
				'name' => 'veke'
			),
			'day' => array(
				'name' => 'dag',
				'relative' => array(
					-3 => 'i forforgårs',
					-2 => 'i forgårs',
					-1 => 'i går',
					0 => 'i dag',
					1 => 'i morgon',
					2 => 'i overmorgon',
					3 => 'i overovermorgon'
				)
			),
			'weekday' => array(
				'name' => 'vekedag'
			),
			'dayperiod' => array(
				'name' => 'f.m./e.m.-val'
			),
			'hour' => array(
				'name' => 'time'
			),
			'minute' => array(
				'name' => 'minutt'
			),
			'second' => array(
				'name' => 'sekund'
			),
			'zone' => array(
				'name' => 'sone'
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
			'exponential' => 'E',
			'perMille' => '‰',
			'infinity' => '∞',
			'nan' => 'NaN'
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
			'CNY' => 'CN¥',
			'COP' => 'COP',
			'CRC' => 'CRC',
			'CUC' => 'CUC',
			'CUP' => 'CUP',
			'CVE' => 'CVE',
			'CYP' => 'CYP',
			'CZK' => 'CZK',
			'DEM' => 'DEM',
			'DJF' => 'DJF',
			'DKK' => 'Dkr',
			'DOP' => 'DOP',
			'DZD' => 'DZD',
			'EEK' => 'Ekr',
			'EGP' => 'EG£',
			'ERN' => 'ERN',
			'ESP' => 'ESP',
			'ETB' => 'ETB',
			'EUR' => 'EUR',
			'FIM' => 'FIM',
			'FJD' => 'FJD',
			'FKP' => 'FKP',
			'FRF' => 'FRF',
			'GBP' => 'GBP',
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
			'ILS' => '₪',
			'INR' => 'INR',
			'ISK' => 'Ikr',
			'ITL' => 'IT₤',
			'JMD' => 'J$',
			'JOD' => 'JD',
			'JPY' => 'JPY',
			'KES' => 'KES',
			'KMF' => 'CF',
			'KRW' => '₩',
			'KWD' => 'KD',
			'KYD' => 'KY$',
			'LAK' => '₭',
			'LBP' => 'LB£',
			'LKR' => 'SLRs',
			'LRD' => 'L$',
			'LSL' => 'LSL',
			'LTL' => 'Lt',
			'LVL' => 'Ls',
			'LYD' => 'LD',
			'MMK' => 'MMK',
			'MNT' => '₮',
			'MOP' => 'MOP$',
			'MRO' => 'UM',
			'MTL' => 'Lm',
			'MTP' => 'MT£',
			'MUR' => 'MURs',
			'MXN' => 'MX$',
			'MYR' => 'RM',
			'MZM' => 'Mt',
			'MZN' => 'MTn',
			'NAD' => 'N$',
			'NGN' => '₦',
			'NIO' => 'C$',
			'NLG' => 'fl',
			'NOK' => 'kr',
			'NPR' => 'NPRs',
			'NZD' => 'NZ$',
			'PAB' => 'B/.',
			'PEI' => 'I/.',
			'PEN' => 'S/.',
			'PGK' => 'PGK',
			'PHP' => '₱',
			'PKR' => 'PKRs',
			'PLN' => 'zł',
			'PTE' => 'Esc',
			'PYG' => 'PYG',
			'QAR' => 'QR',
			'RHD' => 'RH$',
			'RON' => 'RON',
			'RSD' => 'din.',
			'SAR' => 'SR',
			'SBD' => 'SI$',
			'SCR' => 'SRe',
			'SDD' => 'LSd',
			'SEK' => 'Skr',
			'SGD' => 'S$',
			'SHP' => 'SH£',
			'SKK' => 'Sk',
			'SLL' => 'Le',
			'SOS' => 'Ssh',
			'SRD' => 'SR$',
			'SRG' => 'Sf',
			'STD' => 'Db',
			'SVC' => 'SV₡',
			'SYP' => 'SY£',
			'SZL' => 'SZL',
			'THB' => '฿',
			'TMM' => 'TMM',
			'TND' => 'DT',
			'TOP' => 'T$',
			'TRL' => 'TRL',
			'TRY' => 'TL',
			'TTD' => 'TT$',
			'TWD' => 'NT$',
			'TZS' => 'TSh',
			'UAH' => 'UAH',
			'UGX' => 'USh',
			'USD' => 'USD',
			'UYU' => '$U',
			'VEF' => 'Bs.F.',
			'VND' => '₫',
			'VUV' => 'VT',
			'WST' => 'WS$',
			'XAF' => 'FCFA',
			'XCD' => 'EC$',
			'XOF' => 'CFA',
			'XPF' => 'CFPF',
			'YER' => 'YR',
			'ZAR' => 'R',
			'ZMK' => 'ZK',
			'ZRN' => 'NZ',
			'ZRZ' => 'ZRZ',
			'ZWD' => 'Z$',
			'BRB' => 'BRB',
			'BRC' => 'BRC',
			'BRE' => 'BRE',
			'BRN' => 'BRN',
			'BRR' => 'BRR',
			'BRZ' => 'BRZ',
			'CHF' => 'CHF'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} døgn'
			),
			'other' => array(
				'normal' => '{0} døgn'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} time'
			),
			'other' => array(
				'normal' => '{0} timar'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minutt'
			),
			'other' => array(
				'normal' => '{0} minuttar'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} månad'
			),
			'other' => array(
				'normal' => '{0} månadar'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} sekund'
			),
			'other' => array(
				'normal' => '{0} sekundar'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} veke'
			),
			'other' => array(
				'normal' => '{0} veker'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} år'
			),
			'other' => array(
				'normal' => '{0} år'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja:j',
		'no' => 'nei:n'
	)
);