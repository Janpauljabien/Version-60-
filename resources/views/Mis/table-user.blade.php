<center>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Role</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->role}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>
                    @include('Mis.delete-user')
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div>
        {{$users->onEachSide(5)->links() }}

    </div>

</center>