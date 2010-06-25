<?php
/**
 * Locale: sg_CF
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4590',
	'language' => 'sg',
	'territory' => 'CF',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Nyenye',
				2 => 'Fulundïgi',
				3 => 'Mbängü',
				4 => 'Ngubùe',
				5 => 'Bêläwü',
				6 => 'Föndo',
				7 => 'Lengua',
				8 => 'Kükürü',
				9 => 'Mvuka',
				10 => 'Ngberere',
				11 => 'Nabändüru',
				12 => 'Kakauka'
			),
			'narrow' => array(
				1 => 'N',
				2 => 'F',
				3 => 'M',
				4 => 'N',
				5 => 'B',
				6 => 'F',
				7 => 'L',
				8 => 'K',
				9 => 'M',
				10 => 'N',
				11 => 'N',
				12 => 'K'
			),
			'abbreviated' => array(
				1 => 'Nye',
				2 => 'Ful',
				3 => 'Mbä',
				4 => 'Ngu',
				5 => 'Bêl',
				6 => 'Fön',
				7 => 'Len',
				8 => 'Kük',
				9 => 'Mvu',
				10 => 'Ngb',
				11 => 'Nab',
				12 => 'Kak'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Bikua-ôko',
				1 => 'Bïkua-ûse',
				2 => 'Bïkua-ptâ',
				3 => 'Bïkua-usïö',
				4 => 'Bïkua-okü',
				5 => 'Lâpôsö',
				6 => 'Lâyenga'
			),
			'narrow' => array(
				0 => 'K',
				1 => 'S',
				2 => 'T',
				3 => 'S',
				4 => 'K',
				5 => 'P',
				6 => 'Y'
			),
			'abbreviated' => array(
				0 => 'Bk1',
				1 => 'Bk2',
				2 => 'Bk3',
				3 => 'Bk4',
				4 => 'Bk5',
				5 => 'Lâp',
				6 => 'Lây'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Fângbisïö ôko',
				2 => 'Fângbisïö ûse',
				3 => 'Fângbisïö otâ',
				4 => 'Fângbisïö usïö'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'F4-1',
				2 => 'F4-2',
				3 => 'F4-3',
				4 => 'F4-4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'ND',
				'pm' => 'LK'
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
				0 => 'KnK',
				1 => 'NpK'
			),
			'wide' => array(
				0 => 'Kôzo na Krîstu',
				1 => 'Na pekô tî Krîstu'
			),
			'narrow' => array(
				0 => 'KnK',
				1 => 'NpK'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM, y',
			'short' => 'd/M/yyyy'
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
				'name' => 'Kùotângo'
			),
			'year' => array(
				'name' => 'Ngû'
			),
			'month' => array(
				'name' => 'Nze'
			),
			'week' => array(
				'name' => 'Dimâsi'
			),
			'day' => array(
				'name' => 'Lâ',
				'relative' => array(
					-1 => 'Bîrï',
					0 => 'Lâsô',
					1 => 'Kêkerêke'
				)
			),
			'weekday' => array(
				'name' => 'Bïkua'
			),
			'dayperiod' => array(
				'name' => 'Na lâ'
			),
			'hour' => array(
				'name' => 'Ngbonga'
			),
			'minute' => array(
				'name' => 'Ndurü ngbonga'
			),
			'second' => array(
				'name' => 'Nzîna ngbonga'
			),
			'zone' => array(
				'name' => 'Zukangbonga'
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
			'exponential' => 'E',
			'perMille' => '‰',
			'infinity' => '∞',
			'nan' => 'NaN'
		),
		'decimalFormat' => '#,##0.###',
		'scientificFormat' => '#E0',
		'percentFormat' => '#,##0%',
		'currencyFormat' => '¤#,##0.00;¤-#,##0.00',
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
				'normal' => '{0} d'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} h'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} min'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} m'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} s'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} w'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} y'
			)
		)
	),
	'messages' => array(
		'yes' => 'Iin:I',
		'no' => 'Én-en:E'
	)
);