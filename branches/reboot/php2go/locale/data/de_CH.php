<?php
/**
 * Locale: de_CH
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4767',
	'language' => 'de',
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
				8 => 'August',
				9 => 'September',
				10 => 'Oktober',
				11 => 'November',
				12 => 'Dezember'
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
				3 => 'März',
				4 => 'Apr',
				5 => 'Mai',
				6 => 'Jun',
				7 => 'Juli',
				8 => 'Aug.',
				9 => 'Sep.',
				10 => 'Okt.',
				11 => 'Nov.',
				12 => 'Dez.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Sonntag',
				1 => 'Montag',
				2 => 'Dienstag',
				3 => 'Mittwoch',
				4 => 'Donnerstag',
				5 => 'Freitag',
				6 => 'Samstag'
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
				0 => 'So',
				1 => 'Mo',
				2 => 'Di',
				3 => 'Mi',
				4 => 'Do',
				5 => 'Fr',
				6 => 'Sa'
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
				'am' => 'vorm.',
				'pm' => 'nachm.',
				'afternoon' => 'Nachmittag',
				'earlyMorning' => 'Morgen',
				'evening' => 'Abend',
				'morning' => 'Vormittag',
				'night' => 'Nacht',
				'noon' => 'Mittag'
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
				'name' => 'Jahr'
			),
			'month' => array(
				'name' => 'Monat'
			),
			'week' => array(
				'name' => 'Woche'
			),
			'day' => array(
				'name' => 'Tag',
				'relative' => array(
					-3 => 'vorvorgestern',
					-2 => 'vorgestern',
					-1 => 'gestern',
					0 => 'heute',
					1 => 'morgen',
					2 => 'übermorgen',
					3 => 'überübermorgen'
				)
			),
			'weekday' => array(
				'name' => 'Wochentag'
			),
			'dayperiod' => array(
				'name' => 'Tageshälfte'
			),
			'hour' => array(
				'name' => 'Stunde'
			),
			'minute' => array(
				'name' => 'Minute'
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
			'group' => '\'',
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
		'currencyFormat' => '¤ #,##0.00;¤-#,##0.00',
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
				'normal' => '{0} Tag',
				'short' => '{0} Tag'
			),
			'other' => array(
				'normal' => '{0} Tage',
				'short' => '{0} Tage'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} Stunde',
				'short' => '{0} Std.'
			),
			'other' => array(
				'normal' => '{0} Stunden',
				'short' => '{0} Std.'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} Minute',
				'short' => '{0} Min.'
			),
			'other' => array(
				'normal' => '{0} Minuten',
				'short' => '{0} Min.'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} Monat',
				'short' => '{0} Monat'
			),
			'other' => array(
				'normal' => '{0} Monate',
				'short' => '{0} Monate'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} Sekunde',
				'short' => '{0} Sek.'
			),
			'other' => array(
				'normal' => '{0} Sekunden',
				'short' => '{0} Sek.'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} Woche',
				'short' => '{0} Woche'
			),
			'other' => array(
				'normal' => '{0} Wochen',
				'short' => '{0} Wochen'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} Jahr',
				'short' => '{0} Jahr'
			),
			'other' => array(
				'normal' => '{0} Jahre',
				'short' => '{0} Jahre'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja:j',
		'no' => 'nein:n'
	)
);