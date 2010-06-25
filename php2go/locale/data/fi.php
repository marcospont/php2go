<?php
/**
 * Locale: fi
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4876',
	'language' => 'fi',
	'territory' => '',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'tammikuu',
				2 => 'helmikuu',
				3 => 'maaliskuu',
				4 => 'huhtikuu',
				5 => 'toukokuu',
				6 => 'kesäkuu',
				7 => 'heinäkuu',
				8 => 'elokuu',
				9 => 'syyskuu',
				10 => 'lokakuu',
				11 => 'marraskuu',
				12 => 'joulukuu'
			),
			'narrow' => array(
				1 => 'T',
				2 => 'H',
				3 => 'M',
				4 => 'H',
				5 => 'T',
				6 => 'K',
				7 => 'H',
				8 => 'E',
				9 => 'S',
				10 => 'L',
				11 => 'M',
				12 => 'J'
			),
			'abbreviated' => array(
				1 => 'tammi',
				2 => 'helmi',
				3 => 'maalis',
				4 => 'huhti',
				5 => 'touko',
				6 => 'kesä',
				7 => 'heinä',
				8 => 'elo',
				9 => 'syys',
				10 => 'loka',
				11 => 'marras',
				12 => 'joulu'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'sunnuntai',
				1 => 'maanantai',
				2 => 'tiistai',
				3 => 'keskiviikko',
				4 => 'torstai',
				5 => 'perjantai',
				6 => 'lauantai'
			),
			'narrow' => array(
				0 => 'S',
				1 => 'M',
				2 => 'T',
				3 => 'K',
				4 => 'T',
				5 => 'P',
				6 => 'L'
			),
			'abbreviated' => array(
				0 => 'su',
				1 => 'ma',
				2 => 'ti',
				3 => 'ke',
				4 => 'to',
				5 => 'pe',
				6 => 'la'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. neljännes',
				2 => '2. neljännes',
				3 => '3. neljännes',
				4 => '4. neljännes'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1. nelj.',
				2 => '2. nelj.',
				3 => '3. nelj.',
				4 => '4. nelj.'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'aamupäivä',
				'pm' => 'iltapäivä'
			),
			'abbreviated' => array(
				'am' => 'a.p.',
				'pm' => 'i.p.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'e.Kr.',
				1 => 'j.Kr.'
			),
			'wide' => array(
				0 => 'ennen Kristuksen syntymää',
				1 => 'jälkeen Kristuksen syntymän'
			),
			'narrow' => array(
				0 => 'e.Kr.',
				1 => 'j.Kr.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d. MMMM y',
			'long' => 'd. MMMM y',
			'medium' => 'd.M.yyyy',
			'short' => 'd.M.yyyy'
		),
		'timeFormats' => array(
			'full' => 'H.mm.ss zzzz',
			'long' => 'H.mm.ss z',
			'medium' => 'H.mm.ss',
			'short' => 'H.mm'
		),
		'dateTimeFormats' => array(
			'full' => '{1} {0}',
			'long' => '{1} {0}',
			'medium' => '{1} {0}',
			'short' => '{1} {0}'
		),
		'fields' => array(
			'era' => array(
				'name' => 'aikakausi'
			),
			'year' => array(
				'name' => 'vuosi'
			),
			'month' => array(
				'name' => 'kuukausi'
			),
			'week' => array(
				'name' => 'viikko'
			),
			'day' => array(
				'name' => 'päivä',
				'relative' => array(
					-2 => 'toissapäivänä',
					-1 => 'eilen',
					0 => 'tänään',
					1 => 'huomenna',
					2 => 'ylihuomenna'
				)
			),
			'weekday' => array(
				'name' => 'viikonpäivä'
			),
			'dayperiod' => array(
				'name' => 'ap./ip.'
			),
			'hour' => array(
				'name' => 'tunti'
			),
			'minute' => array(
				'name' => 'minuutti'
			),
			'second' => array(
				'name' => 'sekunti'
			),
			'zone' => array(
				'name' => 'aikavyöhyke'
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
			'nan' => 'epäluku'
		),
		'decimalFormat' => '#,##0.###',
		'scientificFormat' => '#E0',
		'percentFormat' => '#,##0 %',
		'currencyFormat' => '#,##0.00 ¤',
		'currencies' => array(
			'AFN' => 'AFN',
			'ANG' => 'ANG',
			'AOA' => 'AOA',
			'ARA' => 'ARA',
			'ARL' => 'ARL',
			'ARM' => 'ARM',
			'ARS' => 'ARS',
			'AUD' => 'AUD',
			'AWG' => 'AWG',
			'AZN' => 'AZN',
			'BAM' => 'BAM',
			'BBD' => 'BBD',
			'BDT' => 'BDT',
			'BEF' => 'BEF',
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
			'DEM' => 'DEM',
			'DJF' => 'DJF',
			'DKK' => 'Tkr',
			'DOP' => 'DOP',
			'DZD' => 'DZD',
			'EEK' => 'EEK',
			'EGP' => 'EGP',
			'ERN' => 'ERN',
			'ESP' => 'ESP',
			'ETB' => 'ETB',
			'EUR' => '€',
			'FIM' => 'mk',
			'FJD' => 'FJD',
			'FKP' => 'FKP',
			'FRF' => 'FRF',
			'GBP' => '£',
			'GHC' => 'GHC',
			'GHS' => 'GHS',
			'GIP' => 'GIP',
			'GMD' => 'GMD',
			'GNF' => 'GNF',
			'GRD' => 'GRD',
			'GTQ' => 'GTQ',
			'GYD' => 'GYD',
			'HKD' => 'HKD',
			'HNL' => 'HNL',
			'HRK' => 'HRK',
			'HTG' => 'HTG',
			'HUF' => 'HUF',
			'IDR' => 'IDR',
			'IEP' => 'IEP',
			'ILP' => 'ILP',
			'ILS' => 'ILS',
			'INR' => 'INR',
			'ISK' => 'ISK',
			'ITL' => 'ITL',
			'JMD' => 'JMD',
			'JOD' => 'JOD',
			'JPY' => '¥',
			'KES' => 'KES',
			'KMF' => 'KMF',
			'KRW' => 'KRW',
			'KWD' => 'KWD',
			'KYD' => 'KYD',
			'LAK' => 'LAK',
			'LBP' => 'LBP',
			'LKR' => 'LKR',
			'LRD' => 'LRD',
			'LSL' => 'LSL',
			'LTL' => 'LTL',
			'LVL' => 'LVL',
			'LYD' => 'LYD',
			'MMK' => 'MMK',
			'MNT' => 'MNT',
			'MOP' => 'MOP',
			'MRO' => 'MRO',
			'MTL' => 'MTL',
			'MTP' => 'MTP',
			'MUR' => 'MUR',
			'MXN' => 'MXN',
			'MYR' => 'MYR',
			'MZM' => 'MZM',
			'MZN' => 'MZN',
			'NAD' => 'NAD',
			'NGN' => 'NGN',
			'NIO' => 'NIO',
			'NLG' => 'NLG',
			'NOK' => 'Nkr',
			'NPR' => 'NPR',
			'NZD' => 'NZD',
			'PAB' => 'PAB',
			'PEI' => 'PEI',
			'PEN' => 'PEN',
			'PGK' => 'PGK',
			'PHP' => 'PHP',
			'PKR' => 'PKR',
			'PLN' => 'PLN',
			'PTE' => 'PTE',
			'PYG' => 'PYG',
			'QAR' => 'QAR',
			'RHD' => 'RHD',
			'RON' => 'RON',
			'RSD' => 'RSD',
			'SAR' => 'SAR',
			'SBD' => 'SBD',
			'SCR' => 'SCR',
			'SDD' => 'SDD',
			'SEK' => 'Rkr',
			'SGD' => 'SGD',
			'SHP' => 'SHP',
			'SKK' => 'SKK',
			'SLL' => 'SLL',
			'SOS' => 'SOS',
			'SRD' => 'SRD',
			'SRG' => 'SRG',
			'STD' => 'STD',
			'SVC' => 'SVC',
			'SYP' => 'SYP',
			'SZL' => 'SZL',
			'THB' => 'THB',
			'TMM' => 'TMM',
			'TND' => 'TND',
			'TOP' => 'TOP',
			'TRL' => 'TRL',
			'TRY' => 'TRY',
			'TTD' => 'TTD',
			'TWD' => 'TWD',
			'TZS' => 'TZS',
			'UAH' => 'UAH',
			'UGX' => 'UGX',
			'USD' => '$',
			'UYU' => 'UYU',
			'VEF' => 'VEF',
			'VND' => 'VND',
			'VUV' => 'VUV',
			'WST' => 'WST',
			'XAF' => 'XAF',
			'XCD' => 'EC$',
			'XOF' => 'CFA',
			'XPF' => 'CFPF',
			'YER' => 'YER',
			'ZAR' => 'ZAR',
			'ZMK' => 'ZMK',
			'ZRN' => 'ZRN',
			'ZRZ' => 'ZRZ',
			'ZWD' => 'ZWD',
			'AED' => 'AED',
			'ALK' => 'ALK',
			'ALL' => 'ALL',
			'AMD' => 'AMD',
			'AZM' => 'AZM',
			'BAN' => 'BAN',
			'BGM' => 'BGM',
			'BGN' => 'BGN',
			'BGO' => 'BGO',
			'BOL' => 'BOL',
			'BRB' => 'BRB',
			'BRC' => 'BRC',
			'BRE' => 'BRE',
			'BRN' => 'BRN',
			'BRR' => 'BRR',
			'BRZ' => 'BRZ',
			'BYR' => 'BYR',
			'CHF' => 'CHF',
			'CNX' => 'CNX',
			'GEL' => 'GEL',
			'IQD' => 'IQD',
			'IRR' => 'IRR',
			'KGS' => 'KGS',
			'KHR' => 'KHR',
			'KPW' => 'KPW',
			'KRH' => 'KRH',
			'KRO' => 'KRO',
			'KZT' => 'KZT',
			'MAD' => 'MAD',
			'MCF' => 'MCF',
			'MDC' => 'MDC',
			'MDL' => 'MDL',
			'MGA' => 'MGA',
			'MKD' => 'MKD',
			'MKN' => 'MKN',
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
			'UZS' => 'UZS',
			'VEB' => 'VEB',
			'VNN' => 'VNN',
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
			'YUR' => 'YUR',
			'ZWL' => 'ZWL'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} vrk',
				'short' => '{0} pv'
			),
			'other' => array(
				'normal' => '{0} vrk',
				'short' => '{0} pv'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} tunti',
				'short' => '{0} t'
			),
			'other' => array(
				'normal' => '{0} tuntia',
				'short' => '{0} t'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minuutti',
				'short' => '{0} min'
			),
			'other' => array(
				'normal' => '{0} minuuttia',
				'short' => '{0} min'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} kuukausi',
				'short' => '{0} kk'
			),
			'other' => array(
				'normal' => '{0} kuukautta',
				'short' => '{0} kk'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} sekunti',
				'short' => '{0} s'
			),
			'other' => array(
				'normal' => '{0} sekuntia',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} viikko',
				'short' => '{0} vko'
			),
			'other' => array(
				'normal' => '{0} viikkoa',
				'short' => '{0} vko'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} vuosi',
				'short' => '{0} v'
			),
			'other' => array(
				'normal' => '{0} vuotta',
				'short' => '{0} v'
			)
		)
	),
	'messages' => array(
		'yes' => 'kyllä:k',
		'no' => 'ei:e'
	)
);