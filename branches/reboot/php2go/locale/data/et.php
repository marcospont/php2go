<?php
/**
 * Locale: et
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'et',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'jaanuar',
				2 => 'veebruar',
				3 => 'märts',
				4 => 'aprill',
				5 => 'mai',
				6 => 'juuni',
				7 => 'juuli',
				8 => 'august',
				9 => 'september',
				10 => 'oktoober',
				11 => 'november',
				12 => 'detsember'
			),
			'narrow' => array(
				1 => 'J',
				2 => 'V',
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
				1 => 'jaan',
				2 => 'veebr',
				3 => 'märts',
				4 => 'apr',
				5 => 'mai',
				6 => 'juuni',
				7 => 'juuli',
				8 => 'aug',
				9 => 'sept',
				10 => 'okt',
				11 => 'nov',
				12 => 'dets'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'pühapäev',
				1 => 'esmaspäev',
				2 => 'teisipäev',
				3 => 'kolmapäev',
				4 => 'neljapäev',
				5 => 'reede',
				6 => 'laupäev'
			),
			'narrow' => array(
				0 => 'P',
				1 => 'E',
				2 => 'T',
				3 => 'K',
				4 => 'N',
				5 => 'R',
				6 => 'L'
			),
			'abbreviated' => array(
				0 => 'püh',
				1 => 'esm',
				2 => 'tei',
				3 => 'kol',
				4 => 'nel',
				5 => 'ree',
				6 => 'lau'
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
				'am' => 'enne keskpäeva',
				'pm' => 'pärast keskpäeva'
			),
			'abbreviated' => array(
				'am' => 'e.k.',
				'pm' => 'p.k.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'e.m.a.',
				1 => 'm.a.j.'
			),
			'wide' => array(
				0 => 'enne meie aega',
				1 => 'meie aja järgi'
			),
			'narrow' => array(
				0 => 'e.m.a.',
				1 => 'm.a.j.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'dd.MM.yyyy',
			'short' => 'dd.MM.yy'
		),
		'timeFormats' => array(
			'full' => 'H:mm.ss zzzz',
			'long' => 'H:mm.ss z',
			'medium' => 'H:mm.ss',
			'short' => 'H:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'ajastu'
			),
			'year' => array(
				'name' => 'aasta'
			),
			'month' => array(
				'name' => 'kuu'
			),
			'week' => array(
				'name' => 'nädal'
			),
			'day' => array(
				'name' => 'päev',
				'relative' => array(
					-3 => 'üleüleeile',
					-2 => 'üleeile',
					-1 => 'eile',
					0 => 'täna',
					1 => 'homme',
					2 => 'ülehomme',
					3 => 'üleülehomme'
				)
			),
			'weekday' => array(
				'name' => 'nädalapäev'
			),
			'dayperiod' => array(
				'name' => 'enne/pärast lõunat'
			),
			'hour' => array(
				'name' => 'tund'
			),
			'minute' => array(
				'name' => 'minut'
			),
			'second' => array(
				'name' => 'sekund'
			),
			'zone' => array(
				'name' => 'vöönd'
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
			'CZK' => 'CZK',
			'DEM' => 'DM',
			'DJF' => 'Fdj',
			'DKK' => 'DKK',
			'DOP' => 'RD$',
			'DZD' => 'DA',
			'EEK' => 'kr',
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
			'NOK' => 'NOK',
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
			'SEK' => 'SEK',
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
				'normal' => '{0} ööpäev'
			),
			'other' => array(
				'normal' => '{0} ööpäeva'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} tund'
			),
			'other' => array(
				'normal' => '{0} tundi'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minut'
			),
			'other' => array(
				'normal' => '{0} minutit'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} kuu'
			),
			'other' => array(
				'normal' => '{0} kuud'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} sekund'
			),
			'other' => array(
				'normal' => '{0} sekundit'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} nädal'
			),
			'other' => array(
				'normal' => '{0} nädalat'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} aasta'
			),
			'other' => array(
				'normal' => '{0} aastat'
			)
		)
	),
	'messages' => array(
		'yes' => 'jah:j',
		'no' => 'ei:e'
	)
);