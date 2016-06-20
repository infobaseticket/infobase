<?
function CalcAngle($Xa,$Ya,$Xb,$Yb)
{
    if(($Xb>$Xa) && ($Yb>$Ya))
    {
      $Angle=atan(($Yb-$Ya)/($Xb-$Xa));
      $AngleAB=90-$Angle*180/M_PI;
      $AngleBA=number_format(180+$AngleAB,2);
      $AngleAB=number_format($AngleAB,2)." &deg;";
      $AngleBA.=" &deg;";
    }
    elseif (($Xb>$Xa) && ($Yb<$Ya))
    {
      $Angle=atan(($Ya-$Yb)/($Xb-$Xa));
      $AngleAB=90+$Angle*180/M_PI;
      $AngleBA=number_format(180+$AngleAB,2);
      $AngleAB=number_format($AngleAB,2)." &deg;";
      $AngleBA.=" &deg;";
    }
    elseif (($Xb<$Xa) && ($Yb>$Ya))
    {
      $Angle=atan(($Yb-$Ya)/($Xa-$Xb));
      $AngleAB=270+$Angle*180/M_PI;
      $AngleBA=number_format(-180+$AngleAB,2);
      $AngleAB=number_format($AngleAB,2)." &deg;";
      $AngleBA.=" &deg;";
    }
    elseif (($Xb<$Xa) && ($Yb<$Ya))
    {
      $Angle=atan(($Ya-$Yb)/($Xa-$Xb));
      $AngleAB=270-$Angle*180/M_PI;
      $AngleBA=number_format(-180+$AngleAB,2);
      $AngleAB=number_format($AngleAB,2)." &deg;";
      $AngleBA.=" &deg;";
    }
    return array($AngleAB,$AngleBA);
}
?>