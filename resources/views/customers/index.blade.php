@extends('layouts.admin')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h3>Customers</h3>

        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            Add Customer
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Description</th>
                <th width="180">Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach($customers as $customer)

            <tr>

                <td>{{ $customer->id }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->mobile }}</td>
                <td>{{ $customer->description }}</td>

                <td>

                    <a href="{{ route('customers.edit',$customer->id) }}"
                       class="btn btn-sm btn-warning">
                        Edit
                    </a>

                    <form action="{{ route('customers.destroy',$customer->id) }}"
                          method="POST"
                          style="display:inline-block">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-danger">
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    {{ $customers->links() }}

</div>

@endsection