<?php
/**
 * Locale: kam_KE
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'kam',
	'territory' => 'KE',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Mwai wa mbee',
				2 => 'Mwai wa kelĩ',
				3 => 'Mwai wa katatũ',
				4 => 'Mwai wa kana',
				5 => 'Mwai wa katano',
				6 => 'Mwai wa thanthatũ',
				7 => 'Mwai wa muonza',
				8 => 'Mwai wa nyaanya',
				9 => 'Mwai wa kenda',
				10 => 'Mwai wa ĩkumi',
				11 => 'Mwai wa ĩkumi na ĩmwe',
				12 => 'Mwai wa ĩkumi na ilĩ'
			),
			'narrow' => array(
				1 => 'M',
				2 => 'K',
				3 => 'K',
				4 => 'K',
				5 => 'K',
				6 => 'T',
				7 => 'M',
				8 => 'N',
				9 => 'K',
				10 => 'Ĩ',
				11 => 'Ĩ',
				12 => 'Ĩ'
			),
			'abbreviated' => array(
				1 => 'Mbe',
				2 => 'Kel',
				3 => 'Ktũ',
				4 => 'Kan',
				5 => 'Ktn',
				6 => 'Tha',
				7 => 'Moo',
				8 => 'Nya',
				9 => 'Knd',
				10 => 'Ĩku',
				11 => 'Ĩkm',
				12 => 'Ĩkl'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Wa kyumwa',
				1 => 'Wa kwambĩlĩlya',
				2 => 'Wa kelĩ',
				3 => 'Wa katatũ',
				4 => 'Wa kana',
				5 => 'Wa katano',
				6 => 'Wa thanthatũ'
			),
			'narrow' => array(
				0 => 'Y',
				1 => 'W',
				2 => 'E',
				3 => 'A',
				4 => 'A',
				5 => 'A',
				6 => 'A'
			),
			'abbreviated' => array(
				0 => 'Wky',
				1 => 'Wkw',
				2 => 'Wkl',
				3 => 'Wtũ',
				4 => 'Wkn',
				5 => 'Wtn',
				6 => 'Wth'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Lovo ya mbee',
				2 => 'Lovo ya kelĩ',
				3 => 'Lovo ya katatũ',
				4 => 'Lovo ya kana'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'L1',
				2 => 'L2',
				3 => 'L3',
				4 => 'L4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'Ĩyakwakya',
				'pm' => 'Ĩyawĩoo'
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
				0 => 'MY',
				1 => 'IY'
			),
			'wide' => array(
				0 => 'Mbee wa Yesũ',
				1 => 'Ĩtina wa Yesũ'
			),
			'narrow' => array(
				0 => 'MY',
				1 => 'IY'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM y',
			'short' => 'dd/MM/yyyy'
		),
		'timeFormats' => array(
			'full' => 'h:mm:ss a zzzz',
			'long' => 'h:mm:ss a z',
			'medium' => 'h:mm:ss a',
			'short' => 'h:mm a'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'Ĩvinda'
			),
			'year' => array(
				'name' => 'Mwaka'
			),
			'month' => array(
				'name' => 'Mwai'
			),
			'week' => array(
				'name' => 'Kyumwa'
			),
			'day' => array(
				'name' => 'Mũthenya',
				'relative' => array(
					-1 => 'Ĩyoo',
					0 => 'Ũmũnthĩ',
					1 => 'Ũnĩ'
				)
			),
			'weekday' => array(
				'name' => 'Kyumwanĩ'
			),
			'dayperiod' => array(
				'name' => 'Ĩyakwakya/Ĩyawĩoo'
			),
			'hour' => array(
				'name' => 'Saa'
			),
			'minute' => array(
				'name' => 'Ndatĩka'
			),
			'second' => array(
				'name' => 'sekondi'
			),
			'zone' => array(
				'name' => 'Kĩsio kya ĩsaa'
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
		'percentFormat' => '#,##0%',
		'currencyFormat' => '¤#,##0.00;(¤#,##0.00)',
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
		'yes' => 'Ĩĩ:Ĩ',
		'no' => 'Aiee:A'
	)
);