<?php

class ViewHelperText extends ViewHelper
{
	private static $linkPattern = '~
	    (						# leading text
	      <\w+.*?>|				# leading HTML tag, or
	      [^=!:\'"/]|			# leading punctuation, or
	      ^						# beginning of line
	    )(
	      (?:(?:https?|ftps?|nntp)://)| # protocol spec, or
	      (?:www\.)				# www.*
	    )(
	      [-\w]+				# subdomain or domain
	      (?:\.[-\w]+)*			# remaining subdomains or domain
	      (?::\d+)?				# port
	      (?:/(?:(?:[\~\w\+%-]|(?:[,.;:][^\s$]))+)?)* # path
	      (?:\?[\w\+%&=.;-]+)?	# query string
	      (?:\#[\w\-]*)?		# trailing anchor
	    )
	    ([[:punct:]]|\s|<|$)	# trailing text
	~x';
	private static $emailPattern = '/([a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9]))/';

	public function truncate($text, $length, array $options=array()) {
		$options = array_merge(array(
			'ending' => '...',
			'exact' => true,
			'html' => false
		), $options);
		if ($options['html']) {
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
				return $text;
			$truncate = '';
			$totalLength = strlen($options['ending']);
			$openTags = $lineMatches = $tagMatches = $entityMatches = array();
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lineMatches, PREG_SET_ORDER);
			foreach ($lineMatches as $line) {
				if (!empty($line[1])) {
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line[1])) {
					} elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line[1], $tagMatches)) {
						$pos = array_search($tagMatches[1], $openTags);
						if ($pos !== false)
							unset($openTags[$pos]);
					} elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line[1], $tagMatches)) {
						array_unshift($openTags, strtolower($tagMatches[1]));
					}
					$truncate .= $line[1];
				}
				$contentLength = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line[2]));
				if ($totalLength + $contentLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line[2], $entityMatches, PREG_OFFSET_CAPTURE)) {
						foreach ($entityMatches[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += strlen($entity[0]);
							} else {
								break;
							}
						}
					}
					$truncate .= substr($line[2], 0, $left + $entitiesLength);
					break;
				} else {
					$truncate .= $line[2];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length)
				return $text;
			$truncate = substr($text, 0, $length - strlen($options['ending']));
		}
        if (!$options['exact']) {
			$pos = strrpos($truncate, ' ');
			if (isset($pos))
				$truncate = substr($truncate, 0, $pos);
        }
        $truncate .= $options['ending'];
        if ($options['html']) {
			foreach ($openTags as $tag)
				$truncate .= '</' . $tag . '>';
        }
        return $truncate;
	}

	public function highlight($text, $phrases, $highlighter='<em>\1</em>') {
		if (empty($text) || empty($phrases))
			return $text;
		$regexp = '/(' . implode('|', array_map('preg_quote', (array)$phrases)) . ')/i';
		return preg_replace($regexp, $highlighter, $text);
	}

	public function excerpt($text, $phrase, $radius, array $options=array()) {
		$options = array_merge(array(
			'ending' => '...',
			'fullWords' => false
		), $options);
		if (empty($text) || empty($phrase))
			return $text;
		$phrase = $phrase;
		$pos = stripos($text, $phrase);
		if ($pos !== false) {
			$start = max($pos - $radius, 0);
			$end = min($pos + strlen($phrase) + $radius - 1, strlen($text));
			$excerpt = substr($text, $start, $end - $start);
			$prefix = ($start > 0 ? $options['ending'] : '');
			$suffix = ($end < strlen($text) ? $options['ending'] : '');
			if ($options['fullWords']) {
				if ($prefix)
					$excerpt = preg_replace('/^(\S+)?\s+?/', ' ', $excerpt);
				if ($suffix)
					$excerpt = preg_replace('/\s+?(\S+)?$/', ' ', $excerpt);
			}
			return $prefix . $excerpt . $suffix;
		}
		return $text;
	}

	public function wrap($text, $lineWidth=80) {
		return preg_replace('/(.{1,' . $lineWidth . '})(\s+|$)/s', "\\1\n", preg_replace("/\n/", "\n\n", $text));
	}

	public function autoLink($text, $link='all', array $linkAttrs=array()) {
		if ($link == 'emails')
			return $this->autoLinkEmails($text, $linkAttrs);
		elseif ($link == 'urls')
			return $this->autoLinkUrls($text, $linkAttrs);
		elseif ($link == 'all')
			return $this->autoLinkUrls($this->autoLinkEmails($text, $linkAttrs), $linkAttrs);
		return $text;
	}

	public function stripLinks($text) {
		return preg_replace('|<a\s+[^>]+>|im', '', preg_replace('|<\/a>|im', '', $text));
	}

	protected function autoLinkUrls($text, array $linkAttrs) {
		return preg_replace_callback(self::$linkPattern, create_function('$matches', '
			if (preg_match("/<a\s/i", $matches[1]))
				return $matches[0];
			return $matches[1] . \'<a href="\' . ($matches[2] == "www." ? "http://www." : $matches[2]) . $matches[3] . \'"' . $this->renderAttrs($linkAttrs) . '>\' . $matches[2] . $matches[3] . \'</a>\' . $matches[4];
		'), $text);
	}

	protected function autoLinkEmails($text, array $linkAttrs) {
		return preg_replace_callback(self::$emailPattern, create_function('$matches', '
			return \'<a href="mailto:\' . $matches[1] . \'"' . $this->renderAttrs($linkAttrs) . '>\' . $matches[1] . \'</a>\';
		'), $text);
	}
}