<div class="container">
    <h2>Category-wise Financial Summary as of {{ $cutoffDate->format('d M, Y') }}</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Cost Category</th>
                <th>Expense as of {{ $cutoffDate->format('d M, Y') }}</th>
                <th>Budget as of {{ $cutoffDate->format('d M, Y') }}</th>
                <th>Budgeted Expenses (%)</th>
                <th>Total Project Budget</th>
                <th>Project Implementation (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
            <tr>
                <td>{{ $row['category'] }}</td>
                <td>{{ number_format($row['expenses']) }}</td>
                <td>{{ number_format($row['budget']) }}</td>
                <td>{{ $row['budgeted_percentage'] }}</td>
                <td>{{ number_format($row['total_project_budget']) }}</td>
                <td>{{ $row['project_implementation'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>