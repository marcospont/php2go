<?php
/**
 * Locale: sk_SK
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'sk',
	'territory' => 'SK',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'január',
				2 => 'február',
				3 => 'marec',
				4 => 'apríl',
				5 => 'máj',
				6 => 'jún',
				7 => 'júl',
				8 => 'august',
				9 => 'september',
				10 => 'október',
				11 => 'november',
				12 => 'december'
			),
			'narrow' => array(
				1 => 'j',
				2 => 'f',
				3 => 'm',
				4 => 'a',
				5 => 'm',
				6 => 'j',
				7 => 'j',
				8 => 'a',
				9 => 's',
				10 => 'o',
				11 => 'n',
				12 => 'd'
			),
			'abbreviated' => array(
				1 => 'jan',
				2 => 'feb',
				3 => 'mar',
				4 => 'apr',
				5 => 'máj',
				6 => 'jún',
				7 => 'júl',
				8 => 'aug',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'dec'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'nedeľa',
				1 => 'pondelok',
				2 => 'utorok',
				3 => 'streda',
				4 => 'štvrtok',
				5 => 'piatok',
				6 => 'sobota'
			),
			'narrow' => array(
				0 => 'N',
				1 => 'P',
				2 => 'U',
				3 => 'S',
				4 => 'Š',
				5 => 'P',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'ne',
				1 => 'po',
				2 => 'ut',
				3 => 'st',
				4 => 'št',
				5 => 'pi',
				6 => 'so'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. štvrťrok',
				2 => '2. štvrťrok',
				3 => '3. štvrťrok',
				4 => '4. štvrťrok'
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
				'am' => 'dopoludnia',
				'pm' => 'popoludní'
			),
			'abbreviated' => array(
				'am' => 'AM',
				'pm' => 'PM'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'pred n.l.',
				1 => 'n.l.'
			),
			'wide' => array(
				0 => 'pred n.l.',
				1 => 'n.l.'
			),
			'narrow' => array(
				0 => 'pred n.l.',
				1 => 'n.l.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'd.M.yyyy',
			'short' => 'd.M.yyyy'
		),
		'timeFormats' => array(
			'full' => 'H:mm:ss zzzz',
			'long' => 'H:mm:ss z',
			'medium' => 'H:mm:ss',
			'short' => 'H:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'Éra'
			),
			'year' => array(
				'name' => 'Rok'
			),
			'month' => array(
				'name' => 'Mesiac'
			),
			'week' => array(
				'name' => 'Týždeň'
			),
			'day' => array(
				'name' => 'Deň',
				'relative' => array(
					-3 => 'Pred tromi dňami',
					-2 => 'Predvčerom',
					-1 => 'Včera',
					0 => 'Dnes',
					1 => 'Zajtra',
					2 => 'Pozajtra',
					3 => 'O tri dni'
				)
			),
			'weekday' => array(
				'name' => 'Deň v týždni'
			),
			'dayperiod' => array(
				'name' => 'Časť dňa'
			),
			'hour' => array(
				'name' => 'Hodina'
			),
			'minute' => array(
				'name' => 'Minúta'
			),
			'second' => array(
				'name' => 'Sekunda'
			),
			'zone' => array(
				'name' => 'Pásmo'
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
			'minusSign' => '-',
			'exponential' => 'E',
			'perMille' => '‰',
			'infinity' => '∞',
			'nan' => 'NaN'
		),
		'decimalFormat' => '#,##0.###',
		'scientificFormat' => '#E0',
		'percentFormat' => '#,##0%',
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
			'other' => array(
				'normal' => '{0} dní',
				'short' => '{0} d.'
			),
			'one' => array(
				'normal' => '{0} deň',
				'short' => '{0} d.'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} hodín',
				'short' => '{0} h'
			),
			'one' => array(
				'normal' => '{0} hodina',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} minút',
				'short' => '{0} min'
			),
			'one' => array(
				'normal' => '{0} minúta',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} mesiacov',
				'short' => '{0} mes.'
			),
			'one' => array(
				'normal' => '{0} mesiac',
				'short' => '{0} mes.'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} sekúnd',
				'short' => '{0} s'
			),
			'one' => array(
				'normal' => '{0} sekunda',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} týždňov',
				'short' => '{0} týžd.'
			),
			'one' => array(
				'normal' => '{0} týždeň',
				'short' => '{0} týžd.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} rokov',
				'short' => '{0} r.'
			),
			'one' => array(
				'normal' => '{0} rok',
				'short' => '{0} r.'
			)
		)
	),
	'messages' => array(
		'yes' => 'ano:a',
		'no' => 'nie:n'
	)
);