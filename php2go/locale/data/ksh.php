<?php
/**
 * Locale: ksh
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4763',
	'language' => 'ksh',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Jannewa',
				2 => 'Fäbrowa',
				3 => 'Määz',
				4 => 'Aprell',
				5 => 'Mäi',
				6 => 'Juuni',
				7 => 'Juuli',
				8 => 'Oujoß',
				9 => 'Septämber',
				10 => 'Oktoober',
				11 => 'Novämber',
				12 => 'Dezämber'
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
				1 => 'Jan.',
				2 => 'Fäb.',
				3 => 'Mar.',
				4 => 'Apr.',
				5 => 'Mäi',
				6 => 'Jun.',
				7 => 'Jul.',
				8 => 'Oug.',
				9 => 'Säp.',
				10 => 'Okt.',
				11 => 'Nov.',
				12 => 'Dez.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Sunndaach',
				1 => 'Moondaach',
				2 => 'Dinnsdaach',
				3 => 'Metwoch',
				4 => 'Dunnersdaach',
				5 => 'Friidaach',
				6 => 'Samsdaach'
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
				0 => 'Su.',
				1 => 'Mo.',
				2 => 'Di.',
				3 => 'Me.',
				4 => 'Du.',
				5 => 'Fr.',
				6 => 'Sa.'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. Quattaal',
				2 => '2. Quattaal',
				3 => '3. Quattaal',
				4 => '4. Quattaal'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1.Q.',
				2 => '2.Q.',
				3 => '3.Q.',
				4 => '4.Q.'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'Vormittag',
				'pm' => 'Nachmittag'
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
				0 => 'v.Ch.',
				1 => 'A.D.'
			),
			'wide' => array(
				0 => 'vür Chrestus',
				1 => 'noh Chrestus'
			),
			'narrow' => array(
				0 => 'v.Ch.',
				1 => 'A.D.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, \'dä\' d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'd. MMM y',
			'short' => 'd. M. yyyy'
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
				'name' => 'Ära'
			),
			'year' => array(
				'name' => 'Johr'
			),
			'month' => array(
				'name' => 'Mohnd'
			),
			'week' => array(
				'name' => 'Woch'
			),
			'day' => array(
				'name' => 'Daach',
				'relative' => array(
					-3 => 'Vörvörjestere',
					-2 => 'Förjestere',
					-1 => 'Jestere',
					0 => 'Hück',
					1 => 'Morje',
					2 => 'Övvermorje',
					3 => 'Övverövvermorje'
				)
			),
			'weekday' => array(
				'name' => 'Wochedaach'
			),
			'dayperiod' => array(
				'name' => 'v.m./n.m.'
			),
			'hour' => array(
				'name' => 'Schtund'
			),
			'minute' => array(
				'name' => 'Menutt'
			),
			'second' => array(
				'name' => 'Sekund'
			),
			'zone' => array(
				'name' => 'Zickzon'
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
			'other' => array(
				'normal' => '{0} Dääsch'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} Schtunde'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0}  Menutte'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} Mohnde'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} Sekunde'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} Woche'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} Johre'
			)
		)
	),
	'messages' => array(
		'yes' => 'jo:joh:joo:j',
		'no' => 'nä:nää:näh:n'
	)
);