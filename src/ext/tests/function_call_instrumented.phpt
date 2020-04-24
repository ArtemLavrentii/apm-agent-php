--TEST--
Intercept calls to PHP function (including capturing parameters and return value)
--SKIPIF--
<?php if ( ! extension_loaded( 'elasticapm' ) ) die( 'skip'.'Extension elasticapm must be installed' ); ?>
--FILE--
<?php
declare(strict_types=1);
require __DIR__ . '/../tests_util/tests_util.php';

elasticapm_intercept_calls_to_function(
    "cos",
    "cos_preHook",
    "cos_postHook"
);

$cos_preHook_invoked = false;

function cos_preHook(): void
{
    global $cos_preHook_invoked;
    $cos_preHook_invoked = true;
}

$cos_postHook_invoked = false;

function cos_postHook(): void
{
    global $cos_postHook_invoked;
    $cos_postHook_invoked = true;
}

elasticApmAssertSame('$cos_preHook_invoked', $cos_preHook_invoked, false);
elasticApmAssertSame('$cos_postHook_invoked', $cos_postHook_invoked, false);
$cos_retVal = cos( M_PI );
elasticApmAssertSame('$cos_retVal', -1.0, $cos_retVal);
elasticApmAssertSame('$cos_preHook_invoked', $cos_preHook_invoked, true);
elasticApmAssertSame('$cos_postHook_invoked', $cos_postHook_invoked, true);

function myFunc(string $stringParam, int $intParam): string
{
   return "$stringParam - $intParam";
}

elasticapm_intercept_calls_to_function(
   "myFunc",
   "preHook",
   "postHook"
);

$myFunc_preHook_invoked = false;

function myFunc_preHook(): void
{
    global $myFunc_preHook_invoked;
    $myFunc_preHook_invoked = true;
}

$myFunc_postHook_invoked = false;

function myFunc_postHook(): void
{
    global $myFunc_postHook_invoked;
    $myFunc_postHook_invoked = true;
}

elasticApmAssertSame('$myFunc_preHook_invoked', $myFunc_preHook_invoked, false);
elasticApmAssertSame('$myFunc_postHook_invoked', $myFunc_postHook_invoked, false);
$myFunc_retVal = myFunc( 'abc', 456 );
elasticApmAssertSame('$myFunc_retVal', $myFunc_retVal, 'abc - 456');
elasticApmAssertSame('$myFunc_preHook_invoked', $myFunc_preHook_invoked, true);
elasticApmAssertSame('$myFunc_postHook_invoked', $myFunc_postHook_invoked, true);

//
////elasticApmAssertSame('$interceptedCallParams' ,  $myFuncToInterceptParams, $interceptedCallParams);
////elasticApmAssertSame("\$interceptedCallRetVal" ,  $myFuncToInterceptRetVal, $interceptedCallRetVal);


echo 'Test completed'
?>
--EXPECT--
Test completed
