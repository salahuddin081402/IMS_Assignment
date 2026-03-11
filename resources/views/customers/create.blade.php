@extends('layouts.admin')

@section('content')

<div class="container">

    <h3>Add Customer</h3>

    <form action="{{ route('customers.store') }}" method="POST">

        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text"
                   name="mobile"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description"
                      class="form-control"></textarea>
        </div>

        <button class="btn btn-success">
            Save
        </button>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection