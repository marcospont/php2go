<?php
/**
 * Locale: ro_MD
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'ro',
	'territory' => 'MD',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'ianuarie',
				2 => 'februarie',
				3 => 'martie',
				4 => 'aprilie',
				5 => 'mai',
				6 => 'iunie',
				7 => 'iulie',
				8 => 'august',
				9 => 'septembrie',
				10 => 'octombrie',
				11 => 'noiembrie',
				12 => 'decembrie'
			),
			'narrow' => array(
				1 => 'I',
				2 => 'F',
				3 => 'M',
				4 => 'A',
				5 => 'M',
				6 => 'I',
				7 => 'I',
				8 => 'A',
				9 => 'S',
				10 => 'O',
				11 => 'N',
				12 => 'D'
			),
			'abbreviated' => array(
				1 => 'ian.',
				2 => 'feb.',
				3 => 'mar.',
				4 => 'apr.',
				5 => 'mai',
				6 => 'iun.',
				7 => 'iul.',
				8 => 'aug.',
				9 => 'sept.',
				10 => 'oct.',
				11 => 'nov.',
				12 => 'dec.'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'duminică',
				1 => 'luni',
				2 => 'marți',
				3 => 'miercuri',
				4 => 'joi',
				5 => 'vineri',
				6 => 'sâmbătă'
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
				0 => 'Du',
				1 => 'Lu',
				2 => 'Ma',
				3 => 'Mi',
				4 => 'Jo',
				5 => 'Vi',
				6 => 'Sâ'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'trimestrul I',
				2 => 'trimestrul al II-lea',
				3 => 'trimestrul al III-lea',
				4 => 'trimestrul al IV-lea'
			),
			'narrow' => array(
				1 => 'T1',
				2 => 'T2',
				3 => 'T3',
				4 => 'T4'
			),
			'abbreviated' => array(
				1 => 'trim. I',
				2 => 'trim. II',
				3 => 'trim. III',
				4 => 'trim. IV'
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
				0 => 'î.Hr.',
				1 => 'd.Hr.'
			),
			'wide' => array(
				0 => 'înainte de Hristos',
				1 => 'după Hristos'
			),
			'narrow' => array(
				0 => 'î.Hr.',
				1 => 'd.Hr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'dd.MM.yyyy',
			'short' => 'dd.MM.yyyy'
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
			'medium' => '{1}, {0}',
			'short' => '{1}, {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'eră'
			),
			'year' => array(
				'name' => 'an'
			),
			'month' => array(
				'name' => 'lună'
			),
			'week' => array(
				'name' => 'săptămână'
			),
			'day' => array(
				'name' => 'zi',
				'relative' => array(
					-3 => 'răsalaltăieri',
					-2 => 'alaltăieri',
					-1 => 'ieri',
					0 => 'azi',
					1 => 'mâine',
					2 => 'poimâine',
					3 => 'răspoimâine'
				)
			),
			'weekday' => array(
				'name' => 'zi a săptămânii'
			),
			'dayperiod' => array(
				'name' => 'perioada zilei'
			),
			'hour' => array(
				'name' => 'oră'
			),
			'minute' => array(
				'name' => 'minut'
			),
			'second' => array(
				'name' => 'secundă'
			),
			'zone' => array(
				'name' => 'zonă'
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
			'other' => array(
				'normal' => '{0} de zile',
				'short' => '{0} zile'
			),
			'one' => array(
				'normal' => '{0} zi',
				'short' => '{0} zi'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} de ore',
				'short' => '{0} ore'
			),
			'one' => array(
				'normal' => '{0} oră',
				'short' => '{0} oră'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} de minute',
				'short' => '{0} min.'
			),
			'one' => array(
				'normal' => '{0} minut',
				'short' => '{0} min.'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} de luni',
				'short' => '{0} luni'
			),
			'one' => array(
				'normal' => '{0} lună',
				'short' => '{0} lună'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} de secunde',
				'short' => '{0} sec.'
			),
			'one' => array(
				'normal' => '{0} secundă',
				'short' => '{0} sec.'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} de săptămâni',
				'short' => '{0} săpt.'
			),
			'one' => array(
				'normal' => '{0} săptămână',
				'short' => '{0} săpt.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} de ani',
				'short' => '{0} ani'
			),
			'one' => array(
				'normal' => '{0} an',
				'short' => '{0} an'
			)
		)
	),
	'messages' => array(
		'yes' => 'da:d',
		'no' => 'nu:n'
	)
);