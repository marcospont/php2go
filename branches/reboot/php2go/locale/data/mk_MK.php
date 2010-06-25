<?php
/**
 * Locale: mk_MK
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'mk',
	'territory' => 'MK',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'јануари',
				2 => 'февруари',
				3 => 'март',
				4 => 'април',
				5 => 'мај',
				6 => 'јуни',
				7 => 'јули',
				8 => 'август',
				9 => 'септември',
				10 => 'октомври',
				11 => 'ноември',
				12 => 'декември'
			),
			'narrow' => array(
				1 => 'ј',
				2 => 'ф',
				3 => 'м',
				4 => 'а',
				5 => 'м',
				6 => 'ј',
				7 => 'ј',
				8 => 'а',
				9 => 'с',
				10 => 'о',
				11 => 'н',
				12 => 'д'
			),
			'abbreviated' => array(
				1 => 'јан.',
				2 => 'фев.',
				3 => 'мар.',
				4 => 'апр.',
				5 => 'мај',
				6 => 'јун.',
				7 => 'јул.',
				8 => 'авг.',
				9 => 'септ.',
				10 => 'окт.',
				11 => 'ноем.',
				12 => 'декем.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'недела',
				1 => 'понеделник',
				2 => 'вторник',
				3 => 'среда',
				4 => 'четврток',
				5 => 'петок',
				6 => 'сабота'
			),
			'narrow' => array(
				0 => 'н',
				1 => 'п',
				2 => 'в',
				3 => 'с',
				4 => 'ч',
				5 => 'п',
				6 => 'с'
			),
			'abbreviated' => array(
				0 => 'нед.',
				1 => 'пон.',
				2 => 'вт.',
				3 => 'сре.',
				4 => 'чет.',
				5 => 'пет.',
				6 => 'саб.'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'прво тромесечје',
				2 => 'второ тромесечје',
				3 => 'трето тромесечје',
				4 => 'четврто тромесечје'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'јан-мар',
				2 => 'апр-јун',
				3 => 'јул-сеп',
				4 => 'окт-дек'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'претпладне',
				'pm' => 'попладне'
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
				0 => 'пр.н.е.',
				1 => 'ае.'
			),
			'wide' => array(
				0 => 'пр.н.е.',
				1 => 'ае.'
			),
			'narrow' => array(
				0 => 'пр.н.е.',
				1 => 'ае.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, dd MMMM y',
			'long' => 'dd MMMM y',
			'medium' => 'dd.M.yyyy',
			'short' => 'dd.M.yy'
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
				'name' => 'Ера'
			),
			'year' => array(
				'name' => 'година'
			),
			'month' => array(
				'name' => 'Месец'
			),
			'week' => array(
				'name' => 'Недела'
			),
			'day' => array(
				'name' => 'ден',
				'relative' => array(
					-3 => 'пред три дена',
					-2 => 'завчера',
					-1 => 'Вчера',
					0 => 'Денес',
					1 => 'Утре',
					2 => 'задутре',
					3 => 'по три дена'
				)
			),
			'weekday' => array(
				'name' => 'Ден во неделата'
			),
			'dayperiod' => array(
				'name' => 'претпладне/попладне'
			),
			'hour' => array(
				'name' => 'Час'
			),
			'minute' => array(
				'name' => 'Минута'
			),
			'second' => array(
				'name' => 'Секунда'
			),
			'zone' => array(
				'name' => 'зона'
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
			'one' => array(
				'normal' => '{0} ден'
			),
			'other' => array(
				'normal' => '{0} денови'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} час'
			),
			'other' => array(
				'normal' => '{0} часови'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} минута'
			),
			'other' => array(
				'normal' => '{0} минути'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} месец'
			),
			'other' => array(
				'normal' => '{0} месеци'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} секунда'
			),
			'other' => array(
				'normal' => '{0} секунди'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} недела'
			),
			'other' => array(
				'normal' => '{0} недели'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} година'
			),
			'other' => array(
				'normal' => '{0} години'
			)
		)
	),
	'messages' => array(
		'yes' => 'да:д',
		'no' => 'не:н'
	)
);