<?php
/**
 * Locale: pt_MZ
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4590',
	'language' => 'pt',
	'territory' => 'MZ',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'janeiro',
				2 => 'fevereiro',
				3 => 'março',
				4 => 'abril',
				5 => 'maio',
				6 => 'junho',
				7 => 'julho',
				8 => 'agosto',
				9 => 'setembro',
				10 => 'outubro',
				11 => 'novembro',
				12 => 'dezembro'
			),
			'narrow' => array(
				1 => 'J',
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
				1 => 'jan',
				2 => 'fev',
				3 => 'mar',
				4 => 'abr',
				5 => 'mai',
				6 => 'jun',
				7 => 'jul',
				8 => 'ago',
				9 => 'set',
				10 => 'out',
				11 => 'nov',
				12 => 'dez'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'domingo',
				1 => 'segunda-feira',
				2 => 'terça-feira',
				3 => 'quarta-feira',
				4 => 'quinta-feira',
				5 => 'sexta-feira',
				6 => 'sábado'
			),
			'narrow' => array(
				0 => 'D',
				1 => 'S',
				2 => 'T',
				3 => 'Q',
				4 => 'Q',
				5 => 'S',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'dom',
				1 => 'seg',
				2 => 'ter',
				3 => 'qua',
				4 => 'qui',
				5 => 'sex',
				6 => 'sáb'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1º trimestre',
				2 => '2º trimestre',
				3 => '3º trimestre',
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
				'am' => 'AM',
				'pm' => 'PM',
				'afternoon' => 'tarde',
				'morning' => 'manhã',
				'night' => 'noite',
				'noon' => 'meio-dia'
			),
			'abbreviated' => array(
				'am' => 'AM',
				'pm' => 'PM',
				'afternoon' => 'tarde',
				'morning' => 'manhã',
				'night' => 'noite',
				'noon' => 'meia-noite'
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
				0 => 'Antes de Cristo',
				1 => 'Ano do Senhor'
			),
			'narrow' => array(
				0 => 'a.C.',
				1 => 'd.C.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d \'de\' MMMM \'de\' y',
			'long' => 'd \'de\' MMMM \'de\' y',
			'medium' => 'dd/MM/yyyy',
			'short' => 'dd/MM/yy'
		),
		'timeFormats' => array(
			'full' => 'HH\'h\'mm\'min\'ss\'s\' zzzz',
			'long' => 'HH\'h\'mm\'min\'ss\'s\' z',
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
				'name' => 'Mês'
			),
			'week' => array(
				'name' => 'Semana'
			),
			'day' => array(
				'name' => 'Dia',
				'relative' => array(
					-3 => 'Há três dias',
					-2 => 'Anteontem',
					-1 => 'Ontem',
					0 => 'Hoje',
					1 => 'Amanhã',
					2 => 'Depois de amanhã',
					3 => 'Daqui a três dias'
				)
			),
			'weekday' => array(
				'name' => 'Dia da semana'
			),
			'dayperiod' => array(
				'name' => 'Período do dia'
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
				'name' => 'Fuso'
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
			'PTE' => 'Esc.',
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
				'normal' => '{0} dia',
				'short' => '{0} dia'
			),
			'other' => array(
				'normal' => '{0} dias',
				'short' => '{0} dias'
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
				'normal' => '{0} mês',
				'short' => '{0} m.'
			),
			'other' => array(
				'normal' => '{0} meses',
				'short' => '{0} m.'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} segundo',
				'short' => '{0} seg'
			),
			'other' => array(
				'normal' => '{0} segundos',
				'short' => '{0} seg'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} semana',
				'short' => '{0} sem.'
			),
			'other' => array(
				'normal' => '{0} semanas',
				'short' => '{0} sem.'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} ano',
				'short' => '{0} ano'
			),
			'other' => array(
				'normal' => '{0} anos',
				'short' => '{0} anos'
			)
		)
	),
	'messages' => array(
		'yes' => 'sim:s',
		'no' => 'não:n'
	)
);