<?php
/**
 * Locale: da_DK
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'da',
	'territory' => 'DK',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'januar',
				2 => 'februar',
				3 => 'marts',
				4 => 'april',
				5 => 'maj',
				6 => 'juni',
				7 => 'juli',
				8 => 'august',
				9 => 'september',
				10 => 'oktober',
				11 => 'november',
				12 => 'december'
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
				1 => 'jan',
				2 => 'feb',
				3 => 'mar',
				4 => 'apr',
				5 => 'maj',
				6 => 'jun',
				7 => 'jul',
				8 => 'aug',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'dec'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'søndag',
				1 => 'mandag',
				2 => 'tirsdag',
				3 => 'onsdag',
				4 => 'torsdag',
				5 => 'fredag',
				6 => 'lørdag'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'M',
				2 => 'T',
				3 => 'O',
				4 => 'T',
				5 => 'F',
				6 => 'L'
			),
			'abbreviated' => array(
				0 => 'søn',
				1 => 'man',
				2 => 'tir',
				3 => 'ons',
				4 => 'tor',
				5 => 'fre',
				6 => 'lør'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. kvartal',
				2 => '2. kvartal',
				3 => '3. kvartal',
				4 => '4. kvartal'
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
				'am' => 'formiddag',
				'pm' => 'eftermiddag'
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
				0 => 'f.v.t.',
				1 => 'e.v.t.'
			),
			'wide' => array(
				0 => 'før Kristus',
				1 => 'efter Kristus'
			),
			'narrow' => array(
				0 => 'f.v.t.',
				1 => 'e.v.t.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE \'den\' d. MMMM y',
			'long' => 'd. MMM y',
			'medium' => 'dd/MM/yyyy',
			'short' => 'dd/MM/yy'
		),
		'timeFormats' => array(
			'full' => 'HH.mm.ss zzzz',
			'long' => 'HH.mm.ss z',
			'medium' => 'HH.mm.ss',
			'short' => 'HH.mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'æra'
			),
			'year' => array(
				'name' => 'år'
			),
			'month' => array(
				'name' => 'måned'
			),
			'week' => array(
				'name' => 'uge'
			),
			'day' => array(
				'name' => 'dag',
				'relative' => array(
					-3 => 'i forforgårs',
					-2 => 'i forgårs',
					-1 => 'i går',
					0 => 'i dag',
					1 => 'i morgen',
					2 => 'i overmorgen',
					3 => 'i overovermorgen'
				)
			),
			'weekday' => array(
				'name' => 'ugedag'
			),
			'dayperiod' => array(
				'name' => 'dagtid'
			),
			'hour' => array(
				'name' => 'time'
			),
			'minute' => array(
				'name' => 'minut'
			),
			'second' => array(
				'name' => 'sekund'
			),
			'zone' => array(
				'name' => 'zone'
			)
		)
	),
	'numbers' => array(
		'defaultNumberingSystem' => 'latn',
		'symbols' => array(
			'decimal' => ',',
			'group' => '.',
			'list' => ',',
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
			'DKK' => 'kr',
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
			'one' => array(
				'normal' => '{0} d',
				'short' => '{0} dag'
			),
			'other' => array(
				'normal' => '{0} d',
				'short' => '{0} dage'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} h',
				'short' => '{0} t.'
			),
			'other' => array(
				'normal' => '{0} h',
				'short' => '{0} t.'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} min',
				'short' => '{0} min.'
			),
			'other' => array(
				'normal' => '{0} min',
				'short' => '{0} min.'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mån',
				'short' => '{0} mdr.'
			),
			'other' => array(
				'normal' => '{0} mån',
				'short' => '{0} mdr.'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} s',
				'short' => '{0} sek.'
			),
			'other' => array(
				'normal' => '{0} s',
				'short' => '{0} sek.'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} u',
				'short' => '{0} uge'
			),
			'other' => array(
				'normal' => '{0} u',
				'short' => '{0} uger'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} år',
				'short' => '{0} år'
			),
			'other' => array(
				'normal' => '{0} år',
				'short' => '{0} år'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja:j',
		'no' => 'nej:n'
	)
);