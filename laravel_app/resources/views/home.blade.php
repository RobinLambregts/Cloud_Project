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
                outputDiv = document.getElementById('output');
                
                data.forEach(sport => {
                    const sportDiv = document.createElement('div');
                    sportDiv.style.border = '2px solid red';
                    sportDiv.style.padding = '10px';
                    sportDiv.style.margin = '5px';

                    const sportName = document.createElement('h2');
                    sportName.innerText = sport[0];
                    sportDiv.appendChild(sportName);

                    const sportLocation = document.createElement('p');
                    sportLocation.innerText ="Location: " + sport[1];
                    sportDiv.appendChild(sportLocation);
                    outputDiv.appendChild(sportDiv);
                })
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
