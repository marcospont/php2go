<?php
/**
 * Locale: nb_NO
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'nb',
	'territory' => 'NO',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'januar',
				2 => 'februar',
				3 => 'mars',
				4 => 'april',
				5 => 'mai',
				6 => 'juni',
				7 => 'juli',
				8 => 'august',
				9 => 'september',
				10 => 'oktober',
				11 => 'november',
				12 => 'desember'
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
				2 => 'feb',
				3 => 'mar',
				4 => 'apr',
				5 => 'mai',
				6 => 'jun',
				7 => 'jul',
				8 => 'aug',
				9 => 'sep',
				10 => 'okt',
				11 => 'nov',
				12 => 'des'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'søndag',
				1 => 'mandag',
				2 => 'tirsdag',
				3 => 'onsdag',
				4 => 'torsdag',
				5 => 'fredag',
				6 => 'lørdag'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'M',
				2 => 'T',
				3 => 'O',
				4 => 'T',
				5 => 'F',
				6 => 'L'
			),
			'abbreviated' => array(
				0 => 'sø.',
				1 => 'ma.',
				2 => 'ti.',
				3 => 'on.',
				4 => 'to.',
				5 => 'fr.',
				6 => 'lø.'
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
				1 => 'K1',
				2 => 'K2',
				3 => 'K3',
				4 => 'K4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'formiddag',
				'pm' => 'ettermiddag'
			),
			'abbreviated' => array(
				'am' => 'f.m.',
				'pm' => 'e.m.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'f.Kr.',
				1 => 'e.Kr.'
			),
			'wide' => array(
				0 => 'før Kristus',
				1 => 'etter Kristus'
			),
			'narrow' => array(
				0 => 'f.Kr.',
				1 => 'e.Kr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'd. MMM y',
			'short' => 'dd.MM.yy'
		),
		'timeFormats' => array(
			'full' => '\'kl\'. HH:mm:ss zzzz',
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
				'name' => 'tidsalder'
			),
			'year' => array(
				'name' => 'år'
			),
			'month' => array(
				'name' => 'måned'
			),
			'week' => array(
				'name' => 'uke'
			),
			'day' => array(
				'name' => 'dag',
				'relative' => array(
					-3 => 'i forforgårs',
					-2 => 'i forgårs',
					-1 => 'i går',
					0 => 'i dag',
					1 => 'i morgen',
					2 => 'i overmorgen',
					3 => 'i overovermorgen'
				)
			),
			'weekday' => array(
				'name' => 'ukedag'
			),
			'dayperiod' => array(
				'name' => 'AM/PM'
			),
			'hour' => array(
				'name' => 'time'
			),
			'minute' => array(
				'name' => 'minutt'
			),
			'second' => array(
				'name' => 'sekund'
			),
			'zone' => array(
				'name' => 'sone'
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
		'currencyFormat' => '¤ #,##0.00',
		'currencies' => array(
			'AFN' => 'AFN',
			'ANG' => 'ANG',
			'AOA' => 'AOA',
			'ARA' => '₳',
			'ARL' => 'ARL',
			'ARM' => 'ARM',
			'ARS' => 'ARS',
			'AUD' => 'AUD',
			'AWG' => 'AWG',
			'AZN' => 'AZN',
			'BAM' => 'BAM',
			'BBD' => 'BBD',
			'BDT' => 'BDT',
			'BEF' => 'BF',
			'BHD' => 'BHD',
			'BIF' => 'BIF',
			'BMD' => 'BMD',
			'BND' => 'BND',
			'BOB' => 'BOB',
			'BOP' => 'BOP',
			'BRL' => 'BRL',
			'BSD' => 'BSD',
			'BTN' => 'BTN',
			'BWP' => 'BWP',
			'BZD' => 'BZD',
			'CAD' => 'CAD',
			'CDF' => 'CDF',
			'CLE' => 'CLE',
			'CLP' => 'CLP',
			'CNY' => 'CNY',
			'COP' => 'COP',
			'CRC' => 'CRC',
			'CUC' => 'CUC',
			'CUP' => 'CUP',
			'CVE' => 'CVE',
			'CYP' => 'CYP',
			'CZK' => 'CZK',
			'DEM' => 'DM',
			'DJF' => 'DJF',
			'DKK' => 'DKK',
			'DOP' => 'DOP',
			'DZD' => 'DZD',
			'EEK' => 'EEK',
			'EGP' => 'EGP',
			'ERN' => 'ERN',
			'ESP' => 'Pts',
			'ETB' => 'ETB',
			'EUR' => 'EUR',
			'FIM' => 'FIM',
			'FJD' => 'FJD',
			'FKP' => 'FKP',
			'FRF' => 'FRF',
			'GBP' => 'GBP',
			'GHC' => 'GHC',
			'GHS' => 'GHS',
			'GIP' => 'GIP',
			'GMD' => 'GMD',
			'GNF' => 'GNF',
			'GRD' => 'GRD',
			'GTQ' => 'GTQ',
			'GYD' => 'GY$',
			'HKD' => 'HK$',
			'HNL' => 'HNL',
			'HRK' => 'kn',
			'HTG' => 'HTG',
			'HUF' => 'Ft',
			'IDR' => 'Rp',
			'IEP' => 'IEP',
			'ILP' => 'I£',
			'ILS' => 'ILS',
			'INR' => 'INR',
			'ISK' => 'Ikr',
			'ITL' => 'ITL',
			'JMD' => 'J$',
			'JOD' => 'JOD',
			'JPY' => 'JPY',
			'KES' => 'Ksh',
			'KMF' => 'CF',
			'KRW' => '₩',
			'KWD' => 'KD',
			'KYD' => 'KY$',
			'LAK' => '₭',
			'LBP' => 'LB£',
			'LKR' => 'LKR',
			'LRD' => 'L$',
			'LSL' => 'LSL',
			'LTL' => 'LTL',
			'LVL' => 'Ls',
			'LYD' => 'LYD',
			'MMK' => 'MMK',
			'MNT' => 'MNT',
			'MOP' => 'MOP$',
			'MRO' => 'UM',
			'MTL' => 'MTL',
			'MTP' => 'MTP',
			'MUR' => 'MURs',
			'MXN' => 'MXN',
			'MYR' => 'MYR',
			'MZM' => 'MZM',
			'MZN' => 'MZN',
			'NAD' => 'NAD',
			'NGN' => 'NGN',
			'NIO' => 'NIO',
			'NLG' => 'NLG',
			'NOK' => 'kr',
			'NPR' => 'NPR',
			'NZD' => 'NZ$',
			'PAB' => 'PAB',
			'PEI' => 'PEI',
			'PEN' => 'PEN',
			'PGK' => 'PGK',
			'PHP' => 'PHP',
			'PKR' => 'PKR',
			'PLN' => 'PLN',
			'PTE' => 'PTE',
			'PYG' => 'PYG',
			'QAR' => 'QR',
			'RHD' => 'RHD',
			'RON' => 'RON',
			'RSD' => 'RSD',
			'SAR' => 'SAR',
			'SBD' => 'SI$',
			'SCR' => 'SCR',
			'SDD' => 'SDD',
			'SEK' => 'SEK',
			'SGD' => 'S$',
			'SHP' => 'SH£',
			'SKK' => 'SKK',
			'SLL' => 'SLL',
			'SOS' => 'SOS',
			'SRD' => 'SR$',
			'SRG' => 'SRG',
			'STD' => 'STD',
			'SVC' => 'SV₡',
			'SYP' => 'SYP',
			'SZL' => 'SZL',
			'THB' => 'THB',
			'TMM' => 'TMM',
			'TND' => 'TND',
			'TOP' => 'TOP',
			'TRL' => 'TRL',
			'TRY' => 'TRY',
			'TTD' => 'TT$',
			'TWD' => 'TWD',
			'TZS' => 'TZS',
			'UAH' => 'UAH',
			'UGX' => 'UGX',
			'USD' => 'USD',
			'UYU' => '$U',
			'VEF' => 'VEF',
			'VND' => 'VND',
			'VUV' => 'VUV',
			'WST' => 'WST',
			'XAF' => 'XAF',
			'XCD' => 'XCD',
			'XOF' => 'CFA',
			'XPF' => 'CFPF',
			'YER' => 'YR',
			'ZAR' => 'ZAR',
			'ZMK' => 'ZMK',
			'ZRN' => 'ZRN',
			'ZRZ' => 'ZRZ',
			'ZWD' => 'ZWD',
			'AED' => 'AED',
			'ALL' => 'ALL',
			'AMD' => 'AMD',
			'AZM' => 'AZM',
			'BGN' => 'BGN',
			'BRB' => 'BRB',
			'BRC' => 'BRC',
			'BRE' => 'BRE',
			'BRZ' => 'BRZ',
			'BYR' => 'BYR',
			'CHF' => 'CHF',
			'GEL' => 'GEL',
			'IQD' => 'IQD',
			'IRR' => 'IRR',
			'KGS' => 'KGS',
			'KHR' => 'KHR',
			'KPW' => 'KPW',
			'KZT' => 'KZT',
			'MAD' => 'MAD',
			'MDL' => 'MDL',
			'MGA' => 'MGA',
			'MKD' => 'MKD',
			'MVR' => 'MVR',
			'MWK' => 'MWK',
			'MXP' => 'MXP',
			'OMR' => 'OMR',
			'ROL' => 'ROL',
			'RUB' => 'RUB',
			'RWF' => 'RWF',
			'SDG' => 'SDG',
			'TJS' => 'TJS',
			'TMT' => 'TMT',
			'XAG' => 'XAG',
			'XAU' => 'XAU',
			'XBA' => 'XBA',
			'XBB' => 'XBB',
			'XBC' => 'XBC',
			'XBD' => 'XBD',
			'XDR' => 'XDR',
			'XFO' => 'XFO',
			'XFU' => 'XFU',
			'XPD' => 'XPD',
			'XPT' => 'XPT',
			'XTS' => 'XTS',
			'XXX' => 'XXX',
			'ZWL' => 'ZWL'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} d',
				'short' => '{0} dag'
			),
			'other' => array(
				'normal' => '{0} d',
				'short' => '{0} dager'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} time',
				'short' => '{0} t'
			),
			'other' => array(
				'normal' => '{0} timer',
				'short' => '{0} t'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minutt',
				'short' => '{0} min'
			),
			'other' => array(
				'normal' => '{0} minutter',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mån',
				'short' => '{0} md.'
			),
			'other' => array(
				'normal' => '{0} mån',
				'short' => '{0} md.'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} s',
				'short' => '{0} sek'
			),
			'other' => array(
				'normal' => '{0} s',
				'short' => '{0} sek'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} u',
				'short' => '{0} uke'
			),
			'other' => array(
				'normal' => '{0} u',
				'short' => '{0} uker'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} år',
				'short' => '{0} år'
			),
			'other' => array(
				'normal' => '{0} år',
				'short' => '{0} år'
			)
		)
	),
	'messages' => array(
		'yes' => 'ja',
		'no' => 'nei'
	)
);