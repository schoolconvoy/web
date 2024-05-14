@extends('public.layout')

@section('content')
    <div class="flex flex-row">
        <div class="w-4/12" style="background-image: url( {{ asset('images/pexels-katerina-holmes-5905497-small-Large.jpeg') }} )">
        </div>
        <div class="mx-16 my-16 w-7/12">
            <h1 class="text-2xl font-bold py-4">Register your pupil</h1>
            @livewire('students.admission')
        </div>
    </div>
@endsection
