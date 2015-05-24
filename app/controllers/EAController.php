<?php
 

 
class EAController extends BaseController {
 
 
  public function lexical()
  {

    $expression = Input::get("expression");


    // Filtering Process

    //Removes tag inputs
    $expression=strip_tags($expression);

    //Removes whitespaces
    $expression=preg_replace('/\s+/', '', $expression);

    // Segments string input into character array
    $elements = str_split ( $expression, 1 );

    //End - Filtering Process

  
   

    // Lexical Analysis

    $lastKey = sizeof($elements)-1;

    $refinedElements = array();

    $labelElements = array();

    $temp = "";


    foreach ($elements as $key => $element) 
    {
      $temp .= $element;

      if($key==0){

        if(ctype_alpha($temp))
        {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";

            if($lastKey==0)
            {
              break;
            }

            //Adds missing multiplication symbol
            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1])||ctype_digit($elements[$key+1]))
            {
              $refinedElements[] = "*";
              $labelElements[] = "operator";
              $temp = "";
            }

           

        }
        else if(ctype_digit(str_replace(str_split(' ,s.'),'',$temp)))
        {
           if($lastKey==0)
            {
              $refinedElements[] = $temp;
              $labelElements[] = "operand";
              $temp = "";
              break;
            }
          //Combines digits to form an integer or decimal element
          if(!ctype_digit(str_replace(str_split(' ,s.'),'',$temp.$elements[$key+1])))
          {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";

            //Adds missing multiplication symbol
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
          //Combines digits to form a decimal or integer element
          if(!ctype_digit(str_replace(str_split(' ,s.'),'',$temp.$elements[$key+1]))||$elements[$key+1]==NULL)
          {
            $refinedElements[] = $temp;
            $labelElements[] = "operand";
            $temp = "";
            //Adds missing multiplication symbol
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

            //Adds missing multiplication symbol
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

    //End - Lexical Analysis

    $output = "<br>";

    foreach ($elements as $key => $element) 
    {
      $output .= $element;
    }

    Session::put("elements",$refinedElements);
    Session::put("labels", $labelElements);
    Session::put("output", $output."<br>");

  

    return Redirect::back();
    
  }

  public function one_parser()
  {
    
  
      $this->parser(Session::get('elements'), Session::get('labels'));
    

    return Redirect::back();
  }


  public function complete_parser()
  {
    ini_set('max_execution_time', 300);
    $lastKey = sizeof(Session::get("elements"))-1;

    while($lastKey!=0)
    {
      if(Session::get('msgfail'))
      {
        
        Session::forget('elements');
        Session::forget('labels');
        return Redirect::back();
      }
  
      $lastKey = sizeof(Session::get("elements"))-1;
      $elements = Session::get("elements");
      $labels = Session::get("labels");

      //
      $this->parser($elements, $labels);
    }
    $this->parser($elements, $labels);

    return Redirect::back();
  }


  public function parser($elements = array(), $labels = array())
  {
    $output = Session::get("output");

    $operatorKey = NULL;

    //Looks for the first operator from the left
    foreach ($labels as $key => $label) 
    {
      if($label=="operator"){
        $operatorKey = $key;
        break;
      }
    }

    $lastKey = sizeof(Session::get("elements"))-1;

    if($lastKey==1)
    {
        $output .= "<font color='red'><b>".$elements[1]."</b></font><br>";

        Session::put("output", $output);
        Session::put("msgfail", "The input was a valid expression.");
    }
      
    elseif($lastKey==0)
    {
      //Single Element Instance
      
      if($labels[0]=="operand")
      {
        //Operand Only Handler
        $output .= "<font color='green'><b>".$elements[0]."</b></font><br>";

        Session::put("output", $output);
        Session::put("msgsuccess", "The input was a valid expression.");
      }
      else
      {
        $output .= "<font color='red'><b>".$elements[0]."</b></font><br>";

        Session::put("output", $output);
        Session::put("msgfail", "The input is not a valid expression.");
      }

      //End - Single Element Instance
    }

    else if($lastKey==2)
    {
      //Single Expression Instance


      if($labels[0]=="operand"&&$labels[1]=="operator"&&$labels[2]=="operand")
      {
        //Operand Operator Operand Handler
        $output .= "<font color='green'><b>".$elements[2]."</b></font><br>";
        $elementsArray = array();
        $labelsArray = array();
        $elementsArray[] = $elements[2];
        $labelsArray[] = $labels[2];
        
        Session::put("elements", $elementsArray);
        Session::put("labels", $labelsArray);
        Session::put("output", $output);
        Session::put("msgsuccess", "The input was a valid expression.");
      }
      else if($labels[0]=="open"&&$labels[1]=="operand"&&$labels[2]=="close")
      {
        //Parenthesis Handler
        $elementsArray = array();
        $labelsArray = array();
        $elementsArray[] = $elements[1];
        $labelsArray[] = $labels[1];

        $output .= "<font color='green'><b>".$elements[1]."</b></font><br>";

        Session::put("elements", $elementsArray);
        Session::put("labels", $labelsArray);
        Session::put("output", $output);
        Session::put("msgsuccess", "The input was a valid expression.");
      }
      else
      {
        //Invalid Handler
        $output .= "<font color='red'><b>".$elements[2]."</b></font><br>";
        Session::put("output", $output);
        Session::put("msgfail", "The input is not a valid expression.");
      }

      //End - Single Expression Instance
    }
    else if($operatorKey==NULL)
    {
      //Completed Parsing Instance

      return Session::get('elements');
      $stringElement = "";

      foreach ($elements as $key => $element) 
      {
        $stringElement .= $element;
         
      }
       
      $output .= "<font color='green'><b>".$stringElement."</b></font> <br>";
      Session::put("msgsuccess", "Successfull complete parsing.");

      //End - Completed Parsing Instance
    }

    // Multiple Expressions Instance

    else if($labels[0]=="open")
    {
      //Parenthesis Handler

      $checkCloseKey = NULL;
      $openCounter = 0;

      foreach ($labels as $key => $label) 
      {
         if($label=="open"&&$key!=0)
         {
          $openCounter+=1;
         }  
         elseif($label=="close")
          {
          if($openCounter==0)
          {
            $checkCloseKey = $key;
            break;
          }
          elseif($openCounter!=0)
          {
            $openCounter-=1;
          }
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

      //End - Parenthesis Handler
    }
    

    else
    {

    //Operand Operator Operand Handler 

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
      if($operatorKey!=NULL)
      {
        $output .= "<font color='red'><b>".$leftExpression."</b></font> "."<font color='green'>".$elements[$operatorKey]."</font> <font color='green'>".$rightExpression."</font><br>";
        Session::put("output", $output);
        Session::put("msgfail", "An expression must start and end with an operand."); 
      }

    }
    
    //End - Operand Operator Operand Handler 
  }
  // End - Multiple Expressions Instance

  }
   
 

}