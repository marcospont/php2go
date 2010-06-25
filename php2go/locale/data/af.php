<?php
/**
 * Locale: af
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4768',
	'language' => 'af',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Januarie',
				2 => 'Februarie',
				3 => 'Maart',
				4 => 'April',
				5 => 'Mei',
				6 => 'Junie',
				7 => 'Julie',
				8 => 'Augustus',
				9 => 'September',
				10 => 'Oktober',
				11 => 'November',
				12 => 'Desember'
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
				1 => 'Jan',
				2 => 'Feb',
				3 => 'Mar',
				4 => 'Apr',
				5 => 'Mei',
				6 => 'Jun',
				7 => 'Jul',
				8 => 'Aug',
				9 => 'Sep',
				10 => 'Okt',
				11 => 'Nov',
				12 => 'Des'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Sondag',
				1 => 'Maandag',
				2 => 'Dinsdag',
				3 => 'Woensdag',
				4 => 'Donderdag',
				5 => 'Vrydag',
				6 => 'Saterdag'
			),
			'narrow' => array(
				0 => '1',
				1 => '2',
				2 => '3',
				3 => '4',
				4 => '5',
				5 => '6',
				6 => '7'
			),
			'abbreviated' => array(
				0 => 'So',
				1 => 'Ma',
				2 => 'Di',
				3 => 'Wo',
				4 => 'Do',
				5 => 'Vr',
				6 => 'Sa'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1ste kwartaal',
				2 => '2de kwartaal',
				3 => '3de kwartaal',
				4 => '4de kwartaal'
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
				'am' => 'voormiddag',
				'pm' => 'namiddag'
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
				0 => 'v.C.',
				1 => 'n.C.'
			),
			'wide' => array(
				0 => 'voor Christus',
				1 => 'na Christus'
			),
			'narrow' => array(
				0 => 'v.C.',
				1 => 'n.C.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE dd MMMM y',
			'long' => 'dd MMMM y',
			'medium' => 'dd MMM y',
			'short' => 'yyyy-MM-dd'
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
				'name' => 'tydperk'
			),
			'year' => array(
				'name' => 'Jaar'
			),
			'month' => array(
				'name' => 'Maand'
			),
			'week' => array(
				'name' => 'Week'
			),
			'day' => array(
				'name' => 'Dag',
				'relative' => array(
					-3 => 'eereergister',
					-2 => 'eergister',
					-1 => 'gister',
					0 => 'vandag',
					1 => 'môre',
					2 => 'overmôre',
					3 => 'overovermôre'
				)
			),
			'weekday' => array(
				'name' => 'Weeksdag'
			),
			'dayperiod' => array(
				'name' => 'AM/PM'
			),
			'hour' => array(
				'name' => 'Uur'
			),
			'minute' => array(
				'name' => 'Minuut'
			),
			'second' => array(
				'name' => 'Sekonde'
			),
			'zone' => array(
				'name' => 'Tydsone'
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
			'one' => array(
				'normal' => '{0} etmaal'
			),
			'other' => array(
				'normal' => '{0} etmale'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} uur'
			),
			'other' => array(
				'normal' => '{0} uur'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minuut'
			),
			'other' => array(
				'normal' => '{0} minute'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} maand'
			),
			'other' => array(
				'normal' => '{0} maande'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} sekonde'
			),
			'other' => array(
				'normal' => '{0} sekondes'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} week'
			),
			'other' => array(
				'normal' => '{0} weke'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} jaar'
			),
			'other' => array(
				'normal' => '{0} jaar'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja:j',
		'no' => 'nee:n'
	)
);