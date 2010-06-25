<?php
/**
 * Locale: trv_TW
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'trv',
	'territory' => 'TW',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Kingal idas',
				2 => 'Dha idas',
				3 => 'Tru idas',
				4 => 'Spat idas',
				5 => 'Rima idas',
				6 => 'Mataru idas',
				7 => 'Empitu idas',
				8 => 'Maspat idas',
				9 => 'Mngari idas',
				10 => 'Maxal idas',
				11 => 'Maxal kingal idas',
				12 => 'Maxal dha idas'
			),
			'narrow' => array(
				1 => 'K',
				2 => 'D',
				3 => 'T',
				4 => 'S',
				5 => 'R',
				6 => 'M',
				7 => 'E',
				8 => 'P',
				9 => 'A',
				10 => 'M',
				11 => 'K',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'Kii',
				2 => 'Dhi',
				3 => 'Tri',
				4 => 'Spi',
				5 => 'Rii',
				6 => 'Mti',
				7 => 'Emi',
				8 => 'Mai',
				9 => 'Mni',
				10 => 'Mxi',
				11 => 'Mxk',
				12 => 'Mxd'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Jiyax sngayan',
				1 => 'tgKingal jiyax iyax sngayan',
				2 => 'tgDha jiyax iyax sngayan',
				3 => 'tgTru jiyax iyax sngayan',
				4 => 'tgSpac jiyax iyax sngayan',
				5 => 'tgRima jiyax iyax sngayan',
				6 => 'tgMataru jiyax iyax sngayan'
			),
			'narrow' => array(
				0 => 'E',
				1 => 'K',
				2 => 'D',
				3 => 'T',
				4 => 'S',
				5 => 'R',
				6 => 'M'
			),
			'abbreviated' => array(
				0 => 'Emp',
				1 => 'Kin',
				2 => 'Dha',
				3 => 'Tru',
				4 => 'Spa',
				5 => 'Rim',
				6 => 'Mat'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'mnprxan',
				2 => 'mndha',
				3 => 'mntru',
				4 => 'mnspat'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'mn1',
				2 => 'mn2',
				3 => 'mn3',
				4 => 'mn4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'Brax kndaax',
				'pm' => 'Baubau kndaax'
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
				0 => 'BRY',
				1 => 'BUY'
			),
			'wide' => array(
				0 => 'Brah jikan Yisu Thulang',
				1 => 'Bukuy jikan Yisu Thulang'
			),
			'narrow' => array(
				0 => 'BRY',
				1 => 'BUY'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, y MMMM dd',
			'long' => 'y MMMM d',
			'medium' => 'y MMM d',
			'short' => 'yyyy-MM-dd'
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
				'name' => 'Hngkawas'
			),
			'year' => array(
				'name' => 'hngkawas'
			),
			'month' => array(
				'name' => 'Idas'
			),
			'week' => array(
				'name' => 'Jiyax iyax sngayan'
			),
			'day' => array(
				'name' => 'Jiyax',
				'relative' => array(
					-1 => 'Shiga',
					0 => 'Jiyax sayang',
					1 => 'Saman'
				)
			),
			'weekday' => array(
				'name' => 'Jiyax quri jiyax iyax sngayan'
			),
			'dayperiod' => array(
				'name' => 'Jikan'
			),
			'hour' => array(
				'name' => 'Tuki'
			),
			'minute' => array(
				'name' => 'Spngan'
			),
			'second' => array(
				'name' => 'Seykn'
			),
			'zone' => array(
				'name' => 'Alang'
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
		'currencyFormat' => '¤ #,##0.00',
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
				'normal' => '{0} Jiyax'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} Tuki'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} spngan'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} Idas'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} Seykn'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} Jiyax iyax sngayan'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} Hnkawas'
			)
		)
	),
	'messages' => array(
		'yes' => 'yiru:y',
		'no' => 'mnan:m'
	)
);