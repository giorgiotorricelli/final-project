@extends('admin.layouts.master')

@section('content')
    <div class="container" onclick="listSort(event)">
        <div id="listView">
            <table class="actors-table" style="width:100%">
                <thead>
                    <tr>
                    <th style="width:20%">Id  
                        <button id="sortByIdBtn" class="list-sort-btn bi bi-arrow-down-circle"></button>
                    </th>
                    <th style="width:30%">Name  
                        <button id="sortByNameBtn" class="list-sort-btn bi bi-circle"></button>
                    </th>
                    <th style="width:20%">Popularity  
                        <button id="sortByPopularityBtn" class="list-sort-btn bi bi-circle"></button>
                    </th>
                    <th style="width:20%">Birthday  
                        <button id="sortByBirthdayBtn" class="list-sort-btn bi bi-circle"></button>
                    </th>
                    <th class="table-manage" style="width:10%">Manage</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <!-- Popolato dinamicamente via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteModalLabel">Deleting</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to delete this director, are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form id="modalDeletionForm" action="" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-info" type="submit">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let sortedList = @json($directors);
        let tbody = document.getElementById('tbody'); 
        let tempContainer = ''; 

        function sortedBy(btn) {
            if (btn.id === 'sortByIdBtn') {
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => b.id - a.id);
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => a.id - b.id);
                }
            }

            if (btn.id === 'sortByNameBtn') {
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => b.name.localeCompare(a.name));
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => a.name.localeCompare(b.name));
                } 
            }

            if (btn.id === 'sortByBirthdayBtn') {
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => new Date(b.birthday) - new Date(a.birthday));
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => new Date(a.birthday) - new Date(b.birthday));
                } 
            }

            if (btn.id === 'sortByPopularityBtn') {
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => (b.popularity || 0) - (a.popularity || 0));
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => (a.popularity || 0) - (b.popularity || 0));
                } 
            }

            renderDirectors();
        }

        function setDestroyDestination(event) {
            const destination = event.target.getAttribute('data-director-id');
            const deletionForm = document.getElementById('modalDeletionForm');
            deletionForm.action = `/admin/directors/${destination}`;
        }

        function renderDirectors() {
            tbody.innerHTML = ``; 

            sortedList.forEach(director => {
                const pop = Number(director.popularity || 0);
                const popFixed = pop.toFixed(2);

                tempContainer += `<tr onclick="window.location='/admin/directors/${director.id}'">
                            <td>${director.id}</td>
                            <td>${director.name}</td>
                            <td>${popFixed}</td>
                            <td>${director.birthday ?? ''}</td>
                            <td class="table-manage" onclick="event.stopPropagation()">
                                <div>
                                    <a href="/admin/directors/${director.id}/edit" class="btn btn-success"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-primary bi bi-trash" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-director-id="${director.id}" onclick="setDestroyDestination(event)">
                                    </button>
                                </div>
                            </td>
                        </tr>`;
            });

            tbody.innerHTML = tempContainer; 
            tempContainer = ''; 
        }

        renderDirectors();

        function listSort(event) {
            const target = event.target;
            const tagname = target.tagName;

            if (!(tagname === 'BUTTON' && (target.classList.contains('bi-arrow-down-circle')
             || target.classList.contains('bi-arrow-up-circle') 
             || target.classList.contains('bi-circle')))) return;
            
            if (target.classList.contains('bi-arrow-down-circle') || target.classList.contains('bi-arrow-up-circle')) {
                target.classList.toggle('bi-arrow-down-circle');
                target.classList.toggle('bi-arrow-up-circle');
                sortedBy(target);
                return;
            } else {
                target.classList.remove('bi-circle'); 
            }

            let allBtns = document.querySelectorAll('.list-sort-btn');
            
            allBtns.forEach(btn => {
                if (btn.classList.length > 2) { 
                    btn.classList.remove('bi-arrow-down-circle','bi-arrow-up-circle');
                    btn.classList.add('bi-circle');
                } 
            });
            
            target.classList.add('bi-arrow-down-circle'); 
            sortedBy(target);
        }
    </script>
@endsection