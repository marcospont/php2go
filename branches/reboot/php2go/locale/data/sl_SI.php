<?php
/**
 * Locale: sl_SI
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'sl',
	'territory' => 'SI',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'januar',
				2 => 'februar',
				3 => 'marec',
				4 => 'april',
				5 => 'maj',
				6 => 'junij',
				7 => 'julij',
				8 => 'avgust',
				9 => 'september',
				10 => 'oktober',
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
				5 => 'maj',
				6 => 'jun',
				7 => 'jul',
				8 => 'avg',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'dec'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'nedelja',
				1 => 'ponedeljek',
				2 => 'torek',
				3 => 'sreda',
				4 => 'četrtek',
				5 => 'petek',
				6 => 'sobota'
			),
			'narrow' => array(
				0 => 'n',
				1 => 'p',
				2 => 't',
				3 => 's',
				4 => 'č',
				5 => 'p',
				6 => 's'
			),
			'abbreviated' => array(
				0 => 'ned',
				1 => 'pon',
				2 => 'tor',
				3 => 'sre',
				4 => 'čet',
				5 => 'pet',
				6 => 'sob'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. četrtletje',
				2 => '2. četrtletje',
				3 => '3. četrtletje',
				4 => '4. četrtletje'
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
				'am' => 'dop.',
				'pm' => 'pop.'
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
				0 => 'pr. n. št.',
				1 => 'po Kr.'
			),
			'wide' => array(
				0 => 'pred našim štetjem',
				1 => 'naše štetje'
			),
			'narrow' => array(
				0 => 'pr. n. št.',
				1 => 'po Kr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, dd. MMMM y',
			'long' => 'dd. MMMM y',
			'medium' => 'd. MMM. yyyy',
			'short' => 'd. MM. yy'
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
				'name' => 'Doba'
			),
			'year' => array(
				'name' => 'Leto'
			),
			'month' => array(
				'name' => 'Mesec'
			),
			'week' => array(
				'name' => 'Teden'
			),
			'day' => array(
				'name' => 'Dan',
				'relative' => array(
					-3 => 'Pred tremi dnevi',
					-2 => 'Predvčerajšnjim',
					-1 => 'Včeraj',
					0 => 'Danes',
					1 => 'Jutri',
					2 => 'Pojutrišnjem',
					3 => 'Čez tri dni'
				)
			),
			'weekday' => array(
				'name' => 'Dan v tednu'
			),
			'dayperiod' => array(
				'name' => 'Čas dneva'
			),
			'hour' => array(
				'name' => 'Ura'
			),
			'minute' => array(
				'name' => 'Minuta'
			),
			'second' => array(
				'name' => 'Sekunda'
			),
			'zone' => array(
				'name' => 'Območje'
			)
		)
	),
	'numbers' => array(
		'defaultNumberingSystem' => 'latn',
		'symbols' => array(
			'decimal' => ',',
			'group' => '.',
			'list' => ';',
			'percentSign' => '%',
			'nativeZeroDigit' => '0',
			'patternDigit' => '#',
			'plusSign' => '+',
			'minusSign' => '-',
			'exponential' => 'e',
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
			'JPY' => '¥',
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
			'USD' => '$',
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
				'normal' => '{0} dni',
				'short' => '{0} d'
			),
			'one' => array(
				'normal' => '{0} dan',
				'short' => '{0} d'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} uri',
				'short' => '{0} h'
			),
			'one' => array(
				'normal' => '{0} ura',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} minuti',
				'short' => '{0} min'
			),
			'one' => array(
				'normal' => '{0} minuta',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} meseca',
				'short' => '{0} m'
			),
			'one' => array(
				'normal' => '{0} mesec',
				'short' => '{0} m'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} sekundi',
				'short' => '{0} s'
			),
			'one' => array(
				'normal' => '{0} sekunda',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} tedna',
				'short' => '{0} t'
			),
			'one' => array(
				'normal' => '{0} teden',
				'short' => '{0} t'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} leti',
				'short' => '{0} l'
			),
			'one' => array(
				'normal' => '{0} leto',
				'short' => '{0} l'
			)
		)
	),
	'messages' => array(
		'yes' => 'da:d',
		'no' => 'ne:n'
	)
);