<?php
/**
 * Locale: se
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4763',
	'language' => 'se',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'ođđajagemánnu',
				2 => 'guovvamánnu',
				3 => 'njukčamánnu',
				4 => 'cuoŋománnu',
				5 => 'miessemánnu',
				6 => 'geassemánnu',
				7 => 'suoidnemánnu',
				8 => 'borgemánnu',
				9 => 'čakčamánnu',
				10 => 'golggotmánnu',
				11 => 'skábmamánnu',
				12 => 'juovlamánnu'
			),
			'narrow' => array(
				1 => 'O',
				2 => 'G',
				3 => 'N',
				4 => 'C',
				5 => 'M',
				6 => 'G',
				7 => 'S',
				8 => 'B',
				9 => 'Č',
				10 => 'G',
				11 => 'S',
				12 => 'J'
			),
			'abbreviated' => array(
				1 => 'ođđj',
				2 => 'guov',
				3 => 'njuk',
				4 => 'cuo',
				5 => 'mies',
				6 => 'geas',
				7 => 'suoi',
				8 => 'borg',
				9 => 'čakč',
				10 => 'golg',
				11 => 'skáb',
				12 => 'juov'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'sotnabeaivi',
				1 => 'vuossárga',
				2 => 'maŋŋebárga',
				3 => 'gaskavahkku',
				4 => 'duorasdat',
				5 => 'bearjadat',
				6 => 'lávvardat'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'V',
				2 => 'M',
				3 => 'G',
				4 => 'D',
				5 => 'B',
				6 => 'L'
			),
			'abbreviated' => array(
				0 => 'sotn',
				1 => 'vuos',
				2 => 'maŋ',
				3 => 'gask',
				4 => 'duor',
				5 => 'bear',
				6 => 'láv'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Q1',
				2 => 'Q2',
				3 => 'Q3',
				4 => 'Q4'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'Q1',
				2 => 'Q2',
				3 => 'Q3',
				4 => 'Q4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'iđitbeaivi',
				'pm' => 'eahketbeaivi'
			),
			'abbreviated' => array(
				'am' => 'i.b.',
				'pm' => 'e.b.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'o.Kr.',
				1 => 'm.Kr.'
			),
			'wide' => array(
				0 => 'ovdal Kristtusa',
				1 => 'maŋŋel Kristtusa'
			),
			'narrow' => array(
				0 => 'o.Kr.',
				1 => 'm.Kr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, y MMMM dd',
			'long' => 'y MMMM d',
			'medium' => 'y MMM d',
			'short' => 'yyyy-MM-dd'
		),
		'timeFormats' => array(
			'full' => 'HH:mm:ss zzzz',
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
				'name' => 'éra'
			),
			'year' => array(
				'name' => 'jáhki'
			),
			'month' => array(
				'name' => 'mánnu'
			),
			'week' => array(
				'name' => 'váhkku'
			),
			'day' => array(
				'name' => 'beaivi',
				'relative' => array(
					-2 => 'oovdebpeivvi',
					-1 => 'ikte',
					0 => 'odne',
					1 => 'ihttin',
					2 => 'paijeelittáá'
				)
			),
			'weekday' => array(
				'name' => 'váhkkubeaivi'
			),
			'dayperiod' => array(
				'name' => 'beaivi ráidodássi'
			),
			'hour' => array(
				'name' => 'diibmu'
			),
			'minute' => array(
				'name' => 'minuhtta'
			),
			'second' => array(
				'name' => 'sekunda'
			),
			'zone' => array(
				'name' => 'áigeavádat'
			)
		)
	),
	'numbers' => array(
		'defaultNumberingSystem' => 'latn',
		'symbols' => array(
			'decimal' => '.',
			'group' => ',',
			'list' => ';',
			'percentSign' => '%',
			'nativeZeroDigit' => '0',
			'patternDigit' => '#',
			'plusSign' => '+',
			'minusSign' => '-',
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
			'AFN' => 'Af',
			'ANG' => 'NAf.',
			'AOA' => 'Kz',
			'ARA' => '₳',
			'ARL' => '$L',
			'ARM' => 'm$n',
			'ARS' => 'AR$',
			'AUD' => 'AU$',
			'AWG' => 'Afl.',
			'AZN' => 'man.',
			'BAM' => 'KM',
			'BBD' => 'Bds$',
			'BDT' => 'Tk',
			'BEF' => 'BF',
			'BHD' => 'BD',
			'BIF' => 'FBu',
			'BMD' => 'BD$',
			'BND' => 'BN$',
			'BOB' => 'Bs',
			'BOP' => '$b.',
			'BRL' => 'R$',
			'BSD' => 'BS$',
			'BTN' => 'Nu.',
			'BWP' => 'BWP',
			'BZD' => 'BZ$',
			'CAD' => 'CA$',
			'CDF' => 'CDF',
			'CLE' => 'Eº',
			'CLP' => 'CL$',
			'CNY' => 'CN¥',
			'COP' => 'CO$',
			'CRC' => '₡',
			'CUC' => 'CUC$',
			'CUP' => 'CU$',
			'CVE' => 'CV$',
			'CYP' => 'CY£',
			'CZK' => 'Kč',
			'DEM' => 'DM',
			'DJF' => 'Fdj',
			'DKK' => 'Dkr',
			'DOP' => 'RD$',
			'DZD' => 'DA',
			'EEK' => 'Ekr',
			'EGP' => 'EG£',
			'ERN' => 'Nfk',
			'ESP' => 'Pts',
			'ETB' => 'Br',
			'EUR' => '€',
			'FIM' => 'mk',
			'FJD' => 'FJ$',
			'FKP' => 'FK£',
			'FRF' => '₣',
			'GBP' => '£',
			'GHC' => '₵',
			'GHS' => 'GH₵',
			'GIP' => 'GI£',
			'GMD' => 'GMD',
			'GNF' => 'FG',
			'GRD' => '₯',
			'GTQ' => 'GTQ',
			'GYD' => 'GY$',
			'HKD' => 'HK$',
			'HNL' => 'HNL',
			'HRK' => 'kn',
			'HTG' => 'HTG',
			'HUF' => 'Ft',
			'IDR' => 'Rp',
			'IEP' => 'IR£',
			'ILP' => 'I£',
			'ILS' => '₪',
			'INR' => 'Rs',
			'ISK' => 'Ikr',
			'ITL' => 'IT₤',
			'JMD' => 'J$',
			'JOD' => 'JD',
			'JPY' => 'JP¥',
			'KES' => 'Ksh',
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
			'NOK' => 'Nkr',
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
			'PYG' => '₲',
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
			'UAH' => '₴',
			'UGX' => 'USh',
			'USD' => 'US$',
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
			'ZWD' => 'Z$'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} jándor'
			),
			'other' => array(
				'normal' => '{0} jándora'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} diibmu'
			),
			'other' => array(
				'normal' => '{0} diimmu'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minuhta'
			),
			'other' => array(
				'normal' => '{0} minuhtta'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mánnu'
			),
			'other' => array(
				'normal' => '{0} mánotbaji'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} sekunda'
			),
			'other' => array(
				'normal' => '{0} sekundda'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} váhkku'
			),
			'other' => array(
				'normal' => '{0} váhkku'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} jahki'
			),
			'other' => array(
				'normal' => '{0} jagi'
			)
		)
	),
	'messages' => array(
		'yes' => 'jo',
		'no' => 'ii'
	)
);