<?php
/**
 * Locale: es_GT
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4582',
	'language' => 'es',
	'territory' => 'GT',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'enero',
				2 => 'febrero',
				3 => 'marzo',
				4 => 'abril',
				5 => 'mayo',
				6 => 'junio',
				7 => 'julio',
				8 => 'agosto',
				9 => 'septiembre',
				10 => 'octubre',
				11 => 'noviembre',
				12 => 'diciembre'
			),
			'narrow' => array(
				1 => 'E',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'M',
				6 => 'J',
				7 => 'J',
				8 => 'A',
				9 => 'S',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'ene',
				2 => 'feb',
				3 => 'mar',
				4 => 'abr',
				5 => 'may',
				6 => 'jun',
				7 => 'jul',
				8 => 'ago',
				9 => 'sep',
				10 => 'oct',
				11 => 'nov',
				12 => 'dic'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'domingo',
				1 => 'lunes',
				2 => 'martes',
				3 => 'miércoles',
				4 => 'jueves',
				5 => 'viernes',
				6 => 'sábado'
			),
			'narrow' => array(
				0 => 'D',
				1 => 'L',
				2 => 'M',
				3 => 'M',
				4 => 'J',
				5 => 'V',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'dom',
				1 => 'lun',
				2 => 'mar',
				3 => 'mié',
				4 => 'jue',
				5 => 'vie',
				6 => 'sáb'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1er trimestre',
				2 => '2º trimestre',
				3 => '3er trimestre',
				4 => '4º trimestre'
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
				'am' => 'a.m.',
				'pm' => 'p.m.'
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
				1 => 'anno Dómini'
			),
			'narrow' => array(
				0 => 'a.C.',
				1 => 'd.C.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d \'de\' MMMM \'de\' y',
			'long' => 'd \'de\' MMMM \'de\' y',
			'medium' => 'd/MM/yyyy',
			'short' => 'd/MM/yy'
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
				'name' => 'era'
			),
			'year' => array(
				'name' => 'año'
			),
			'month' => array(
				'name' => 'mes'
			),
			'week' => array(
				'name' => 'semana'
			),
			'day' => array(
				'name' => 'día',
				'relative' => array(
					-3 => 'Hace tres días',
					-2 => 'antes de ayer',
					-1 => 'ayer',
					0 => 'hoy',
					1 => 'mañana',
					2 => 'pasado mañana',
					3 => 'Dentro de tres días'
				)
			),
			'weekday' => array(
				'name' => 'día de la semana'
			),
			'dayperiod' => array(
				'name' => 'periodo del día'
			),
			'hour' => array(
				'name' => 'hora'
			),
			'minute' => array(
				'name' => 'minuto'
			),
			'second' => array(
				'name' => 'segundo'
			),
			'zone' => array(
				'name' => 'zona'
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
			'AFN' => 'AFN',
			'ANG' => 'ANG',
			'AOA' => 'AOA',
			'ARA' => '₳',
			'ARL' => '$L',
			'ARM' => 'm$n',
			'ARS' => 'ARS',
			'AUD' => 'AUD',
			'AWG' => 'AWG',
			'AZN' => 'AZN',
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
			'GTQ' => 'Q',
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
				'normal' => '{0} día',
				'short' => '{0} día'
			),
			'other' => array(
				'normal' => '{0} días',
				'short' => '{0} días'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} hora',
				'short' => '{0} h'
			),
			'other' => array(
				'normal' => '{0} horas',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minuto',
				'short' => '{0} min'
			),
			'other' => array(
				'normal' => '{0} minutos',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mes',
				'short' => '{0} mes'
			),
			'other' => array(
				'normal' => '{0} meses',
				'short' => '{0} meses'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} segundo',
				'short' => '{0} s'
			),
			'other' => array(
				'normal' => '{0} segundos',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} semana',
				'short' => '{0} semana'
			),
			'other' => array(
				'normal' => '{0} semanas',
				'short' => '{0} semanas'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} año',
				'short' => '{0} año'
			),
			'other' => array(
				'normal' => '{0} años',
				'short' => '{0} años'
			)
		)
	),
	'messages' => array(
		'yes' => 'sí:si:s',
		'no' => 'no:n'
	)
);