@extends('layouts/main')

@section('title')
Analyzer
@stop


@section('main')

    <div class="row">
       
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading" align='center'>
                    EA - Expression Analyzer Program
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12" >
    @if(Session::get('msgfail'))
      <div class="alert alert-danger fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <center>{{ Session::get('msgfail') }}</center>
      </div>
      {{ Session::forget('msgfail') }}
      {{ Session::forget('elements') }}
      {{ Session::forget('labels') }}
      {{ Session::forget('msgsuccess') }}
    @elseif(Session::get('msgsuccess'))
      <div class="alert alert-success fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <center>{{ Session::get('msgsuccess') }}</center>
      </div>
      {{ Session::forget('msgsuccess') }}
    @endif


        {{ Form::open(array('class' => 'form-signin', 'role' => 'form', )) }}

        <div class="form-group @if ($errors->has('msgfail')) has-error @endif">     
                {{ Form::text('expression',Session::get('expression'), array('class' => 'form-control  ', 'placeholder' => 'Please input expression here...','maxlength'=>'255')) }}
       
            @if ($errors->has('expression')) 
                <p class="help-block">{{ $errors->first('expression') }}</p>  
            @endif

        </div>
       
        <div class="col-lg-12" align="center">
            <input type="submit" class="btn btn-success left-sbs sbmt" value="Analyze">
        </div>
        {{ Form::close(); }}
        

     
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>


   
@stop

@section('footer')
@stop