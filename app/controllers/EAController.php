<?php
 

 
class EAController extends BaseController {
 
 
  public function lexical()
  {

    $expression = Input::get("expression");

    $expression=strip_tags($expression);

    $expression=preg_replace('/\s+/', '', $expression);

    $elements = str_split ( $expression, 1 );

  
    $lastKey = sizeof($elements)-1;

    $refinedElements = array();

    $labelElements = array();

    $temp = "";
    foreach ($elements as $key => $element) {
      $temp .= $element;

      if($key==0){

        if(ctype_alpha($temp))
        {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";

            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1])||ctype_digit($elements[$key+1]))
            {
              $refinedElements[] = "*";
              $labelElements[] = "operator";
              $temp = "";
            }
        }
        else if(ctype_digit(str_replace(str_split(' ,s.'),'',$temp)))
        {
          if(!ctype_digit(str_replace(str_split(' ,s.'),'',$temp.$elements[$key+1])))
          {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";
            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1]))
            {
              $refinedElements[] = "*";
              $labelElements[] = "operator";
              $temp = "";
            }
          }
        }
        else if($temp=="(")
        {
            $refinedElements[] = $temp;
            $labelElements[] = "open";
            $temp = "";
        }
        else if($temp==")")
        {
            $refinedElements[] = $temp;
            $labelElements[] = "close";
            $temp = "";

           
        }
        else if($temp=="+"||$temp=="-"||$temp=="/"||$temp=="*"||$temp=="^")
        {
              $refinedElements[] = $temp;
              $labelElements[] = "operator";
              $temp = "";
        }
        else
        {
              $refinedElements[] = $temp;
              $labelElements[] = "invalid";
              $temp = "";
        }

      }
      elseif($key==$lastKey)
      {
        if(ctype_alpha($temp))
        {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";
        }
        else if(ctype_digit(str_replace(str_split(' ,s.'),'',$temp)))
        {
          
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";
          
        }
        else if($temp=="(")
        {
            $refinedElements[] = $temp;
            $labelElements[] = "open";
            $temp = "";
        }
        else if($temp==")")
        {
            $refinedElements[] = $temp;
            $labelElements[] = "close";
            $temp = "";
        }
        else if($temp=="+"||$temp=="-"||$temp=="/"||$temp=="*"||$temp=="^")
        {
              $refinedElements[] = $temp;
              $labelElements[] = "operator";
              $temp = "";
        }
        else
        {
              $refinedElements[] = $temp;
              $labelElements[] = "invalid";
              $temp = "";
        }
      }
      else
      {
        if(ctype_alpha($temp))
        {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";

            if($elements[$key+1]=="("||$elements[$key-1]==")"||ctype_alpha($elements[$key+1])||ctype_digit($elements[$key+1]))
            {
              $refinedElements[] = "*";
              $labelElements[] = "operator";
              $temp = "";
            }
        }
        else if(ctype_digit(str_replace(str_split(' ,s.'),'',$temp)))
        {
          if(!ctype_digit(str_replace(str_split(' ,s.'),'',$temp.$elements[$key+1]))||$elements[$key+1]==NULL)
          {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";
            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1])||$elements[$key-1]==")")
            {
              $refinedElements[] = "*";
              $labelElements[] = "operator";
              $temp = "";
            }
          }
        }
        else if($temp=="(")
        {
            $refinedElements[] = $temp;
            $labelElements[] = "open";
            $temp = "";
        }
        else if($temp==")")
        {
            $refinedElements[] = $temp;
            $labelElements[] = "close";
            $temp = "";

            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1]))
            {
              $refinedElements[] = "*";
              $labelElements[] = "operator";
              $temp = "";
            }
        }
        else if($temp=="+"||$temp=="-"||$temp=="/"||$temp=="*"||$temp=="^")
        {
              $refinedElements[] = $temp;
              $labelElements[] = "operator";
              $temp = "";
        }
        else
        {
              $refinedElements[] = $temp;
              $labelElements[] = "invalid";
              $temp = "";
        }
      }

    }

    $output = "<br>";

    foreach ($elements as $key => $element) {
      $output .= $element;
    }

    Session::put("elements",$refinedElements);
    Session::put("labels", $labelElements);
    Session::put("output", $output."<br>");

  

    return Redirect::back();
    
  }

  public function one_parser()
  {


    $lastKey = sizeof(Session::get("elements"))-1;

    if($lastKey!=-1)
    {
      $this->parser(Session::get('elements'), Session::get('labels'));
    }

    return Redirect::back();
  }


  public function complete_parser()
  {
    $lastKey = sizeof(Session::get("elements"))-1;

    while($lastKey!=-1)
    {
      $lastKey = sizeof(Session::get("elements"))-1;
      $elements = Session::get("elements");
      $labels = Session::get("labels");
      $this->parser($elements, $labels);
    }
    return Redirect::back();
  }


  public function parser($elements = array(), $labels = array())
  {
    $output = Session::get("output");

    $operatorKey = NULL;

    foreach ($labels as $key => $label) 
    {
      if($label=="operator"){
        $operatorKey = $key;
        break;
      }
    }

    if($labels[0]=="open")
    {

      $checkCloseKey = NULL;
      
      foreach ($labels as $key => $label) 
      {
        if($label=="close")
        {
          $checkCloseKey = $key;
          break;
        }
      }

      if($checkCloseKey!=NULL)
      {
        unset($elements[0]);
        unset($labels[0]);
        unset($elements[$checkCloseKey]);
        unset($labels[$checkCloseKey]);

        $stringElement = "";
        $newElements = array();
        foreach ($elements as $key => $element) 
        {
          $stringElement .= $element;
          $newElements[] = $element; 
        }
        $newLabels = array();
        foreach ($labels as $key => $label) 
        {
          $newLabels[] = $label; 
        }

      

       
          $output .= "<font color='green'><b>".$stringElement."</b></font> <br>";
        
        Session::put("output", $output);

        Session::put("elements", $newElements);
        Session::put("labels", $newLabels);
        Session::put("msgsuccess", "Successfull parsing.");
      }
      else
      {
        $stringElement = "";
       
        foreach ($elements as $key => $element) 
        {
          $stringElement .= $element;
        }
        $output .= "<font color='red'><b>".$stringElement."</b></font><br>";
        Session::put("output", $output);

        Session::put("msgfail", "Invalid parenthesis placement.");
      }
    }
    else{

    $leftExpressionArray = array();
    $leftExpression = "";
    $rightExpressionArray = array();
    $rightExpression = "";
    $rightLabels = array();

    $side = "left";

    foreach ($elements as $key => $element) 
    {
      if($key==$operatorKey)
        {
          $side="right";
          continue;
        }
      
      if($side=="left")
      {

        
          $leftExpression .= $element;
          $leftExpressionArray[] = $element;
        
      }
      if($side=="right")
      {
        
          $rightExpression .= $element;
          $rightExpressionArray[] = $element;
          $rightLabels[] = $labels[$key];
        
      }
    }


    if($labels[0]=="operand"&&$operatorKey!=NULL)
    {


      if($labels[$operatorKey-1]=="operand")
      {
        $output .= "<font color='green'><b>".$leftExpression."</b></font> "."<font color='green'>".$elements[$operatorKey]."</font> <font color='green'>".$rightExpression."</font><br>";
        Session::put("output", $output);
        Session::put("elements", $rightExpressionArray);
        Session::put("labels", $rightLabels);
        Session::put("msgsuccess", "Successfull parsing.");

      }
      else
      {
        Session::put("msgfail", "An expression must start and end with an operand.");
        $output .= "<font color='red'><b>".$leftExpression."</b></font> "."<font color='green'>".$elements[$operatorKey]."</font> <font color='green'>".$rightExpression."</font><br>";
        Session::put("output", $output);

      }
    }
    
    else
    {
      if($operatorKey!=NULL){
      $output .= "<font color='red'><b>".$leftExpression."</b></font> "."<font color='green'>".$elements[$operatorKey]."</font> <font color='green'>".$rightExpression."</font><br>";
        Session::put("output", $output);

     Session::put("msgfail", "An expression must start and end with an operand."); 
      }
    }
    

  }
  }
   
 

}