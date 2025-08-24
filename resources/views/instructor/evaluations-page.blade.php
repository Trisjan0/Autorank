@extends('layouts.view-all-layout')

@section('title', 'Evaluations | Autorank')

@section('content')
<div class="header">
    <h1>KRA IA : Teaching Effectiveness</h1>
</div>
<div class="performance-metric-container">
    <table>
        <tbody>
            <tr>
                <th>ID Number</th>
                <th>Title</th>
                <th>Category</th>
                <th>Score</th>
                <th>Date</th>
                <th>
                    <div class="search-bar-container">
                        <form action="">
                            <input type="text" placeholder="Search..">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
            @foreach ($evaluations as $evaluation)
            <tr>
                <td>{{ $evaluation->id  }}</td>
                <td>{{ $evaluation->title  }}</td>
                <td>{{ $evaluation->category  }}</td>
                <td>{{ $evaluation->score ?? 'N/A'  }}</td>
                <td>{{ optional($evaluation->created_at)->format('F j, Y') ?? 'N/A'  }}</td>
                <td>
                    <div>
                        <a href="{{ $evaluation->link }}" target="blank"><button>View</button></a>
                        <button>Edit</button>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="6" style="text-align: center;">No evaluations found.</td>
            </tr>
            @endforeach
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <button>Upload Evaluation</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>

<div class="header">
    <h1>KRA IB : Curriculum & Instruction</h1>
</div>
<div class="performance-metric-container">
    <table>
        <tbody>
            <tr>
                <th>ID Number</th>
                <th>Title</th>
                <th>Type</th>
                <th>Category</th>
                <th>Date</th>
                <th>
                    <div class="search-bar-container">
                        <form action="">
                            <input type="text" placeholder="Search..">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
            @foreach ($materials as $material)
            <tr>
                <td>{{ $material->id  }}</td>
                <td>{{ $material->title  }}</td>
                <td>{{ $material->category  }}</td>
                <td>{{ $material->score ?? 'N/A'  }}</td>
                <td>{{ optional($material->created_at)->format('F j, Y') ?? 'N/A'  }}</td>
                <td>
                    <div>
                        <a href="{{ $material->link }}" target="blank"><button>View</button></a>
                        <button>Edit</button>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="6" style="text-align: center;">No instructional materials uploaded.</td>
            </tr>
            @endforeach
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <button>Upload Material</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>
@endsection