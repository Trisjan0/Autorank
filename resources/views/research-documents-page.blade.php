@extends('layouts.view-all-layout')

@section('title', 'Research Documents')

@section('content')
<div class="header">
    <h1>Research Documents</h1>
</div>
<div class="performance-metric-container">
    <table>
        <tbody>
            <tr>
                <th>ID Number</th>
                <th>Title</th>
                <th>Publication Date</th>
                <th>Status</th>
                <th>
                    <div class="search-bar-container">
                        <form action="">
                            <input type="text" placeholder="Search..">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
            <tr>
                <td>72</td>
                <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit</td>
                <td>N/A</td>
                <td>Ongoing</td>
                <td><button>Upload</button></td>
            </tr>
            <tr>
                <td>71</td>
                <td>Sed do eiusmod tempor incididunt ut labore</td>
                <td>July 12, 2024</td>
                <td>Done</td>
                <td><button>View</button></td>
            </tr>
            <tr>
                <td>70</td>
                <td>Eiusmod do tempor incididunt ut labore</td>
                <td>June 2, 2024</td>
                <td>Done</td>
                <td><button>View</button></td>
            </tr>
            <tr>
                <td>69</td>
                <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit</td>
                <td>February 16, 2024</td>
                <td>Done</td>
                <td><button>View</button></td>
            </tr>
            <tr>
                <td>68</td>
                <td>Sed do eiusmod tempor incididunt ut labore</td>
                <td>March 17, 2024</td>
                <td>Done</td>
                <td><button>View</button></td>
            </tr>
            <tr>
                <td>67</td>
                <td>Eiusmod do tempor incididunt ut labore</td>
                <td>April 2, 2024</td>
                <td>Done</td>
                <td><button>View</button></td>
            </tr>
            <tr>
                <td>66</td>
                <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit</td>
                <td>April 15, 2024</td>
                <td>Done</td>
                <td><button>View</button></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>
@endsection