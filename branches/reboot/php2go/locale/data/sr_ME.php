<?php
/**
 * Locale: sr_ME
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4165',
	'language' => 'sr',
	'territory' => 'ME',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'јануар',
				2 => 'фебруар',
				3 => 'март',
				4 => 'април',
				5 => 'мај',
				6 => 'јун',
				7 => 'јул',
				8 => 'август',
				9 => 'септембар',
				10 => 'октобар',
				11 => 'новембар',
				12 => 'децембар'
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
				1 => 'јан',
				2 => 'феб',
				3 => 'мар',
				4 => 'апр',
				5 => 'мај',
				6 => 'јун',
				7 => 'јул',
				8 => 'авг',
				9 => 'сеп',
				10 => 'окт',
				11 => 'нов',
				12 => 'дец'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'недеља',
				1 => 'понедељак',
				2 => 'уторак',
				3 => 'среда',
				4 => 'четвртак',
				5 => 'петак',
				6 => 'субота'
			),
			'narrow' => array(
				0 => 'н',
				1 => 'п',
				2 => 'у',
				3 => 'с',
				4 => 'ч',
				5 => 'п',
				6 => 'с'
			),
			'abbreviated' => array(
				0 => 'нед',
				1 => 'пон',
				2 => 'уто',
				3 => 'сре',
				4 => 'чет',
				5 => 'пет',
				6 => 'суб'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Прво тромесечје',
				2 => 'Друго тромесечје',
				3 => 'Треће тромесечје',
				4 => 'Четврто тромесечје'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'К1',
				2 => 'К2',
				3 => 'К3',
				4 => 'К4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'пре подне',
				'pm' => 'по подне'
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
				0 => 'п. н. е.',
				1 => 'н. е.'
			),
			'wide' => array(
				0 => 'Пре нове ере',
				1 => 'Нове ере'
			),
			'narrow' => array(
				0 => 'п.н.е.',
				1 => 'н.е.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, dd. MMMM y.',
			'long' => 'dd. MMMM y.',
			'medium' => 'dd.MM.y.',
			'short' => 'd.M.yy.'
		),
		'timeFormats' => array(
			'full' => 'HH.mm.ss zzzz',
			'long' => 'HH.mm.ss z',
			'medium' => 'HH.mm.ss',
			'short' => 'HH.mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'ера'
			),
			'year' => array(
				'name' => 'година'
			),
			'month' => array(
				'name' => 'месец'
			),
			'week' => array(
				'name' => 'недеља'
			),
			'day' => array(
				'name' => 'дан',
				'relative' => array(
					-3 => 'пре три дана',
					-2 => 'прекјуче',
					-1 => 'јуче',
					0 => 'данас',
					1 => 'сутра',
					2 => 'прекосутра',
					3 => 'за три дана'
				)
			),
			'weekday' => array(
				'name' => 'дан у недељи'
			),
			'dayperiod' => array(
				'name' => 'пре подне/поподне'
			),
			'hour' => array(
				'name' => 'час'
			),
			'minute' => array(
				'name' => 'минут'
			),
			'second' => array(
				'name' => 'секунд'
			),
			'zone' => array(
				'name' => 'зона'
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
			'CZK' => 'Кч',
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
			'PLN' => 'зл',
			'PTE' => 'Esc',
			'PYG' => '₲',
			'QAR' => 'QR',
			'RHD' => 'RH$',
			'RON' => 'RON',
			'RSD' => 'дин.',
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
			'TRY' => 'Тл',
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
				'normal' => '{0} дана',
				'short' => '{0} дан'
			),
			'one' => array(
				'normal' => '{0} дан',
				'short' => '{0} дан'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} сати',
				'short' => '{0} сат'
			),
			'one' => array(
				'normal' => '{0} сат',
				'short' => '{0} сат'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} минута',
				'short' => '{0} мин'
			),
			'one' => array(
				'normal' => '{0} минут',
				'short' => '{0} мин'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} месеци',
				'short' => '{0} мес'
			),
			'one' => array(
				'normal' => '{0} месец',
				'short' => '{0} мес'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} секунди',
				'short' => '{0} сек'
			),
			'one' => array(
				'normal' => '{0} секунда',
				'short' => '{0} сек'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} недеља',
				'short' => '{0} нед'
			),
			'one' => array(
				'normal' => '{0} недеља',
				'short' => '{0} нед'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} година',
				'short' => '{0} год'
			),
			'one' => array(
				'normal' => '{0} година',
				'short' => '{0} год'
			)
		)
	),
	'messages' => array(
		'yes' => 'да:д',
		'no' => 'не:н'
	)
);