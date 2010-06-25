<?php
/**
 * Locale: so_ET
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'so',
	'territory' => 'ET',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Bisha Koobaad',
				2 => 'Bisha Labaad',
				3 => 'Bisha Saddexaad',
				4 => 'Bisha Afraad',
				5 => 'Bisha Shanaad',
				6 => 'Bisha Lixaad',
				7 => 'Bisha Todobaad',
				8 => 'Bisha Sideedaad',
				9 => 'Bisha Sagaalaad',
				10 => 'Bisha Tobnaad',
				11 => 'Bisha Kow iyo Tobnaad',
				12 => 'Bisha Laba iyo Tobnaad'
			),
			'narrow' => array(
				1 => 'K',
				2 => 'L',
				3 => 'S',
				4 => 'A',
				5 => 'S',
				6 => 'L',
				7 => 'T',
				8 => 'S',
				9 => 'S',
				10 => 'T',
				11 => 'K',
				12 => 'L'
			),
			'abbreviated' => array(
				1 => 'Kob',
				2 => 'Lab',
				3 => 'Sad',
				4 => 'Afr',
				5 => 'Sha',
				6 => 'Lix',
				7 => 'Tod',
				8 => 'Sid',
				9 => 'Sag',
				10 => 'Tob',
				11 => 'KIT',
				12 => 'LIT'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Axad',
				1 => 'Isniin',
				2 => 'Talaado',
				3 => 'Arbaco',
				4 => 'Khamiis',
				5 => 'Jimco',
				6 => 'Sabti'
			),
			'narrow' => array(
				0 => 'A',
				1 => 'I',
				2 => 'T',
				3 => 'A',
				4 => 'K',
				5 => 'J',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'Axd',
				1 => 'Isn',
				2 => 'Tal',
				3 => 'Arb',
				4 => 'Kha',
				5 => 'Jim',
				6 => 'Sab'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Rubaca 1aad',
				2 => 'Rubaca 2aad',
				3 => 'Rubaca 3aad',
				4 => 'Rubaca 4aad'
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
				'am' => 'sn.',
				'pm' => 'gn.'
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
				0 => 'CK',
				1 => 'CD'
			),
			'wide' => array(
				0 => 'Ciise ka hor (CS)',
				1 => 'Ciise ka dib (CS)'
			),
			'narrow' => array(
				0 => 'CK',
				1 => 'CD'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, MMMM dd, y',
			'long' => 'dd MMMM y',
			'medium' => 'dd-MMM-y',
			'short' => 'dd/MM/yy'
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
				'name' => 'Qarni'
			),
			'year' => array(
				'name' => 'Sanad'
			),
			'month' => array(
				'name' => 'Bil'
			),
			'week' => array(
				'name' => 'Toddobaad'
			),
			'day' => array(
				'name' => 'Maalin',
				'relative' => array(
					-1 => 'Shalay',
					0 => 'Maanta',
					1 => 'Berri'
				)
			),
			'weekday' => array(
				'name' => 'Maalinta toddobaadka'
			),
			'dayperiod' => array(
				'name' => 'sn./gn.'
			),
			'hour' => array(
				'name' => 'Saacad'
			),
			'minute' => array(
				'name' => 'Daqiiqad'
			),
			'second' => array(
				'name' => 'Il biriqsi'
			),
			'zone' => array(
				'name' => 'Xadka waqtiga'
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
		'yes' => 'haa:h',
		'no' => 'maya:m'
	)
);