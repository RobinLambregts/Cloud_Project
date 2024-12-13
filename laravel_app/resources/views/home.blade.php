@extends('layouts.app')

<script>
    function getSports() {
        fetch('http://127.0.0.1:5000/sports')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                data.forEach(sport => {
                    document.getElementById('output').innerHTML += `<p>${sport}</p>`;
                });
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                alert('Staat de Python server aan?');
            });
    }
</script>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
            <div class="m-5 flex-column">
                <h1>Select your favourite sports!</h1>
                <button onclick="getSports()">GO TO SPORTS</button>
                <div id="output" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
@endsection