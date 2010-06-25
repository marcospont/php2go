<?php
/**
 * Locale: cs
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4753',
	'language' => 'cs',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'leden',
				2 => 'únor',
				3 => 'březen',
				4 => 'duben',
				5 => 'květen',
				6 => 'červen',
				7 => 'červenec',
				8 => 'srpen',
				9 => 'září',
				10 => 'říjen',
				11 => 'listopad',
				12 => 'prosinec'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
				6 => '6',
				7 => '7',
				8 => '8',
				9 => '9',
				10 => '10',
				11 => '11',
				12 => '12'
			),
			'abbreviated' => array(
				1 => '1.',
				2 => '2.',
				3 => '3.',
				4 => '4.',
				5 => '5.',
				6 => '6.',
				7 => '7.',
				8 => '8.',
				9 => '9.',
				10 => '10.',
				11 => '11.',
				12 => '12.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'neděle',
				1 => 'pondělí',
				2 => 'úterý',
				3 => 'středa',
				4 => 'čtvrtek',
				5 => 'pátek',
				6 => 'sobota'
			),
			'narrow' => array(
				0 => 'N',
				1 => 'P',
				2 => 'Ú',
				3 => 'S',
				4 => 'Č',
				5 => 'P',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'ne',
				1 => 'po',
				2 => 'út',
				3 => 'st',
				4 => 'čt',
				5 => 'pá',
				6 => 'so'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. čtvrtletí',
				2 => '2. čtvrtletí',
				3 => '3. čtvrtletí',
				4 => '4. čtvrtletí'
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
				'pm' => 'odp.'
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
				0 => 'př.n.l.',
				1 => 'n.l.'
			),
			'wide' => array(
				0 => 'př.n.l.',
				1 => 'n.l.'
			),
			'narrow' => array(
				0 => 'př.n.l.',
				1 => 'n.l.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'd.M.yyyy',
			'short' => 'd.M.yy'
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
				'name' => 'Epocha'
			),
			'year' => array(
				'name' => 'Rok'
			),
			'month' => array(
				'name' => 'Měsíc'
			),
			'week' => array(
				'name' => 'Týden'
			),
			'day' => array(
				'name' => 'Den',
				'relative' => array(
					-2 => 'Předevčírem',
					-1 => 'Včera',
					0 => 'Dnes',
					1 => 'Zítra',
					2 => 'Pozítří'
				)
			),
			'weekday' => array(
				'name' => 'Den v týdnu'
			),
			'dayperiod' => array(
				'name' => 'Část dne'
			),
			'hour' => array(
				'name' => 'Hodina'
			),
			'minute' => array(
				'name' => 'Minuta'
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
			'other' => array(
				'normal' => '{0} dní',
				'short' => '{0} dní'
			),
			'one' => array(
				'normal' => '{0} den',
				'short' => '{0} den'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} hodin',
				'short' => '{0} hod.'
			),
			'one' => array(
				'normal' => '{0} hodina',
				'short' => '{0} hod.'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} minut',
				'short' => '{0} min.'
			),
			'one' => array(
				'normal' => '{0} minuta',
				'short' => '{0} min.'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} měsíců',
				'short' => '{0} měs.'
			),
			'one' => array(
				'normal' => '{0} měsíc',
				'short' => '{0} měs.'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} sekund',
				'short' => '{0} sek.'
			),
			'one' => array(
				'normal' => '{0} sekunda',
				'short' => '{0} sek.'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} týdnů',
				'short' => '{0} týd.'
			),
			'one' => array(
				'normal' => '{0} týden',
				'short' => '{0} týd.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} let',
				'short' => '{0} let'
			),
			'one' => array(
				'normal' => '{0} rok',
				'short' => '{0} rok'
			)
		)
	),
	'messages' => array(
		'yes' => 'ano:a',
		'no' => 'ne:n'
	)
);