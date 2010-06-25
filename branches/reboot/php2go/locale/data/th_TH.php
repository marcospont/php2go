<?php
/**
 * Locale: th_TH
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'th',
	'territory' => 'TH',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'มกราคม',
				2 => 'กุมภาพันธ์',
				3 => 'มีนาคม',
				4 => 'เมษายน',
				5 => 'พฤษภาคม',
				6 => 'มิถุนายน',
				7 => 'กรกฎาคม',
				8 => 'สิงหาคม',
				9 => 'กันยายน',
				10 => 'ตุลาคม',
				11 => 'พฤศจิกายน',
				12 => 'ธันวาคม'
			),
			'narrow' => array(
				1 => 'ม',
				2 => 'ก',
				3 => 'ม',
				4 => 'ม',
				5 => 'พ',
				6 => 'ม',
				7 => 'ก',
				8 => 'ส',
				9 => 'ก',
				10 => 'ต',
				11 => 'พ',
				12 => 'ธ'
			),
			'abbreviated' => array(
				1 => 'ม.ค.',
				2 => 'ก.พ.',
				3 => 'มี.ค.',
				4 => 'เม.ย.',
				5 => 'พ.ค.',
				6 => 'มิ.ย.',
				7 => 'ก.ค.',
				8 => 'ส.ค.',
				9 => 'ก.ย.',
				10 => 'ต.ค.',
				11 => 'พ.ย.',
				12 => 'ธ.ค.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'วันอาทิตย์',
				1 => 'วันจันทร์',
				2 => 'วันอังคาร',
				3 => 'วันพุธ',
				4 => 'วันพฤหัสบดี',
				5 => 'วันศุกร์',
				6 => 'วันเสาร์'
			),
			'narrow' => array(
				0 => 'อ',
				1 => 'จ',
				2 => 'อ',
				3 => 'พ',
				4 => 'พ',
				5 => 'ศ',
				6 => 'ส'
			),
			'abbreviated' => array(
				0 => 'อา.',
				1 => 'จ.',
				2 => 'อ.',
				3 => 'พ.',
				4 => 'พฤ.',
				5 => 'ศ.',
				6 => 'ส.'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'ไตรมาส 1',
				2 => 'ไตรมาส 2',
				3 => 'ไตรมาส 3',
				4 => 'ไตรมาส 4'
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
				'am' => 'ก่อนเที่ยง',
				'pm' => 'หลังเที่ยง'
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
				0 => 'ก่อน ค.ศ.',
				1 => 'ค.ศ.'
			),
			'wide' => array(
				0 => 'ปีก่อนคริสต์ศักราช',
				1 => 'คริสต์ศักราช'
			),
			'narrow' => array(
				0 => 'ก่อน ค.ศ.',
				1 => 'ค.ศ.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEEที่ d MMMM G y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM y',
			'short' => 'd/M/yyyy'
		),
		'timeFormats' => array(
			'full' => 'H นาฬิกา m นาที ss วินาที zzzz',
			'long' => 'H นาฬิกา m นาที ss วินาที z',
			'medium' => 'H:mm:ss',
			'short' => 'H:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1}, {0}',
			'long' => '{1}, {0}',
			'medium' => '{1}, {0}',
			'short' => '{1}, {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'สมัย'
			),
			'year' => array(
				'name' => 'ปี'
			),
			'month' => array(
				'name' => 'เดือน'
			),
			'week' => array(
				'name' => 'สัปดาห์'
			),
			'day' => array(
				'name' => 'วัน',
				'relative' => array(
					-3 => 'สามวันก่อน',
					-2 => 'เมื่อวานซืน',
					-1 => 'เมื่อวาน',
					0 => 'วันนี้',
					1 => 'พรุ่งนี้',
					2 => 'มะรืนนี้',
					3 => 'สามวันต่อจากนี้'
				)
			),
			'weekday' => array(
				'name' => 'วันในสัปดาห์'
			),
			'dayperiod' => array(
				'name' => 'ช่วงวัน'
			),
			'hour' => array(
				'name' => 'ชั่วโมง'
			),
			'minute' => array(
				'name' => 'นาที'
			),
			'second' => array(
				'name' => 'วินาที'
			),
			'zone' => array(
				'name' => 'เขต'
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
		'currencyFormat' => '¤#,##0.00;¤-#,##0.00',
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
			'other' => array(
				'normal' => '{0} ว.',
				'short' => '{0} ว'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} ชม.',
				'short' => '{0} ชั่วโมง'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} น.',
				'short' => '{0} นา'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} ด.',
				'short' => '{0} ด'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} ว.',
				'short' => '{0} วิ'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} ส.',
				'short' => '{0} สัปดาห์'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} ป.',
				'short' => '{0} ป'
			)
		)
	),
	'messages' => array(
		'yes' => 'ใช่',
		'no' => 'ไม่ใช่'
	)
);