@extends('layouts.pdf')
@push('styles')
    <style>
        .photo {
            width: 64px;
            height: 64px;
            background-size: cover;
            border: 1px solid gray;
            background-repeat: no-repeat;
            border-radius: 50%;
        }
    </style>
@endpush

@section('heading')
    Members List
@endsection

@section('body')
    <table class="poppins-regular">
        <thead>
            <tr>
                <th>#</th>
                <th>Photo</th>
                <th>Member ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                {{-- <th>Date of Birth</th> --}}
                <th>Gender</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $member)
                <tr>
                    <td class="text-right">{{ $loop->iteration }}.</td>
                    <td>
                        <div class="photo" style="background-image:url({!! $member->photo !!})" alt="Photo">
                        </div>
                    </td>
                    <td style="text-align:center">{{ $member->id }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->email }}</td>
                    {{-- <td>{{ $member->date_of_birth }}</td> --}}
                    <td>{{ $member->gender }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
