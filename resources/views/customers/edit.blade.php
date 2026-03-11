@extends('layouts.admin')

@section('content')

<div class="container">

    <h3>Edit Customer</h3>

    <form action="{{ route('customers.update',$customer->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ $customer->name }}"
                   required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ $customer->email }}">
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text"
                   name="mobile"
                   class="form-control"
                   value="{{ $customer->mobile }}"
                   required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description"
                      class="form-control">{{ $customer->description }}</textarea>
        </div>

        <button class="btn btn-success">
            Update
        </button>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection