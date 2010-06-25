<?php
/**
 * Locale: vi
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'vi',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'tháng một',
				2 => 'tháng hai',
				3 => 'tháng ba',
				4 => 'tháng tư',
				5 => 'tháng năm',
				6 => 'tháng sáu',
				7 => 'tháng bảy',
				8 => 'tháng tám',
				9 => 'tháng chín',
				10 => 'tháng mười',
				11 => 'tháng mười một',
				12 => 'tháng mười hai'
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
				1 => 'thg 1',
				2 => 'thg 2',
				3 => 'thg 3',
				4 => 'thg 4',
				5 => 'thg 5',
				6 => 'thg 6',
				7 => 'thg 7',
				8 => 'thg 8',
				9 => 'thg 9',
				10 => 'thg 10',
				11 => 'thg 11',
				12 => 'thg 12'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Chủ nhật',
				1 => 'Thứ hai',
				2 => 'Thứ ba',
				3 => 'Thứ tư',
				4 => 'Thứ năm',
				5 => 'Thứ sáu',
				6 => 'Thứ bảy'
			),
			'narrow' => array(
				0 => 'CN',
				1 => 'T2',
				2 => 'T3',
				3 => 'T4',
				4 => 'T5',
				5 => 'T6',
				6 => 'T7'
			),
			'abbreviated' => array(
				0 => 'CN',
				1 => 'Th 2',
				2 => 'Th 3',
				3 => 'Th 4',
				4 => 'Th 5',
				5 => 'Th 6',
				6 => 'Th 7'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Q1',
				2 => 'Q2',
				3 => 'Q3',
				4 => 'Q4'
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
				'am' => 'SA',
				'pm' => 'CH'
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
				0 => 'tr. CN',
				1 => 'sau CN'
			),
			'wide' => array(
				0 => 'tr. CN',
				1 => 'sau CN'
			),
			'narrow' => array(
				0 => 'tr. CN',
				1 => 'sau CN'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, \'ngày\' dd MMMM \'năm\' y',
			'long' => '\'Ngày\' dd \'tháng\' M \'năm\' y',
			'medium' => 'dd-MM-yyyy',
			'short' => 'dd/MM/yyyy'
		),
		'timeFormats' => array(
			'full' => 'HH:mm:ss zzzz',
			'long' => 'HH:mm:ss z',
			'medium' => 'HH:mm:ss',
			'short' => 'HH:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{0} {1}',
			'long' => '{0} {1}',
			'medium' => '{0} {1}',
			'short' => '{0} {1}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'Era'
			),
			'year' => array(
				'name' => 'Year'
			),
			'month' => array(
				'name' => 'Month'
			),
			'week' => array(
				'name' => 'Week'
			),
			'day' => array(
				'name' => 'Day',
				'relative' => array(
					-1 => 'Yesterday',
					0 => 'Today',
					1 => 'Tomorrow'
				)
			),
			'weekday' => array(
				'name' => 'Day of the Week'
			),
			'dayperiod' => array(
				'name' => 'Dayperiod'
			),
			'hour' => array(
				'name' => 'Hour'
			),
			'minute' => array(
				'name' => 'Minute'
			),
			'second' => array(
				'name' => 'Second'
			),
			'zone' => array(
				'name' => 'Zone'
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
				'normal' => '{0} ngày',
				'short' => '{0} ngày'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} giờ',
				'short' => '{0} g'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} phút',
				'short' => '{0} ph'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} tháng',
				'short' => '{0} tháng'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} giây',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} tuần',
				'short' => '{0} tuần'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} năm',
				'short' => '{0} năm'
			)
		)
	),
	'messages' => array(
		'yes' => 'yes:y',
		'no' => 'no:n'
	)
);