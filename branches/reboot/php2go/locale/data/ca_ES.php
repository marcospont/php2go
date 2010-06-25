<?php
/**
 * Locale: ca_ES
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'ca',
	'territory' => 'ES',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'gener',
				2 => 'febrer',
				3 => 'març',
				4 => 'abril',
				5 => 'maig',
				6 => 'juny',
				7 => 'juliol',
				8 => 'agost',
				9 => 'setembre',
				10 => 'octubre',
				11 => 'novembre',
				12 => 'desembre'
			),
			'narrow' => array(
				1 => 'G',
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
				1 => 'gen.',
				2 => 'febr.',
				3 => 'març',
				4 => 'abr.',
				5 => 'maig',
				6 => 'juny',
				7 => 'jul.',
				8 => 'ag.',
				9 => 'set.',
				10 => 'oct.',
				11 => 'nov.',
				12 => 'des.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Diumenge',
				1 => 'Dilluns',
				2 => 'Dimarts',
				3 => 'Dimecres',
				4 => 'Dijous',
				5 => 'Divendres',
				6 => 'dissabte'
			),
			'narrow' => array(
				0 => 'G',
				1 => 'l',
				2 => 'T',
				3 => 'G',
				4 => 'J',
				5 => 'V',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'dg',
				1 => 'dl',
				2 => 'dt',
				3 => 'dc',
				4 => 'dj',
				5 => 'dv',
				6 => 'ds'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1r trimestre',
				2 => '2n trimestre',
				3 => '3r trimestre',
				4 => '4t trimestre'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1T',
				2 => '2T',
				3 => '3T',
				4 => '4T'
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
				0 => 'aC',
				1 => 'dC'
			),
			'wide' => array(
				0 => 'abans de Crist',
				1 => 'després de Crist'
			),
			'narrow' => array(
				0 => 'aC',
				1 => 'dC'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d MMMM \'de\' y',
			'long' => 'd MMMM \'de\' y',
			'medium' => 'dd/MM/yyyy',
			'short' => 'dd/MM/yy'
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
				'name' => 'era'
			),
			'year' => array(
				'name' => 'any'
			),
			'month' => array(
				'name' => 'mes'
			),
			'week' => array(
				'name' => 'setmana'
			),
			'day' => array(
				'name' => 'dia',
				'relative' => array(
					-3 => 'fa tres dies',
					-2 => 'abans d\'ahir',
					-1 => 'ahir',
					0 => 'avui',
					1 => 'demà',
					2 => 'demà passat',
					3 => 'd\'aquí a tres dies'
				)
			),
			'weekday' => array(
				'name' => 'dia de la setmana'
			),
			'dayperiod' => array(
				'name' => 'a.m./p.m.'
			),
			'hour' => array(
				'name' => 'hora'
			),
			'minute' => array(
				'name' => 'minut'
			),
			'second' => array(
				'name' => 'segon'
			),
			'zone' => array(
				'name' => 'zona'
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
				'normal' => '{0} dia',
				'short' => '{0} dia'
			),
			'other' => array(
				'normal' => '{0} dies',
				'short' => '{0} dies'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} hora',
				'short' => '{0} h'
			),
			'other' => array(
				'normal' => '{0} hores',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minut',
				'short' => '{0} m'
			),
			'other' => array(
				'normal' => '{0} minuts',
				'short' => '{0} m'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mes',
				'short' => '{0} mes'
			),
			'other' => array(
				'normal' => '{0} mesos',
				'short' => '{0} mesos'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} segon',
				'short' => '{0} s'
			),
			'other' => array(
				'normal' => '{0} segons',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} setmana',
				'short' => '{0} setmana'
			),
			'other' => array(
				'normal' => '{0} setmanes',
				'short' => '{0} setmanes'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} any',
				'short' => '{0} any'
			),
			'other' => array(
				'normal' => '{0} anys',
				'short' => '{0} anys'
			)
		)
	),
	'messages' => array(
		'yes' => 'sí:s',
		'no' => 'no:n'
	)
);