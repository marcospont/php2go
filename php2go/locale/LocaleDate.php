<?php

class LocaleDate extends LocaleDateTime
{
	public function __toString() {
		return DateTimeFormatter::formatIso($this->format('U'), Php2Go::app()->getLocale()->getDateInputFormat());
	}
}