<?php
/**
 * Locale: ee
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4753',
	'language' => 'ee',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Dzove',
				2 => 'Dzodze',
				3 => 'Tedoxe',
				4 => 'Afɔfiɛ',
				5 => 'Dama',
				6 => 'Masa',
				7 => 'Siamlɔm',
				8 => 'Deasiamime',
				9 => 'Anyɔnyɔ',
				10 => 'Kele',
				11 => 'Adeɛmekpɔxe',
				12 => 'Dzome'
			),
			'narrow' => array(
				1 => 'D',
				2 => 'D',
				3 => 'T',
				4 => 'A',
				5 => 'D',
				6 => 'M',
				7 => 'S',
				8 => 'D',
				9 => 'A',
				10 => 'K',
				11 => 'A',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'Dzv',
				2 => 'Dzd',
				3 => 'Ted',
				4 => 'Afɔ',
				5 => 'Dam',
				6 => 'Mas',
				7 => 'Sia',
				8 => 'Dea',
				9 => 'Any',
				10 => 'Kel',
				11 => 'Ade',
				12 => 'Dzm'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Kɔsiɖa',
				1 => 'Dzoɖa',
				2 => 'Braɖa',
				3 => 'Kuɖa',
				4 => 'Yawoɖa',
				5 => 'Fiɖa',
				6 => 'Memleɖa'
			),
			'narrow' => array(
				0 => 'K',
				1 => 'D',
				2 => 'B',
				3 => 'K',
				4 => 'Y',
				5 => 'F',
				6 => 'M'
			),
			'abbreviated' => array(
				0 => 'Kɔs Kwe',
				1 => 'Dzo',
				2 => 'Bra',
				3 => 'Kuɖ',
				4 => 'Yaw',
				5 => 'Fiɖ',
				6 => 'Mem'
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
				'am' => 'AN',
				'pm' => 'EW'
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
				0 => 'HY',
				1 => 'YŊ'
			),
			'wide' => array(
				0 => 'Hafi Yesu Va Do ŋgɔ',
				1 => 'Yesu Ŋɔli'
			),
			'narrow' => array(
				0 => 'HY',
				1 => 'YŊ'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM y',
			'short' => 'dd/MM/yyyy'
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
				'name' => 'Ɛra'
			),
			'year' => array(
				'name' => 'Efe'
			),
			'month' => array(
				'name' => 'Xleti'
			),
			'week' => array(
				'name' => 'Kwasida deka'
			),
			'day' => array(
				'name' => 'Ŋkɛkɛ',
				'relative' => array(
					-1 => 'Nyitsɔ',
					0 => 'Egbe',
					1 => 'Etsɔ'
				)
			),
			'weekday' => array(
				'name' => 'Ŋkɛkɛ'
			),
			'dayperiod' => array(
				'name' => 'Ŋkɛkɛ'
			),
			'hour' => array(
				'name' => 'Gaƒoƒo'
			),
			'minute' => array(
				'name' => 'Mintigaƒoƒo'
			),
			'second' => array(
				'name' => 'Sekondgaƒoƒo'
			),
			'zone' => array(
				'name' => 'Gamɛ'
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
		'yes' => 'ɛ:Ɛ',
		'no' => 'ao:A'
	)
);