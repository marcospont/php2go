<?php
/**
 * Locale: fr_MG
 *
 * This file is a subset of the original CLDR file produced by unicode.org.
 * http://cldr.unicode.org/
 *
 * Copyright © 1991-2010 Unicode, Inc. All rights reserved.
 * Distributed under the Terms of Use in http://www.unicode.org/copyright.html.
 */
return array(
	'version' => '4590',
	'language' => 'fr',
	'territory' => 'MG',
	'orientation' => 'ltr',
	'dates' => array(
		'months' => array(
			'wide' => array(
				1 => 'janvier',
				2 => 'février',
				3 => 'mars',
				4 => 'avril',
				5 => 'mai',
				6 => 'juin',
				7 => 'juillet',
				8 => 'août',
				9 => 'septembre',
				10 => 'octobre',
				11 => 'novembre',
				12 => 'décembre'
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
				2 => 'fév',
				3 => 'mar',
				4 => 'avr',
				5 => 'mai',
				6 => 'juin',
				7 => 'juil',
				8 => 'août',
				9 => 'sept',
				10 => 'oct',
				11 => 'nov',
				12 => 'déc'
			)
		),
		'weekDays' => array(
			'wide' => array(
				0 => 'dimanche',
				1 => 'lundi',
				2 => 'mardi',
				3 => 'mercredi',
				4 => 'jeudi',
				5 => 'vendredi',
				6 => 'samedi'
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
				0 => 'dim',
				1 => 'lun',
				2 => 'mar',
				3 => 'mer',
				4 => 'jeu',
				5 => 'ven',
				6 => 'sam'
			)
		),
		'quarters' => array(
			'wide' => array(
				1 => '1er trimestre',
				2 => '2e trimestre',
				3 => '3e trimestre',
				4 => '4e trimestre'
			),
			'narrow' => array(
				1 => 'T1',
				2 => 'T2',
				3 => 'T3',
				4 => 'T4'
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
				'am' => 'avant-midi',
				'pm' => 'après-midi',
				'afternoon' => 'après-midi',
				'morning' => 'matin',
				'night' => 'soir',
				'noon' => 'midi'
			),
			'abbreviated' => array(
				'am' => 'av.m.',
				'pm' => 'ap.m.',
				'afternoon' => 'ap.m.'
			),
			'narrow' => array(
				'am' => 'AM',
				'pm' => 'PM'
			)
		),
		'eras' => array(
			'abbreviated' => array(
				0 => 'av. J.-C.',
				1 => 'ap. J.-C.'
			),
			'wide' => array(
				0 => 'avant Jésus-Christ',
				1 => 'après Jésus-Christ'
			),
			'narrow' => array(
				0 => 'av. J.-C.',
				1 => 'ap. J.-C.'
			)
		),
		'dateFormats' => array(
			'full' => 'EEEE d MMMM y',
			'long' => 'd MMMM y',
			'medium' => 'd MMM y',
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
				'name' => 'ère'
			),
			'year' => array(
				'name' => 'année'
			),
			'month' => array(
				'name' => 'mois'
			),
			'week' => array(
				'name' => 'semaine'
			),
			'day' => array(
				'name' => 'jour',
				'relative' => array(
					-3 => 'avant-avant-hier',
					-2 => 'avant-hier',
					-1 => 'hier',
					0 => 'aujourd’hui',
					1 => 'demain',
					2 => 'après-demain',
					3 => 'après-après-demain'
				)
			),
			'weekday' => array(
				'name' => 'jour de la semaine'
			),
			'dayperiod' => array(
				'name' => 'cadran'
			),
			'hour' => array(
				'name' => 'heure'
			),
			'minute' => array(
				'name' => 'minute'
			),
			'second' => array(
				'name' => 'seconde'
			),
			'zone' => array(
				'name' => 'fuseau horaire'
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
		'currencyFormat' => '#,##0.00 ¤',
		'currencies' => array(
			'AFN' => 'AFN',
			'ANG' => 'f.NA',
			'AOA' => 'AOA',
			'ARA' => 'ARA',
			'ARL' => 'ARL',
			'ARM' => 'ARM',
			'ARS' => '$AR',
			'AUD' => '$AU',
			'AWG' => 'AWG',
			'AZN' => 'AZN',
			'BAM' => 'BAM',
			'BBD' => 'BBD',
			'BDT' => 'BDT',
			'BEF' => 'FB',
			'BHD' => 'BHD',
			'BIF' => 'BIF',
			'BMD' => '$BM',
			'BND' => '$BN',
			'BOB' => 'BOB',
			'BOP' => 'BOP',
			'BRL' => 'BRL',
			'BSD' => '$BS',
			'BTN' => 'BTN',
			'BWP' => 'BWP',
			'BZD' => '$BZ',
			'CAD' => '$CA',
			'CDF' => 'FrCD',
			'CLE' => 'CLE',
			'CLP' => '$CL',
			'CNY' => 'Ұ',
			'COP' => '$CO',
			'CRC' => 'CRC',
			'CUC' => 'CUC',
			'CUP' => '$CU',
			'CVE' => '$CV',
			'CYP' => '£CY',
			'CZK' => 'Kč',
			'DEM' => 'DM',
			'DJF' => 'DJF',
			'DKK' => 'krD',
			'DOP' => 'DOP',
			'DZD' => 'DZD',
			'EEK' => 'krE',
			'EGP' => '£EG',
			'ERN' => 'ERN',
			'ESP' => '₧',
			'ETB' => 'ETB',
			'EUR' => '€',
			'FIM' => 'FIM',
			'FJD' => '$FJ',
			'FKP' => '£FK',
			'FRF' => 'F',
			'GBP' => '£UK',
			'GHC' => 'GHC',
			'GHS' => 'GHS',
			'GIP' => '£GI',
			'GMD' => 'GMD',
			'GNF' => 'GNF',
			'GRD' => 'GRD',
			'GTQ' => 'GTQ',
			'GYD' => '$GY',
			'HKD' => '$HK',
			'HNL' => 'HNL',
			'HRK' => 'HRK',
			'HTG' => 'HTG',
			'HUF' => 'HUF',
			'IDR' => 'IDR',
			'IEP' => '£IE',
			'ILP' => '£IL',
			'ILS' => '₪',
			'INR' => 'Rs',
			'ISK' => 'krI',
			'ITL' => '₤IT',
			'JMD' => '$JM',
			'JOD' => 'DJ',
			'JPY' => '¥JP',
			'KES' => 'KES',
			'KMF' => 'FC',
			'KRW' => '₩',
			'KWD' => 'DK',
			'KYD' => '$KY',
			'LAK' => 'LAK',
			'LBP' => '£LB',
			'LKR' => 'RsSL',
			'LRD' => '$LR',
			'LSL' => 'LSL',
			'LTL' => 'LTL',
			'LVL' => 'LVL',
			'LYD' => 'DL',
			'MMK' => 'MMK',
			'MNT' => 'MNT',
			'MOP' => 'MOP',
			'MRO' => 'MRO',
			'MTL' => 'MTL',
			'MTP' => '£MT',
			'MUR' => 'RsMU',
			'MXN' => 'MX$',
			'MYR' => 'MYR',
			'MZM' => 'MZM',
			'MZN' => 'MZN',
			'NAD' => '$NA',
			'NGN' => 'NGN',
			'NIO' => 'NIO',
			'NLG' => 'NLG',
			'NOK' => 'krN',
			'NPR' => 'RsNP',
			'NZD' => '$NZ',
			'PAB' => 'PAB',
			'PEI' => 'PEI',
			'PEN' => 'PEN',
			'PGK' => 'PGK',
			'PHP' => 'PHP',
			'PKR' => 'RsPK',
			'PLN' => 'PLN',
			'PTE' => 'PTE',
			'PYG' => 'PYG',
			'QAR' => 'RQ',
			'RHD' => '$RH',
			'RON' => 'RON',
			'RSD' => 'RSD',
			'SAR' => 'SAR',
			'SBD' => '$SB',
			'SCR' => 'SCR',
			'SDD' => 'SDD',
			'SEK' => 'krS',
			'SGD' => '$SG',
			'SHP' => '£SH',
			'SKK' => 'SKK',
			'SLL' => 'SLL',
			'SOS' => 'SOS',
			'SRD' => '$SR',
			'SRG' => 'SRG',
			'STD' => 'STD',
			'SVC' => '₡SV',
			'SYP' => '£SY',
			'SZL' => 'SZL',
			'THB' => '฿',
			'TMM' => 'TMM',
			'TND' => 'TND',
			'TOP' => 'TOP',
			'TRL' => 'TRL',
			'TRY' => 'TRY',
			'TTD' => '$TT',
			'TWD' => 'TWD',
			'TZS' => 'TZS',
			'UAH' => 'UAH',
			'UGX' => 'UGX',
			'USD' => '$US',
			'UYU' => '$UY',
			'VEF' => 'VEF',
			'VND' => 'VND',
			'VUV' => 'VUV',
			'WST' => 'WST',
			'XAF' => 'XAF',
			'XCD' => 'XCD',
			'XOF' => 'CFA',
			'XPF' => 'FCFP',
			'YER' => 'RY',
			'ZAR' => 'ZAR',
			'ZMK' => 'ZMK',
			'ZRN' => 'ZRN',
			'ZRZ' => 'ZRZ',
			'ZWD' => '$Z',
			'ADP' => '₧A',
			'ATS' => 'ATS',
			'CHF' => 'CHF',
			'GEK' => 'GEK',
			'GWE' => 'GWE',
			'KPW' => '₩KP',
			'MXP' => 'MXP',
			'RWF' => 'FR'
		)
	),
	'units' => array(
		'day' => array(
			'one' => array(
				'normal' => '{0} jour',
				'short' => '{0} j'
			),
			'other' => array(
				'normal' => '{0} jours',
				'short' => '{0} j'
			)
		),
		'hour' => array(
			'one' => array(
				'normal' => '{0} heure',
				'short' => '{0} h'
			),
			'other' => array(
				'normal' => '{0} heures',
				'short' => '{0} h'
			)
		),
		'minute' => array(
			'one' => array(
				'normal' => '{0} minute',
				'short' => '{0} mn'
			),
			'other' => array(
				'normal' => '{0} minutes',
				'short' => '{0} mn'
			)
		),
		'month' => array(
			'one' => array(
				'normal' => '{0} mois',
				'short' => '{0} mois'
			),
			'other' => array(
				'normal' => '{0} mois',
				'short' => '{0} mois'
			)
		),
		'second' => array(
			'one' => array(
				'normal' => '{0} seconde',
				'short' => '{0} s'
			),
			'other' => array(
				'normal' => '{0} secondes',
				'short' => '{0} s'
			)
		),
		'week' => array(
			'one' => array(
				'normal' => '{0} semaine',
				'short' => '{0} sem.'
			),
			'other' => array(
				'normal' => '{0} semaines',
				'short' => '{0} sem.'
			)
		),
		'year' => array(
			'one' => array(
				'normal' => '{0} année',
				'short' => '{0} an'
			),
			'other' => array(
				'normal' => '{0} années',
				'short' => '{0} ans'
			)
		)
	),
	'messages' => array(
		'yes' => 'oui:o',
		'no' => 'non:n'
	)
);