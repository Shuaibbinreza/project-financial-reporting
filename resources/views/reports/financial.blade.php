<div class="container">
    <h2>Quarterly Financial Report</h2>

    <!-- Filters Form -->
    <form method="GET" action="{{ route('reports.financial') }}" class="mb-4">
        <div class="row g-2">
            <div class="col">
                <select name="project_id" class="form-control">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <select name="division_ids[]" class="form-control" multiple>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <select name="quarter" class="form-control">
                    <option value="">All Quarters</option>
                    <option value="Q1">Q1</option>
                    <option value="Q2">Q2</option>
                    <option value="Q3">Q3</option>
                    <option value="Q4">Q4</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- Report Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Project</th>
                <th>Category</th>
                <th>Economic Code</th>
                <th>Quarter</th>
                <th>Budget</th>
                <th>Expenses</th>
                <th>% Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
                @foreach($row['quarters'] as $quarter => $data)
                <tr>
                    <td>{{ $row['project'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['economic_code'] }}</td>
                    <td>{{ $quarter }}</td>
                    <td>{{ number_format($data['budget']) }}</td>
                    <td>{{ number_format($data['expenses']) }}</td>
                    <td>{{ $data['percent_spent'] }}</td>
                </tr>
                @endforeach
                <!-- Yearly Total -->
                <tr class="fw-bold bg-light">
                    <td colspan="4">Yearly Total</td>
                    <td>{{ number_format($row['yearly_total']['budget']) }}</td>
                    <td>{{ number_format($row['yearly_total']['expenses']) }}</td>
                    <td>{{ $row['yearly_total']['percent_spent'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>