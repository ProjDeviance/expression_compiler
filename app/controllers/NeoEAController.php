<?php
 

 
class NeoEAController extends BaseController {
 
 
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
              $labelElements[] = "*";
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
              $labelElements[] = "*";
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
              $labelElements[] = $temp;
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
              $labelElements[] = "*";
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
              $labelElements[] = "*";
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
              $labelElements[] = "*";
              $temp = "";
            }
        }
        else if($temp=="+"||$temp=="-"||$temp=="/"||$temp=="*"||$temp=="^")
        {
              $refinedElements[] = $temp;
              $labelElements[] = $temp;
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

    Session::put("output", $output."<br>");
    $this->evaluate($refinedElements, $labelElements);
    return Redirect::back();
  }

  public function evaluate($elements = array(), $tokens = array())
  {
    Session::put("next", 0);
    Session::put("tokens", $tokens);
   
    $check = $this->E(Session::get("tokens"));

    if($check==true)
      Session::put("msgsuccess", "The input is an valid expression.");
    else
      Session::put("msgfail", "The input is an invalid expression.");
  }



  public function term ($tok)
  { 
      $tokens=Session::get("tokens");
      $next = Session::get("next");

      $lastKey = sizeof($tokens)-1;
      
      if($next>$lastKey)
      {
        return false;
      }
      else if($tok==$tokens[$next])
      {
        $next = $next+1;
        Session::put("next", $next);
        return true;
      }
      else
      {
        $next = $next+1;
        Session::put("next", $next);
        return false;
      }
  }

  public function E1($save)
  {
    Session::put("next", $save);
    return $this->T($save);
  }
  public function E2($save)
  {
    Session::put("next", $save);
    return $this->T($save) &&$this->term("+") && $this->term("operand");
  }
  public function E3($save)
  {
    Session::put("next", $save);
    return $this->T($save) &&$this->term("*") && $this->term("operand");
  }
  public function E4($save)
  {
    Session::put("next", $save);
    return $this->T($save) &&$this->term("/") && $this->term("operand");
  }
  public function E5($save)
  {
    Session::put("next", $save);
    return $this->T($save) &&$this->term("-") && $this->term("operand");
  }
  public function E6($save)
  {
    Session::put("next", $save);
    return $this->T($save) &&$this->term("-") && $this->term("operand");
  }
  public function E7($save)
  {
    Session::put("next", $save);
    return $this->T($save) &&$this->term("^") && $this->term("operand");
  }
 
  public function E($tokens)
  {

    $tokens = Session::get("tokens");
    Session::put("tokens", $tokens);

    $save = Session::get("next");
    
    $lastKey = sizeof($tokens)-1;

    if($lastKey==0)
      return $this->E1($save);
    elseif($tokens[$save]=="open")
      return ($this->E1($save));
 
    else
      return ($this->E7($save))||($this->E6($save))||($this->E5($save))||($this->E4($save))||($this->E3($save))||($this->E2($save));
  }

  public function T1($save)
  {
    Session::put("next", $save);
    return $this->term("*")&&$this->T($save);
  }
  public function T2($save)
  {
    Session::put("next", $save);
    return $this->term("operand");
  }
  public function T3($save)
  {
 
    Session::put("next", $save);
    $tokens = Session::get("tokens");

    return $this->term("open") && $this->E($tokens) && $this->term("close");
    
  }
  public function T4($save)
  {
    Session::put("next", $save);
    return $this->term("/")&&$this->T($save);
  }
  public function T5($save)
  {
    Session::put("next", $save);
    return $this->term("-")&&$this->T($save);
  }
  public function T6($save)
  {
    Session::put("next", $save);
    return $this->term("+")&&$this->T($save);
  }
  public function T7($save)
  {
    Session::put("next", $save);
    return $this->term("^")&&$this->T($save);
  }
  public function T($save)
  {
    return ($this->T1($save))||($this->T2($save))||( $this->T4($save))||( $this->T5($save))||( $this->T6($save))||( $this->T7($save))||( $this->T3($save));
  }


}