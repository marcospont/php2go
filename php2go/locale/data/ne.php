<?php
/**
 * Locale: ne
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4763',
	'language' => 'ne',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'जनवरी',
				2 => 'फेब्रुअरी',
				3 => 'मार्च',
				4 => 'अप्रिल',
				5 => 'मे',
				6 => 'जुन',
				7 => 'जुलाई',
				8 => 'अगस्त',
				9 => 'सेप्टेम्बर',
				10 => 'अक्टोबर',
				11 => 'नोभेम्बर',
				12 => 'डिसेम्बर'
			),
			'narrow' => array(
				1 => '१',
				2 => '२',
				3 => '३',
				4 => '४',
				5 => '५',
				6 => '६',
				7 => '७',
				8 => '८',
				9 => '९',
				10 => '१०',
				11 => '११',
				12 => '१२'
			),
			'abbreviated' => array(
				1 => 'जन',
				2 => 'फेब',
				3 => 'मार्च',
				4 => 'अप्रि',
				5 => 'मे',
				6 => 'जुन',
				7 => 'जुला',
				8 => 'अग',
				9 => 'सेप्ट',
				10 => 'अक्टो',
				11 => 'नोभे',
				12 => 'डिसे'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'आइतबार',
				1 => 'सोमबार',
				2 => 'मङ्गलबार',
				3 => 'बुधबार',
				4 => 'बिहीबार',
				5 => 'शुक्रबार',
				6 => 'शनिबार'
			),
			'narrow' => array(
				0 => '१',
				1 => '२',
				2 => '३',
				3 => '४',
				4 => '५',
				5 => '६',
				6 => '७'
			),
			'abbreviated' => array(
				0 => 'आइत',
				1 => 'सोम',
				2 => 'मङ्गल',
				3 => 'बुध',
				4 => 'बिही',
				5 => 'शुक्र',
				6 => 'शनि'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'पहिलो सत्र',
				2 => 'दोस्रो सत्र',
				3 => 'तेस्रो सत्र',
				4 => 'चौथो सत्र'
			),
			'narrow' => array(
				1 => '१',
				2 => '२',
				3 => '३',
				4 => '४'
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
				'am' => 'पूर्व मध्यान्ह',
				'pm' => 'उत्तर मध्यान्ह'
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
				0 => 'ईसा पूर्व',
				1 => 'सन्'
			),
			'wide' => array(
				0 => 'ईसा पूर्व',
				1 => 'सन्'
			),
			'narrow' => array(
				0 => 'ईसा पूर्व',
				1 => 'सन्'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, y MMMM dd',
			'long' => 'y MMMM d',
			'medium' => 'y MMM d',
			'short' => 'yyyy-MM-dd'
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
				'name' => 'काल'
			),
			'year' => array(
				'name' => 'बर्ष'
			),
			'month' => array(
				'name' => 'महिना'
			),
			'week' => array(
				'name' => 'हप्ता'
			),
			'day' => array(
				'name' => 'बार',
				'relative' => array(
					-3 => 'तीन दिन पछि',
					-2 => 'अस्ति',
					-1 => 'हिजो',
					0 => 'आज',
					1 => 'भोलि'
				)
			),
			'weekday' => array(
				'name' => 'हप्ताको बार'
			),
			'dayperiod' => array(
				'name' => 'पूर्व मध्यान्ह/उत्तर मध्यान्ह'
			),
			'hour' => array(
				'name' => 'घण्टा'
			),
			'minute' => array(
				'name' => 'मिनेट'
			),
			'second' => array(
				'name' => 'दोस्रो'
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
			'NPR' => 'नेरू',
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
				'normal' => '{0} दिन'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} घण्टा'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} मिनेट'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} महिना'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} सेकेण्ड'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} हप्ता'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} बर्ष'
			),
			'other' => array(
				'normal' => '{0} बर्ष'
			)
		)
	),
	'messages' => array(
		'yes' => 'yes:y',
		'no' => 'no:n'
	)
);