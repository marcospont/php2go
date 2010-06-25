<?php
/**
 * Locale: zh_Hant_HK
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4625',
	'language' => 'zh',
	'territory' => 'HK',
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
				1 => '一月',
				2 => '二月',
				3 => '三月',
				4 => '四月',
				5 => '五月',
				6 => '六月',
				7 => '七月',
				8 => '八月',
				9 => '九月',
				10 => '十月',
				11 => '十一月',
				12 => '十二月'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => '星期日',
				1 => '星期一',
				2 => '星期二',
				3 => '星期三',
				4 => '星期四',
				5 => '星期五',
				6 => '星期六'
			),
			'narrow' => array(
				0 => '日',
				1 => '一',
				2 => '二',
				3 => '三',
				4 => '四',
				5 => '五',
				6 => '六'
			),
			'abbreviated' => array(
				0 => '週日',
				1 => '週一',
				2 => '週二',
				3 => '週三',
				4 => '週四',
				5 => '週五',
				6 => '週六'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '第1季',
				2 => '第2季',
				3 => '第3季',
				4 => '第4季'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1季',
				2 => '2季',
				3 => '3季',
				4 => '4季'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => '上午',
				'pm' => '下午',
				'afternoon' => '下午',
				'earlyMorning' => '清晨',
				'midDay' => '中午',
				'morning' => '上午',
				'night' => '晚上',
				'weeHours' => '凌晨'
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
				0 => '西元前',
				1 => '西元'
			),
			'wide' => array(
				0 => '公元前',
				1 => '公元'
			),
			'narrow' => array(
				0 => '公元前',
				1 => '公元'
			)
		),
		'dateFormats' => array(
			'full' => 'y年M月d日EEEE',
			'long' => 'y年M月d日',
			'medium' => 'y年M月d日',
			'short' => 'yy年M月d日'
		),
		'timeFormats' => array(
			'full' => 'zzzzah時mm分ss秒',
			'long' => 'zah時mm分ss秒',
			'medium' => 'ahh:mm:ss',
			'short' => 'ah:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1}{0}',
			'long' => '{1}{0}',
			'medium' => '{1}{0}',
			'short' => '{1}{0}'
		),
		'fields' => array(
			'era' => array(
				'name' => '年代'
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
					-3 => '大前天',
					-2 => '前天',
					-1 => '昨天',
					0 => '今天',
					1 => '明天',
					2 => '後天',
					3 => '大後天'
				)
			),
			'weekday' => array(
				'name' => '週天'
			),
			'dayperiod' => array(
				'name' => '上午/下午'
			),
			'hour' => array(
				'name' => '小時'
			),
			'minute' => array(
				'name' => '分鐘'
			),
			'second' => array(
				'name' => '秒'
			),
			'zone' => array(
				'name' => '區域'
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
		'currencyFormat' => '¤#,##0.00;(¤#,##0.00)',
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
			'CNY' => '￥',
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
			'HKD' => '$',
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
				'normal' => '{0}天',
				'short' => '{0}天'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0}時'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0}分',
				'short' => '{0}分'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0}個月',
				'short' => '{0}個月'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0}秒',
				'short' => '{0}秒'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0}星期'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0}年',
				'short' => '{0}年'
			)
		)
	),
	'messages' => array(
		'yes' => '是',
		'no' => '否'
	)
);