{{-- @extends('layouts.app') --}}
@extends('layouts.hpEventSearch')

@section('javascript-document-ready')
    @parent

    {{--  Smooth Scroll on search - when we have anchor on the url --}}
        if ( window.location.hash ) scroll(0,0); {{-- to top right away --}}
        setTimeout( function() { scroll(0,0); }, 1); {{-- void some browsers issue --}}

        if(window.location.hash) {
            {{-- smooth scroll to the anchor id --}}
            $('html, body').animate({
                scrollTop: $(window.location.hash).offset().top + 'px'
            }, 1300, 'swing');
            
        }
    
    {{-- Update Continent SELECT on change Country SELECT --}}
        $("select[name='country_id']").on('change', function() {
            //alert( this.value );

            var request = $.ajax({
                url: "/update_continents_dropdown",
                data: {
                    country_id: this.value,
                },
                success: function( data ) {
                    $("#continent_id").selectpicker('val', data);
                }
            });
        });

    {{-- Update Country SELECT on change Continent SELECT --}}
        $("select[name='continent_id']").on('change', function() {
            updateCountriesDropdown(this.value);
        });
        
        
        
        $(document).ready(function(){
            
            {{-- On page load update the Country SELECT if a Continent is selected --}}
                var continent_id =  $("select[name='continent_id']").val();
                var country_id =  $("select[name='country_id']").val();
                 
                if (continent_id != ''){
                    
                    //alert(continent_id);
                    updateCountriesDropdown(continent_id);
                    if (country_id != null){
                        setTimeout(() => {
                            $("#country_id").selectpicker('val', country_id);
                        }, 300);
                     }
                 }
		});
        
        {{-- Update the Countries SELECT with just the ones 
             relative to the selected continent --}}
        function updateCountriesDropdown(selectedContinent){
            var request = $.ajax({
                url: "/update_countries_dropdown",
                data: {
                    continent_id: selectedContinent,
                },
                success: function( data ) {
                    $("#country_id").html(data);
                    $("#country_id").selectpicker('refresh');
                }
            });
        }

@stop

@section('beforeContent')

    {{-- This is to show the user activation message in homepage to the Admin, when click on the user activation link --}}
        @if(session()->has('message'))
            <div class="alert alert-success" style="z-index:3;">
                {{ session()->get('message') }}
            </div>
        @endif
        
        

    {{-- HI-Light for the donations --}}
    <div class="container">
        <div class="row">
            <div class="col-12">
                @include('partials.hilight', [
                    'title' =>  'Dear users: ',
                    'text' =>  'The CI Global Calendar is a non-profit project to support the CI Global Community. 
                                To protect our independence we don’t want to run ads. We have no governmental funds. 
                                If the calendar is useful to you take one minute to help us keep it online another year. If everyone reading this message would give the same amount that you offer for a jam, our fundraiser would be done within a week. Thank you!',
                      'linkText' => 'Donate',
                      'linkUrl'  => '/post/donate',
                ])
            </div>
        </div>
    </div>
    
    


    {{-- The event search interface in Homepage --}}
    <div class="eventSearch jumbotron">
        
        @include('partials.jumboBackgroundChange')
        
        <div class="container">
            <div class="row intro">
                <div class="col-12 text-center">

                    <h1 class="text-white mb-3">@lang('homepage-serach.contact_improvisation')</h1>
                    <h4 class="text-uppercase"><strong>- @lang('homepage-serach.global_calendar') -</strong></h4>
                    <p class="subtitle text-white">
                        @lang('homepage-serach.find_information')<br />
                        {{--@lang('homepage-serach.under_costruction')--}}
                    </p>
                    <p>
                    
                    
                    {{--@include('partials.forms.button', [
                          'text' =>  'Help us with the Global Fill-in',
                          'name' => 'category_id',
                          'url' => '/post/help-us-with-the-global-fill-in',
                          'roundedCorners' => 'true',
                    ])--}}
                    </p>
                    <p class="searchHere text-white mt-5">
                        @lang('homepage-serach.criteria')
                    </p>
                </div>
            </div>

            @if ($message = Session::get('success'))
                <div class="alert alert-success mt-4">
                    <p>{{ $message }}</p>
                </div>
            @endif
             
            
            {{-- Search form --}}
            {{--<form class="searchForm" action="{{ route('eventSearch.index') }}" method="GET">--}}
            <form class="searchForm" action="/eventSearch#dataarea" method="GET">
                {{--@csrf--  CSRF is just for POST requests }}

                {{--<div class="row mt-3">
                    <div class="form-group col-12">
                        <input type="text" name="keywords" id="keywords" class="form-control" placeholder="Search by event name" value="{{ $searchKeywords }}">
                    </div>
                </div>--}}

                <div class="row">
                    <div class="col-md-4 order-2 order-sm-1">
                        
                        {{-- WHAT --}}
                            <p><strong class="text-white">@lang('homepage-serach.what')</strong></p>
                            
                            @include('laravel-form-partials::select', [
                                  'title' =>  '',
                                  'name' => 'category_id',
                                  'placeholder' => __('homepage-serach.all_kind_of_events'),
                                  'records' => $eventCategories,
                                  'selected' => $searchCategory,
                                  'liveSearch' => 'true',
                                  'mobileNativeMenu' => false,  //disabled for the bug on iPad and iPhone - Retry when will be available v.2 of bootstrap-select - https://github.com/snapappointments/bootstrap-select/issues/2228
                            ])
                        
                        {{-- WHO --}}
                            <p class="mt-3"><strong class="text-white">@lang('homepage-serach.who')</strong></p>
                            
                            @include('partials.forms.event-search.select-teacher')
                            
                    </div>
                    <div class="col-md-4 order-1 order-sm-2">
                        <p class="text-white">
                            <strong>@lang('homepage-serach.where')</strong>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="@lang('homepage-serach.where_tooltip')"></i>
                        </p>
                        
                        
                        {{--
                        <continents-countries-selects select_a_continent_placeholder="@lang('homepage-serach.select_a_continent')" select_a_country_placeholder="@lang('homepage-serach.select_a_country')" continent-selected="{{$searchContinent}}" country-selected="{{$searchCountry}}"></continents-countries-selects>
                        --}}
                        
                        
                        @include('laravel-form-partials::select', [
                              'title' =>  '',
                              'name' => 'continent_id',
                              'placeholder' => __('homepage-serach.select_a_continent'),
                              'records' => $continents,
                              'selected' => $searchContinent,
                              'liveSearch' => 'false',
                              'mobileNativeMenu' => false, // disabled for the bug on iPad and iPhone - Retry when will be available v.2 of bootstrap-select - https://github.com/snapappointments/bootstrap-select/issues/2228
                        ])
                        
                        @include('laravel-form-partials::select', [
                              'title' =>  '',
                              'name' => 'country_id',
                              'placeholder' => __('homepage-serach.select_a_country'),
                              'records' => $countries,
                              'selected' => $searchCountry,
                              'liveSearch' => 'true',
                              'mobileNativeMenu' => false,
                        ])
                        
                        @include('laravel-form-partials::input', [
                              'title' => '',
                              'name' => 'city_name',
                              'placeholder' => __('homepage-serach.search_by_city'),
                              'value' => $searchCity
                        ])
                        
                        {{--<p class="mt-3"><strong class="text-white">@lang('homepage-serach.search_by_venue')</strong></p>--}}
                        @include('laravel-form-partials::input', [
                              'title' => '',
                              'name' => 'venue_name',
                              'placeholder' => __('homepage-serach.venue_name'),
                              'value' => $searchVenue
                        ])
                    </div>
                    <div class="col-md-4 order-3">
                        <p class="text-white">
                            <strong>@lang('homepage-serach.when')</strong>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="@lang('homepage-serach.when_tooltip')"></i>
                        </p>
                        @include('partials.forms.event-search.input-date-start')
                        @include('partials.forms.event-search.input-date-end')
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mt-sm-10 mt-3">
                        <a id="resetButton" class="btn btn-info float-right ml-2" href="{{ URL::route('home') }}">@lang('general.reset')</a>
                        <input type="submit" value="@lang('general.search')" class="btn btn-primary float-right">
                    </div>
                </div>
                
                {{-- Photo Credits--}}
                <div class="row">
                    <small class="col-12 credits mt-5">
                        @lang('homepage-serach.photo_credits'):
                        <span class="name">
                            
                        </span>
                    </small>
                </div>
                    
                
            </form>

            @if (Route::is('eventSearch.index'))  {{-- Show search results just when search button is pressed --}}
                
                {{-- List of events --}}
                <a id="dataarea"></a> {{-- Anchor to scroll on search --}}
                <div class="row mt-5">
                    <div class="col-7 col-md-9"></div>
                    <div class="col-5 col-md-3 bg-light text-right py-1">
                        <small>{{$events->total()}} @lang('homepage-serach.results_found')</small>
                    </div>
                </div>
                
                @include('partials.event-list', [
                      'events' => $events,
                      'iframeLinkBlank' => false,
                ])

                {{--{!! $events->links() !!}--}}
                
                {!! $events->appends([
                    'category_id' => $searchCategory,
                    'continent_id' => $searchContinent,
                    'country_id' => $searchCountry,
                    'city_name' => $searchCity,
                    'venue_name' => $searchVenue,
                    'startDate' => $searchStartDate,
                    'endDate' => $searchEndDate,
                ])->links() !!}
                
                
            @endif   
        </div>
        <div class="bg-overlay"></div>


    </div>

@endsection

{{--
@section('content')

@endsection
--}}
