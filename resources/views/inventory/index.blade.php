@extends('components.navbar')
@section('head')
<style>
  .default-card{
    border: 5px solid #28a745 !important;
  }

  .hidden{
    visibility: hidden;
  }
  </style>
@endsection
@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark"></h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h1 class="m-0 text-dark">Bank Account 
              <a class="btn btn-success" style="float:right;" href="{{route('inventory.create')}}"><i class="fas fa-plus"></i> Add New Bank Account</a></h1>
          </div>
          <div class="card-body">
            <div class="row">

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
  @endsection
  
  @section('script')
  <script>
    $('.default-button').on('click', function(e){
      $('.default-card').removeClass('default-card');
      $('.default-button').removeClass('hidden');
      $(this).closest('.card').addClass('default-card');
      $(this).addClass('hidden');
      var bank_account_id = $(this).attr('bank-account-id');
      $.post("{{url('/bank-account/default')}}",
        {
            _token : "{{ csrf_token() }}",
            bank_account_id: bank_account_id
        });
    });
  </script>
  @endsection