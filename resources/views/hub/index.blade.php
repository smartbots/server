@extends('hub.master')
@section('title',@trans('hub/hub.login'))
@section('additionHeader')
@endsection
@section('additionFooter')
<script>
  function chooseHub(id)
  {
    $.ajax({
      url : '@route('h::login')',
      type : 'post',
      dataType : 'json',
      data : {
        _token: '{{ csrf_token() }}',
        id: id
      },
      success : function (response)
      {
        if (response.error == 0) {
          window.location.href = '@route('h::dashboard')';
        };
      }
    });
  };
</script>
@endsection
@section('body')
@header('Hub login', [
    'Hub' => '#',
    'Login' => 'active'
])
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
          <a href="@route('h::create')">
            {!! Form::button('<span class="btn-label"><i class="fa fa-plus"></i></span>'.trans('hub/hub.new_hub'), ['class' => 'btn btn-default waves-effect waves-light btn-create']) !!}
          </a>
          <p>
          @if (count($hubs) > 0)
          {{ trans('hub/hub.login_tip_exist') }}</p>
            <ul class="hubs-list clearfix">
            @foreach ($hubs as $hub)
              <li>
                <a href='javascript:chooseHub({{ $hub['id'] }})'>
                  <img class="img-thumbnail" src="@asset($hub['image'])">
                  <span class="hubs-list-name">{{ $hub['name'] }}</span>
                  <span class="hubs-list-bots">{{ count($hub['bots']) }} bots</span>
                </a>
              </li>
            @endforeach
            </ul>
          @else
          {{ trans('hub/hub.login_tip_nothing') }}</p>
          @endif
        </div>
    </div>
</div>
@endsection
