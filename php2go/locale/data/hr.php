<?php
/**
 * Locale: hr
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'hr',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'siječanj',
				2 => 'veljača',
				3 => 'ožujak',
				4 => 'travanj',
				5 => 'svibanj',
				6 => 'lipanj',
				7 => 'srpanj',
				8 => 'kolovoz',
				9 => 'rujan',
				10 => 'listopad',
				11 => 'studeni',
				12 => 'prosinac'
			),
			'narrow' => array(
				1 => '1.',
				2 => '2.',
				3 => '3.',
				4 => '4.',
				5 => '5.',
				6 => '6.',
				7 => '7.',
				8 => '8.',
				9 => '9.',
				10 => '10.',
				11 => '11.',
				12 => '12.'
			),
			'abbreviated' => array(
				1 => 'sij',
				2 => 'velj',
				3 => 'ožu',
				4 => 'tra',
				5 => 'svi',
				6 => 'lip',
				7 => 'srp',
				8 => 'kol',
				9 => 'ruj',
				10 => 'lis',
				11 => 'stu',
				12 => 'pro'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'nedjelja',
				1 => 'ponedjeljak',
				2 => 'utorak',
				3 => 'srijeda',
				4 => 'četvrtak',
				5 => 'petak',
				6 => 'subota'
			),
			'narrow' => array(
				0 => 'n',
				1 => 'p',
				2 => 'u',
				3 => 's',
				4 => 'č',
				5 => 'p',
				6 => 's'
			),
			'abbreviated' => array(
				0 => 'ned',
				1 => 'pon',
				2 => 'uto',
				3 => 'sri',
				4 => 'čet',
				5 => 'pet',
				6 => 'sub'
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
				1 => '1kv',
				2 => '2kv',
				3 => '3kv',
				4 => '4kv'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'prije podne',
				'pm' => 'poslije podne'
			),
			'abbreviated' => array(
				'am' => 'AM',
				'pm' => 'PM',
				'afternoon' => 'popodne',
				'earlyMorning' => 'ujutro',
				'evening' => 'navečer',
				'morning' => 'prijepodne',
				'night' => 'noću',
				'noon' => 'podne'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'p. n. e.',
				1 => 'A. D.'
			),
			'wide' => array(
				0 => 'Prije Krista',
				1 => 'Poslije Krista'
			),
			'narrow' => array(
				0 => 'pr.n.e.',
				1 => 'AD'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d. MMMM y.',
			'long' => 'd. MMMM y.',
			'medium' => 'd. M. yyyy.',
			'short' => 'dd. MM. yyyy.'
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
				'name' => 'era'
			),
			'year' => array(
				'name' => 'godina'
			),
			'month' => array(
				'name' => 'mjesec'
			),
			'week' => array(
				'name' => 'tjedan'
			),
			'day' => array(
				'name' => 'dan',
				'relative' => array(
					-3 => 'prije tri dana',
					-2 => 'prekjučer',
					-1 => 'jučer',
					0 => 'danas',
					1 => 'sutra',
					2 => 'prekosutra',
					3 => 'za tri dana'
				)
			),
			'weekday' => array(
				'name' => 'dan u tjednu'
			),
			'dayperiod' => array(
				'name' => 'dio dana'
			),
			'hour' => array(
				'name' => 'sat'
			),
			'minute' => array(
				'name' => 'minuta'
			),
			'second' => array(
				'name' => 'sekunda'
			),
			'zone' => array(
				'name' => 'zona'
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
				'normal' => '{0} dana',
				'short' => '{0} dan'
			),
			'one' => array(
				'normal' => '{0} dan',
				'short' => '{0} dan'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} sati',
				'short' => '{0} h'
			),
			'one' => array(
				'normal' => '{0} sat',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} minuta',
				'short' => '{0} min'
			),
			'one' => array(
				'normal' => '{0} minuta',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} mjeseci',
				'short' => '{0} mj.'
			),
			'one' => array(
				'normal' => '{0} mjesec',
				'short' => '{0} mj.'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} sekunda',
				'short' => '{0} s'
			),
			'one' => array(
				'normal' => '{0} sekunda',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} tjedana',
				'short' => '{0} tj.'
			),
			'one' => array(
				'normal' => '{0} tjedan',
				'short' => '{0} tj.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} godina',
				'short' => '{0} g.'
			),
			'one' => array(
				'normal' => '{0} godina',
				'short' => '{0} g.'
			)
		)
	),
	'messages' => array(
		'yes' => 'da:d',
		'no' => 'ne:n'
	)
);