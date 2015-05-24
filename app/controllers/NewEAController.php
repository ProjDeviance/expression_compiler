<?php

class NewEAController extends BaseController {

    public $lp = 0;

    public $operator = 0;

    public $x = 0;

    public $left = 0;

    public $right = 0;

    public $getstring = "";


  public function lexical()
  {

    $getstring = Input::get("expression");

    // Filtering Process

    //Removes tag inputs
    $getstring = strip_tags($getstring);

    //Removes whitespaces
    $getstring = preg_replace('/\s+/', '', $getstring);

    // Segments string input into character array
    $elements = str_split ( $getstring, 1 );

    //End - Filtering Process

    // Lexical Analysis

    $lastKey = sizeof($elements)-1;

    $refinedElements = array();

    $temp = "";


    foreach ($elements as $key => $element) 
    {
      $temp .= $element;

      if($key==0){

        if(ctype_alpha($temp))
        {
            $refinedElements[] = $temp;
            $temp = "";

            if($lastKey==0)
            {
              break;
            }

            //Adds missing multiplication symbol
            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1])||ctype_digit($elements[$key+1]))
            {
              $refinedElements[] = "*";
              $temp = "";
            }

           

        }
        else if(ctype_digit(str_replace(str_split(' ,s.'),'',$temp)))
        {
           if($lastKey==0)
            {
              $refinedElements[] = $temp;
              $temp = "";
              break;
            }
          //Combines digits to form an integer or decimal element
          if(!ctype_digit(str_replace(str_split(' ,s.'),'',$temp.$elements[$key+1])))
          {
            $refinedElements[] = $temp;
            $temp = "";

            //Adds missing multiplication symbol
            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1]))
            {
              $refinedElements[] = "*";
              $temp = "";
            }
          }
        }
        else if($temp=="(")
        {
            $refinedElements[] = $temp;
    
            $temp = "";
        }
        else if($temp==")")
        {
            $refinedElements[] = $temp;
      
            $temp = "";

           
        }
        else if($temp=="+"||$temp=="-"||$temp=="/"||$temp=="*"||$temp=="^")
        {
              $refinedElements[] = $temp;
          
              $temp = "";
        }
        else
        {
              $refinedElements[] = $temp;

              $temp = "";
        }

      }
      elseif($key==$lastKey)
      {
        if(ctype_alpha($temp))
        {
            $refinedElements[] = $temp;
    
            $temp = "";
        }
        else if(ctype_digit(str_replace(str_split(' ,s.'),'',$temp)))
        {
          
            $refinedElements[] = $temp;
  
            $temp = "";
          
        }
        else if($temp=="(")
        {
            $refinedElements[] = $temp;
      
            $temp = "";
        }
        else if($temp==")")
        {
            $refinedElements[] = $temp;
 
            $temp = "";
        }
        else if($temp=="+"||$temp=="-"||$temp=="/"||$temp=="*"||$temp=="^")
        {
              $refinedElements[] = $temp;

              $temp = "";
        }
        else
        {
              $refinedElements[] = $temp;
   
              $temp = "";
        }
      }
      else
      {
        if(ctype_alpha($temp))
        {
            $refinedElements[] = $temp;

            $temp = "";

            if($elements[$key+1]=="("||$elements[$key-1]==")"||ctype_alpha($elements[$key+1])||ctype_digit($elements[$key+1]))
            {
              $refinedElements[] = "*";
     
              $temp = "";
            }
        }
        else if(ctype_digit(str_replace(str_split(' ,s.'),'',$temp)))
        {
          //Combines digits to form a decimal or integer element
          if(!ctype_digit(str_replace(str_split(' ,s.'),'',$temp.$elements[$key+1]))||$elements[$key+1]==NULL)
          {
            $refinedElements[] = $temp;

            $temp = "";
            //Adds missing multiplication symbol
            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1])||$elements[$key-1]==")")
            {
              $refinedElements[] = "*";

              $temp = "";
            }
          }
        }
        else if($temp=="(")
        {
            $refinedElements[] = $temp;

            $temp = "";
        }
        else if($temp==")")
        {
            $refinedElements[] = $temp;
  
            $temp = "";

            //Adds missing multiplication symbol
            if($elements[$key+1]=="("||ctype_alpha($elements[$key+1]))
            {
              $refinedElements[] = "*";
      
              $temp = "";
            }
        }
        else if($temp=="+"||$temp=="-"||$temp=="/"||$temp=="*"||$temp=="^")
        {
              $refinedElements[] = $temp;
     
              $temp = "";
        }
        else
        {
              $refinedElements[] = $temp;
      
              $temp = "";
        }
      }

    }

    //End - Lexical Analysis

    $output = "";

    foreach ($refinedElements as $key => $element) 
    {
      $output .= $element;
    }


    $this->getstring = $output;
    $this->lp = 0;
    $this->operator = 0;
    $this->x = 0;
    $this->left = 0;
    $this->right = 0;

    return $this->factor($this->getstring{$this->x});


    
  }



    public function factor($input)
    {

        if($this->accept($input) == 1)
        {

          $this->term($input);
          return 1;
          
        }
        else if($this->accept($input) == 2)
        {
           $this->lp++;
           $this->expression($input);
           return 1;
           
        }
        else if($this->accept($input) == 3)
        {
           $this->lp--;
           $this->expression($input);
           return 1;
           
        }
        
        if($this->x >= strlen($this->getstring))
        {
            return 1;
        }
        else
        {
            return 0;
        }
 
    
    }
    
    public function expression($input)
    {
        
        if($this->right >= 1 && $this->operator >= 1)
        {
            return 0;
        }

        $this->operator = 0;
        $this->x = $this->x + 1;

        if($this->x >= strlen($this->getstring))
        {
           if(lp!=0)
           {
               return 0;
           }
           
           return 1;
        }
        else
        {
            $input = $this->getstring{$this->x};
            return $this->factor($input);
        }
        
         
    }
    
    public function term($input)
    {
 
        if($this->left >= 1 && $this->operator >= 1)
        {
            return 0;
        }
       
        if($this->x==0 && ($input=='+'||$input=='-'||$input=='*'||$input=='/'||$input=='^'||$input=='%'||$input==')'))
        {
               return 0;
        }
        
        $this->x = $this->x + 1;

        if($this->x >= strlen($this->getstring))
        {
            if($input=='+'||$input=='-'||$input=='*'||$input=='^'||$input=='%'||$input=='(')
            {
                return 0;
            }

            return 1;
        }
        else
        { 
            if($this->operator>=2)
            {
                return 0;
            }

            $input = $this->getstring{$this->x};
            return $this->factor($input);      
        }
    }
    


    
    public function accept($input)
    {
        
        if(ctype_alpha($input) || ctype_digit(str_replace(str_split(' ,s.'),'',$input)))
        {
            $this->operator=0;
            $this->left=0;
            $this->right=0;

            return 1;
        }
        
        else if(($input=='+' || $input=='-' || $input=='*' || $input=='/'|| $input=='^'
                || $input=='%'))
        {
         
            $this->operator++;
            $this->right = 0;
            return 1;
        }
        
        else if($input=='(')
        {
            $this->left++;
            return 2;
        }
        
        else if( $input==')')
        {
            $this->right++;
            return 3;
        }
        
        return 0;
    }

}
