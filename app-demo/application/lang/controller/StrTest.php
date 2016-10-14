<?php
namespace app\lang\controller;

use bb\Controller;

use bb\lang\Str;
use bb\O;

class StrTest extends Controller {

	public function index() {

		dump(Str::convertToUtf8('ddd'));

		dump(Str::at('dad', 2));

		// dump(Str::ditto('abc', 3, 'dd'));

		dump(number_format('1003123.12', 10, '.', ','));

		dump(floatval('10.1231'));

		dump(Str::split('abcdfasf', 'f'));

		dump(Str::indexOfAny('dfsass', ['s', 'd']));

		// dump(is_int(0x100312312123));

		// dump(STR_PAD_BOTH);

	}



}