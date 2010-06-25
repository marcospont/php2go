<?php
/**
 * Locale: km_KH
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'km',
	'territory' => 'KH',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'មករា',
				2 => 'កុម្ភៈ',
				3 => 'មិនា',
				4 => 'មេសា',
				5 => 'ឧសភា',
				6 => 'មិថុនា',
				7 => 'កក្កដា',
				8 => 'សីហា',
				9 => 'កញ្ញា',
				10 => 'តុលា',
				11 => 'វិច្ឆិកា',
				12 => 'ធ្នូ'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
				6 => '6',
				7 => '7',
				8 => '8',
				9 => '9',
				10 => '10',
				11 => '11',
				12 => '12'
			),
			'abbreviated' => array(
				1 => '១',
				2 => '២',
				3 => '៣',
				4 => '៤',
				5 => '៥',
				6 => '៦',
				7 => '៧',
				8 => '៨',
				9 => '៩',
				10 => '១០',
				11 => '១១',
				12 => '១២'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'ថ្ងៃអាទិត្យ',
				1 => '​ថ្ងៃច័ន្ទ',
				2 => 'ថ្ងៃអង្គារ',
				3 => 'ថ្ងៃពុធ',
				4 => 'ថ្ងៃព្រហស្បតិ៍',
				5 => 'ថ្ងៃសុក្រ',
				6 => 'ថ្ងៃសៅរ៍'
			),
			'narrow' => array(
				0 => '1',
				1 => '2',
				2 => '3',
				3 => '4',
				4 => '5',
				5 => '6',
				6 => '7'
			),
			'abbreviated' => array(
				0 => 'អា',
				1 => 'ច',
				2 => 'អ',
				3 => 'ពុ',
				4 => 'ព្រ',
				5 => 'សុ',
				6 => 'ស'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'ត្រីមាសទី១',
				2 => 'ត្រីមាសទី២',
				3 => 'ត្រីមាសទី៣',
				4 => 'ត្រីមាសទី៤'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'ត្រី១',
				2 => 'ត្រី២',
				3 => 'ត្រី៣',
				4 => 'ត្រី៤'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'ព្រឹក',
				'pm' => 'ល្ងាច'
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
				0 => 'មុន​គ.ស.',
				1 => 'គ.ស.'
			),
			'wide' => array(
				0 => 'មុន​គ្រិស្តសករាជ',
				1 => 'គ្រិស្តសករាជ'
			),
			'narrow' => array(
				0 => 'មុន​គ.ស.',
				1 => 'គ.ស.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE ថ្ងៃ d ខែ MMMM ឆ្នាំ y',
			'long' => 'd ខែ MMMM ឆ្នាំ y',
			'medium' => 'd MMM y',
			'short' => 'd/M/yyyy'
		),
		'timeFormats' => array(
			'full' => 'H ម៉ោង m នាទី ss វិនាទី​ zzzz',
			'long' => 'H ម៉ោង m នាទី ss វិនាទី​z',
			'medium' => 'H:mm:ss',
			'short' => 'H:mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1}, {0}',
			'long' => '{1}, {0}',
			'medium' => '{1}, {0}',
			'short' => '{1}, {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'Era'
			),
			'year' => array(
				'name' => 'Year'
			),
			'month' => array(
				'name' => 'Month'
			),
			'week' => array(
				'name' => 'Week'
			),
			'day' => array(
				'name' => 'Day',
				'relative' => array(
					-1 => 'Yesterday',
					0 => 'Today',
					1 => 'Tomorrow'
				)
			),
			'weekday' => array(
				'name' => 'Day of the Week'
			),
			'dayperiod' => array(
				'name' => 'Dayperiod'
			),
			'hour' => array(
				'name' => 'Hour'
			),
			'minute' => array(
				'name' => 'Minute'
			),
			'second' => array(
				'name' => 'Second'
			),
			'zone' => array(
				'name' => 'Zone'
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
		'currencyFormat' => '#,##0.00¤',
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
			'ZWD' => 'Z$',
			'KHR' => '៛'
		)
	),
	'units' => array(
		'day' => array(
			'other' => array(
				'normal' => '{0} d'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} h'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} min'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} m'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} s'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} w'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} y'
			)
		)
	),
	'messages' => array(
		'yes' => 'yes:y',
		'no' => 'no:n'
	)
);