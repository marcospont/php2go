<?php
/**
 * Locale: he
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4847',
	'language' => 'he',
	'territory' => '',
	'orientation' => 'rtl',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'ינואר',
				2 => 'פברואר',
				3 => 'מרץ',
				4 => 'אפריל',
				5 => 'מאי',
				6 => 'יוני',
				7 => 'יולי',
				8 => 'אוגוסט',
				9 => 'ספטמבר',
				10 => 'אוקטובר',
				11 => 'נובמבר',
				12 => 'דצמבר'
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
				1 => 'ינו׳',
				2 => 'פבר׳',
				3 => 'מרץ',
				4 => 'אפר׳',
				5 => 'מאי',
				6 => 'יונ׳',
				7 => 'יול׳',
				8 => 'אוג׳',
				9 => 'ספט׳',
				10 => 'אוק׳',
				11 => 'נוב׳',
				12 => 'דצמ׳'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'יום ראשון',
				1 => 'יום שני',
				2 => 'יום שלישי',
				3 => 'יום רביעי',
				4 => 'יום חמישי',
				5 => 'יום שישי',
				6 => 'יום שבת'
			),
			'narrow' => array(
				0 => 'א׳',
				1 => 'ב׳',
				2 => 'ג׳',
				3 => 'ד׳',
				4 => 'ה׳',
				5 => 'ו׳',
				6 => 'ש׳'
			),
			'abbreviated' => array(
				0 => 'יום א׳',
				1 => 'יום ב׳',
				2 => 'יום ג׳',
				3 => 'יום ד׳',
				4 => 'יום ה׳',
				5 => 'יום ו׳',
				6 => 'שבת'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'רבעון 1',
				2 => 'רבעון 2',
				3 => 'רבעון 3',
				4 => 'רבעון 4'
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
				'am' => 'לפנהצ',
				'pm' => 'אחהצ'
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
				0 => 'לפנה״ס',
				1 => 'לסה״נ'
			),
			'wide' => array(
				0 => 'לפני הספירה',
				1 => 'לספירה'
			),
			'narrow' => array(
				0 => 'לפנה״ס',
				1 => 'לסה״נ'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d בMMMM y',
			'long' => 'd בMMMM y',
			'medium' => 'd בMMM yyyy',
			'short' => 'dd/MM/yy'
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
				'name' => 'תקופה'
			),
			'year' => array(
				'name' => 'שנה'
			),
			'month' => array(
				'name' => 'חודש'
			),
			'week' => array(
				'name' => 'שבוע'
			),
			'day' => array(
				'name' => 'יום',
				'relative' => array(
					-3 => 'לפני שלושה ימים',
					-2 => 'שלשום',
					-1 => 'אתמול',
					0 => 'היום',
					1 => 'מחר',
					2 => 'מחרתיים',
					3 => 'בעוד שלושה ימים'
				)
			),
			'weekday' => array(
				'name' => 'יום בשבוע'
			),
			'dayperiod' => array(
				'name' => 'לפה״צ/אחה״צ'
			),
			'hour' => array(
				'name' => 'שעה'
			),
			'minute' => array(
				'name' => 'דקה'
			),
			'second' => array(
				'name' => 'שנייה'
			),
			'zone' => array(
				'name' => 'אזור'
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
			'ILP' => 'ל״י',
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
				'normal' => '{0} יום',
				'short' => '{0} יום'
			),
			'other' => array(
				'normal' => '{0} ימים',
				'short' => '{0} ימים'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} שעה',
				'short' => '{0} שעה'
			),
			'other' => array(
				'normal' => '{0} שעות',
				'short' => '{0} שעות'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} דקה',
				'short' => '{0} דק׳'
			),
			'other' => array(
				'normal' => '{0} דקות',
				'short' => '{0} דק׳'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} חודש',
				'short' => '{0} חודש'
			),
			'other' => array(
				'normal' => '{0} חודשים',
				'short' => '{0} חודשים'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} שניה',
				'short' => '{0} שנ׳'
			),
			'other' => array(
				'normal' => '{0} שניות',
				'short' => '{0} שנ׳'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} שבוע',
				'short' => '{0} שבוע'
			),
			'other' => array(
				'normal' => '{0} שבועות',
				'short' => '{0} שבועות'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} שנה',
				'short' => '{0} שנה'
			),
			'other' => array(
				'normal' => '{0} שנים',
				'short' => '{0} שנים'
			)
		)
	),
	'messages' => array(
		'yes' => 'כן:כ',
		'no' => 'לא:ל'
	)
);