<?php
/**
 * Locale: sq_AL
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'sq',
	'territory' => 'AL',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'janar',
				2 => 'shkurt',
				3 => 'mars',
				4 => 'prill',
				5 => 'maj',
				6 => 'qershor',
				7 => 'korrik',
				8 => 'gusht',
				9 => 'shtator',
				10 => 'tetor',
				11 => 'nëntor',
				12 => 'dhjetor'
			),
			'narrow' => array(
				1 => 'J',
				2 => 'S',
				3 => 'M',
				4 => 'P',
				5 => 'M',
				6 => 'Q',
				7 => 'K',
				8 => 'G',
				9 => 'S',
				10 => 'T',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'Jan',
				2 => 'Shk',
				3 => 'Mar',
				4 => 'Pri',
				5 => 'Maj',
				6 => 'Qer',
				7 => 'Kor',
				8 => 'Gsh',
				9 => 'Sht',
				10 => 'Tet',
				11 => 'Nën',
				12 => 'Dhj'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'e diel',
				1 => 'e hënë',
				2 => 'e martë',
				3 => 'e mërkurë',
				4 => 'e enjte',
				5 => 'e premte',
				6 => 'e shtunë'
			),
			'narrow' => array(
				0 => 'D',
				1 => 'H',
				2 => 'M',
				3 => 'M',
				4 => 'E',
				5 => 'P',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'Die',
				1 => 'Hën',
				2 => 'Mar',
				3 => 'Mër',
				4 => 'Enj',
				5 => 'Pre',
				6 => 'Sht'
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
				'am' => 'PD',
				'pm' => 'MD'
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
				0 => 'p.e.r.',
				1 => 'n.e.r.'
			),
			'wide' => array(
				0 => 'p.e.r.',
				1 => 'n.e.r.'
			),
			'narrow' => array(
				0 => 'p.e.r.',
				1 => 'n.e.r.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, dd MMMM y',
			'long' => 'dd MMMM y',
			'medium' => 'yyyy-MM-dd',
			'short' => 'yy-MM-dd'
		),
		'timeFormats' => array(
			'full' => 'h.mm.ss.a zzzz',
			'long' => 'h.mm.ss.a z',
			'medium' => 'h.mm.ss.a',
			'short' => 'h.mm.a'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'Era'
			),
			'year' => array(
				'name' => 'Year'
			),
			'month' => array(
				'name' => 'Month'
			),
			'week' => array(
				'name' => 'Week'
			),
			'day' => array(
				'name' => 'Day',
				'relative' => array(
					-1 => 'Yesterday',
					0 => 'Today',
					1 => 'Tomorrow'
				)
			),
			'weekday' => array(
				'name' => 'Day of the Week'
			),
			'dayperiod' => array(
				'name' => 'Dayperiod'
			),
			'hour' => array(
				'name' => 'Hour'
			),
			'minute' => array(
				'name' => 'Minute'
			),
			'second' => array(
				'name' => 'Second'
			),
			'zone' => array(
				'name' => 'Zone'
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
		'currencyFormat' => '¤#,##0.00',
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
			'ZWD' => 'Z$',
			'ALL' => 'Lek'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} ditë'
			),
			'other' => array(
				'normal' => '{0} ditë'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} orë'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minutë'
			),
			'other' => array(
				'normal' => '{0} minuta'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} muaj'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} sekondë'
			),
			'other' => array(
				'normal' => '{0} sekonda'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} javë'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} vit'
			),
			'other' => array(
				'normal' => '{0} vjet'
			)
		)
	),
	'messages' => array(
		'yes' => 'po:p',
		'no' => 'jo:j'
	)
);