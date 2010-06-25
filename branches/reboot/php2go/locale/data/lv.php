<?php
/**
 * Locale: lv
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'lv',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'janvāris',
				2 => 'februāris',
				3 => 'marts',
				4 => 'aprīlis',
				5 => 'maijs',
				6 => 'jūnijs',
				7 => 'jūlijs',
				8 => 'augusts',
				9 => 'septembris',
				10 => 'oktobris',
				11 => 'novembris',
				12 => 'decembris'
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
				5 => 'mai',
				6 => 'jūn',
				7 => 'jūl',
				8 => 'aug',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'dec'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'svētdiena',
				1 => 'pirmdiena',
				2 => 'otrdiena',
				3 => 'trešdiena',
				4 => 'ceturtdiena',
				5 => 'piektdiena',
				6 => 'sestdiena'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'P',
				2 => 'O',
				3 => 'T',
				4 => 'C',
				5 => 'P',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'Sv',
				1 => 'Pr',
				2 => 'Ot',
				3 => 'Tr',
				4 => 'Ce',
				5 => 'Pk',
				6 => 'Se'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. ceturksnis',
				2 => '2. ceturksnis',
				3 => '3. ceturksnis',
				4 => '4. ceturksnis'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'C1',
				2 => 'C2',
				3 => 'C3',
				4 => 'C4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'priekšpusdienā',
				'pm' => 'pēcpusdienā'
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
				0 => 'p.m.ē.',
				1 => 'm.ē.'
			),
			'wide' => array(
				0 => 'pirms mūsu ēras',
				1 => 'mūsu ērā'
			),
			'narrow' => array(
				0 => 'p.m.ē.',
				1 => 'm.ē.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, y. \'gada\' d. MMMM',
			'long' => 'y. \'gada\' d. MMMM',
			'medium' => 'y. \'gada\' d. MMM',
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
				'name' => 'ēra'
			),
			'year' => array(
				'name' => 'Gads'
			),
			'month' => array(
				'name' => 'Mēnesis'
			),
			'week' => array(
				'name' => 'Nedēļa'
			),
			'day' => array(
				'name' => 'diena',
				'relative' => array(
					-3 => 'aizaizvakar',
					-2 => 'aizvakar',
					-1 => 'vakar',
					0 => 'šodien',
					1 => 'rīt',
					2 => 'parīt',
					3 => 'aizparīt'
				)
			),
			'weekday' => array(
				'name' => 'Nedēļas diena'
			),
			'dayperiod' => array(
				'name' => 'Dayperiod'
			),
			'hour' => array(
				'name' => 'Stundas'
			),
			'minute' => array(
				'name' => 'Minūtes'
			),
			'second' => array(
				'name' => 'Sekundes'
			),
			'zone' => array(
				'name' => 'Josla'
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
			'minusSign' => '−',
			'exponential' => 'E',
			'perMille' => '‰',
			'infinity' => '∞',
			'nan' => 'nav skaitlis'
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
				'normal' => '{0} diena',
				'short' => '{0} d'
			),
			'other' => array(
				'normal' => '{0} dienas',
				'short' => '{0} d'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} stunda',
				'short' => '{0} h'
			),
			'other' => array(
				'normal' => '{0} stundas',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minūte',
				'short' => '{0} min'
			),
			'other' => array(
				'normal' => '{0} minūtes',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mēnesis',
				'short' => '{0} mēn'
			),
			'other' => array(
				'normal' => '{0} mēneši',
				'short' => '{0} mēn'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} sekunde',
				'short' => '{0} s'
			),
			'other' => array(
				'normal' => '{0} sekundes',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} nedēļa',
				'short' => '{0} ned'
			),
			'other' => array(
				'normal' => '{0} nedēļas',
				'short' => '{0} ned'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} gads',
				'short' => '{0} g'
			),
			'other' => array(
				'normal' => '{0} gadi',
				'short' => '{0} g'
			)
		)
	),
	'messages' => array(
		'yes' => 'jā:ja:j',
		'no' => 'nē:ne:n'
	)
);