<?php
/**
 * Locale: sr_Latn_ME
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4582',
	'language' => 'sr',
	'territory' => 'ME',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'januar',
				2 => 'februar',
				3 => 'mart',
				4 => 'april',
				5 => 'maj',
				6 => 'jun',
				7 => 'jul',
				8 => 'avgust',
				9 => 'septembar',
				10 => 'oktobar',
				11 => 'novembar',
				12 => 'decembar'
			),
			'narrow' => array(
				1 => 'j',
				2 => 'f',
				3 => 'm',
				4 => 'a',
				5 => 'm',
				6 => 'j',
				7 => 'j',
				8 => 'a',
				9 => 's',
				10 => 'o',
				11 => 'n',
				12 => 'd'
			),
			'abbreviated' => array(
				1 => 'jan',
				2 => 'feb',
				3 => 'mar',
				4 => 'apr',
				5 => 'maj',
				6 => 'jun',
				7 => 'jul',
				8 => 'avg',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'dec'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'nedelja',
				1 => 'ponedeljak',
				2 => 'utorak',
				3 => 'sreda',
				4 => 'četvrtak',
				5 => 'petak',
				6 => 'subota'
			),
			'narrow' => array(
				0 => 'n',
				1 => 'p',
				2 => 'u',
				3 => 's',
				4 => 'č',
				5 => 'p',
				6 => 's'
			),
			'abbreviated' => array(
				0 => 'ned',
				1 => 'pon',
				2 => 'uto',
				3 => 'sre',
				4 => 'čet',
				5 => 'pet',
				6 => 'sub'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. kvartal',
				2 => '2. kvartal',
				3 => '3. kvartal',
				4 => '4. kvartal'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'Q1',
				2 => 'Q2',
				3 => 'Q3',
				4 => 'Q4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'pre podne',
				'pm' => 'popodne'
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
				0 => 'p. n. e.',
				1 => 'n. e'
			),
			'wide' => array(
				0 => 'Pre nove ere',
				1 => 'Nove ere'
			),
			'narrow' => array(
				0 => 'p.n.e.',
				1 => 'n.e.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, dd. MMMM y.',
			'long' => 'd.MM.yyyy.',
			'medium' => 'dd.MM.y.',
			'short' => 'd.M.yy.'
		),
		'timeFormats' => array(
			'full' => 'HH.mm.ss zzzz',
			'long' => 'HH.mm.ss z',
			'medium' => 'HH.mm.ss',
			'short' => 'HH.mm'
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
				'name' => 'godina'
			),
			'month' => array(
				'name' => 'mesec'
			),
			'week' => array(
				'name' => 'nedelja'
			),
			'day' => array(
				'name' => 'dan',
				'relative' => array(
					-3 => 'pre tri dana',
					-2 => 'prekjuče',
					-1 => 'juče',
					0 => 'danas',
					1 => 'sutra',
					2 => 'prekosutra',
					3 => 'za tri dana'
				)
			),
			'weekday' => array(
				'name' => 'dan u nedelji'
			),
			'dayperiod' => array(
				'name' => 'pre podne/ popodne'
			),
			'hour' => array(
				'name' => 'čas'
			),
			'minute' => array(
				'name' => 'minut'
			),
			'second' => array(
				'name' => 'sekund'
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
			'PLN' => 'zl',
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
				'normal' => '{0} dana',
				'short' => '{0} dan'
			),
			'one' => array(
				'normal' => '{0} dan',
				'short' => '{0} dan'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} sati',
				'short' => '{0} sat'
			),
			'one' => array(
				'normal' => '{0} sat',
				'short' => '{0} sat'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} minuta',
				'short' => '{0} min'
			),
			'one' => array(
				'normal' => '{0} minut',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} meseci',
				'short' => '{0} mes'
			),
			'one' => array(
				'normal' => '{0} mesec',
				'short' => '{0} mes'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} sekundi',
				'short' => '{0} sek'
			),
			'one' => array(
				'normal' => '{0} sekunda',
				'short' => '{0} sek'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} nedelja',
				'short' => '{0} ned'
			),
			'one' => array(
				'normal' => '{0} nedelja',
				'short' => '{0} ned'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} godina',
				'short' => '{0} god'
			),
			'one' => array(
				'normal' => '{0} godina',
				'short' => '{0} god'
			)
		)
	),
	'messages' => array(
		'yes' => 'da:d',
		'no' => 'ne:n'
	)
);