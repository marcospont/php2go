<?php
/**
 * Locale: ko
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4872',
	'language' => 'ko',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => '1월',
				2 => '2월',
				3 => '3월',
				4 => '4월',
				5 => '5월',
				6 => '6월',
				7 => '7월',
				8 => '8월',
				9 => '9월',
				10 => '10월',
				11 => '11월',
				12 => '12월'
			),
			'narrow' => array(
				1 => '1월',
				2 => '2월',
				3 => '3월',
				4 => '4월',
				5 => '5월',
				6 => '6월',
				7 => '7월',
				8 => '8월',
				9 => '9월',
				10 => '10월',
				11 => '11월',
				12 => '12월'
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
				0 => '일요일',
				1 => '월요일',
				2 => '화요일',
				3 => '수요일',
				4 => '목요일',
				5 => '금요일',
				6 => '토요일'
			),
			'narrow' => array(
				0 => '일',
				1 => '월',
				2 => '화',
				3 => '수',
				4 => '목',
				5 => '금',
				6 => '토'
			),
			'abbreviated' => array(
				0 => '일',
				1 => '월',
				2 => '화',
				3 => '수',
				4 => '목',
				5 => '금',
				6 => '토'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '제 1/4분기',
				2 => '제 2/4분기',
				3 => '제 3/4분기',
				4 => '제 4/4분기'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1분기',
				2 => '2분기',
				3 => '3분기',
				4 => '4분기'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => '오전',
				'pm' => '오후'
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
				0 => '기원전',
				1 => '서기'
			),
			'wide' => array(
				0 => '기원전',
				1 => '서기'
			),
			'narrow' => array(
				0 => '기원전',
				1 => '서기'
			)
		),
		'dateFormats' => array(
			'full' => 'y년 M월 d일 EEEE',
			'long' => 'y년 M월 d일',
			'medium' => 'yyyy. M. d.',
			'short' => 'yy. M. d.'
		),
		'timeFormats' => array(
			'full' => 'a h시 m분 s초 zzzz',
			'long' => 'a h시 m분 s초 z',
			'medium' => 'a h:mm:ss',
			'short' => 'a h:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => '연호'
			),
			'year' => array(
				'name' => '년'
			),
			'month' => array(
				'name' => '월'
			),
			'week' => array(
				'name' => '주'
			),
			'day' => array(
				'name' => '일',
				'relative' => array(
					-3 => '그끄제',
					-2 => '그저께',
					-1 => '어제',
					0 => '오늘',
					1 => '내일',
					2 => '모레',
					3 => '3일후'
				)
			),
			'weekday' => array(
				'name' => '요일'
			),
			'dayperiod' => array(
				'name' => '오전/오후'
			),
			'hour' => array(
				'name' => '시'
			),
			'minute' => array(
				'name' => '분'
			),
			'second' => array(
				'name' => '초'
			),
			'zone' => array(
				'name' => '시간대'
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
				'normal' => '{0}일',
				'short' => '{0}일'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0}시간',
				'short' => '{0}시간'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0}분',
				'short' => '{0}분'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0}개월',
				'short' => '{0}개월'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0}초',
				'short' => '{0}초'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0}주',
				'short' => '{0}주'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0}년',
				'short' => '{0}년'
			)
		)
	),
	'messages' => array(
		'yes' => '예',
		'no' => '아니오'
	)
);