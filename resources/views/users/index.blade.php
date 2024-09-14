@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-3xl font-bold mb-6">{{ __('Users') }}</h1>
    <button class="btn btn-primary mb-3" onclick="showCreateUserForm()">Create New User</button>

    <table id="userTable" class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Profile Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded via AJAX -->
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
            // Show the create user form
            function showCreateUserForm() {
            $('#userModalTitle').text('Create User');
            $('#userForm')[0].reset();
            $('#userId').val('');
            $('#userModal').modal('show');
        }
    $(document).ready(function() {
        // Initialize DataTable with AJAX
        var table = $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('users.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { 
                    data: 'profile_image', 
                    name: 'profile_image',
                    render: function(data) {
                        console.log('data', data);
                        
                        return data
                    }
                },
                {
                    data: 'actions', 
                    name: 'actions', 
                    orderable: false, 
                    searchable: false
                }
            ]
        });



        // Handle create/edit form submission
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var url = $('#userId').val() ? `/users/${$('#userId').val()}` : '/users';
            var method = $('#userId').val() ? 'PUT' : 'POST';
            
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: method,
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#userModal').modal('hide');
                    table.ajax.reload(null, false);
                    alert(response.success);
                },
                error: function() {
                    alert('An error occurred');
                }
            });
        });

        // Edit user
        window.editUser = function(id) {
            $.get(`/users/${id}/edit`, function(data) {
                $('#userModalTitle').text('Edit User');
                $('#userId').val(data.user.id);
                $('#name').val(data.user.name);
                $('#email').val(data.user.email);
                $('#userModal').modal('show');
            });
        };

        // Delete user
        window.deleteUser = function(id) {
            if (confirm('Are you sure?')) {
                $.ajax({
                    type: 'DELETE',
                    url: `/users/${id}`,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        table.ajax.reload(null, false);
                        alert(response.success);
                    },
                    error: function() {
                        alert('An error occurred');
                    }
                });
            }
        };
    });
</script>
@endsection
