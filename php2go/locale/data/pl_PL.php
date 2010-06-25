<?php
/**
 * Locale: pl_PL
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'pl',
	'territory' => 'PL',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'styczeń',
				2 => 'luty',
				3 => 'marzec',
				4 => 'kwiecień',
				5 => 'maj',
				6 => 'czerwiec',
				7 => 'lipiec',
				8 => 'sierpień',
				9 => 'wrzesień',
				10 => 'październik',
				11 => 'listopad',
				12 => 'grudzień'
			),
			'narrow' => array(
				1 => 's',
				2 => 'l',
				3 => 'm',
				4 => 'k',
				5 => 'm',
				6 => 'c',
				7 => 'l',
				8 => 's',
				9 => 'w',
				10 => 'p',
				11 => 'l',
				12 => 'g'
			),
			'abbreviated' => array(
				1 => 'sty',
				2 => 'lut',
				3 => 'mar',
				4 => 'kwi',
				5 => 'maj',
				6 => 'cze',
				7 => 'lip',
				8 => 'sie',
				9 => 'wrz',
				10 => 'paź',
				11 => 'lis',
				12 => 'gru'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'niedziela',
				1 => 'poniedziałek',
				2 => 'wtorek',
				3 => 'środa',
				4 => 'czwartek',
				5 => 'piątek',
				6 => 'sobota'
			),
			'narrow' => array(
				0 => 'N',
				1 => 'P',
				2 => 'W',
				3 => 'Ś',
				4 => 'C',
				5 => 'P',
				6 => 'S'
			),
			'abbreviated' => array(
				0 => 'niedz.',
				1 => 'pon.',
				2 => 'wt.',
				3 => 'śr.',
				4 => 'czw.',
				5 => 'pt.',
				6 => 'sob.'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => 'I kwartał',
				2 => 'II kwartał',
				3 => 'III kwartał',
				4 => 'IV kwartał'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => '1 kw.',
				2 => '2 kw.',
				3 => '3 kw.',
				4 => '4 kw.'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'AM',
				'pm' => 'PM',
				'afternoon' => 'po południu',
				'earlyMorning' => 'nad ranem',
				'evening' => 'wieczorem',
				'lateMorning' => 'przed południem',
				'morning' => 'rano',
				'night' => 'w nocy',
				'noon' => 'w południe'
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
				0 => 'p.n.e.',
				1 => 'n.e.'
			),
			'wide' => array(
				0 => 'przed naszą erą',
				1 => 'naszej ery'
			),
			'narrow' => array(
				0 => 'p.n.e.',
				1 => 'n.e.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE, d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'dd-MM-yyyy',
			'short' => 'dd-MM-yyyy'
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
				'name' => 'Era'
			),
			'year' => array(
				'name' => 'Rok'
			),
			'month' => array(
				'name' => 'Miesiąc'
			),
			'week' => array(
				'name' => 'Tydzień'
			),
			'day' => array(
				'name' => 'Dzień',
				'relative' => array(
					-3 => 'Trzy dni temu',
					-2 => 'Przedwczoraj',
					-1 => 'Wczoraj',
					0 => 'Dzisiaj',
					1 => 'Jutro',
					2 => 'Pojutrze',
					3 => 'Za trzy dni'
				)
			),
			'weekday' => array(
				'name' => 'Dzień tygodnia'
			),
			'dayperiod' => array(
				'name' => 'Dayperiod'
			),
			'hour' => array(
				'name' => 'Godzina'
			),
			'minute' => array(
				'name' => 'Minuta'
			),
			'second' => array(
				'name' => 'Sekunda'
			),
			'zone' => array(
				'name' => 'Strefa'
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
				'normal' => '{0} dni',
				'short' => '{0} dni'
			),
			'one' => array(
				'normal' => '{0} dzień',
				'short' => '{0} dzień'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} godzin',
				'short' => '{0} godz.'
			),
			'one' => array(
				'normal' => '{0} godzina',
				'short' => '{0} godz.'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} minut',
				'short' => '{0} min.'
			),
			'one' => array(
				'normal' => '{0} minuta',
				'short' => '{0} min.'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} miesięcy',
				'short' => '{0} mies.'
			),
			'one' => array(
				'normal' => '{0} miesiąc',
				'short' => '{0} mies.'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} sekund',
				'short' => '{0} sek.'
			),
			'one' => array(
				'normal' => '{0} sekunda',
				'short' => '{0} sek.'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} tygodni',
				'short' => '{0} tyg.'
			),
			'one' => array(
				'normal' => '{0} tydzień',
				'short' => '{0} tydz.'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} lat',
				'short' => '{0} lat'
			),
			'one' => array(
				'normal' => '{0} rok',
				'short' => '{0} rok'
			)
		)
	),
	'messages' => array(
		'yes' => 'tak:t',
		'no' => 'nie:n'
	)
);