<?php
/**
 * Locale: dav
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4590',
	'language' => 'dav',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Mori ghwa imbiri',
				2 => 'Mori ghwa kawi',
				3 => 'Mori ghwa kadadu',
				4 => 'Mori ghwa kana',
				5 => 'Mori ghwa kasanu',
				6 => 'Mori ghwa karandadu',
				7 => 'Mori ghwa mfungade',
				8 => 'Mori ghwa wunyanya',
				9 => 'Mori ghwa ikenda',
				10 => 'Mori ghwa ikumi',
				11 => 'Mori ghwa ikumi na imweri',
				12 => 'Mori ghwa ikumi na iwi'
			),
			'narrow' => array(
				1 => 'I',
				2 => 'K',
				3 => 'K',
				4 => 'K',
				5 => 'K',
				6 => 'K',
				7 => 'M',
				8 => 'W',
				9 => 'I',
				10 => 'I',
				11 => 'I',
				12 => 'I'
			),
			'abbreviated' => array(
				1 => 'Imb',
				2 => 'Kaw',
				3 => 'Kad',
				4 => 'Kan',
				5 => 'Kas',
				6 => 'Kar',
				7 => 'Mfu',
				8 => 'Wun',
				9 => 'Ike',
				10 => 'Iku',
				11 => 'Imw',
				12 => 'Iwi'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Ituku ja jumwa',
				1 => 'Kuramuka jimweri',
				2 => 'Kuramuka  kawi',
				3 => 'Kuramuka kadadu',
				4 => 'Kuramuka kana',
				5 => 'Kuramuka kasanu',
				6 => 'Kifula nguwo'
			),
			'narrow' => array(
				0 => 'J',
				1 => 'J',
				2 => 'K',
				3 => 'K',
				4 => 'K',
				5 => 'K',
				6 => 'N'
			),
			'abbreviated' => array(
				0 => 'Jum',
				1 => 'Jim',
				2 => 'Kaw',
				3 => 'Kad',
				4 => 'Kan',
				5 => 'Kas',
				6 => 'Ngu'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Kimu cha imbiri',
				2 => 'Kimu cha kawi',
				3 => 'Kimu cha kadadu',
				4 => 'Kimu cha kana'
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
				'am' => 'Luma lwa K',
				'pm' => 'luma lwa p'
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
				0 => 'KK',
				1 => 'BK'
			),
			'wide' => array(
				0 => 'Kabla ya Kristo',
				1 => 'Baada ya Kristo'
			),
			'narrow' => array(
				0 => 'KK',
				1 => 'BK'
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
				'name' => 'Ngelo'
			),
			'year' => array(
				'name' => 'Mwaka'
			),
			'month' => array(
				'name' => 'Mori'
			),
			'week' => array(
				'name' => 'Juma'
			),
			'day' => array(
				'name' => 'Ituku',
				'relative' => array(
					-1 => 'Iguo',
					0 => 'Idime',
					1 => 'Kesho'
				)
			),
			'weekday' => array(
				'name' => 'Ituku'
			),
			'dayperiod' => array(
				'name' => 'KE/PE'
			),
			'hour' => array(
				'name' => 'Saa'
			),
			'minute' => array(
				'name' => 'Dakika'
			),
			'second' => array(
				'name' => 'Sekunde'
			),
			'zone' => array(
				'name' => 'Majira ya saa'
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
		'yes' => 'Huwo:H',
		'no' => 'Mbai:M'
	)
);