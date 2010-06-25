<?php
/**
 * Locale: ga_IE
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'ga',
	'territory' => 'IE',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Eanáir',
				2 => 'Feabhra',
				3 => 'Márta',
				4 => 'Aibreán',
				5 => 'Bealtaine',
				6 => 'Meitheamh',
				7 => 'Iúil',
				8 => 'Lúnasa',
				9 => 'Meán Fómhair',
				10 => 'Deireadh Fómhair',
				11 => 'Samhain',
				12 => 'Nollaig'
			),
			'narrow' => array(
				1 => 'E',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'B',
				6 => 'M',
				7 => 'I',
				8 => 'L',
				9 => 'M',
				10 => 'D',
				11 => 'S',
				12 => 'N'
			),
			'abbreviated' => array(
				1 => 'Ean',
				2 => 'Feabh',
				3 => 'Márta',
				4 => 'Aib',
				5 => 'Beal',
				6 => 'Meith',
				7 => 'Iúil',
				8 => 'Lún',
				9 => 'MFómh',
				10 => 'DFómh',
				11 => 'Samh',
				12 => 'Noll'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Dé Domhnaigh',
				1 => 'Dé Luain',
				2 => 'Dé Máirt',
				3 => 'Dé Céadaoin',
				4 => 'Déardaoin',
				5 => 'Dé hAoine',
				6 => 'Dé Sathairn'
			),
			'narrow' => array(
				0 => 'D',
				1 => 'L',
				2 => 'M',
				3 => 'C',
				4 => 'D',
				5 => 'A',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'Domh',
				1 => 'Luan',
				2 => 'Máirt',
				3 => 'Céad',
				4 => 'Déar',
				5 => 'Aoine',
				6 => 'Sath'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1ú ráithe',
				2 => '2ú ráithe',
				3 => '3ú ráithe',
				4 => '4ú ráithe'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'R1',
				2 => 'R2',
				3 => 'R3',
				4 => 'R4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'a.m.',
				'pm' => 'p.m.'
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
				0 => 'RC',
				1 => 'AD'
			),
			'wide' => array(
				0 => 'Roimh Chríost',
				1 => 'Anno Domini'
			),
			'narrow' => array(
				0 => 'RC',
				1 => 'AD'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM y',
			'short' => 'dd/MM/yyyy'
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
				'name' => 'Ré'
			),
			'year' => array(
				'name' => 'Bliain'
			),
			'month' => array(
				'name' => 'Mí'
			),
			'week' => array(
				'name' => 'Seachtain'
			),
			'day' => array(
				'name' => 'Lá',
				'relative' => array(
					-2 => 'Arú inné',
					-1 => 'Inné',
					0 => 'Inniu',
					1 => 'Amárach',
					2 => 'Arú amárach'
				)
			),
			'weekday' => array(
				'name' => 'Lá na seachtaine'
			),
			'dayperiod' => array(
				'name' => 'a.m./p.m.'
			),
			'hour' => array(
				'name' => 'Uair'
			),
			'minute' => array(
				'name' => 'Nóiméad'
			),
			'second' => array(
				'name' => 'Soicind'
			),
			'zone' => array(
				'name' => 'Crios'
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
		'currencyFormat' => '¤#,##0.00',
		'currencies' => array(
			'AFN' => 'Af',
			'ANG' => 'AÍ f.',
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
		'yes' => 'tá:t',
		'no' => 'níl:n'
	)
);