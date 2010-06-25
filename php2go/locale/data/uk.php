<?php
/**
 * Locale: uk
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'uk',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Січень',
				2 => 'Лютий',
				3 => 'Березень',
				4 => 'Квітень',
				5 => 'Травень',
				6 => 'Червень',
				7 => 'Липень',
				8 => 'Серпень',
				9 => 'Вересень',
				10 => 'Жовтень',
				11 => 'Листопад',
				12 => 'Грудень'
			),
			'narrow' => array(
				1 => 'С',
				2 => 'Л',
				3 => 'Б',
				4 => 'К',
				5 => 'Т',
				6 => 'Ч',
				7 => 'Л',
				8 => 'С',
				9 => 'В',
				10 => 'Ж',
				11 => 'Л',
				12 => 'Г'
			),
			'abbreviated' => array(
				1 => 'Січ',
				2 => 'Лют',
				3 => 'Бер',
				4 => 'Кві',
				5 => 'Тра',
				6 => 'Чер',
				7 => 'Лип',
				8 => 'Сер',
				9 => 'Вер',
				10 => 'Жов',
				11 => 'Лис',
				12 => 'Гру'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Неділя',
				1 => 'Понеділок',
				2 => 'Вівторок',
				3 => 'Середа',
				4 => 'Четвер',
				5 => 'Пʼятниця',
				6 => 'Субота'
			),
			'narrow' => array(
				0 => 'Н',
				1 => 'П',
				2 => 'В',
				3 => 'С',
				4 => 'Ч',
				5 => 'П',
				6 => 'С'
			),
			'abbreviated' => array(
				0 => 'Нд',
				1 => 'Пн',
				2 => 'Вт',
				3 => 'Ср',
				4 => 'Чт',
				5 => 'Пт',
				6 => 'Сб'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'I квартал',
				2 => 'II квартал',
				3 => 'III квартал',
				4 => 'IV квартал'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'I кв.',
				2 => 'II кв.',
				3 => 'III кв.',
				4 => 'IV кв.'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'дп',
				'pm' => 'пп',
				'afternoon' => 'дня',
				'evening' => 'вечора',
				'morning' => 'ранку',
				'night' => 'ночі'
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
				0 => 'до н.е.',
				1 => 'н.е.'
			),
			'wide' => array(
				0 => 'до нашої ери',
				1 => 'нашої ери'
			),
			'narrow' => array(
				0 => 'до н.е.',
				1 => 'н.е.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d MMMM y \'р\'.',
			'long' => 'd MMMM y \'р\'.',
			'medium' => 'd MMM y',
			'short' => 'dd.MM.yy'
		),
		'timeFormats' => array(
			'full' => 'HH:mm:ss zzzz',
			'long' => 'HH:mm:ss z',
			'medium' => 'HH:mm:ss',
			'short' => 'HH:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1}, {0}',
			'long' => '{1}, {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'Ера'
			),
			'year' => array(
				'name' => 'Рік'
			),
			'month' => array(
				'name' => 'Місяць'
			),
			'week' => array(
				'name' => 'Тиждень'
			),
			'day' => array(
				'name' => 'День',
				'relative' => array(
					-3 => 'Три дні тому',
					-2 => 'Позавчора',
					-1 => 'Вчора',
					0 => 'Сьогодні',
					1 => 'Завтра',
					2 => 'Післязавтра',
					3 => 'Через три дні з цього моменту'
				)
			),
			'weekday' => array(
				'name' => 'День тижня'
			),
			'dayperiod' => array(
				'name' => 'Частина доби'
			),
			'hour' => array(
				'name' => 'Година'
			),
			'minute' => array(
				'name' => 'Хвилина'
			),
			'second' => array(
				'name' => 'Секунда'
			),
			'zone' => array(
				'name' => 'Зона'
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
			'AZN' => 'ман.',
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
			'ZWD' => 'Z$',
			'RUB' => 'руб.',
			'UAK' => 'крб.'
		)
	),
	'units' => array(
		'day' => array(
			'other' => array(
				'normal' => '{0} дня',
				'short' => '{0} дня'
			),
			'one' => array(
				'normal' => '{0} день',
				'short' => '{0} день'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} години',
				'short' => '{0} год.'
			),
			'one' => array(
				'normal' => '{0} година',
				'short' => '{0} год.'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} хвилини',
				'short' => '{0} хв.'
			),
			'one' => array(
				'normal' => '{0} хвилина',
				'short' => '{0} хв.'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} місяця',
				'short' => '{0} міс.'
			),
			'one' => array(
				'normal' => '{0} місяць',
				'short' => '{0} міс.'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} секунди',
				'short' => '{0} сек.'
			),
			'one' => array(
				'normal' => '{0} секунда',
				'short' => '{0} сек.'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} тижня',
				'short' => '{0} тиж.'
			),
			'one' => array(
				'normal' => '{0} тиждень',
				'short' => '{0} тиж.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} року',
				'short' => '{0} р.'
			),
			'one' => array(
				'normal' => '{0} рік',
				'short' => '{0} р.'
			)
		)
	),
	'messages' => array(
		'yes' => 'так:т',
		'no' => 'ні:н'
	)
);