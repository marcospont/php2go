<?php
/**
 * Locale: fur
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4768',
	'language' => 'fur',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Zenâr',
				2 => 'Fevrâr',
				3 => 'Març',
				4 => 'Avrîl',
				5 => 'Mai',
				6 => 'Jugn',
				7 => 'Lui',
				8 => 'Avost',
				9 => 'Setembar',
				10 => 'Otubar',
				11 => 'Novembar',
				12 => 'Dicembar'
			),
			'narrow' => array(
				1 => 'Z',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'M',
				6 => 'J',
				7 => 'L',
				8 => 'A',
				9 => 'S',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'Zen',
				2 => 'Fev',
				3 => 'Mar',
				4 => 'Avr',
				5 => 'Mai',
				6 => 'Jug',
				7 => 'Lui',
				8 => 'Avo',
				9 => 'Set',
				10 => 'Otu',
				11 => 'Nov',
				12 => 'Dic'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'domenie',
				1 => 'lunis',
				2 => 'martars',
				3 => 'miercus',
				4 => 'joibe',
				5 => 'vinars',
				6 => 'sabide'
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
				3 => 'mie',
				4 => 'joi',
				5 => 'vin',
				6 => 'sab'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'Prin trimestri',
				2 => 'Secont trimestri',
				3 => 'Tierç trimestri',
				4 => 'Cuart trimestri'
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
				'am' => 'a.',
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
				0 => 'pdC',
				1 => 'ddC'
			),
			'wide' => array(
				0 => 'pdC',
				1 => 'ddC'
			),
			'narrow' => array(
				0 => 'pdC',
				1 => 'ddC'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d \'di\' MMMM \'dal\' y',
			'long' => 'd \'di\' MMMM \'dal\' y',
			'medium' => 'dd/MM/yyyy',
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
				'name' => 'ere'
			),
			'year' => array(
				'name' => 'an'
			),
			'month' => array(
				'name' => 'mês'
			),
			'week' => array(
				'name' => 'setemane'
			),
			'day' => array(
				'name' => 'dì',
				'relative' => array(
					-3 => 'trê dîs fa',
					-2 => 'îr l\'altri',
					-1 => 'îr',
					0 => 'vuê',
					1 => 'doman',
					2 => 'passantdoman',
					3 => 'tra trê dîs'
				)
			),
			'weekday' => array(
				'name' => 'dì de setemane'
			),
			'dayperiod' => array(
				'name' => 'toc dal dì'
			),
			'hour' => array(
				'name' => 'ore'
			),
			'minute' => array(
				'name' => 'minût'
			),
			'second' => array(
				'name' => 'secont'
			),
			'zone' => array(
				'name' => 'zone'
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
				'normal' => '{0} zornade'
			),
			'other' => array(
				'normal' => '{0} zornadis'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} ore'
			),
			'other' => array(
				'normal' => '{0} oris'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minût'
			),
			'other' => array(
				'normal' => '{0} minûts'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mês'
			),
			'other' => array(
				'normal' => '{0} mês'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} secont'
			),
			'other' => array(
				'normal' => '{0} seconts'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} setemane'
			),
			'other' => array(
				'normal' => '{0} setemanis'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} an'
			),
			'other' => array(
				'normal' => '{0} agns'
			)
		)
	),
	'messages' => array(
		'yes' => 'sì:si:s',
		'no' => 'no:n'
	)
);