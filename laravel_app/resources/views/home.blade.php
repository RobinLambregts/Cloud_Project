@extends('layouts.app')

<script>
    function switchToSports() {
        const output = document.getElementById('output');
        output.innerHTML = '<p>Loading...</p>';
        
        // Use the Fetch API to call the Python backend
        fetch('http://127.0.0.0:5000/sports')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // Update the output div with data from the API
                document.getElementById('output').innerHTML = 
                    `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                alert('Failed to fetch sports data.');
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
                <button onclick="switchToSports()">GO TO SPORTS</button>
                <div id="output" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
@endsection
