<?php

use Illuminate\Http\Request;
use App\Http\Resources\Event as EventResource;
use App\Http\Resources\Country as CountryResource;
use App\Http\Resources\Teacher as TeacherResource;
use App\Http\Resources\Continent as ContinentResource;
use DavideCasiraghi\LaravelEventsCalendar\Models\Event;
use DavideCasiraghi\LaravelEventsCalendar\Models\Country;
use DavideCasiraghi\LaravelEventsCalendar\Models\Teacher;
use DavideCasiraghi\LaravelEventsCalendar\Models\Continent;
use DavideCasiraghi\LaravelEventsCalendar\Models\EventRepetition;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* Teachers */
    Route::get('/teacher/{id}', function ($id) {  // http://ciglobalcalendar.net/api/teacher/33
        return new TeacherResource(Teacher::find($id));
    });

    Route::get('/teachers', function () {  // http://ciglobalcalendar.net/api/teachers
        return TeacherResource::collection(Teacher::all());
    });

/* Events */
    Route::get('/event/{id}', function ($id) {
        return new EventResource(Event::find($id));
    });

    /*Route::get('/events', function () {
        return EventResource::collection(Event::all());
    });*/

    Route::get('/events/country/{countryId}', function ($countryId) {
        return EventResource::collection(Event::getEvents([
            'country' => $countryId,
            'endDate' => '',
            'keywords' => '',
            'category' => '',
            'city' => '',
            'teacher' => '',
            'continent' => '',
            'venue' => '',
        ], 200)->getCollection());
    });

    Route::get('/events/teacher/{id}', function ($id) {  // http://ciglobalcalendar.net/api/events/teacher/1
        date_default_timezone_set('Europe/Rome');
        $searchStartDate = date('Y-m-d', time());
        $lastestEventsRepetitionsQuery = EventRepetition::getLastestEventsRepetitionsQuery($searchStartDate, null);

        return EventResource::collection(
            Teacher::eventsByTeacher(Teacher::find($id), $lastestEventsRepetitionsQuery, $searchStartDate)
        );
    });

/* Continents */
    Route::get('/continent/{id}', function ($id) {
        return new ContinentResource(Continent::find($id));
    });
    Route::get('/continents', function () {
        return ContinentResource::collection(Continent::all());
    });
    Route::get('/countries/activeContinentCountriesJsonTree', function () {
        return ContinentResource::collection(Continent::all());
    });

/* Countries */
    Route::get('/country/{id}', function ($id) {
        return new CountryResource(Country::find($id));
    });
    Route::get('/countries', function () {
        return CountryResource::collection(Country::all());
    });

    Route::get('/activeCountries', function () {
        return CountryResource::collection(Country::getActiveCountries()->unique('name')->sortBy('name'));
    });

/*
Route::get('posts/{post}/comments/{comment}', function ($postId, $commentId) {
    //
});
https://readouble.com/laravel/5.7/en/routing.html
*/
