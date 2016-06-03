@extends('hub.master')
@section('title','Add new schedule')
@section('additionHeader')
<link rel="stylesheet" href="{{ asset('public/libs/bootstrap-datetimepicker/bootstrap-datetimepicker.css') }}">
<style>
  .up-zindex {
    z-index: 3 !important;
  }
  select {
    width: 105px !important;
  }
</style>
@endsection
@section('additionFooter')
<script src="{{ asset('public/libs/typeahead.js/typeahead.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/libs/handlebars/handlebars.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/libs/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/libs/bootstrap-datetimepicker/bootstrap-datetimepicker.js') }}" type="text/javascript"></script>
<script>
  var bot = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: '{{ route('h::b::search') }}/%Q/{{ session('currentHub') }}',
      wildcard: '%Q'
    }
  });

  var typeahead_bot_option = {
    name: 'bot',
    display: 'id',
    source: bot,
    templates: {
      empty: [
      '<div class="tt-no-result">',
      'No result',
      '</div>'
      ].join(''),
      suggestion: Handlebars.compile([
          '<div class="">',
            '<div class="pull-left">',
                '<img src="@{{ image }}" alt="" class="user-mini-ava">',
            '</div>',
            '<div>',
                '<strong>@{{ name }}</strong>',
                '<p class="m-0">@{{ id }}</p>',
            '</div>',
          '</div>'].join(''))
    }
  };

  $(function () {
    $('[name="action[bot][]"]').typeahead(null, typeahead_bot_option);
    $('[name="condition[bot][]"]').typeahead(null, typeahead_bot_option);
    $('.datetimepicker').datetimepicker({useCurrent: true});
  });

  function addAction() {
    $('#action_div').append([
      '<div class="input-group margin-top-sm">',
      '<div class="input-group-btn">',
      '{!! Form::select('action[type][]', ['1' => 'Toggle', '2' => 'Turn on', '3' => 'Turn off'], null, ['class' => 'form-control']) !!}',
      '</div>',
      '{!! Form::text('action[bot][]', null, ['class' => 'form-control border-left-none']) !!}',
      '</div>'
      ].join(''));
    $('select').css('width: 105px');
    $('[name="action[bot][]"]').typeahead(null, typeahead_bot_option);
  }

  function changeType() {
    if ($('[name=type]').val() == '1') {
      $('#one-many-time-div').html([
        '<div class="form-group">',
        '{!! Form::label('time', 'Time', ['class' => 'col-sm-2 control-label']) !!}',
        '<div class="col-sm-10">',
        '{!! Form::text('time', old('time'), ['class' => 'form-control datetimepicker']) !!}',
        '</div>',
        '</div>'
      ].join(''));
      $('.datetimepicker').datetimepicker();
    } else {
      $('#one-many-time-div').html([
        '<div class="form-group">',
        '{!! Form::label('frequency', 'Frequency', ['class' => 'col-sm-2 control-label']) !!}',
        '<div class="col-sm-10">',
        '<div id="frequency-div" count="1" class="margin-bottom-sm">',
        '<div class="input-group">',
        '<span class="input-group-addon">Every</span>',
        '{!! Form::text('frequency[value][]', 1, ['class' => 'form-control border-right-none']) !!}',
        '<div class="input-group-btn">',
        '{!! Form::select('frequency[unit][]', ['1' => 'minute(s)', '2' => 'hour(s)', '3' => 'day(s)', '4' => 'week(s)', '5' => 'month(s)', '6' => 'year(s)'], null, ['id' => 'fre1','class' => 'form-control up-zindex', 'onChange' => 'changeFrequency(1)']) !!}',
        '</div>',
        '<span class="input-group-addon border-left-none">At</span>',
        '{!! Form::text('frequency[at][]', null, ['id' => 'f2re1', 'class' => 'form-control datetimepicker border-left-none', 'readonly' => 'readonly']) !!}',
        '</div>',
        '</div>',
        '{!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add frequency', ['class' => 'btn btn-default pull-right','onclick' => 'addFrequency()']) !!}',
        '</div>',
        '</div>'
      ].join(''));
      $('.datetimepicker').datetimepicker();
    }
  }

  function changeFrequency(id) {
    var dtpicker = $('#f2re'+id);
    switch ($('#fre'+id).val()) {
      case "1":
      dtpicker.attr('readonly','readonly').val('');
      break;
      case "2":
      dtpicker.removeAttr('readonly').data("DateTimePicker").format('mm');
      break;
      case "3":
      dtpicker.removeAttr('readonly').data("DateTimePicker").format('HH:mm');
      break;
      case "4":
      dtpicker.removeAttr('readonly').data("DateTimePicker").format('dddd HH:mm');
      break;
      case "5":
      dtpicker.removeAttr('readonly').data("DateTimePicker").format('D HH:mm');
      break;
      case "6":
      dtpicker.removeAttr('readonly').data("DateTimePicker").format('MMMM D HH:mm');
      break;
    }
  }

  function addFrequency() {
    var count = $('#frequency-div').attr('count');
    count++;
    $('#frequency-div').attr('count',count);
    $('#frequency-div').append([
      '<div class="input-group margin-top">',
      '<span class="input-group-addon">Every</span>',
      '<input class="form-control border-right-none" name="frequency[value][]" type="text" value="1">',
      '<div class="input-group-btn">',
      '<select id="fre'+count+'" class="form-control up-zindex" onchange="changeFrequency('+count+')" name="frequency[unit][]">',
      '<option value="1">minute(s)</option>',
      '<option value="2">hour(s)</option>',
      '<option value="3">day(s)</option>',
      '<option value="4">week(s)</option>',
      '<option value="5">month(s)</option>',
      '<option value="6">year(s)</option>',
      '</select>',
      '</div>',
      '<span class="input-group-addon border-left-none">At</span>',
      '<input id="f2re'+count+'" class="form-control datetimepicker border-right-none border-left-none" readonly name="frequency[at][]" type="text">',
      '</div>',
      ].join(''));
    $('.datetimepicker').datetimepicker();
  }

  function addCondition() {
    $('#conditions-div').append([
      '<div class="input-group margin-top-sm">',
      '<input class="form-control border-right-none" name="condition[bot][]" type="text">',
      '<div class="input-group-btn">',
      '<select class="form-control" style="width: 120px !important;" name="condition[state][]">',
      '<option value="1">is turned on</option>',
      '<option value="2">is turned off</option>',
      '</select>',
      '</div>',
      '</div>',
      ].join(''));
    $('[name="condition[bot][]"]').typeahead(null, typeahead_bot_option);
  }

  function changeCondition() {
    if ($('[name="condition[type]"]').val() != "0") {
      $('#conditions-div').html([
        '<div class="input-group margin-top">',
        '{!! Form::text('condition[bot][]', null, ['class' => 'form-control border-right-none']) !!}',
        '<div class="input-group-btn">',
        '{!! Form::select('condition[state][]', ['0' => 'is turned on', '1' => 'is turned off'], null, ['class' => 'form-control', 'onChange' => 'changeCondition()','style' => 'width: 120px !important;']) !!}',
        '</div>',
        '</div>'
      ].join(''));
      $('#add-cond-btn-div').html('{!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add condition', ['class' => 'btn btn-default pull-right margin-top-sm','onclick' => 'addCondition()']) !!}');
      $('#condMethod').html('{!! Form::select('condition[method]', ['1' => 'And', '2' => 'Or'], null, ['class' => 'form-control border-left-none', 'onChange' => 'changeCondition()']) !!}');
      $('[name="condition[bot][]"]').typeahead(null, typeahead_bot_option);
    } else {
      $('#conditions-div').html('');
      $('#add-cond-btn-div').html('');
      $('#condMethod').html('');
    }
  }

  function createSchedule() {
    $.ajax({
      url : '{{ route('h::s::create') }}',
      type : 'post',
      data : $('[name=create-schedule-form]').serializeArray(),
      dataType : 'json',
      success : function (response)
      {
        $('[name=create-schedule-form] input').haz('nothing');
        for (var prop in response) {
          var _prop = prop;
          if (prop.search('action.bot') != -1 || prop.search('action.type') != -1 ) {
            prop = 'action[bot][]';
          } else if (prop.search('frequency.unit') != -1 || prop.search('frequency.value') != -1 || prop.search('frequency.at') != -1) {
            prop = 'frequency[unit][]';
          } else if (prop.search('condition.type') != -1 || prop.search('condition.method') != -1 || prop.search('condition.bot') != -1 || prop.search('condition.state') != -1) {
            prop = 'condition[bot][]';
          };
          $('[name="'+prop+'"]').haz('error',response[_prop]);
        };
      }
    });
    return false;
  }
</script>
@endsection
@section('body')
{!! content_header('Add new schedule', [
    'Hub' => route('h::edit'),
    'Schadule' => route('h::s::index'),
    'Create' => 'active']) !!}
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
    {!! Form::open(['route' => 'h::s::create', 'class' => 'form-horizontal', 'name' => 'create-schedule-form', 'onsubmit' => 'return createSchedule()']) !!}
      <div class="form-group">
        {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
        </div>
      </div>
      <div class="form-group">
        {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
        </div>
      </div>
      <div class="form-group">
        {!! Form::label('action', 'Action', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          <div id="action_div">
            <div class="input-group">
              <div class="input-group-btn">
                {!! Form::select('action[type][]', ['1' => 'Toggle', '2' => 'Turn on', '3' => 'Turn off'], null, ['class' => 'form-control']) !!}
              </div>
              {!! Form::text('action[bot][]', null, ['class' => 'form-control border-left-none']) !!}
            </div>
          </div>
          {!! Form::button('<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add action', ['class' => 'btn btn-default pull-right','onclick' => 'addAction()']) !!}
        </div>
      </div>
      <div class="form-group">
        {!! Form::label('type', 'Type', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          {!! Form::select('type', ['1' => 'One time', '2' => 'Repeat'], old('type'), ['class' => 'form-control up-zindex', 'onChange' => 'changeType()', 'style' => 'width: 100% !important']) !!}
        </div>
      </div>
      <div id="one-many-time-div">
        <div class="form-group">
          {!! Form::label('time', 'Time', ['class' => 'col-sm-2 control-label']) !!}
          <div class="col-sm-10">
            {!! Form::text('time', old('time'), ['class' => 'form-control datetimepicker']) !!}
          </div>
        </div>
      </div>
      <div class="form-group">
        {!! Form::label('condition', 'Condition', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          <div class="input-group">
            {!! Form::select('condition[type]', ['0' => 'Off', '1' => 'Work if', '2' => 'Not work if'], null, ['class' => 'form-control', 'onChange' => 'changeCondition()']) !!}
            <span id="condMethod"></span>
          </div>
          <div id='conditions-div'></div>
          <div id='add-cond-btn-div'></div>
        </div>
      </div>
      <div class="form-group">
        {!! Form::label('activate_after', 'Activate after', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          {!! Form::text('activate_after', old('activate_after'), ['class' => 'form-control datetimepicker']) !!}
          <span class="help-block margin-bottom-none">
            Leave blank to activate immediately
          </span>
        </div>
      </div>
      <div class="form-group">
        {!! Form::label('deactivate_after', 'Deactivate after', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          <div class="input-group">
            {!! Form::text('deactivate_after_times', old('deactivate_after_times'), ['class' => 'form-control']) !!}
            <span class="input-group-addon border-left-none border-right-none">
              time(s) or
            </span>
            {!! Form::text('deactivate_after_datetime', old('deactivate_after_datetime'), ['class' => 'form-control datetimepicker']) !!}
          </div>
          <span class="help-block margin-bottom-none">
            Leave blank to avoid (infinitive loop or deactivate manually)
          </span>
        </div>
      </div>
      {!! Form::button('<span class="btn-label"><i class="fa fa-plus" aria-hidden="true"></i></span>Add', ['type' => 'submit', 'class' => 'btn btn-primary  waves-effect waves-light']) !!}
    {!! Form::close() !!}
  </div>
</div>
</div>
@endsection
