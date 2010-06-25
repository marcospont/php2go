<?php
/**
 * Locale: nl_NL
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'nl',
	'territory' => 'NL',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'januari',
				2 => 'februari',
				3 => 'maart',
				4 => 'april',
				5 => 'mei',
				6 => 'juni',
				7 => 'juli',
				8 => 'augustus',
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
				3 => 'mrt',
				4 => 'apr',
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
				0 => 'zondag',
				1 => 'maandag',
				2 => 'dinsdag',
				3 => 'woensdag',
				4 => 'donderdag',
				5 => 'vrijdag',
				6 => 'zaterdag'
			),
			'narrow' => array(
				0 => 'Z',
				1 => 'M',
				2 => 'D',
				3 => 'W',
				4 => 'D',
				5 => 'V',
				6 => 'Z'
			),
			'abbreviated' => array(
				0 => 'zo',
				1 => 'ma',
				2 => 'di',
				3 => 'wo',
				4 => 'do',
				5 => 'vr',
				6 => 'za'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1e kwartaal',
				2 => '2e kwartaal',
				3 => '3e kwartaal',
				4 => '4e kwartaal'
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
				0 => 'voor Christus',
				1 => 'na Christus'
			),
			'narrow' => array(
				0 => 'v.C.',
				1 => 'n.C.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM y',
			'short' => 'dd-MM-yy'
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
				'name' => 'Tijdperk'
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
					-3 => 'eereergisteren',
					-2 => 'eergisteren',
					-1 => 'gisteren',
					0 => 'vandaag',
					1 => 'morgen',
					2 => 'overmorgen',
					3 => 'overovermorgen'
				)
			),
			'weekday' => array(
				'name' => 'Dag van de week'
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
				'name' => 'Seconde'
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
		'currencyFormat' => '¤ #,##0.00;¤ #,##0.00-',
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
				'normal' => '{0} d',
				'short' => '{0} dag'
			),
			'other' => array(
				'normal' => '{0} d',
				'short' => '{0} dagen'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} uur',
				'short' => '{0} u'
			),
			'other' => array(
				'normal' => '{0} uur',
				'short' => '{0} u'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} m',
				'short' => '{0} min.'
			),
			'other' => array(
				'normal' => '{0} m',
				'short' => '{0} min.'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} md',
				'short' => '{0} mnd'
			),
			'other' => array(
				'normal' => '{0} md',
				'short' => '{0} mnd'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} s',
				'short' => '{0} sec.'
			),
			'other' => array(
				'normal' => '{0} s',
				'short' => '{0} sec.'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} w',
				'short' => '{0} wk'
			),
			'other' => array(
				'normal' => '{0} w',
				'short' => '{0} wkn'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} jaar',
				'short' => '{0} jr'
			),
			'other' => array(
				'normal' => '{0} jaar',
				'short' => '{0} jr'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja:j',
		'no' => 'nee:n'
	)
);