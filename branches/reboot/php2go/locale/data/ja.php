<?php
/**
 * Locale: ja
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4847',
	'language' => 'ja',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => '1月',
				2 => '2月',
				3 => '3月',
				4 => '4月',
				5 => '5月',
				6 => '6月',
				7 => '7月',
				8 => '8月',
				9 => '9月',
				10 => '10月',
				11 => '11月',
				12 => '12月'
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
				0 => '日曜日',
				1 => '月曜日',
				2 => '火曜日',
				3 => '水曜日',
				4 => '木曜日',
				5 => '金曜日',
				6 => '土曜日'
			),
			'narrow' => array(
				0 => '日',
				1 => '月',
				2 => '火',
				3 => '水',
				4 => '木',
				5 => '金',
				6 => '土'
			),
			'abbreviated' => array(
				0 => '日',
				1 => '月',
				2 => '火',
				3 => '水',
				4 => '木',
				5 => '金',
				6 => '土'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '第1四半期',
				2 => '第2四半期',
				3 => '第3四半期',
				4 => '第4四半期'
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
				'am' => '午前',
				'pm' => '午後'
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
				0 => 'BC',
				1 => 'AD'
			),
			'wide' => array(
				0 => '紀元前',
				1 => '西暦'
			),
			'narrow' => array(
				0 => 'BC',
				1 => 'AD'
			)
		),
		'dateFormats' => array(
			'full' => 'y年M月d日EEEE',
			'long' => 'y年M月d日',
			'medium' => 'yyyy/MM/dd',
			'short' => 'yy/MM/dd'
		),
		'timeFormats' => array(
			'full' => 'H時mm分ss秒 zzzz',
			'long' => 'H:mm:ss z',
			'medium' => 'H:mm:ss',
			'short' => 'H:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1}{0}',
			'long' => '{1}{0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => '時代'
			),
			'year' => array(
				'name' => '年'
			),
			'month' => array(
				'name' => '月'
			),
			'week' => array(
				'name' => '週'
			),
			'day' => array(
				'name' => '日',
				'relative' => array(
					-3 => '3日前',
					-2 => '一昨日',
					-1 => '昨日',
					0 => '今日',
					1 => '明日',
					2 => '明後日',
					3 => '3日後'
				)
			),
			'weekday' => array(
				'name' => '曜日'
			),
			'dayperiod' => array(
				'name' => '午前/午後'
			),
			'hour' => array(
				'name' => '時'
			),
			'minute' => array(
				'name' => '分'
			),
			'second' => array(
				'name' => '秒'
			),
			'zone' => array(
				'name' => 'タイムゾーン'
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
		'currencyFormat' => '¤#,##0.00',
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
			'CNY' => '元',
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
			'JPY' => '￥',
			'KES' => 'Ksh',
			'KMF' => 'CF',
			'KRW' => '￦',
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
				'normal' => '{0} 日',
				'short' => '{0} 日'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} 時間',
				'short' => '{0} 時間'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} 分',
				'short' => '{0} 分'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} か月',
				'short' => '{0} か月'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} 秒',
				'short' => '{0} 秒'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} 週',
				'short' => '{0} 週'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} 年',
				'short' => '{0} 年'
			)
		)
	),
	'messages' => array(
		'yes' => 'はい:ハイ',
		'no' => 'いいえ:イイエ'
	)
);