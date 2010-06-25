<?php
/**
 * Locale: hu
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'hu',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'január',
				2 => 'február',
				3 => 'március',
				4 => 'április',
				5 => 'május',
				6 => 'június',
				7 => 'július',
				8 => 'augusztus',
				9 => 'szeptember',
				10 => 'október',
				11 => 'november',
				12 => 'december'
			),
			'narrow' => array(
				1 => 'J',
				2 => 'F',
				3 => 'M',
				4 => 'Á',
				5 => 'M',
				6 => 'J',
				7 => 'J',
				8 => 'A',
				9 => 'Sz',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'jan.',
				2 => 'febr.',
				3 => 'márc.',
				4 => 'ápr.',
				5 => 'máj.',
				6 => 'jún.',
				7 => 'júl.',
				8 => 'aug.',
				9 => 'szept.',
				10 => 'okt.',
				11 => 'nov.',
				12 => 'dec.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'vasárnap',
				1 => 'hétfő',
				2 => 'kedd',
				3 => 'szerda',
				4 => 'csütörtök',
				5 => 'péntek',
				6 => 'szombat'
			),
			'narrow' => array(
				0 => 'V',
				1 => 'H',
				2 => 'K',
				3 => 'Sz',
				4 => 'Cs',
				5 => 'P',
				6 => 'Sz'
			),
			'abbreviated' => array(
				0 => 'V',
				1 => 'H',
				2 => 'K',
				3 => 'Sze',
				4 => 'Cs',
				5 => 'P',
				6 => 'Szo'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'I. negyedév',
				2 => 'II. negyedév',
				3 => 'III. negyedév',
				4 => 'IV. negyedév'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'N1',
				2 => 'N2',
				3 => 'N3',
				4 => 'N4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'de.',
				'pm' => 'du.'
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
				0 => 'i. e.',
				1 => 'i. sz.'
			),
			'wide' => array(
				0 => 'időszámításunk előtt',
				1 => 'időszámításunk szerint'
			),
			'narrow' => array(
				0 => 'i. e.',
				1 => 'i. sz.'
			)
		),
		'dateFormats' => array(
			'full' => 'y. MMMM d., EEEE',
			'long' => 'y. MMMM d.',
			'medium' => 'yyyy.MM.dd.',
			'short' => 'yyyy.MM.dd.'
		),
		'timeFormats' => array(
			'full' => 'H:mm:ss zzzz',
			'long' => 'H:mm:ss z',
			'medium' => 'H:mm:ss',
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
				'name' => 'éra'
			),
			'year' => array(
				'name' => 'év'
			),
			'month' => array(
				'name' => 'hónap'
			),
			'week' => array(
				'name' => 'hét'
			),
			'day' => array(
				'name' => 'nap',
				'relative' => array(
					-3 => 'három nappal ezelőtt',
					-2 => 'tegnapelőtt',
					-1 => 'tegnap',
					0 => 'ma',
					1 => 'holnap',
					2 => 'holnapután',
					3 => 'három nap múlva'
				)
			),
			'weekday' => array(
				'name' => 'hét napja'
			),
			'dayperiod' => array(
				'name' => 'napszak'
			),
			'hour' => array(
				'name' => 'óra'
			),
			'minute' => array(
				'name' => 'perc'
			),
			'second' => array(
				'name' => 'másodperc'
			),
			'zone' => array(
				'name' => 'zóna'
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
			'ZWD' => 'Z$'
		)
	),
	'units' => array(
		'day' => array(
			'other' => array(
				'normal' => '{0} nap',
				'short' => '{0} nap'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} óra',
				'short' => '{0} ó'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} perc',
				'short' => '{0} p'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} hónap',
				'short' => '{0} hónap'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} másodperc',
				'short' => '{0} mp'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} hét',
				'short' => '{0} hét'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} év',
				'short' => '{0} év'
			)
		)
	),
	'messages' => array(
		'yes' => 'igen:i',
		'no' => 'nem:n'
	)
);