<?php
function cleantext($text)
{
	// removes soft hyphen
	return str_replace(['­'], [''], normalizer_normalize($text));
}