@extends('admin.layouts.app')

@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @lang('custom.nav.customer')
            </h1>
            <ol class="breadcrumb">
                <li>
                    {!! html_entity_decode(
                        Html::linkRoute(
                            'admin.home',
                            '<i class="fa fa-dashboard"></i> ' . Lang::get('custom.common.dashboard')
                        )
                    ) !!}
                </li>
                <li>
                    {!! Html::linkRoute(
                        'admin.order.index',
                        Lang::get('custom.nav.orders')
                    ) !!}
                </li>
                <li class="active">
                    @lang('custom.common.list')
                </li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            {!! Form::open(
                [
                    'url' => 'admin/show-customer',
                    'enctype' => 'multipart/form-data',
                ]
            ) !!}
            {!! Form::hidden('confirm', Lang::get('custom.form.confirm_delete')) !!}
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <h3 class="pull-left">
                            @lang('custom.common.title_potential_customer')
                        </h3>

                        <br />

                        {{-- {!! Form::select(
                            'filter-order',
                            [
                                '' => Lang::get('custom.common.orders'),
                                Lang::get('custom.common.pending') => Lang::get('custom.common.pending'),
                                Lang::get('custom.common.completed') => Lang::get('custom.common.completed'),
                                Lang::get('custom.common.reject') => Lang::get('custom.common.reject'),
                            ],
                            null,
                            [
                                'class' => 'form-control',
                                'id' => 'filter-order',
                            ]
                        ) !!} --}}
                                {!! Form::text(
                                        'revenueRange',
                                        old('revenueRange'),
                                        [
                                            'class' => 'form-control',
                                            'placeholder' => Lang::get('custom.common.promotion_date'),
                                            'required' => 'required',
                                            'id' => 'potential_customer',
                                        ]
                                    ) !!}

                                @if($errors->first('daterangepicker_start'))
                                    <p class="text-danger">
                                        <strong>{{ $errors->first('daterangepicker_start') }}</strong>
                                    </p>
                                @endif

                                @if($errors->first('daterangepicker_end'))
                                    <p class="text-danger">
                                        <strong>{{ $errors->first('daterangepicker_end') }}</strong>
                                    </p>
                                @endif
                                <br>
                                {!! Form::submit(
                                    'filter',
                                    [
                                        'id' => 'btn-filter-revenue',
                                        'class' => 'btn btn-primary',
                                    ]
                                ) !!}
                    </div>

                    @include('admin.partials.success')
                    <div class="alert alert-success alert-dismissible col-sm-3" style="display: none;">
                        {!! Form::button(
                            '&times;',
                            [
                                'class' => 'close',
                                'data-dismiss' => 'alert',
                            ]
                        ) !!}
                        <strong></strong>
                    </div>

                </div>
                <div class="box-body">
                    <table id="orders-table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>@lang('custom.common.customer_time')</th>
                                <th>@lang('custom.common.customer_name')</th>
                                <th>@lang('custom.common.customer_email')</th>
                                <th>@lang('custom.common.customer_amount_order')</th>
                                <th>@lang('custom.common.customer_total_money')</th>
                                {{-- <th>@lang('custom.common.name')</th>
                                <th>@lang('custom.common.purchase')</th>
                                <th>@lang('custom.common.delivery')</th>
                                <th>@lang('custom.common.status')</th>
                                <th></th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @if(isset($monthFormat))
                                    <td><b>{{ $monthFormat }}</b></td>
                                    <td><b>{{ $potential->name }} </b></td>
                                    <td><b>{{ $potential->email }} </b></td>
                                    <td><b>{{ $maxNumber }}</b></td>
                                    <td><b>{{ $formatTotalMoney }} VND</b></td>
                                @else
                                    <td><b>{{ $start }} -> {{ $end }}</b></td>
                                    <td><b>{{ $potential ? $potential->name : 0 }} </b></td>
                                    <td><b>{{ $potential ? $potential->email : 0 }} </b></td>
                                    <td><b>{{ $maxNumber }}</b></td>
                                    <td><b>{{ $formatTotalMoney }} VND</b></td>
                                @endif
                            </tr>
                        </tbody>
                        {{-- <tfoot>
                            <tr>
                                <th>thoi gian</th>
                                <th>doanh so</th>
                                <th>@lang('custom.common.name')</th>
                                <th>@lang('custom.common.purchase')</th>
                                <th>@lang('custom.common.delivery')</th>
                                <th>@lang('custom.common.status')</th>
                                <th></th>
                            </tr>
                        </tfoot> --}}
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            {!! Form::close() !!}
            <!-- /.box -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('script')
    {!! Html::script('js/admin/revenue.js') !!}
 @endsection
