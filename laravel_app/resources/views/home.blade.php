@extends('layouts.app')

<script>
    const currentUser = @json(Auth::user());

    function voteSport(sport, stem, button) {
        button.style.display = 'none';

        fetch('http://127.0.0.1:5000/sports/vote/' + sport + '/' + stem, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Staat de Python server aan?');
        });
    }

    function addSport() {
        outputDiv = document.getElementById('output');
        outputDiv.innerHTML = '';
        const sportName = document.getElementById('sportName').value;
        const sportLocation = document.getElementById('sportLocation').value;
        const eenheid = document.getElementById('eenheid').value;

        if (!sportName || !sportLocation || !eenheid) {
            alert("All fields are required!");
            return;
        }

        fetch('http://127.0.0.1:5000/sports/add/' + sportName + '/' + sportLocation + '/' + eenheid, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            getSports();
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Staat de Python server aan?');
        });
    }

    function deleteSport(sport, outputDiv, sportDiv) {
        fetch('http://127.0.0.1:5000/sports/remove/' + sport[0], {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            outputDiv.removeChild(sportDiv);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Staat de Python server aan?');
        });
    }

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
                outputDiv.innerHTML = '';
                
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

                    const deleteButton = document.createElement('button');
                    if (currentUser.role !== 'praesidium') {
                        deleteButton.style.display = 'none';
                    }
                    deleteButton.innerText = 'Delete';
                    deleteButton.onclick = function() {
                        deleteSport(sport, outputDiv, sportDiv);
                    };
                    sportDiv.appendChild(deleteButton);

                    const upvoteButton = document.createElement('button');
                    upvoteButton.id = 'upvoteButton';
                    upvoteButton.innerText = 'Upvote';
                    upvoteButton.onclick = function() {
                        voteSport(sport[0], 1, upvoteButton);
                    };
                    sportDiv.appendChild(upvoteButton);
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

                    {{ __('Welkom ') }} {{ Auth::user()->name }}
                </div>
            </div>
            <div class="m-5 flex-column">
                <div>
                    <h1>Select your favourite sports!</h1>
                    <button onclick="getSports()">GO TO SPORTS</button>
                    <div id="output" class="mt-3"></div>
                </div>
                @if (Auth::user()->role == 'praesidium')
                    <div>
                        <h1>Add a sport</h1>
                        <input type="text" id="sportName" placeholder="Sport name">
                        <input type="text" id="sportLocation" placeholder="Sport location">
                        <input type="text" id="eenheid" placeholder="Sport eenheid">
                        <button onclick="addSport()">ADD SPORT</button>
                    </div>
                @endif
            </div>
            <div>
                <h1>
                    <a href="./kalender">bekijk jouw kalender</a>
                </h1>
            </div>
        </div>
    </div>
</div>
@endsection
