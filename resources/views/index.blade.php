@extends('DashboardModule::base')

@section('title','Dashboard')

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="{{ mix('vendor/css/LanguageModule.css','') }}">
    <link rel="stylesheet" href="{{ mix('vendor/css/MenuModule.css','') }}">
@endsection

@section('sidebar')
    @include('DashboardModule::sidebar.index', ['menu' => Selene\Support\Facades\MenuRepository::getPresences()])
@endsection

@section('content')
    <div class="content-wrapper" id="app">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header clearfix">
                        <h4 class="card-title float-left">Dostępne menu</h4>
                        <a href="{{route('MenuModule::add')}}" class="text-success float-right">
                            <i class="mdi mdi-plus-circle"></i> Dodaj
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="form-inline">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Język</label>
                                </div>
                                <div class="col-md-4">
                                    <select name="lang">
                                        @foreach($langs as $ln)
                                            <option value="{{ $ln->short_name }}" @if($ln->short_name === $lang) selected @endif>
                                                {{ $ln->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Nazwa</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="name" value="{{ $name }}"/>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td>Nazwa</td>
                                    <td>Język</td>
                                    <td>Tłumaczenia</td>
                                    <td>Akcje</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menus as $menu)
                                    <tr>
                                        <td>{{ $menu->name }}</td>
                                        <td>
                                            @if($menu->lang === 'en')
                                                <span class="flag-icon flag-icon-gb"></span>
                                            @else
                                                <span class="flag-icon flag-icon-{{ $menu->lang }}"></span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $translations = $menu->getTranslations();
                                            @endphp
                                            @foreach($langs as $ln)
                                                @if ($menu->lang !== $ln->short_name)
                                                    @if (isset($translations[$ln->short_name]))
                                                        <a href="{{ route('MenuModule::edit', [
                                                            'menu' => $translations[$ln->short_name]
                                                        ]) }}">
                                                            @if($ln->short_name === 'en')
                                                                <span class="flag-icon flag-icon-gb"></span>
                                                            @else
                                                                <span class="flag-icon flag-icon-{{ $ln->short_name }}"></span>
                                                            @endif
                                                        </a>
                                                    @else
                                                        <a href="{{ route('MenuModule::addTranslation', [
                                                            'menu' => $menu->id,
                                                            'lang' => $ln->short_name
                                                        ]) }}">
                                                            @if($ln->short_name === 'en')
                                                                <span class="flag-icon flag-icon-gb" style="opacity: 0.5"></span>
                                                            @else
                                                                <span class="flag-icon flag-icon-{{ $ln->short_name }}" style="opacity: 0.5"></span>
                                                            @endif
                                                        </a>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <div>
                                                <a href="{{ route('MenuModule::edit', ['menu' => $menu->_id]) }}" class="btn btn-sm btn-primary">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="{{ route('MenuModule::destroy', ['menu' => $menu->_id]) }}" class="btn btn-sm btn-danger remove">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row mt-2 ml-5">
                            {{ $menus->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    @parent
    <script src="{{ mix('vendor/js/MenuModule.js') }}"></script>
    <script>
        $(document).ready(function(){
            let form = $('form');

            $('select').change(function(){
                form.submit();
            });

            $('a.remove').click(function(e){
                e.preventDefault();
                let url = $(this).attr('href');

                Swal.fire({
                    title: 'Na pewno chcesz to zrobić?',
                    text: 'Nie będzie można tego przywrócić!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d53f3a',
                    confirmButtonText: 'Tak',
                    cancelButtonText: 'Powrót'
                }).then(result => {
                    if(!result.value) return;

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            "_method": "DELETE",
                            "_token": $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function () {
                            Swal.fire('Usunięto!', 'Akcja zakończyła się sukcesem', 'success');
                            location.reload() ;
                        },
                        error: function () {
                            Swal.fire('Wystąpił błąd!', 'Wystąpił błąd po stronie serwera', 'error');
                        }
                    })
                })
            });
        });
    </script>
@endsection

