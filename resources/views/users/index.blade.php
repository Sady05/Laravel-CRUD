@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-3xl font-bold mb-6">{{ __('Users') }}</h1>
    <button class="btn btn-primary" onclick="showCreateUserForm()">Create New User</button>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Profile Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTable">
            @foreach($users as $user)
            <tr id="user-{{ $user->id }}">
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" width="50" height="50" />
                    @else
                    N/A
                    @endif
                </td>
                <td>
                    <button onclick="editUser({{ $user->id }})" class="btn btn-warning">Edit</button>
                    <button onclick="deleteUser({{ $user->id }})" class="btn btn-danger">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- User Create/Edit Form Modal -->
<div id="userModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Create User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="profile_image">Profile Image</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>

    function showCreateUserForm() {
        $('#userModalTitle').text('Create User');
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#userModal').modal('show');
    }

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData($('#userForm')[0]);
        console.log(FormData);
        
        $.ajax({
            type: 'POST',
            url: "{{ isset($user) ? route('users.update') : route('users.store') }}",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response.success);
                // location.reload();
            },
            error: function(response) {
                alert('An error occurred');
            }
        });
    });

    function editUser(id) {
        // $(document).ready(function() {
        console.log('in edit');
        
        $.get(`/users/${id}/edit`, function(data) {
            console.log('data', data.user.id);
            
            $('#userModalTitle').text('Edit User');
            $('#userId').val(data.user.id);
            $('#name').val(data.user.name);
            $('#email').val(data.user.email);
            $('#userModal').modal('show');
        });
    // })
    }


    function deleteUser(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'DELETE',
                url: `/users/${id}`,
                success: function(response) {
                    alert(response.success);
                    location.reload();
                },
                error: function(response) {
                    alert('An error occurred');
                }
            });
        }
    }

</script>

@endsection

