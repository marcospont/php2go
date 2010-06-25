<?php
/**
 * Locale: fa
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4772',
	'language' => 'fa',
	'territory' => '',
	'orientation' => 'rtl',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'ژانویه',
				2 => 'فوریه',
				3 => 'مارس',
				4 => 'آوریل',
				5 => 'مه',
				6 => 'ژوئن',
				7 => 'ژوئیه',
				8 => 'اوت',
				9 => 'سپتامبر',
				10 => 'اکتبر',
				11 => 'نوامبر',
				12 => 'دسامبر'
			),
			'narrow' => array(
				1 => 'ژ',
				2 => 'ف',
				3 => 'م',
				4 => 'آ',
				5 => 'می',
				6 => 'ژ',
				7 => 'ژ',
				8 => 'ا',
				9 => 'س',
				10 => 'ا',
				11 => 'ن',
				12 => 'د'
			),
			'abbreviated' => array(
				1 => 'ژانویهٔ',
				2 => 'فوریهٔ',
				3 => 'مارس',
				4 => 'آوریل',
				5 => 'می',
				6 => 'جون',
				7 => 'جولای',
				8 => 'اوت',
				9 => 'سپتامبر',
				10 => 'اکتبر',
				11 => 'نوامبر',
				12 => 'دسامبر'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'یکشنبه',
				1 => 'دوشنبه',
				2 => 'سه‌شنبه',
				3 => 'چهارشنبه',
				4 => 'پنجشنبه',
				5 => 'جمعه',
				6 => 'شنبه'
			),
			'narrow' => array(
				0 => 'ی',
				1 => 'د',
				2 => 'س',
				3 => 'چ',
				4 => 'پ',
				5 => 'ج',
				6 => 'ش'
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
				1 => 'سه‌ماههٔ اول',
				2 => 'سه‌ماههٔ دوم',
				3 => 'سه‌ماههٔ سوم',
				4 => 'سه‌ماههٔ چهارم'
			),
			'narrow' => array(
				1 => '۱',
				2 => '۲',
				3 => '۳',
				4 => '۴'
			),
			'abbreviated' => array(
				1 => 'س‌م۱',
				2 => 'س‌م۲',
				3 => 'س‌م۳',
				4 => 'س‌م۴'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'قبل از ظهر',
				'pm' => 'بعد از ظهر'
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
				0 => 'ق.م.',
				1 => 'ب. م.'
			),
			'wide' => array(
				0 => 'قبل از میلاد',
				1 => 'میلادی'
			),
			'narrow' => array(
				0 => 'ق',
				1 => 'م'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, MMMM d, y',
			'long' => 'MMMM d, y',
			'medium' => 'MMM d, y',
			'short' => 'M/d/yy'
		),
		'timeFormats' => array(
			'full' => 'H:mm:ss (zzzz)',
			'long' => 'H:mm:ss (z)',
			'medium' => 'H:mm:ss',
			'short' => 'H:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1}، ساعت {0}',
			'long' => '{1}، ساعت {0}',
			'medium' => '{1}،‏ {0}',
			'short' => '{1}،‏ {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'دوره'
			),
			'year' => array(
				'name' => 'سال'
			),
			'month' => array(
				'name' => 'ماه'
			),
			'week' => array(
				'name' => 'هفته'
			),
			'day' => array(
				'name' => 'روز',
				'relative' => array(
					-3 => 'سه روز پیش',
					-2 => 'پریروز',
					-1 => 'دیروز',
					0 => 'امروز',
					1 => 'فردا',
					2 => 'پس‌فردا',
					3 => 'سه روز بعد'
				)
			),
			'weekday' => array(
				'name' => 'روز هفته'
			),
			'dayperiod' => array(
				'name' => 'قبل/بعد از ظهر'
			),
			'hour' => array(
				'name' => 'ساعت'
			),
			'minute' => array(
				'name' => 'دقیقه'
			),
			'second' => array(
				'name' => 'ثانیه'
			),
			'zone' => array(
				'name' => 'منطقهٔ زمانی'
			)
		)
	),
	'numbers' => array(
		'defaultNumberingSystem' => 'arabext',
		'symbols' => array(
			'decimal' => '/',
			'group' => '،',
			'list' => ';',
			'percentSign' => '%',
			'nativeZeroDigit' => '0',
			'patternDigit' => '#',
			'plusSign' => '+',
			'minusSign' => '−',
			'exponential' => 'E',
			'perMille' => '‰',
			'infinity' => '∞',
			'nan' => 'NaN'
		),
		'decimalFormat' => '#,##0.###;\'‪\'-#,##0.###\'‬\'',
		'scientificFormat' => '#E0',
		'percentFormat' => '\'‪\'%#,##0\'‬\'',
		'currencyFormat' => '#,##0.00 ¤;\'‪\'-#,##0.00\'‬\' ¤',
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
			'ZWD' => 'Z$',
			'IRR' => '﷼'
		)
	),
	'units' => array(
		'day' => array(
			'other' => array(
				'normal' => '{0} روز'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} ساعت'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} دقیقه'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} ماه'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} ثانیه'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} هفته'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} سال'
			)
		)
	),
	'messages' => array(
		'yes' => 'بله:ب:آری:آ',
		'no' => 'نه:ن:خیر:خ'
	)
);