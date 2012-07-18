<?php
  function ToInt($s, $AllowNegative = FALSE) {
    if(!$s) {
      return 0;
    }
    $s = FloatString($s, 0);
    $Result = '';
    $s = '' . $s;
    $n = strlen($s);
    for($i = 0; $i<$n; $i++) {
      $c = $s[$i];
      if(strpos('0123456789', $c)===FALSE and ($i>=1 or !$AllowNegative or $c<>'-'))
        return 0;
      $Result .= $c;
    }
    $Result_Int = $Result;
    settype($Result_Int, 'int');
    if($Result_Int==$Result)
      return $Result_Int;
    else
    {
      settype($Result, 'float');
      return $Result;
    }
  }

  function FloatString($Value, $Decimals = -4, $SeparateTriads = FALSE) {
    $Format = '%.' . abs($Decimals) . 'f';
    $s = sprintf($Format, $Value);
    if($Decimals<0) {
      $l = strlen($s);
      while($l and substr($s, $l-1)=='0')
        $s = substr($s, 0, --$l);
      if($l and substr($s, $l-1)==DecimalDelimiter())
        $s = substr($s, 0, --$l);
    }
    if($SeparateTriads)
      $s = SeparateTriads($s);
    return $s;
  }

  function SeparateTriads($s) {
//    if(!$s)
//      return '';
    $DigitsGroupDelimiter = DigitsGroupingDelimiter();
    $i = strpos($s, DecimalDelimiter());
    if(!$i)
      $i = strlen($s);
    if(substr($s, 0, 1)=='-')
      $l = 1;
    else
      $l = 0;
    while($i>$l) {
      $i -= 3;
      if($i>$l)
        $s = substr($s, 0, $i) . $DigitsGroupDelimiter . substr($s, $i);
    }
    return $s;
  }

  function DigitsGroupingDelimiter() {
    if(DecimalDelimiter()=='.')
      return ',';
    else
      return "'";
  }

  function DecimalDelimiter() {
    $s = sprintf('%1.1f', 1.1);
    return substr($s, 1, 1);
  }

  function GetStringBeginningWithEllipsis($s, $MaxLength) {
    if(strlen($s)>$MaxLength) {
      $s = substr($s, 0, $MaxLength-3);
      if($p = strrpos($s, ' '))
        $s = substr($s, 0, $p);
      $s .= '...';
    }
    return $s;
  }
