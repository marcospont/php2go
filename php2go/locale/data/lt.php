<?php
/**
 * Locale: lt
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'lt',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'sausis',
				2 => 'vasaris',
				3 => 'kovas',
				4 => 'balandis',
				5 => 'gegužė',
				6 => 'birželis',
				7 => 'liepa',
				8 => 'rugpjūtis',
				9 => 'rugsėjis',
				10 => 'spalis',
				11 => 'lapkritis',
				12 => 'gruodis'
			),
			'narrow' => array(
				1 => 'S',
				2 => 'V',
				3 => 'K',
				4 => 'B',
				5 => 'G',
				6 => 'B',
				7 => 'L',
				8 => 'R',
				9 => 'R',
				10 => 'S',
				11 => 'L',
				12 => 'G'
			),
			'abbreviated' => array(
				1 => 'sau',
				2 => 'Vas',
				3 => 'Kov',
				4 => 'bal',
				5 => 'geg',
				6 => 'bir',
				7 => 'L',
				8 => 'Rgp',
				9 => 'rgs',
				10 => 'Spl',
				11 => 'Lap',
				12 => 'grd'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'sekmadienis',
				1 => 'pirmadienis',
				2 => 'antradienis',
				3 => 'trečiadienis',
				4 => 'ketvirtadienis',
				5 => 'penktadienis',
				6 => 'šeštadienis'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'P',
				2 => 'A',
				3 => 'T',
				4 => 'K',
				5 => 'P',
				6 => 'Š'
			),
			'abbreviated' => array(
				0 => 'Sk',
				1 => 'Pr',
				2 => 'A',
				3 => 'Tr',
				4 => 'Kt',
				5 => 'Pn',
				6 => 'Št'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. ketvirtis',
				2 => '2. ketvirtis',
				3 => '3. ketvirtis',
				4 => '4. ketvirtis'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1K',
				2 => '2K',
				3 => '3K',
				4 => '4K'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'priešpiet',
				'pm' => 'popiet'
			),
			'abbreviated' => array(
				'am' => 'pr.p.',
				'pm' => 'pop.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'pr. Kr.',
				1 => 'po Kr.'
			),
			'wide' => array(
				0 => 'prieš Kristų',
				1 => 'po Kristaus'
			),
			'narrow' => array(
				0 => 'pr. Kr.',
				1 => 'po Kr.'
			)
		),
		'dateFormats' => array(
			'full' => 'y \'m\'. MMMM d \'d\'.,EEEE',
			'long' => 'y \'m\'. MMMM d \'d\'.',
			'medium' => 'yyyy.MM.dd',
			'short' => 'yyyy-MM-dd'
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
				'name' => 'metai'
			),
			'month' => array(
				'name' => 'mėnuo'
			),
			'week' => array(
				'name' => 'savaitė'
			),
			'day' => array(
				'name' => 'diena',
				'relative' => array(
					-3 => 'už užvakar',
					-2 => 'užvakar',
					-1 => 'vakar',
					0 => 'šiandien',
					1 => 'rytoj',
					2 => 'poryt',
					3 => 'užporyt'
				)
			),
			'weekday' => array(
				'name' => 'savaitės diena'
			),
			'dayperiod' => array(
				'name' => 'dienos metas'
			),
			'hour' => array(
				'name' => 'valanda'
			),
			'minute' => array(
				'name' => 'minutė'
			),
			'second' => array(
				'name' => 'sekundė'
			),
			'zone' => array(
				'name' => 'juosta'
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
			'minusSign' => '−',
			'exponential' => '×10^',
			'perMille' => '‰',
			'infinity' => '∞',
			'nan' => '¤¤¤'
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
				'normal' => '{0} dienų',
				'short' => '{0} d.'
			),
			'one' => array(
				'normal' => '{0} diena',
				'short' => '{0} d.'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} valandų',
				'short' => '{0} val.'
			),
			'one' => array(
				'normal' => '{0} valanda',
				'short' => '{0} val.'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} minučių',
				'short' => '{0} min.'
			),
			'one' => array(
				'normal' => '{0} minutė',
				'short' => '{0} min.'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} mėnesių',
				'short' => '{0} mėn.'
			),
			'one' => array(
				'normal' => '{0} mėnuo',
				'short' => '{0} mėn.'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} sekundžių',
				'short' => '{0} sek.'
			),
			'one' => array(
				'normal' => '{0} sekundė',
				'short' => '{0} sek.'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} savaičių',
				'short' => '{0} sav.'
			),
			'one' => array(
				'normal' => '{0} savaitė',
				'short' => '{0} sav.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} metų',
				'short' => '{0} m.'
			),
			'one' => array(
				'normal' => '{0} metai',
				'short' => '{0} m.'
			)
		)
	),
	'messages' => array(
		'yes' => 'taip:t',
		'no' => 'ne:n'
	)
);