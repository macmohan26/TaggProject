@extends('layouts.app')

@section('content')
    <script>
        $('#app').hide();
        $('#navDemo').wrap('<span style="display: none;" hidden />');
    </script>
    <div class="container">
        <div>
<h1 style="left-padding:20%;"><b>Business Rules Help</b></h1><br>
            <iframe src="{{ asset('files/business_rules_help.pdf') }}" width="100%" class="rulesifr" scrolling="Yes"
                    style=" border:solid;left-paddinf:20%;"></iframe>
        </div>
        <script type="text/javascript" language="javascript">
            $('.rulesifr').css('height', $(window).height() + 'px');
        </script>
    </div>

@endsection
