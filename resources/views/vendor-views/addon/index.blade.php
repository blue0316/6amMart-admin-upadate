@extends('layouts.vendor.app')

@section('title',translate('messages.add_new_addon'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/addon.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.add_new_addon')}}</span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('vendor.addon.store')}}" method="post">
                            @csrf
                            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')
                            @if($language)
                                @php($default_lang = json_decode($language)[0])
                                <ul class="nav nav-tabs mb-4 border-0">
                                    @foreach(json_decode($language) as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                @foreach(json_decode($language) as $lang)
                                    <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_addon')}}" maxlength="191" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            @else
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                                    <input type="text" name="name" class="form-control" placeholder="{{translate('messages.new_addon')}}" value="{{old('name')}}" required maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                            @endif

                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                                <input type="number" min="0" max="999999999999.99" name="price" step="0.01" class="form-control" placeholder="100.00" value="{{old('price')}}" required>
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{translate('messages.addon')}} {{translate('messages.list')}}  
                                <span class="badge badge-soft-dark ml-2" id="itemCount">{{$addons->total()}}</span>
                            </h5>
                            <form id="search-form" class="search-form">
                                <div class="input-group input--group">
                                    <input type="text" id="column1_search" class="form-control" placeholder="{{translate('messages.ex_search_name')}}">
                                    <button type="button" class="btn btn--secondary">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0 w-10p">{{translate('messages.#')}}</th>
                                    <th class="border-0 w-50p">{{translate('messages.name')}}</th>
                                    <th class="border-0 w-40p">{{translate('messages.price')}}</th>
                                    <th class="border-0 w-20p text-center">{{translate('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($addons as $key=>$addon)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($addon['name'], 20, '...')}}
                                    </span>
                                    </td>
                                    <td>{{\App\CentralLogics\Helpers::format_currency($addon['price'])}}</td>
                                    <td>
                                        <div class="btn--container">
                                            <a class="btn action-btn btn--primary btn-outline-primary"
                                                    href="{{route('vendor.addon.edit',[$addon['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.addon')}}"><i class="tio-edit"></i></a>
                                            <a class="btn action-btn btn--danger btn-outline-danger"     href="javascript:"
                                                onclick="form_alert('addon-{{$addon['id']}}','Want to delete this addon ?')" title="{{translate('messages.delete')}} {{translate('messages.addon')}}"><i class="tio-delete-outlined"></i></a>
                                        </div>
                                        <form action="{{route('vendor.addon.delete',[$addon['id']])}}"
                                                    method="post" id="addon-{{$addon['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <table>
                            <tfoot>
                            {!! $addons->links() !!}
                            </tfoot>
                        </table>
                        @if(count($addons) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$default_lang}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });
    </script>
@endpush
