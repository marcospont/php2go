<?php
/**
 * Locale: bg_BG
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'bg',
	'territory' => 'BG',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'януари',
				2 => 'февруари',
				3 => 'март',
				4 => 'април',
				5 => 'май',
				6 => 'юни',
				7 => 'юли',
				8 => 'август',
				9 => 'септември',
				10 => 'октомври',
				11 => 'ноември',
				12 => 'декември'
			),
			'narrow' => array(
				1 => 'я',
				2 => 'ф',
				3 => 'м',
				4 => 'а',
				5 => 'м',
				6 => 'ю',
				7 => 'ю',
				8 => 'а',
				9 => 'с',
				10 => 'о',
				11 => 'н',
				12 => 'д'
			),
			'abbreviated' => array(
				1 => 'ян.',
				2 => 'февр.',
				3 => 'март',
				4 => 'апр.',
				5 => 'май',
				6 => 'юни',
				7 => 'юли',
				8 => 'авг.',
				9 => 'септ.',
				10 => 'окт.',
				11 => 'ноем.',
				12 => 'дек.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'неделя',
				1 => 'понеделник',
				2 => 'вторник',
				3 => 'сряда',
				4 => 'четвъртък',
				5 => 'петък',
				6 => 'събота'
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
				0 => 'нд',
				1 => 'пн',
				2 => 'вт',
				3 => 'ср',
				4 => 'чт',
				5 => 'пт',
				6 => 'сб'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1-во тримесечие',
				2 => '2-ро тримесечие',
				3 => '3-то тримесечие',
				4 => '4-то тримесечие'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'I трим.',
				2 => 'II трим.',
				3 => 'III трим.',
				4 => 'IV трим.'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'пр. об.',
				'pm' => 'сл. об.'
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
				0 => 'пр. н. е.',
				1 => 'от н. е.'
			),
			'wide' => array(
				0 => 'пр.Хр.',
				1 => 'сл.Хр.'
			),
			'narrow' => array(
				1 => 'сл.н.е.'
			)
		),
		'dateFormats' => array(
			'full' => 'dd MMMM y, EEEE',
			'long' => 'dd MMMM y',
			'medium' => 'dd.MM.yyyy',
			'short' => 'dd.MM.yy'
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
				'name' => 'ера'
			),
			'year' => array(
				'name' => 'година'
			),
			'month' => array(
				'name' => 'месец'
			),
			'week' => array(
				'name' => 'седмица'
			),
			'day' => array(
				'name' => 'Ден',
				'relative' => array(
					-2 => 'Онзи ден',
					-1 => 'Вчера',
					0 => 'Днес',
					1 => 'Утре',
					2 => 'Вдругиден'
				)
			),
			'weekday' => array(
				'name' => 'Ден от седмицата'
			),
			'dayperiod' => array(
				'name' => 'ден'
			),
			'hour' => array(
				'name' => 'час'
			),
			'minute' => array(
				'name' => 'минута'
			),
			'second' => array(
				'name' => 'секунда'
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
			'group' => ' ',
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
			'ZWD' => 'Z$',
			'BGN' => 'лв.',
			'RUB' => 'Руб.'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} ден',
				'short' => '{0} дн.'
			),
			'other' => array(
				'normal' => '{0} дена',
				'short' => '{0} дн.'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} час',
				'short' => '{0} ч'
			),
			'other' => array(
				'normal' => '{0} часа',
				'short' => '{0} ч'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} минута',
				'short' => '{0} мин'
			),
			'other' => array(
				'normal' => '{0} минути',
				'short' => '{0} мин'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} месец',
				'short' => '{0} мес.'
			),
			'other' => array(
				'normal' => '{0} месеца',
				'short' => '{0} мес.'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} секунда',
				'short' => '{0} сек'
			),
			'other' => array(
				'normal' => '{0} секунди',
				'short' => '{0} сек'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} седмица',
				'short' => '{0} седм.'
			),
			'other' => array(
				'normal' => '{0} седмици',
				'short' => '{0} седм.'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} година',
				'short' => '{0} год.'
			),
			'other' => array(
				'normal' => '{0} години',
				'short' => '{0} год.'
			)
		)
	),
	'messages' => array(
		'yes' => 'да:д',
		'no' => 'не:н'
	)
);