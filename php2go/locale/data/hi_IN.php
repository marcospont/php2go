<?php
/**
 * Locale: hi_IN
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'hi',
	'territory' => 'IN',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'जनवरी',
				2 => 'फरवरी',
				3 => 'मार्च',
				4 => 'अप्रैल',
				5 => 'मई',
				6 => 'जून',
				7 => 'जुलाई',
				8 => 'अगस्त',
				9 => 'सितम्बर',
				10 => 'अक्तूबर',
				11 => 'नवम्बर',
				12 => 'दिसम्बर'
			),
			'narrow' => array(
				1 => 'ज',
				2 => 'फ़',
				3 => 'मा',
				4 => 'अ',
				5 => 'म',
				6 => 'जू',
				7 => 'जु',
				8 => 'अ',
				9 => 'सि',
				10 => 'अ',
				11 => 'न',
				12 => 'दि'
			),
			'abbreviated' => array(
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
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'रविवार',
				1 => 'सोमवार',
				2 => 'मंगलवार',
				3 => 'बुधवार',
				4 => 'बृहस्पतिवार',
				5 => 'शुक्रवार',
				6 => 'शनिवार'
			),
			'narrow' => array(
				0 => 'र',
				1 => 'सो',
				2 => 'मं',
				3 => 'बु',
				4 => 'गु',
				5 => 'शु',
				6 => 'श'
			),
			'abbreviated' => array(
				0 => 'रवि.',
				1 => 'सोम.',
				2 => 'मंगल.',
				3 => 'बुध.',
				4 => 'बृह.',
				5 => 'शुक्र.',
				6 => 'शनि.'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'तिमाही',
				2 => 'दूसरी तिमाही',
				3 => 'तीसरी तिमाही',
				4 => 'चौथी तिमाही'
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
				'am' => 'पूर्वाह्न',
				'pm' => 'अपराह्न'
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
				0 => 'ईसापूर्व',
				1 => 'सन'
			),
			'wide' => array(
				0 => 'ईसापूर्व',
				1 => 'सन'
			),
			'narrow' => array(
				0 => 'ईसापूर्व',
				1 => 'सन'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'dd-MM-yyyy',
			'short' => 'd-M-yy'
		),
		'timeFormats' => array(
			'full' => 'h:mm:ss a zzzz',
			'long' => 'h:mm:ss a z',
			'medium' => 'h:mm:ss a',
			'short' => 'h:mm a'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'युग'
			),
			'year' => array(
				'name' => 'वर्ष'
			),
			'month' => array(
				'name' => 'मास'
			),
			'week' => array(
				'name' => 'सप्ताह'
			),
			'day' => array(
				'name' => 'दिन',
				'relative' => array(
					-3 => 'नरसों',
					-2 => 'परसों',
					-1 => 'कल',
					0 => 'आज',
					1 => 'कल',
					2 => 'परसों',
					3 => 'नरसों'
				)
			),
			'weekday' => array(
				'name' => 'सप्ताह का दिन'
			),
			'dayperiod' => array(
				'name' => 'समय अवधि'
			),
			'hour' => array(
				'name' => 'घंटा'
			),
			'minute' => array(
				'name' => 'मिनट'
			),
			'second' => array(
				'name' => 'सेकेंड'
			),
			'zone' => array(
				'name' => 'क्षेत्र'
			)
		)
	),
	'numbers' => array(
		'defaultNumberingSystem' => 'deva',
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
		'decimalFormat' => '#,##,##0.###',
		'scientificFormat' => '#E0',
		'percentFormat' => '#,##,##0%',
		'currencyFormat' => '¤ #,##,##0.00',
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
			'INR' => 'रु.',
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
				'normal' => '{0} दिन',
				'short' => '{0} दि.'
			),
			'other' => array(
				'normal' => '{0} दिन',
				'short' => '{0} दि.'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} घंटा',
				'short' => '{0} घं.'
			),
			'other' => array(
				'normal' => '{0} घंटे',
				'short' => '{0} घंटे'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} मिनट',
				'short' => '{0} मिन.'
			),
			'other' => array(
				'normal' => '{0} मिनट',
				'short' => '{0} मिन.'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} महीना',
				'short' => '{0} मही.'
			),
			'other' => array(
				'normal' => '{0} महीने',
				'short' => '{0} मही.'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} सेकंड',
				'short' => '{0} सेकं.'
			),
			'other' => array(
				'normal' => '{0} सेकंड',
				'short' => '{0} सेकं.'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} सप्ताह',
				'short' => '{0} सप्त.'
			),
			'other' => array(
				'normal' => '{0} सप्ताह',
				'short' => '{0} सप्त.'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} साल',
				'short' => '{0} साल'
			),
			'other' => array(
				'normal' => '{0} साल',
				'short' => '{0} साल'
			)
		)
	),
	'messages' => array(
		'yes' => 'हाँ',
		'no' => 'नहीं'
	)
);