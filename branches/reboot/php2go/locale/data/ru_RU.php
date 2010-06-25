<?php
/**
 * Locale: ru_RU
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'ru',
	'territory' => 'RU',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Январь',
				2 => 'Февраль',
				3 => 'Март',
				4 => 'Апрель',
				5 => 'Май',
				6 => 'Июнь',
				7 => 'Июль',
				8 => 'Август',
				9 => 'Сентябрь',
				10 => 'Октябрь',
				11 => 'Ноябрь',
				12 => 'Декабрь'
			),
			'narrow' => array(
				1 => 'Я',
				2 => 'Ф',
				3 => 'М',
				4 => 'А',
				5 => 'М',
				6 => 'И',
				7 => 'И',
				8 => 'А',
				9 => 'С',
				10 => 'О',
				11 => 'Н',
				12 => 'Д'
			),
			'abbreviated' => array(
				1 => 'янв.',
				2 => 'февр.',
				3 => 'март',
				4 => 'апр.',
				5 => 'май',
				6 => 'июнь',
				7 => 'июль',
				8 => 'авг.',
				9 => 'сент.',
				10 => 'окт.',
				11 => 'нояб.',
				12 => 'дек.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Воскресенье',
				1 => 'Понедельник',
				2 => 'Вторник',
				3 => 'Среда',
				4 => 'Четверг',
				5 => 'Пятница',
				6 => 'Суббота'
			),
			'narrow' => array(
				0 => 'В',
				1 => 'П',
				2 => 'В',
				3 => 'С',
				4 => 'Ч',
				5 => 'П',
				6 => 'С'
			),
			'abbreviated' => array(
				0 => 'Вс',
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
				1 => '1-й квартал',
				2 => '2-й квартал',
				3 => '3-й квартал',
				4 => '4-й квартал'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1-й кв.',
				2 => '2-й кв.',
				3 => '3-й кв.',
				4 => '4-й кв.'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'AM',
				'pm' => 'PM'
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
				0 => 'до н. э.',
				1 => 'н. э.'
			),
			'wide' => array(
				0 => 'до н. э.',
				1 => 'н. э.'
			),
			'narrow' => array(
				0 => 'до н. э.',
				1 => 'н. э.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d MMMM y \'г\'.',
			'long' => 'd MMMM y \'г\'.',
			'medium' => 'dd.MM.yyyy',
			'short' => 'dd.MM.yy'
		),
		'timeFormats' => array(
			'full' => 'H:mm:ss zzzz',
			'long' => 'H:mm:ss z',
			'medium' => 'H:mm:ss',
			'short' => 'H:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'Эра'
			),
			'year' => array(
				'name' => 'Год'
			),
			'month' => array(
				'name' => 'Месяц'
			),
			'week' => array(
				'name' => 'Неделя'
			),
			'day' => array(
				'name' => 'День',
				'relative' => array(
					-2 => 'Позавчера',
					-1 => 'Вчера',
					0 => 'Сегодня',
					1 => 'Завтра',
					2 => 'Послезавтра'
				)
			),
			'weekday' => array(
				'name' => 'День недели'
			),
			'dayperiod' => array(
				'name' => 'AM/PM'
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
				'name' => 'Часовой пояс'
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
		'percentFormat' => '#,##0 %',
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
			'RUR' => 'р.'
		)
	),
	'units' => array(
		'day' => array(
			'other' => array(
				'normal' => '{0} дня',
				'short' => '{0} дн.'
			),
			'one' => array(
				'normal' => '{0} день',
				'short' => '{0} дн.'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} часа',
				'short' => '{0} ч.'
			),
			'one' => array(
				'normal' => '{0} час',
				'short' => '{0} ч.'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} минуты',
				'short' => '{0} мин.'
			),
			'one' => array(
				'normal' => '{0} минута',
				'short' => '{0} мин.'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} месяца',
				'short' => '{0} мес.'
			),
			'one' => array(
				'normal' => '{0} месяц',
				'short' => '{0} мес.'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} секунды',
				'short' => '{0} сек.'
			),
			'one' => array(
				'normal' => '{0} секунда',
				'short' => '{0} сек.'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} недели',
				'short' => '{0} нед.'
			),
			'one' => array(
				'normal' => '{0} неделя',
				'short' => '{0} нед.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} года',
				'short' => '{0} г.'
			),
			'one' => array(
				'normal' => '{0} год',
				'short' => '{0} г.'
			)
		)
	),
	'messages' => array(
		'yes' => 'да:д',
		'no' => 'нет:н'
	)
);