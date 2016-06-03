@extends('hub.master')
@section('title','Add new member')
@section('additionHeader')
  <link href="{{ asset('public/libs/multiselect/css/multi-select.css') }}" media="screen" rel="stylesheet" type="text/css">

  <style>
    .table td {
      font-weight: bold;
      text-align: center;
    }
  </style>
@endsection
@section('additionFooter')
  <script src="{{ asset('public/libs/multiselect/js/jquery.multi-select.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/libs/quicksearch/jquery.quicksearch.js') }}" type="text/javascript"></script>
<script>
  $("[name='hubpermissions[]']").materialSwitch();
  var searchableObj = {
      selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Search...'>",
      selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Search...'>",
      afterInit: function (ms) {
          var that = this,
              $selectableSearch = that.$selectableUl.prev(),
              $selectionSearch = that.$selectionUl.prev(),
              selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
              selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

          that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
              .on('keydown', function (e) {
                  if (e.which === 40) {
                      that.$selectableUl.focus();
                      return false;
                  }
              });

          that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
              .on('keydown', function (e) {
                  if (e.which == 40) {
                      that.$selectionUl.focus();
                      return false;
                  }
              });
      },
      afterSelect: function () {
          this.qs1.cache();
          this.qs2.cache();
      },
      afterDeselect: function () {
          this.qs1.cache();
          this.qs2.cache();
      }
  };

  $("[name='permissions[]']").multiSelect(searchableObj);

  $("[name='higherpermissions[]']").multiSelect(searchableObj);

  function memDeactivate(id) {
      bootbox.confirm("R u sure?", function(result) {
        if (result == true) {
        $.ajax({
            url : '{{ route('h::m::deactivate',$mem['id']) }}',
            type : 'post',
            dataType: 'json',
            data : {
              _token: '{{ csrf_token() }}',
              id: id
            },
            success : function (response)
            {
                $('#memTus').text('Deactivated').removeClass('label-primary').addClass('label-danger');
                memDeactivateBtn = $('#memDeactivateBtn').attr('id','memReactivateBtn').removeClass('btn-warning').addClass('bg-olive').attr('onclick','memReactivate()');
                memDeactivateBtn.find('i').removeClass('fa-ban').addClass('fa-check-square-o');
                memDeactivateBtn.find('span').text('Reactivate');
            }
          });
        }
      });
    }

  function memReactivate(id) {
    bootbox.confirm("R u sure?", function(result) {
      if (result == true) {
      $.ajax({
          url : '{{ route('h::m::reactivate',$mem['id']) }}',
          type : 'post',
          dataType: 'json',
          data : {
            _token: '{{ csrf_token() }}',
            id: id
          },
          success : function (response)
          {
              $('#memTus').text('Activated').removeClass('label-danger').addClass('label-primary');
              memReactivateBtn = $('#memReactivateBtn').attr('id','memDeactivateBtn').addClass('btn-warning').removeClass('bg-olive').attr('onclick','memDeactivate()');
              memReactivateBtn.find('i').addClass('fa-ban').removeClass('fa-check-square-o');
              memReactivateBtn.find('span').text('Deactivate');
          }
        });
      }
    });
  }

  function memDelete() {
    bootbox.confirm("R u sure?", function(result) {
      if (result == true) {
      $.ajax({
          url : '{!!  route('h::m::destroy',$mem['id']) !!}',
          type : 'post',
          dataType: 'json',
          data : { _token: '{{ csrf_token() }}' },
          success : function (response)
          {
              window.location.href = '{{ route('h::m::index') }}';
          }
        });
      }
    });
  }
</script>
@endsection
@section('body')
  {!! content_header('Hub members', [
    'Hub' => route('h::edit'),
    'Member' => route('h::m::index'),
    'Edit' => '#']) !!}
    <div class="row">
    <div class="col-sm-12">
        <div class="card-box">
    {!! Form::open(['route' => ['h::m::edit',$mem['id']], 'files' => true, 'class' => 'form-horizontal']) !!}
      <div class="box-body">
        <div class="form-group margin-bottom-sm">
        {!! Form::label('status', 'Status',['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
          <?php
            switch ($mem['status']) {
              case 0:
                echo '<h4><span class="label label-danger" id="memTus">Deactivated</span></h4>';
                break;
              case 1:
                echo '<h4><span class="label label-primary" id="memTus">Activated</span></h4>';
                break;
            }
          ?>
        </div>
      </div>
        {!! Form::hidden('id', $mem['id']) !!}
        <div class="form-group">
          {!! Form::label('username', 'Members\'s username', ['class' => 'col-sm-2 control-label']) !!}
          <div class="col-sm-10">
            {!! Form::text('username', $username, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('username', 'Members\'s permissions', ['class' => 'col-sm-2 control-label']) !!}
          <div class="col-sm-10">
            {!! Form::select('permissions[]', $bots, $selected, ['class' => 'form-control', 'multiple' => 'multiple']) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('username', 'Members\'s higher-permissions', ['class' => 'col-sm-2 control-label']) !!}
          <div class="col-sm-10">
            {!! Form::select('higherpermissions[]', $bots, $selected2, ['class' => 'form-control', 'multiple' => 'multiple']) !!}
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-12">
            <table class="table table-bordered table-striped margin-bottom-none">
            <thead>
              <tr>
              <td></td>
              <td width="1%">Add/Create</td>
              <td width="1%">View&nbsp;/Control</td>
              <td width="1%">Edit/Delete</td>
              </tr>
            </thead>
            <tbody>
            <tr>
              <td class="text-left">Hub</td>
              <td></td>
              <td>{!! Form::checkbox('hubpermissions[]', 1, in_array(1,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 2, in_array(2,$hubperms)) !!}</td>
            </tr>
            <tr>
              <td class="text-left">Bots</td>
              <td>{!! Form::checkbox('hubpermissions[]', 3, in_array(3,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 4, in_array(4,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 5, in_array(5,$hubperms)) !!}</td>
            </tr>
            <tr>
              <td class="text-left">Schedules</td>
              <td>{!! Form::checkbox('hubpermissions[]', 6, in_array(6,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 7, in_array(7,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 8, in_array(8,$hubperms)) !!}</td>
            </tr>
            <tr>
              <td class="text-left">Automations</td>
              <td>{!! Form::checkbox('hubpermissions[]', 9, in_array(9,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 10, in_array(10,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 11, in_array(11,$hubperms)) !!}</td>
            </tr>
            <tr>
              <td class="text-left">Members</td>
              <td>{!! Form::checkbox('hubpermissions[]', 12, in_array(12,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 13, in_array(13,$hubperms)) !!}</td>
              <td>{!! Form::checkbox('hubpermissions[]', 13, in_array(14,$hubperms)) !!}</td>
            </tr>
            </tbody>
            </table>
          </div>
        </div>
        {!! Form::button('<span class="btn-label"><i class="fa fa-floppy-o" aria-hidden="true"></i></span>Save', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
        {!! Form::button('<span class="btn-label"><i class="fa fa-trash" aria-hidden="true"></i></span>Delete', ['type' => 'button', 'class' => 'btn btn-danger pull-right', 'onclick' => 'memDelete()']) !!}</a>
        @if ($mem['status'] != 0)
          {!! Form::button('<span class="btn-label"><i class="fa fa-ban" aria-hidden="true"></i></span><span>Deactivate</span>', ['type' => 'button', 'class' => 'btn btn-warning pull-right m-r-5','id' => 'memDeactivateBtn','onclick' => 'memDeactivate()']) !!}
        @else
          {!! Form::button('<span class="btn-label"><i class="fa fa-check-square-o" aria-hidden="true"></i></i></span><span>Reactivate</span>', ['type' => 'button', 'class' => 'btn bg-olive pull-right m-r-5','id' => 'memReactivateBtn','onclick' => 'memReactivate()']) !!}
        @endif
    {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection
