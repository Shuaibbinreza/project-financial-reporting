/**
 * Project Summary Report - Pagination and Export Functions
 */

const rowsPerPage = 10;
let currentPage = 1;
let allRows = [];

function initDataTable() {
    // Note: DataTables cannot be used with merged cells (rowspan)
    // Using custom export buttons instead
    
    // Add custom export buttons
    const tableId = 'projectSummaryTable';
    const container = document.querySelector(`#${tableId}`).parentNode;
    
    // Remove existing buttons if any
    const existingButtons = container.previousElementSibling;
    if (existingButtons && existingButtons.classList.contains('dt-buttons')) {
        existingButtons.remove();
    }
    
    // Add export buttons
    const buttonsDiv = document.createElement('div');
    buttonsDiv.className = 'dt-buttons mb-2';
    buttonsDiv.innerHTML = `
        <button class="btn btn-secondary dt-button" onclick="copyTable()"><i class="bi bi-clipboard"></i> Copy</button>
        <button class="btn btn-success dt-button" onclick="excelTable()"><i class="bi bi-file-earmark-excel"></i> Excel</button>
        <button class="btn btn-info dt-button" onclick="csvTable()"><i class="bi bi-file-earmark-text"></i> CSV</button>
        <button class="btn btn-danger dt-button" onclick="pdfTable()"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="btn btn-primary dt-button" onclick="printTable()"><i class="bi bi-printer"></i> Print</button>
    `;
    
    // Insert buttons before table container
    container.parentNode.insertBefore(buttonsDiv, container);
    
    // Initialize pagination
    initPagination();
}

// Copy function
function copyTable() {
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
                } else if (rowIdx % 2 === 0) {
                    data.cell.styles.fillColor = [255, 255, 255];
                } else {
                    data.cell.styles.fillColor = [248, 248, 248];
                }
            }
        },
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
            </style>
        </head>
        <body>
            <h1>Project Summary Report</h1>
            <div class="info">
                <div><strong>Period:</strong> ${quarterLabel}</div>
                <div><strong>Projects:</strong> ${projectCount}</div>
                <div><strong>Generated:</strong> ${today}</div>
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

function numberFormat(num) {
    return new Intl.NumberFormat('en-US').format(num);
}
