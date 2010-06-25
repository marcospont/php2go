<?php
/**
 * Locale: ar_MA
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4850',
	'language' => 'ar',
	'territory' => 'MA',
	'orientation' => 'rtl',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'يناير',
				2 => 'فبراير',
				3 => 'مارس',
				4 => 'أبريل',
				5 => 'مايو',
				6 => 'يونيو',
				7 => 'يوليو',
				8 => 'أغسطس',
				9 => 'سبتمبر',
				10 => 'أكتوبر',
				11 => 'نوفمبر',
				12 => 'ديسمبر'
			),
			'narrow' => array(
				1 => 'ي',
				2 => 'ف',
				3 => 'م',
				4 => 'أ',
				5 => 'و',
				6 => 'ن',
				7 => 'ل',
				8 => 'غ',
				9 => 'س',
				10 => 'ك',
				11 => 'ب',
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
				0 => 'الأحد',
				1 => 'الإثنين',
				2 => 'الثلاثاء',
				3 => 'الأربعاء',
				4 => 'الخميس',
				5 => 'الجمعة',
				6 => 'السبت'
			),
			'narrow' => array(
				0 => 'ح',
				1 => 'ن',
				2 => 'ث',
				3 => 'ر',
				4 => 'خ',
				5 => 'ج',
				6 => 'س'
			),
			'abbreviated' => array(
				0 => 'أحد',
				1 => 'إثنين',
				2 => 'ثلاثاء',
				3 => 'أربعاء',
				4 => 'خميس',
				5 => 'جمعة',
				6 => 'سبت'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'الربع الأول',
				2 => 'الربع الثاني',
				3 => 'الربع الثالث',
				4 => 'الربع الرابع'
			),
			'narrow' => array(
				1 => '١',
				2 => '٢',
				3 => '٣',
				4 => '٤'
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
				'am' => 'ص',
				'pm' => 'م'
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
				0 => 'ق.م',
				1 => 'م'
			),
			'wide' => array(
				0 => 'قبل الميلاد',
				1 => 'ميلادي'
			),
			'narrow' => array(
				0 => 'ق.م',
				1 => 'م'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE، d MMMM، y',
			'long' => 'd MMMM، y',
			'medium' => 'yyyy/MM/dd',
			'short' => 'yyyy/M/d'
		),
		'timeFormats' => array(
			'full' => 'zzzz h:mm:ss a',
			'long' => 'z h:mm:ss a',
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
				'name' => 'العصر'
			),
			'year' => array(
				'name' => 'السنة'
			),
			'month' => array(
				'name' => 'الشهر'
			),
			'week' => array(
				'name' => 'الأسبوع'
			),
			'day' => array(
				'name' => 'يوم',
				'relative' => array(
					-1 => 'أمس',
					0 => 'اليوم',
					1 => 'غدًا',
					2 => 'بعد الغد'
				)
			),
			'weekday' => array(
				'name' => 'اليوم'
			),
			'dayperiod' => array(
				'name' => 'ص/م'
			),
			'hour' => array(
				'name' => 'الساعات'
			),
			'minute' => array(
				'name' => 'الدقائق'
			),
			'second' => array(
				'name' => 'الثواني'
			),
			'zone' => array(
				'name' => 'التوقيت'
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
		'decimalFormat' => '#,##0.###;#,##0.###-',
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
			'BHD' => 'د.ب.‏',
			'BIF' => 'FBu',
			'BMD' => 'BD$',
			'BND' => 'BN$',
			'BOB' => 'Bs',
			'BOP' => '$b.',
			'BRL' => 'ر.ب.‏',
			'BSD' => 'BS$',
			'BTN' => 'Nu.',
			'BWP' => 'BWP',
			'BZD' => 'BZ$',
			'CAD' => 'CA$',
			'CDF' => 'CDF',
			'CLE' => 'Eº',
			'CLP' => 'CL$',
			'CNY' => 'ي.ص',
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
			'DZD' => 'د.ج.‏',
			'EEK' => 'Ekr',
			'EGP' => 'ج.م.‏',
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
			'INR' => 'ر.ه.‏',
			'ISK' => 'Ikr',
			'ITL' => 'IT₤',
			'JMD' => 'J$',
			'JOD' => 'د.أ.‏',
			'JPY' => 'JP¥',
			'KES' => 'Ksh',
			'KMF' => 'ف.ج.ق.‏',
			'KRW' => '₩',
			'KWD' => 'د.ك.‏',
			'KYD' => 'KY$',
			'LAK' => '₭',
			'LBP' => 'ل.ل.‏',
			'LKR' => 'SLRs',
			'LRD' => 'L$',
			'LSL' => 'LSL',
			'LTL' => 'Lt',
			'LVL' => 'Ls',
			'LYD' => 'د.ل.‏',
			'MMK' => 'MMK',
			'MNT' => '₮',
			'MOP' => 'MOP$',
			'MRO' => 'أ.م.‏',
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
			'QAR' => 'ر.ق.‏',
			'RHD' => 'RH$',
			'RON' => 'RON',
			'RSD' => 'din.',
			'SAR' => 'ر.س.‏',
			'SBD' => 'SI$',
			'SCR' => 'SRe',
			'SDD' => 'د.س.‏',
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
			'SYP' => 'ل.س.‏',
			'SZL' => 'SZL',
			'THB' => '฿',
			'TMM' => 'TMM',
			'TND' => 'د.ت.‏',
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
			'XAF' => 'ف.ا.‏',
			'XCD' => 'EC$',
			'XOF' => 'CFA',
			'XPF' => 'CFPF',
			'YER' => 'ر.ي.‏',
			'ZAR' => 'R',
			'ZMK' => 'ZK',
			'ZRN' => 'NZ',
			'ZRZ' => 'ZRZ',
			'ZWD' => 'Z$',
			'AED' => 'د.إ.‏',
			'IQD' => 'د.ع.‏',
			'MAD' => 'د.م.‏',
			'OMR' => 'ر.ع.‏',
			'RUB' => 'ر.ر.‏',
			'SDP' => 'ج.س.‏',
			'XXX' => '***'
		)
	),
	'units' => array(
		'day' => array(
			'other' => array(
				'normal' => 'لا أيام',
				'short' => 'لا أيام'
			),
			'one' => array(
				'normal' => 'يوم',
				'short' => 'يوم'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => 'لا ساعات',
				'short' => 'لا ساعات'
			),
			'one' => array(
				'normal' => 'ساعة',
				'short' => 'ساعة'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => 'لا دقائق',
				'short' => 'لا دقائق'
			),
			'one' => array(
				'normal' => 'دقيقة',
				'short' => 'دقيقة'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => 'لا أشهر',
				'short' => 'لا أشهر'
			),
			'one' => array(
				'normal' => 'شهر',
				'short' => 'شهر'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => 'لا ثوان',
				'short' => 'لا ثوان'
			),
			'one' => array(
				'normal' => 'ثانية',
				'short' => 'ثانية'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => 'لا أسابيع',
				'short' => 'لا أسابيع'
			),
			'one' => array(
				'normal' => 'أسبوع',
				'short' => 'أسبوع'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => 'لا سنوات',
				'short' => 'لا سنوات'
			),
			'one' => array(
				'normal' => 'سنة',
				'short' => 'سنة'
			)
		)
	),
	'messages' => array(
		'yes' => 'نعم:ن',
		'no' => 'لا:ل'
	)
);