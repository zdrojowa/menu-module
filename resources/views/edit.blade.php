@extends('DashboardModule::base')

@section('title','Dashboard')

@section('stylesheets')
    <link rel="stylesheet" href="{{ mix('vendor/css/RevisionModule.css','') }}">
    <link rel="stylesheet" href="{{ mix('vendor/css/MenuModule.css','') }}">
@endsection

@section('sidebar')
    @include('DashboardModule::sidebar.index', ['menu' => Selene\Support\Facades\MenuRepository::getPresences()])
@endsection

@section('content')
    <div class="content-wrapper">
        <div id="app">
            @if (isset($menu))
                <menu-editor :_id=`{{ $menu->_id }}` :lang=`{{ $lang }}`>
                    {{ csrf_field() }}
                </menu-editor>
                <div class="row">
                    <div class="col-12 mt-2">
                        @include('RevisionModule::revisions', [
                            'table'      => 'menu',
                            'content_id' => $menu->id
                        ])
                    </div>
                </div>
            @else
                <menu-editor :_id="0">
                    {{ csrf_field() }}
                </menu-editor>
            @endif
        </div>
    </div>
@endsection

@section('javascripts')
    @parent
    @javascript('csrf', csrf_token())
    <script src="{{ mix('vendor/js/RevisionModule.js') }}"></script>
    <script src="{{ mix('vendor/js/MenuModule.js') }}"></script>
@endsection
