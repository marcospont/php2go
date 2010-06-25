<?php
/**
 * Locale: gl
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4753',
	'language' => 'gl',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Xaneiro',
				2 => 'Febreiro',
				3 => 'Marzo',
				4 => 'Abril',
				5 => 'Maio',
				6 => 'Xuño',
				7 => 'Xullo',
				8 => 'Agosto',
				9 => 'Setembro',
				10 => 'Outubro',
				11 => 'Novembro',
				12 => 'Decembro'
			),
			'narrow' => array(
				1 => 'X',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'M',
				6 => 'X',
				7 => 'X',
				8 => 'A',
				9 => 'S',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'Xan',
				2 => 'Feb',
				3 => 'Mar',
				4 => 'Abr',
				5 => 'Mai',
				6 => 'Xuñ',
				7 => 'Xul',
				8 => 'Ago',
				9 => 'Set',
				10 => 'Out',
				11 => 'Nov',
				12 => 'Dec'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Domingo',
				1 => 'Luns',
				2 => 'Martes',
				3 => 'Mércores',
				4 => 'Xoves',
				5 => 'Venres',
				6 => 'Sábado'
			),
			'narrow' => array(
				0 => 'D',
				1 => 'L',
				2 => 'M',
				3 => 'M',
				4 => 'X',
				5 => 'V',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'Dom',
				1 => 'Lun',
				2 => 'Mar',
				3 => 'Mér',
				4 => 'Xov',
				5 => 'Ven',
				6 => 'Sáb'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1o trimestre',
				2 => '2o trimestre',
				3 => '3o trimestre',
				4 => '4o trimestre'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'T1',
				2 => 'T2',
				3 => 'T3',
				4 => 'T4'
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
				0 => 'a.C.',
				1 => 'd.C.'
			),
			'wide' => array(
				0 => 'antes de Cristo',
				1 => 'despois de Cristo'
			),
			'narrow' => array(
				0 => 'a.C.',
				1 => 'd.C.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE dd MMMM y',
			'long' => 'dd MMMM y',
			'medium' => 'd MMM, y',
			'short' => 'dd/MM/yy'
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
				'name' => 'Era'
			),
			'year' => array(
				'name' => 'Ano'
			),
			'month' => array(
				'name' => 'Mes'
			),
			'week' => array(
				'name' => 'Semana'
			),
			'day' => array(
				'name' => 'Día',
				'relative' => array(
					-3 => 'trasantonte',
					-2 => 'antonte',
					-1 => 'onte',
					0 => 'hoxe',
					1 => 'mañá',
					2 => 'pasadomañá'
				)
			),
			'weekday' => array(
				'name' => 'Día da semana'
			),
			'dayperiod' => array(
				'name' => 'Dayperiod'
			),
			'hour' => array(
				'name' => 'Hora'
			),
			'minute' => array(
				'name' => 'Minuto'
			),
			'second' => array(
				'name' => 'Segundo'
			),
			'zone' => array(
				'name' => 'Fuso horario'
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
			'ESP' => '₧',
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
				'normal' => '{0} día'
			),
			'other' => array(
				'normal' => '{0} días'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} hora'
			),
			'other' => array(
				'normal' => '{0} horas'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minuto'
			),
			'other' => array(
				'normal' => '{0} minutos'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mes'
			),
			'other' => array(
				'normal' => '{0} meses'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} segundo'
			),
			'other' => array(
				'normal' => '{0} segundos'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} semana'
			),
			'other' => array(
				'normal' => '{0} semanas'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} ano'
			),
			'other' => array(
				'normal' => '{0} anos'
			)
		)
	),
	'messages' => array(
		'yes' => 'si:s',
		'no' => 'non:n'
	)
);