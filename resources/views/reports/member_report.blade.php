<!DOCTYPE html>
<html>

<head>
    <title>Member Report</title>
    <style>
        * {
            font-family: 'Open Sans', sans-serif;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.5;
        }

        table {
            --color: #d0d0f5;
            text-align: left;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid;
        }

        thead {
            border-block-end: 2px solid;
            background: whitesmoke;
        }

        tfoot {
            border-block: 2px solid;
            background: whitesmoke;
        }

        thead,
        tfoot {
            background: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        th,
        td {
            border: 1px solid lightgrey;
            padding: 0.25rem 0.75rem;
        }

        .photo {
            width: 64px;
            height: 64px;
            background-size: cover;
            border: 1px solid gray;
            background-repeat: no-repeat;
            border-radius: 1rem;
        }
    </style>
</head>

<body>
    <h1>Member Report</h1>
    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Member ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Gender</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $member)
                <tr>
                    <td>
                        <div class="photo" style="background-image:url({!! $member->photo !!})" alt="Photo">
                        </div>
                    </td>
                    <td style="text-align:center">{{ $member->id }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->date_of_birth }}</td>
                    <td>{{ $member->gender }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
