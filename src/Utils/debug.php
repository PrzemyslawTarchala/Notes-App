<?php

declare(strict_types = 1);

error_reporting(E_ALL); 			//wyświetla wszystkie błędy, które wystąpią w naszym kodzie
ini_set('display_errors', '1'); //wyświetlanie błędów. Te dwie funkcje przeważnie chodza w parze!
															//jeżeli chcemy mieć pełen zakres zglaszanych błędów, to muszą być wywołane

function dump($data)
{
	echo '<br/><div 
	style="
		display: inline-block;
		padding: 0 10px;
		border: 1px dashed black;
		background: lightgrey;
	"
>

<pre>';
print_r($data);
echo '</pre>
</div>
<br/>';
}
