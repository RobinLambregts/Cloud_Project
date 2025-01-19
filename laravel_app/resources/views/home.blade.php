@extends('layouts.app')

<!-- Add Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

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
                    sportDiv.classList.add('border', 'border-red-500', 'p-4', 'm-2', 'rounded');

                    const sportName = document.createElement('h2');
                    sportName.classList.add('text-xl', 'font-bold');
                    sportName.innerText = sport[0];
                    sportDiv.appendChild(sportName);

                    const sportLocation = document.createElement('p');
                    sportLocation.innerText = "Location: " + sport[1];
                    sportDiv.appendChild(sportLocation);
                    outputDiv.appendChild(sportDiv);

                    const deleteButton = document.createElement('button');
                    if (currentUser.role !== 'praesidium') {
                        deleteButton.style.display = 'none';
                    }
                    deleteButton.classList.add('bg-red-500', 'text-white', 'rounded', 'p-2', 'mt-2');
                    deleteButton.innerText = 'Delete';
                    deleteButton.onclick = function() {
                        deleteSport(sport, outputDiv, sportDiv);
                    };
                    sportDiv.appendChild(deleteButton);

                    const upvoteButton = document.createElement('button');
                    upvoteButton.classList.add('bg-blue-500', 'text-white', 'rounded', 'p-2', 'mt-2', 'ml-2');
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
<div class="container mx-auto p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-white shadow-md rounded-lg overflow-hidden">
                <div class="card-header bg-blue-500 text-white p-4">{{ __('Dashboard') }}</div>

                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Welkom ') }} {{ Auth::user()->name }}
                </div>
            </div>
            <div class="m-5 flex flex-col">
                <div>
                    <h1 class="text-2xl font-bold mb-4">Select your favourite sports!</h1>
                    <button onclick="getSports()" class="bg-blue-500 text-white rounded p-2">GO TO SPORTS</button>
                    <div id="output" class="mt-3"></div>
                </div>
                @if (Auth::user()->role == 'praesidium')
                    <div class="mt-5">
                        <h1 class="text-2xl font-bold mb-4">Add a sport</h1>
                        <input type="text" id="sportName" placeholder="Sport name" class="border rounded p-2 mb-2">
                        <input type="text" id="sportLocation" placeholder="Sport location" class="border rounded p-2 mb-2">
                        <input type="text" id="eenheid" placeholder="Sport eenheid" class="border rounded p-2 mb-2">
                        <button onclick="addSport()" class="bg-blue-500 text-white rounded p-2">ADD SPORT</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection