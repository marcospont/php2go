<?php
/**
 * Locale: tr_TR
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4123',
	'language' => 'tr',
	'territory' => 'TR',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'Ocak',
				2 => 'Şubat',
				3 => 'Mart',
				4 => 'Nisan',
				5 => 'Mayıs',
				6 => 'Haziran',
				7 => 'Temmuz',
				8 => 'Ağustos',
				9 => 'Eylül',
				10 => 'Ekim',
				11 => 'Kasım',
				12 => 'Aralık'
			),
			'narrow' => array(
				1 => 'O',
				2 => 'Ş',
				3 => 'M',
				4 => 'N',
				5 => 'M',
				6 => 'H',
				7 => 'T',
				8 => 'A',
				9 => 'E',
				10 => 'E',
				11 => 'K',
				12 => 'A'
			),
			'abbreviated' => array(
				1 => 'Oca',
				2 => 'Şub',
				3 => 'Mar',
				4 => 'Nis',
				5 => 'May',
				6 => 'Haz',
				7 => 'Tem',
				8 => 'Ağu',
				9 => 'Eyl',
				10 => 'Eki',
				11 => 'Kas',
				12 => 'Ara'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'Pazar',
				1 => 'Pazartesi',
				2 => 'Salı',
				3 => 'Çarşamba',
				4 => 'Perşembe',
				5 => 'Cuma',
				6 => 'Cumartesi'
			),
			'narrow' => array(
				0 => 'P',
				1 => 'P',
				2 => 'S',
				3 => 'Ç',
				4 => 'P',
				5 => 'C',
				6 => 'C'
			),
			'abbreviated' => array(
				0 => 'Paz',
				1 => 'Pzt',
				2 => 'Sal',
				3 => 'Çar',
				4 => 'Per',
				5 => 'Cum',
				6 => 'Cmt'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1. çeyrek',
				2 => '2. çeyrek',
				3 => '3. çeyrek',
				4 => '4. çeyrek'
			),
			'narrow' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4'
			),
			'abbreviated' => array(
				1 => 'Ç1',
				2 => 'Ç2',
				3 => 'Ç3',
				4 => 'Ç4'
			)
		),
		'dayPeriods' => array(
			'wide' => array(
				'am' => 'ÖÖ',
				'pm' => 'ÖS'
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
				0 => 'MÖ',
				1 => 'MS'
			),
			'wide' => array(
				0 => 'Milattan Önce',
				1 => 'Milattan Sonra'
			),
			'narrow' => array(
				0 => 'MÖ',
				1 => 'MS'
			)
		),
		'dateFormats' => array(
			'full' => 'dd MMMM y EEEE',
			'long' => 'dd MMMM y',
			'medium' => 'dd MMM y',
			'short' => 'dd.MM.yyyy'
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
				'name' => 'Miladi Dönem'
			),
			'year' => array(
				'name' => 'Yıl'
			),
			'month' => array(
				'name' => 'Ay'
			),
			'week' => array(
				'name' => 'Hafta'
			),
			'day' => array(
				'name' => 'Gün',
				'relative' => array(
					-3 => 'Üç gün önce',
					-2 => 'Evvelsi gün',
					-1 => 'Dün',
					0 => 'Bugün',
					1 => 'Yarın',
					2 => 'Yarından sonraki gün',
					3 => 'Üç gün sonra'
				)
			),
			'weekday' => array(
				'name' => 'Haftanın Günü'
			),
			'dayperiod' => array(
				'name' => 'AM/PM'
			),
			'hour' => array(
				'name' => 'Saat'
			),
			'minute' => array(
				'name' => 'Dakika'
			),
			'second' => array(
				'name' => 'Saniye'
			),
			'zone' => array(
				'name' => 'Saat Dilimi'
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
		'percentFormat' => '% #,##0',
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
			'USD' => '$',
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
				'normal' => '{0} gün',
				'short' => '{0} gün'
			)
		),
		'hour' => array(
			'other' => array(
				'normal' => '{0} saat',
				'short' => '{0} sa.'
			)
		),
		'minute' => array(
			'other' => array(
				'normal' => '{0} dakika',
				'short' => '{0} dk.'
			)
		),
		'month' => array(
			'other' => array(
				'normal' => '{0} ay',
				'short' => '{0} ay'
			)
		),
		'second' => array(
			'other' => array(
				'normal' => '{0} saniye',
				'short' => '{0} sn.'
			)
		),
		'week' => array(
			'other' => array(
				'normal' => '{0} hafta',
				'short' => '{0} hafta'
			)
		),
		'year' => array(
			'other' => array(
				'normal' => '{0} yıl',
				'short' => '{0} yıl'
			)
		)
	),
	'messages' => array(
		'yes' => 'evet:e',
		'no' => 'hayır:hayir:h'
	)
);