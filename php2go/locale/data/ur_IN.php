<?php
/**
 * Locale: ur_IN
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'ur',
	'territory' => 'IN',
	'orientation' => 'rtl',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'جنوری',
				2 => 'فروری',
				3 => 'مار چ',
				4 => 'اپريل',
				5 => 'مئ',
				6 => 'جون',
				7 => 'جولائ',
				8 => 'اگست',
				9 => 'ستمبر',
				10 => 'اکتوبر',
				11 => 'نومبر',
				12 => 'دسمبر'
			),
			'narrow' => array(
				1 => 'ج',
				2 => 'ف',
				3 => 'م',
				4 => 'ا',
				5 => 'م',
				6 => 'ج',
				7 => 'ج',
				8 => 'ا',
				9 => 'س',
				10 => 'ا',
				11 => 'ن',
				12 => 'د'
			),
			'abbreviated' => array(
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
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'اتوار',
				1 => 'پير',
				2 => 'منگل',
				3 => 'بده',
				4 => 'جمعرات',
				5 => 'جمعہ',
				6 => 'ہفتہ'
			),
			'narrow' => array(
				0 => 'ا',
				1 => 'پ',
				2 => 'م',
				3 => 'ب',
				4 => 'ج',
				5 => 'ج',
				6 => 'ہ'
			),
			'abbreviated' => array(
				0 => '1',
				1 => '2',
				2 => '3',
				3 => '4',
				4 => '5',
				5 => '6',
				6 => '7'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'پہلی سہ ماہی',
				2 => 'دوسری سہ ماہی',
				3 => 'تيسری سہ ماہی',
				4 => 'چوتهی سہ ماہی'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1سہ ماہی',
				2 => '2سہ ماہی',
				3 => '3سہ ماہی',
				4 => '4سہ ماہی'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'قبل دوپہر',
				'pm' => 'بعد دوپہر'
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
				0 => 'ق م',
				1 => 'عيسوی سن'
			),
			'wide' => array(
				0 => 'قبل مسيح',
				1 => 'عيسوی سن'
			),
			'narrow' => array(
				0 => 'ق م',
				1 => 'عيسوی سن'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d, MMMM y',
			'long' => 'd, MMMM y',
			'medium' => 'd, MMM y',
			'short' => 'd/M/yy'
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
				'name' => 'دور'
			),
			'year' => array(
				'name' => 'سال'
			),
			'month' => array(
				'name' => 'مہینہ'
			),
			'week' => array(
				'name' => 'ہفتہ'
			),
			'day' => array(
				'name' => 'دن',
				'relative' => array(
					-3 => 'ترسوں',
					-2 => 'پرسوں',
					-1 => 'کل',
					0 => 'آج',
					1 => 'کل',
					2 => 'پرسوں',
					3 => 'ترسوں'
				)
			),
			'weekday' => array(
				'name' => 'ہفتے کا دن'
			),
			'dayperiod' => array(
				'name' => 'رات/صبح'
			),
			'hour' => array(
				'name' => 'گھنٹہ'
			),
			'minute' => array(
				'name' => 'منٹ'
			),
			'second' => array(
				'name' => 'سیکنڈ'
			),
			'zone' => array(
				'name' => 'زون'
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
		'decimalFormat' => '#,##,##0.###',
		'scientificFormat' => '#E0',
		'percentFormat' => '#,##,##0%',
		'currencyFormat' => '¤ #,##,##0.00',
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
			'PKR' => 'روپے',
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
			'USD' => 'ڈالر',
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
				'normal' => '{0} d'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} h'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} min'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} m'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} s'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} w'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} y'
			)
		)
	),
	'messages' => array(
		'yes' => 'yes:y',
		'no' => 'no:n'
	)
);