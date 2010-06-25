<?php
/**
 * Locale: gsw_CH
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'gsw',
	'territory' => 'CH',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Januar',
				2 => 'Februar',
				3 => 'März',
				4 => 'April',
				5 => 'Mai',
				6 => 'Juni',
				7 => 'Juli',
				8 => 'Auguscht',
				9 => 'Septämber',
				10 => 'Oktoober',
				11 => 'Novämber',
				12 => 'Dezämber'
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
				1 => 'Jan',
				2 => 'Feb',
				3 => 'Mär',
				4 => 'Apr',
				5 => 'Mai',
				6 => 'Jun',
				7 => 'Jul',
				8 => 'Aug',
				9 => 'Sep',
				10 => 'Okt',
				11 => 'Nov',
				12 => 'Dez'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Sunntig',
				1 => 'Määntig',
				2 => 'Ziischtig',
				3 => 'Mittwuch',
				4 => 'Dunschtig',
				5 => 'Friitig',
				6 => 'Samschtig'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'M',
				2 => 'D',
				3 => 'M',
				4 => 'D',
				5 => 'F',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'Su.',
				1 => 'Mä.',
				2 => 'Zi.',
				3 => 'Mi.',
				4 => 'Du.',
				5 => 'Fr.',
				6 => 'Sa.'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. Quartal',
				2 => '2. Quartal',
				3 => '3. Quartal',
				4 => '4. Quartal'
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
				'am' => 'Vormittag',
				'pm' => 'Namittag'
			),
			'abbreviated' => array(
				'am' => 'v.m.',
				'pm' => 'n.m.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'v. Chr.',
				1 => 'n. Chr.'
			),
			'wide' => array(
				0 => 'vor Christus',
				1 => 'nach Christus'
			),
			'narrow' => array(
				0 => 'v. Chr.',
				1 => 'n. Chr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'dd.MM.yyyy',
			'short' => 'dd.MM.yy'
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
				'name' => 'Epoche'
			),
			'year' => array(
				'name' => 'Jaar'
			),
			'month' => array(
				'name' => 'Monet'
			),
			'week' => array(
				'name' => 'Wuche'
			),
			'day' => array(
				'name' => 'Tag',
				'relative' => array(
					-3 => 'vorvorgeschter',
					-2 => 'vorgeschter',
					-1 => 'geschter',
					0 => 'hüt',
					1 => 'moorn',
					2 => 'übermoorn',
					3 => 'überübermoorn'
				)
			),
			'weekday' => array(
				'name' => 'Wuchetag'
			),
			'dayperiod' => array(
				'name' => 'Tageshälfti'
			),
			'hour' => array(
				'name' => 'Schtund'
			),
			'minute' => array(
				'name' => 'Minuute'
			),
			'second' => array(
				'name' => 'Sekunde'
			),
			'zone' => array(
				'name' => 'Zone'
			)
		)
	),
	'numbers' => array(
		'defaultNumberingSystem' => 'latn',
		'symbols' => array(
			'decimal' => '.',
			'group' => '’',
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
			'ZWD' => 'Z$',
			'ATS' => 'öS'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} Taag'
			),
			'other' => array(
				'normal' => '{0} Tääg'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} Schtund'
			),
			'other' => array(
				'normal' => '{0} Schtunde'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} Minuute'
			),
			'other' => array(
				'normal' => '{0} Minuute'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} Monet'
			),
			'other' => array(
				'normal' => '{0} Mönet'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} Sekunde'
			),
			'other' => array(
				'normal' => '{0} Sekunde'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} Wuche'
			),
			'other' => array(
				'normal' => '{0} Wuche'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} Jahr'
			),
			'other' => array(
				'normal' => '{0} Jahr'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja:j',
		'no' => 'näi:n'
	)
);