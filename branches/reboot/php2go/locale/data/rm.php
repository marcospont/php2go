<?php
/**
 * Locale: rm
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4763',
	'language' => 'rm',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'schaner',
				2 => 'favrer',
				3 => 'mars',
				4 => 'avrigl',
				5 => 'matg',
				6 => 'zercladur',
				7 => 'fanadur',
				8 => 'avust',
				9 => 'settember',
				10 => 'october',
				11 => 'november',
				12 => 'december'
			),
			'narrow' => array(
				1 => 'S',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'M',
				6 => 'Z',
				7 => 'F',
				8 => 'A',
				9 => 'S',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'schan.',
				2 => 'favr.',
				3 => 'mars',
				4 => 'avr.',
				5 => 'matg',
				6 => 'zercl.',
				7 => 'fan.',
				8 => 'avust',
				9 => 'sett.',
				10 => 'oct.',
				11 => 'nov.',
				12 => 'dec.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'dumengia',
				1 => 'glindesdi',
				2 => 'mardi',
				3 => 'mesemna',
				4 => 'gievgia',
				5 => 'venderdi',
				6 => 'sonda'
			),
			'narrow' => array(
				0 => 'D',
				1 => 'G',
				2 => 'M',
				3 => 'M',
				4 => 'G',
				5 => 'V',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'du',
				1 => 'gli',
				2 => 'ma',
				3 => 'me',
				4 => 'gie',
				5 => 've',
				6 => 'so'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. quartal',
				2 => '2. quartal',
				3 => '3. quartal',
				4 => '4. quartal'
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
				'am' => 'am',
				'pm' => 'sm'
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
				0 => 'av. Cr.',
				1 => 's. Cr.'
			),
			'wide' => array(
				0 => 'avant Cristus',
				1 => 'suenter Cristus'
			),
			'narrow' => array(
				0 => 'av. Cr.',
				1 => 's. Cr.'
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
				'name' => 'epoca'
			),
			'year' => array(
				'name' => 'onn'
			),
			'month' => array(
				'name' => 'mais'
			),
			'week' => array(
				'name' => 'emna'
			),
			'day' => array(
				'name' => 'Tag',
				'relative' => array(
					-3 => 'squarsas',
					-2 => 'stersas',
					-1 => 'ier',
					0 => 'oz',
					1 => 'damaun',
					2 => 'puschmaun',
					3 => 'squartmaun'
				)
			),
			'weekday' => array(
				'name' => 'di da l\'emna'
			),
			'dayperiod' => array(
				'name' => 'mesadad dal di'
			),
			'hour' => array(
				'name' => 'ura'
			),
			'minute' => array(
				'name' => 'minuta'
			),
			'second' => array(
				'name' => 'secunda'
			),
			'zone' => array(
				'name' => 'zona d\'urari'
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
			'one' => array(
				'normal' => '{0} di',
				'short' => '{0} di'
			),
			'other' => array(
				'normal' => '{0} dis',
				'short' => '{0} dis'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} ura',
				'short' => '{0} ura'
			),
			'other' => array(
				'normal' => '{0} uras',
				'short' => '{0} uras'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minuta',
				'short' => '{0} min.'
			),
			'other' => array(
				'normal' => '{0} minutas',
				'short' => '{0} mins.'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mais',
				'short' => '{0} mais'
			),
			'other' => array(
				'normal' => '{0} mais',
				'short' => '{0} mais'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} secunda',
				'short' => '{0} sec.'
			),
			'other' => array(
				'normal' => '{0} secundas',
				'short' => '{0} secs.'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} emna',
				'short' => '{0} emna'
			),
			'other' => array(
				'normal' => '{0} emnas',
				'short' => '{0} emnas'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} onn',
				'short' => '{0} onn'
			),
			'other' => array(
				'normal' => '{0} onns',
				'short' => '{0} onns'
			)
		)
	),
	'messages' => array(
		'yes' => 'gea:g',
		'no' => 'na:n'
	)
);