<?php
/**
 * Locale: it_IT
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'it',
	'territory' => 'IT',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Gennaio',
				2 => 'Febbraio',
				3 => 'Marzo',
				4 => 'Aprile',
				5 => 'Maggio',
				6 => 'Giugno',
				7 => 'Luglio',
				8 => 'Agosto',
				9 => 'Settembre',
				10 => 'Ottobre',
				11 => 'Novembre',
				12 => 'Dicembre'
			),
			'narrow' => array(
				1 => 'G',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'M',
				6 => 'G',
				7 => 'L',
				8 => 'A',
				9 => 'S',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'gen',
				2 => 'feb',
				3 => 'mar',
				4 => 'apr',
				5 => 'mag',
				6 => 'giu',
				7 => 'lug',
				8 => 'ago',
				9 => 'set',
				10 => 'ott',
				11 => 'nov',
				12 => 'dic'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Domenica',
				1 => 'Lunedì',
				2 => 'Martedì',
				3 => 'Mercoledì',
				4 => 'Giovedì',
				5 => 'Venerdì',
				6 => 'Sabato'
			),
			'narrow' => array(
				0 => 'D',
				1 => 'L',
				2 => 'M',
				3 => 'M',
				4 => 'G',
				5 => 'V',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'dom',
				1 => 'lun',
				2 => 'mar',
				3 => 'mer',
				4 => 'gio',
				5 => 'ven',
				6 => 'sab'
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
				'am' => 'm.',
				'pm' => 'p.'
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
				0 => 'a.C.',
				1 => 'd.C'
			),
			'narrow' => array(
				0 => 'aC',
				1 => 'dC'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d MMMM y',
			'long' => 'dd MMMM y',
			'medium' => 'dd/MMM/y',
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
				'name' => 'era'
			),
			'year' => array(
				'name' => 'anno'
			),
			'month' => array(
				'name' => 'mese'
			),
			'week' => array(
				'name' => 'settimana'
			),
			'day' => array(
				'name' => 'giorno',
				'relative' => array(
					-3 => 'tre giorni fa',
					-2 => 'l\'altro ieri',
					-1 => 'ieri',
					0 => 'oggi',
					1 => 'domani',
					2 => 'dopodomani',
					3 => 'tra tre giorni'
				)
			),
			'weekday' => array(
				'name' => 'giorno della settimana'
			),
			'dayperiod' => array(
				'name' => 'periodo del giorno'
			),
			'hour' => array(
				'name' => 'ora'
			),
			'minute' => array(
				'name' => 'minuto'
			),
			'second' => array(
				'name' => 'secondo'
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
				'normal' => '{0} giorno',
				'short' => '{0} g'
			),
			'other' => array(
				'normal' => '{0} giorni',
				'short' => '{0} gg'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} ora',
				'short' => '{0} h'
			),
			'other' => array(
				'normal' => '{0} ore',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minuto',
				'short' => '{0} min'
			),
			'other' => array(
				'normal' => '{0} minuti',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mese',
				'short' => '{0} mese'
			),
			'other' => array(
				'normal' => '{0} mesi',
				'short' => '{0} mesi'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} secondo',
				'short' => '{0} sec'
			),
			'other' => array(
				'normal' => '{0} secondi',
				'short' => '{0} sec'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} settimana',
				'short' => '{0} sett.'
			),
			'other' => array(
				'normal' => '{0} settimane',
				'short' => '{0} sett.'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} anno',
				'short' => '{0} anno'
			),
			'other' => array(
				'normal' => '{0} anni',
				'short' => '{0} anni'
			)
		)
	),
	'messages' => array(
		'yes' => 'sì:si:s',
		'no' => 'no:n'
	)
);