@extends('layouts.report')

@section('title', 'Project Summary')
@section('breadcrumb', 'Project Summary')
@section('icon', 'bi bi-briefcase')
@section('description', 'View project-wise financial summary with budget vs expense analysis')

@section('quick-links')
<div class="mb-3">
    <div class="btn-group" role="group">
        <a href="{{ route('reports.financial') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bar-chart me-1"></i>Financial Report
        </a>
        <a href="{{ route('reports.cutoff') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-scissors me-1"></i>Cutoff Report
        </a>
        <a href="{{ route('reports.category-summary') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pie-chart me-1"></i>Category Summary
        </a>
        <a href="{{ route('reports.project-summary') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-briefcase me-1"></i>Project Summary
        </a>
    </div>
</div>
@endsection

@section('filters')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="division_id" class="form-label">Division</label>
                <select name="division_id" id="division_id" class="form-select">
                    <option value="">All Divisions</option>
                    @foreach(\App\Models\Division::all() as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="district_id" class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-select" disabled>
                    <option value="">All Districts</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="quarter" class="form-label">Quarter</label>
                <select name="quarter" id="quarter" class="form-select">
                    <option value="all">All Quarters</option>
                    @foreach($quarters as $quarter)
                        <option value="{{ $quarter->code }}" {{ $loop->last ? 'selected' : '' }}>{{ $quarter->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button type="button" class="btn btn-primary" id="filterBtn">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clearBtn">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('report-content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>Project Summary - <span id="quarterLabel">Q4</span>
        </h5>
        <span class="badge bg-secondary" id="projectCount">0 Projects</span>
    </div>
    <div class="card-body p-0">
        <div id="reportTableContainer">
            <!-- Table will be loaded here via AJAX -->
        </div>
        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading data...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>
<script src="{{ asset('js/project-summary.js') }}"></script>
<script>
    // Get districts by division (cascading dropdown)
    document.getElementById('division_id').addEventListener('change', function() {
        const divisionId = this.value;
        const districtSelect = document.getElementById('district_id');

        // Clear existing options
        districtSelect.innerHTML = '<option value="">All Districts</option>';

        if (divisionId) {
            districtSelect.disabled = true;
            fetch(`/reports/get-districts?division_id=${divisionId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching districts:', error);
                    districtSelect.disabled = false;
                });
        } else {
            districtSelect.disabled = true;
        }
    });

    // Quarter descriptions
    const quarterDescriptions = {
        @foreach($quarters as $quarter)
        '{{ $quarter->code }}': 'Showing data for {{ $quarter->name }}',
        @endforeach
        'All': 'Showing total summation of all quarters'
    };

    // Fetch report data via AJAX
    function fetchReport() {
        const divisionId = document.getElementById('division_id').value;
        const districtId = document.getElementById('district_id').value;
        const quarter = document.getElementById('quarter').value;

        // Show loading spinner
        document.getElementById('reportTableContainer').innerHTML = '';
        document.getElementById('loadingSpinner').style.display = 'block';

        // Build query string
        const params = new URLSearchParams();
        if (divisionId) params.append('division_id', divisionId);
        if (districtId) params.append('district_id', districtId);
        if (quarter) params.append('quarter', quarter);

        fetch(`/reports/project-summary/ajax?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingSpinner').style.display = 'none';
                renderTable(data);
                updateHeader(data);
            })
            .catch(error => {
                console.error('Error fetching report:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('reportTableContainer').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                        <p class="text-danger mt-3">Error loading data. Please try again.</p>
                    </div>
                `;
            });
    }

    function updateHeader(data) {
        const quarterLabel = data.selectedQuarterName || (data.showAllQuarters ? 'All Quarters' : data.selectedQuarter);
        document.getElementById('quarterLabel').textContent = quarterLabel;
        document.getElementById('projectCount').textContent = `${data.report.length} Projects`;
    }

    function renderTable(data) {
        const container = document.getElementById('reportTableContainer');

        if (data.report.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No data available for the selected filters</p>
                </div>
            `;
            return;
        }

        let thead = '';
        let tbody = '';

        // Table with merged cells (rowspan for project column)
        thead = `
            <thead class="table-dark">
                <tr>
                    <th class="align-middle">Project</th>
                    <th class="align-middle">Quarter</th>
                    <th class="align-middle text-end">Expense</th>
                    <th class="align-middle text-end">Budget</th>
                    <th class="align-middle text-center">Implementation on<br>Quarterly Budget (%)</th>
                    <th class="align-middle text-center">Implementation on<br>Total Budget (%)</th>
                </tr>
            </thead>
        `;

        // Build rows with merged cells (rowspan for project column) - Total at bottom
        tbody = data.report.map(project => {
            const projectPct = parseFloat(project.budgeted_percentage);
            const implPct = parseFloat(project.project_implementation);
            const totalBudget = project.total_budget_all || 1;
            
            const rows = [];
            const rowCount = project.quarters.length + 1;
            
            // Add quarter rows first
            project.quarters.forEach((q, index) => {
                const qPct = parseFloat(q.budgeted_percentage);
                const implOnTotal = ((q.expenses / totalBudget) * 100).toFixed(2);
                
                // First quarter row has project name with rowspan
                if (index === 0) {
                    rows.push(`
                        <tr class="table-secondary">
                            <td class="align-middle" rowspan="${project.quarters.length}">${project.project}</td>
                            <td>${q.quarter_name}</td>
                            <td class="text-end">${numberFormat(q.expenses)}</td>
                            <td class="text-end">${numberFormat(q.budget)}</td>
                            <td class="text-center">
                                <span class="badge ${qPct >= 100 ? 'bg-danger' : (qPct >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${q.budgeted_percentage}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge ${implOnTotal >= 100 ? 'bg-danger' : (implOnTotal >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${implOnTotal}%
                                </span>
                            </td>
                        </tr>
                    `);
                } else {
                    rows.push(`
                        <tr class="table-secondary">
                            <td>${q.quarter_name}</td>
                            <td class="text-end">${numberFormat(q.expenses)}</td>
                            <td class="text-end">${numberFormat(q.budget)}</td>
                            <td class="text-center">
                                <span class="badge ${qPct >= 100 ? 'bg-danger' : (qPct >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${q.budgeted_percentage}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge ${implOnTotal >= 100 ? 'bg-danger' : (implOnTotal >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${implOnTotal}%
                                </span>
                            </td>
                        </tr>
                    `);
                }
            });
            
            // Add project total row at the bottom (no rowspan)
            rows.push(`
                <tr class="table-primary fw-bold">
                    <td>${project.project}</td>
                    <td>Total</td>
                    <td class="text-end">${numberFormat(project.total_expenses)}</td>
                    <td class="text-end">${numberFormat(project.total_budget)}</td>
                    <td class="text-center">
                        <span class="badge ${projectPct >= 100 ? 'bg-danger' : (projectPct >= 75 ? 'bg-warning' : 'bg-success')}">
                            ${project.budgeted_percentage}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge ${implPct >= 100 ? 'bg-danger' : (implPct >= 75 ? 'bg-warning' : 'bg-success')}">
                            ${project.project_implementation}
                        </span>
                    </td>
                </tr>
            `);

            return rows.join('');
        }).join('');

        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-primary table-striped mb-0 report-table" id="projectSummaryTable">
                    ${thead}
                    <tbody>${tbody}</tbody>
                </table>
            </div>
        `;
        
        // Initialize DataTables after table is rendered
        initDataTable();
    }

    // All pagination and export functions moved to public/js/project-summary.js
        const table = document.getElementById('projectSummaryTable');
        let text = '';
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            let rowText = [];
            cells.forEach(cell => rowText.push(cell.textContent.trim()));
            text += rowText.join('\t') + '\n';
        });
        navigator.clipboard.writeText(text).then(() => {
            alert('Table copied to clipboard!');
        });
    }
    
    // Excel function
    function excelTable() {
        const table = document.getElementById('projectSummaryTable');
        const html = table.outerHTML;
        const blob = new Blob(['<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head><body>' + html + '</body></html>'], {type: 'application/vnd.ms-excel'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'project_summary.xls';
        a.click();
        URL.revokeObjectURL(url);
    }
    
    // CSV function
    function csvTable() {
        const table = document.getElementById('projectSummaryTable');
        let csv = '';
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            let rowData = [];
            cells.forEach(cell => rowData.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"'));
            csv += rowData.join(',') + '\r\n';
        });
        const blob = new Blob([csv], {type: 'text/csv'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'project_summary.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
    
    // PDF function - downloads actual PDF file using HTML table conversion
    function pdfTable() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Get table element
        const table = document.getElementById('projectSummaryTable');
        const quarterLabel = document.getElementById('quarterLabel').textContent;
        
        // Add title
        doc.setFontSize(16);
        doc.text('Project Summary Report', 14, 15);
        
        // Add period info
        doc.setFontSize(10);
        doc.text(`Period: ${quarterLabel}`, 14, 22);
        doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 27);
        
        // Clone the table and prepare it for PDF
        const tableClone = table.cloneNode(true);
        
        // Use html() method which preserves rowspan
        doc.autoTable({
            html: tableClone,
            startY: 32,
            styles: { 
                fontSize: 8,
                cellPadding: 2,
                overflow: 'linebreak'
            },
            headStyles: { 
                fillColor: [26, 26, 46],
                textColor: [255, 255, 255],
                fontStyle: 'bold'
            },
            // Keep row colors
            didParseCell: function(data) {
                if (data.section === 'body') {
                    // Check row index - alternate coloring
                    const rowIdx = data.row.index;
                    // Total rows should have different color
                    const cellText = data.cell.raw.textContent || '';
                    if (cellText.includes('Total')) {
                        data.cell.styles.fillColor = [227, 242, 253];
                        data.cell.styles.fontStyle = 'bold';
                    } else {
                        data.cell.styles.fillColor = [233, 236, 239];
                    }
                }
            },
            useCss: false,
            useCellStyles: true
        });
        
        // Download PDF
        doc.save('project_summary.pdf');
    }
    
    // Print function - opens formatted print preview
    function printTable() {
        const table = document.getElementById('projectSummaryTable');
        const quarterLabel = document.getElementById('quarterLabel').textContent;
        const projectCount = document.getElementById('projectCount').textContent;
        
        const today = new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Project Summary - ${quarterLabel}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { font-size: 18px; margin-bottom: 5px; }
                    .info { font-size: 12px; color: #666; margin-bottom: 15px; }
                    table { width: 100%; border-collapse: collapse; font-size: 11px; }
                    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                    th { background-color: #333 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .text-end { text-align: right; }
                    .text-center { text-align: center; }
                    .table-primary { background-color: #e3f2fd !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .table-secondary { background-color: #f5f5f5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .fw-bold { font-weight: bold; }
                    .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
                    .bg-success { background-color: #28a745 !important; color: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .bg-warning { background-color: #ffc107 !important; color: black; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .bg-danger { background-color: #dc3545 !important; color: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    @media print { body { padding: 0; } }
                </style>
            </head>
            <body>
                <h1>Project Summary Report</h1>
                <div class="info">
                    <strong>Period:</strong> ${quarterLabel} | 
                    <strong>Generated:</strong> ${today} | 
                    <strong>${projectCount}</strong>
                </div>
                ${table.outerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }
    
    // Pagination
    const rowsPerPage = 10;
    let currentPage = 1;
    let allRows = [];
    
    function initPagination() {
        const table = document.getElementById('projectSummaryTable');
        if (!table) return;
        
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        
        // Store all rows
        allRows = Array.from(tbody.querySelectorAll('tr'));
        
        // Show first page
        showPage(1);
    }
    
    function showPage(page) {
        currentPage = page;
        const table = document.getElementById('projectSummaryTable');
        if (!table) return;
        
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        
        // Hide all rows first
        allRows.forEach(row => row.style.display = 'none');
        
        // Calculate start and end indices
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        // Show rows for current page
        for (let i = start; i < end && i < allRows.length; i++) {
            allRows[i].style.display = '';
        }
        
        // Update pagination controls
        updatePaginationControls();
    }
    
    function updatePaginationControls() {
        const table = document.getElementById('projectSummaryTable');
        if (!table) return;
        
        const container = table.parentNode;
        
        // Remove existing pagination
        const existingPagination = container.nextElementSibling;
        if (existingPagination && existingPagination.classList.contains('pagination-wrapper')) {
            existingPagination.remove();
        }
        
        const totalPages = Math.ceil(allRows.length / rowsPerPage);
        
        if (totalPages <= 1) return;
        
        // Create pagination controls
        const paginationDiv = document.createElement('div');
        paginationDiv.className = 'pagination-wrapper mt-3 d-flex justify-content-between align-items-center';
        
        const showingStart = (currentPage - 1) * rowsPerPage + 1;
        const showingEnd = Math.min(currentPage * rowsPerPage, allRows.length);
        
        paginationDiv.innerHTML = `
            <div class="text-muted small">Showing ${showingStart} to ${showingEnd} of ${allRows.length} entries</div>
            <ul class="pagination mb-0">
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); showPage(1)">First</a>
                </li>
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); showPage(${currentPage - 1})">Previous</a>
                </li>
                ${generatePageNumbers(totalPages)}
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); showPage(${currentPage + 1})">Next</a>
                </li>
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); showPage(${totalPages})">Last</a>
                </li>
            </ul>
        `;
        
        container.parentNode.insertBefore(paginationDiv, container.nextSibling);
    }
    
    function generatePageNumbers(totalPages) {
        let html = '';
        const maxVisible = 5;
        let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);
        
        if (end - start < maxVisible - 1) {
            start = Math.max(1, end - maxVisible + 1);
        }
        
        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); showPage(${i})">${i}</a></li>`;
        }
        return html;
    }
    
    // Event listeners
    document.getElementById('filterBtn').addEventListener('click', fetchReport);

    document.getElementById('clearBtn').addEventListener('click', function() {
        document.getElementById('division_id').value = '';
        document.getElementById('district_id').innerHTML = '<option value="">All Districts</option>';
        document.getElementById('district_id').disabled = true;
        document.getElementById('quarter').value = 'all';
        fetchReport();
    });

    // Load data on page load
    document.addEventListener('DOMContentLoaded', fetchReport);
</script>
@endsection
