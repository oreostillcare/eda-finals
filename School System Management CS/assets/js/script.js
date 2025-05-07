/**
 * CS Student Management System - Main JavaScript
 * Modern and functional UI with Bootstrap 5
 */

// Theme toggling functionality
function setupThemeToggle() {
    const toggleSwitches = document.querySelectorAll('.theme-switch-input');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply theme on initial load
    document.body.setAttribute('data-theme', currentTheme);
    
    // Set toggle switches to match current theme
    if (currentTheme === 'dark') {
        toggleSwitches.forEach(toggle => {
            toggle.checked = true;
        });
    }
    
    // Add event listeners to all toggle switches
    toggleSwitches.forEach(toggle => {
        toggle.addEventListener('change', function(e) {
            const theme = this.checked ? 'dark' : 'light';
            
            // Update document theme
            document.body.setAttribute('data-theme', theme);
            
            // Store preference in localStorage
            localStorage.setItem('theme', theme);
            
            // Synchronize all toggle switches
            toggleSwitches.forEach(otherToggle => {
                if (otherToggle !== this) {
                    otherToggle.checked = this.checked;
                }
            });
            
            // Fix button colors in dark mode
            updateButtonColors(theme);
        });
    });
}

// Update button colors based on theme
function updateButtonColors(theme) {
    const buttons = document.querySelectorAll('.btn');
    
    if (theme === 'dark') {
        buttons.forEach(btn => {
            // Fix text-dark buttons in dark mode
            if (btn.classList.contains('btn-link') && btn.classList.contains('text-dark')) {
                btn.classList.remove('text-dark');
                btn.classList.add('text-white');
                btn.setAttribute('data-original-class', 'text-dark'); // Store original class
            }
        });
    } else {
        // Restore original button classes
        buttons.forEach(btn => {
            if (btn.hasAttribute('data-original-class')) {
                const originalClass = btn.getAttribute('data-original-class');
                btn.classList.remove('text-white');
                btn.classList.add(originalClass);
            }
        });
    }
}

// Fixed print functionality - properly displaying content for both individual students and Print All
function setupSmartPrint() {
    // Get all individual student print buttons
    const printButtons = document.querySelectorAll('.print-btn');
    
    // For each print button, set up direct printing functionality
    printButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-print-target');
            
            if (targetId) {
                // Get the target element to print
                const printContent = document.getElementById(targetId);
                
                if (printContent) {
                    // Create a print-specific div
                    const printDiv = document.createElement('div');
                    printDiv.id = 'temp-print-div';
                    printDiv.style.display = 'none';
                    document.body.appendChild(printDiv);
                    
                    // Get student image if available
                    let imageHtml = '';
                    const studentImage = printContent.querySelector('.student-image') || 
                                        printContent.querySelector('img');
                    
                    if (studentImage) {
                        if (studentImage.tagName === 'IMG') {
                            imageHtml = `<img src="${studentImage.src}" class="student-photo" alt="Student Photo">`;
                        } else if (studentImage.querySelector('i.bx-user')) {
                            imageHtml = `<div class="default-avatar"><i class='bx bx-user'></i></div>`;
                        }
                    }
                    
                    // Get student info
                    let studentName = '';
                    let studentId = '';
                    let studentSection = '';
                    let studentYear = '';
                    let studentSex = '';
                    let studentEmail = '';
                    let studentPhone = '';
                    let studentAddress = '';
                    let studentDate = '';
                    
                    // Extract data from different possible formats
                    if (targetId === 'student-detail') {
                        // From modal
                        studentName = document.getElementById('student-name') ? 
                            document.getElementById('student-name').textContent : '';
                        studentId = document.getElementById('student-id') ? 
                            document.getElementById('student-id').textContent : '';
                        studentSection = document.getElementById('student-section') ? 
                            document.getElementById('student-section').textContent : '';
                        studentYear = document.getElementById('student-year') ? 
                            document.getElementById('student-year').textContent : '';
                        studentSex = document.getElementById('student-sex') ? 
                            document.getElementById('student-sex').textContent : '';
                        studentEmail = document.getElementById('student-email') ? 
                            document.getElementById('student-email').textContent : '';
                        studentPhone = document.getElementById('student-phone') ? 
                            document.getElementById('student-phone').textContent : '';
                        studentAddress = document.getElementById('student-address') ? 
                            document.getElementById('student-address').textContent : '';
                        studentDate = document.getElementById('student-date') ? 
                            document.getElementById('student-date').textContent : '';
                    } else {
                        // From hidden print div in table
                        const nameEl = printContent.querySelector('.student-name');
                        const idEl = printContent.querySelector('.student-id');
                        
                        studentName = nameEl ? nameEl.textContent : '';
                        studentId = idEl ? idEl.textContent.replace('Student ID: ', '') : '';
                        
                        // Get details using span.detail-label as markers
                        const detailLabels = printContent.querySelectorAll('.detail-label');
                        
                        detailLabels.forEach(label => {
                            const text = label.textContent;
                            const value = label.parentNode.textContent.replace(text, '').trim();
                            
                            if (text.includes('Section:')) studentSection = value;
                            if (text.includes('Year Level:')) studentYear = value;
                            if (text.includes('Sex:')) studentSex = value;
                            if (text.includes('Email:')) studentEmail = value;
                            if (text.includes('Phone:')) studentPhone = value;
                            if (text.includes('Address:')) studentAddress = value;
                            if (text.includes('Date Added:')) studentDate = value;
                        });
                    }
                    
                    // Add content to the print div
                    printDiv.innerHTML = `
                        <div class="student-print-page">
                            <div class="student-print-header">
                                <h2>Student Information</h2>
                                ${imageHtml}
                                <h3>${studentName}</h3>
                                <p class="student-id-print">${studentId}</p>
                            </div>
                            <div class="student-print-details">
                                <div class="detail-row">
                                    <div class="detail-col">
                                        <p><strong>Section:</strong> ${studentSection}</p>
                                    </div>
                                    <div class="detail-col">
                                        <p><strong>Year Level:</strong> ${studentYear}</p>
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-col">
                                        <p><strong>Sex:</strong> ${studentSex}</p>
                                    </div>
                                    <div class="detail-col">
                                        <p><strong>Email:</strong> ${studentEmail}</p>
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-col">
                                        <p><strong>Phone:</strong> ${studentPhone}</p>
                                    </div>
                                    <div class="detail-col">
                                        <p><strong>Date Added:</strong> ${studentDate}</p>
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-col full-width">
                                        <p><strong>Address:</strong> ${studentAddress}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="student-print-footer">
                                <p>CS Student Management System &copy; ${new Date().getFullYear()}</p>
                                <p>Printed on ${new Date().toLocaleDateString()}</p>
                            </div>
                        </div>
                    `;
                    
                    // Create style for printing
                    const styleTag = document.createElement('style');
                    styleTag.id = 'print-style';
                    styleTag.innerHTML = `
                        @media print {
                            body * {
                                visibility: hidden;
                            }
                            
                            #temp-print-div {
                                visibility: visible !important;
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                                padding: 20px;
                                background: white;
                                z-index: 9999;
                                display: block !important;
                            }
                            
                            #temp-print-div * {
                                visibility: visible;
                            }
                            
                            .student-print-page {
                                font-family: 'Arial', sans-serif;
                                max-width: 800px;
                                margin: 0 auto;
                                padding: 20px;
                            }
                            
                            .student-print-header {
                                text-align: center;
                                margin-bottom: 30px;
                            }
                            
                            .student-print-header h2 {
                                font-size: 24px;
                                margin-bottom: 20px;
                            }
                            
                            .student-print-header h3 {
                                font-size: 20px;
                                margin: 10px 0 5px;
                            }
                            
                            .student-id-print {
                                font-size: 16px;
                                color: #555;
                                margin-bottom: 15px;
                            }
                            
                            .student-photo {
                                width: 150px;
                                height: 150px;
                                border-radius: 50%;
                                object-fit: cover;
                                margin: 0 auto 15px;
                                display: block;
                                border: 1px solid #ddd;
                            }
                            
                            .default-avatar {
                                width: 150px;
                                height: 150px;
                                border-radius: 50%;
                                background-color: #f8f9fa;
                                margin: 0 auto 15px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                border: 1px solid #ddd;
                                font-size: 60px;
                                color: #6c757d;
                            }
                            
                            .student-print-details {
                                margin-bottom: 30px;
                                font-size: 14px;
                            }
                            
                            .detail-row {
                                display: flex;
                                margin-bottom: 15px;
                                border-bottom: 1px solid #eee;
                                padding-bottom: 10px;
                            }
                            
                            .detail-col {
                                width: 50%;
                                padding: 0 10px;
                            }
                            
                            .detail-col.full-width {
                                width: 100%;
                            }
                            
                            .student-print-details p {
                                margin: 0 0 5px;
                            }
                            
                            .student-print-footer {
                                text-align: center;
                                font-size: 12px;
                                color: #666;
                                margin-top: 30px;
                                padding-top: 15px;
                                border-top: 1px solid #eee;
                            }
                            
                            @page {
                                size: portrait;
                                margin: 1.5cm;
                            }
                        }
                    `;
                    
                    // Add the style tag to the head
                    document.head.appendChild(styleTag);
                    
                    // Show print div during printing
                    printDiv.style.display = "block";
                    
                    // Print directly
                    setTimeout(() => {
                        window.print();
                        
                        // Clean up
                        setTimeout(() => {
                            document.head.removeChild(styleTag);
                            document.body.removeChild(printDiv);
                        }, 500);
                    }, 300);
                }
            }
        });
    });
    
    // Fix Print All button functionality using the improved approach
    const printAllBtn = document.getElementById('print-all-btn');
    if (printAllBtn) {
        printAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Create temporary print div for all students
            const printAllDiv = document.createElement('div');
            printAllDiv.id = 'temp-print-all-div';
            printAllDiv.style.display = 'none';
            document.body.appendChild(printAllDiv);
            
            // Get student data from the main table
            const studentsTable = document.getElementById('students-table');
            
            if (studentsTable) {
                // Get all rows except header
                const rows = studentsTable.querySelectorAll('tbody tr');
                
                // Get column headers
                const headers = [];
                // Add Photo header manually as the first column
                headers.push("Photo");
                
                studentsTable.querySelectorAll('thead th').forEach((th, idx) => {
                    // Skip the photo (idx 0) and actions (idx 6) columns in the original headers
                    if (idx !== 0 && idx !== 6) {
                        headers.push(th.textContent.trim());
                    }
                });
                
                // Create table data rows
                let tableRows = '';
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 6) {
                        tableRows += '<tr>';
                        
                        // Add photo cell first (this is cells[0])
                        const photoCell = cells[0];
                        const img = photoCell.querySelector('img');
                        const defaultAvatar = photoCell.querySelector('.avatar');
                        
                        if (img) {
                            // If there's an image, use its src
                            tableRows += `<td class="photo-cell"><img src="${img.src}" class="student-table-photo" alt="Student Photo"></td>`;
                        } else if (defaultAvatar) {
                            // If there's a default avatar div
                            tableRows += `<td class="photo-cell"><div class="default-table-avatar"><i class='bx bx-user'></i></div></td>`;
                        } else {
                            // Fallback
                            tableRows += `<td class="photo-cell"><div class="default-table-avatar"><i class='bx bx-user'></i></div></td>`;
                        }
                        
                        // Add remaining cells (skip photo and actions columns)
                        for (let i = 1; i < 6; i++) {
                            tableRows += `<td>${cells[i].textContent.trim()}</td>`;
                        }
                        tableRows += '</tr>';
                    }
                });
                
                // Create the printable content
                printAllDiv.innerHTML = `
                    <div class="print-all-page">
                        <div class="print-all-header">
                            <h2>Computer Science Student Records</h2>
                            <p>Date Generated: ${new Date().toLocaleDateString()}</p>
                            <p>Total Students: ${rows.length}</p>
                        </div>
                        <div class="print-all-table">
                            <table>
                                <thead>
                                    <tr>
                                        ${headers.map(header => `<th>${header}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRows}
                                </tbody>
                            </table>
                        </div>
                        <div class="print-all-footer">
                            <p>CS Student Management System &copy; ${new Date().getFullYear()}</p>
                            <p>Printed on ${new Date().toLocaleDateString()}</p>
                        </div>
                    </div>
                `;
                
                // Create styles for printing
                const printAllStyle = document.createElement('style');
                printAllStyle.id = 'print-all-style';
                printAllStyle.innerHTML = `
                    @media print {
                        body * {
                            visibility: hidden;
                        }
                        
                        #temp-print-all-div {
                            visibility: visible !important;
                            position: absolute;
                            left: 0;
                            top: 0;
                            width: 100%;
                            padding: 20px;
                            background: white;
                            z-index: 9999;
                            display: block !important;
                        }
                        
                        #temp-print-all-div * {
                            visibility: visible;
                        }
                        
                        .print-all-page {
                            font-family: 'Arial', sans-serif;
                            width: 100%;
                            margin: 0 auto;
                            padding: 20px;
                        }
                        
                        .print-all-header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        
                        .print-all-header h2 {
                            font-size: 24px;
                            margin-bottom: 10px;
                        }
                        
                        .print-all-header p {
                            font-size: 14px;
                            margin: 5px 0;
                            color: #555;
                        }
                        
                        .print-all-table {
                            overflow-x: auto;
                            margin-bottom: 20px;
                        }
                        
                        .print-all-table table {
                            width: 100%;
                            border-collapse: collapse;
                            font-size: 12px;
                        }
                        
                        .print-all-table th, 
                        .print-all-table td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        
                        .print-all-table th {
                            background-color: #f2f2f2;
                            font-weight: bold;
                        }
                        
                        .print-all-table tr:nth-child(even) {
                            background-color: #f9f9f9;
                        }
                        
                        .student-table-photo {
                            width: 40px;
                            height: 40px;
                            border-radius: 50%;
                            object-fit: cover;
                            display: block;
                            border: 1px solid #ddd;
                            margin: 0 auto;
                        }
                        
                        .default-table-avatar {
                            width: 40px;
                            height: 40px;
                            border-radius: 50%;
                            background-color: #f8f9fa;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border: 1px solid #ddd;
                            font-size: 20px;
                            color: #6c757d;
                            margin: 0 auto;
                        }
                        
                        .photo-cell {
                            width: 60px;
                            text-align: center;
                        }
                        
                        .print-all-footer {
                            text-align: center;
                            font-size: 12px;
                            color: #666;
                            margin-top: 20px;
                            padding-top: 15px;
                            border-top: 1px solid #eee;
                        }
                        
                        @page {
                            size: landscape;
                            margin: 1cm;
                        }
                    }
                `;
                
                document.head.appendChild(printAllStyle);
                
                // Make the print div visible
                printAllDiv.style.display = "block";
                
                // Print directly
                setTimeout(() => {
                    window.print();
                    
                    // Clean up
                    setTimeout(() => {
                        document.head.removeChild(printAllStyle);
                        document.body.removeChild(printAllDiv);
                    }, 500);
                }, 300);
            } else {
                console.error("Students table not found");
            }
        });
    }
}

// Enhanced chart responsiveness for better mobile display
function setupResponsiveCharts() {
    const chartCanvas = document.getElementById('studentChart');
    if (chartCanvas) {
        // Make sure the chart preserves aspect ratio
        chartCanvas.style.width = '100%';
        chartCanvas.style.maxHeight = '400px';
        chartCanvas.style.margin = '0 auto';
        chartCanvas.style.display = 'block';
        
        // Add chart class for CSS styling
        chartCanvas.classList.add('chart');
        
        // Listen for window resize to adjust chart
        window.addEventListener('resize', function() {
            // Recalculate chart dimensions based on container
            const container = chartCanvas.parentElement;
            if (container) {
                // Ensure chart is responsive to container size
                const chartWidth = container.clientWidth;
                chartCanvas.style.width = chartWidth + 'px';
            }
        });
        
        // Initial trigger of resize
        window.dispatchEvent(new Event('resize'));
    }
}

// Improve student details popup with circular image styling
function setupStudentDetails() {
    // AJAX functionality for loading student data in modal
    const viewButtons = document.querySelectorAll('.view-btn');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-id');
            
            // Set the edit link
            const editButton = document.getElementById('edit-student');
            if (editButton) {
                editButton.href = `edit.php?id=${studentId}`;
            }
            
            // Load student data via AJAX
            fetch(`get_student.php?id=${studentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        // Populate the modal with student data
                        document.getElementById('student-name').textContent = `${data.first_name} ${data.last_name}`;
                        document.getElementById('student-id').textContent = data.student_id;
                        document.getElementById('student-section').textContent = data.section;
                        document.getElementById('student-year').textContent = `Year ${data.year_level}`;
                        document.getElementById('student-sex').textContent = data.sex;
                        document.getElementById('student-email').textContent = data.email || 'N/A';
                        document.getElementById('student-phone').textContent = data.phone || 'N/A';
                        document.getElementById('student-address').textContent = data.address || 'N/A';
                        document.getElementById('student-date').textContent = new Date(data.created_at).toLocaleDateString();
                        
                        // Set image if available with proper styling
                        const imgElement = document.getElementById('student-image');
                        if (imgElement) {
                            if (data.photo) {
                                imgElement.src = `../../assets/images/students/${data.photo}`;
                                imgElement.alt = `${data.first_name} ${data.last_name}`;
                                imgElement.classList.add('avatar');
                                imgElement.classList.add('avatar-lg');
                                imgElement.classList.add('mx-auto');
                                imgElement.classList.add('d-block');
                                imgElement.style.width = '80px';
                                imgElement.style.height = '80px';
                            } else {
                                imgElement.src = '../../assets/images/default-avatar.jpg';
                                imgElement.alt = 'Default Avatar';
                                imgElement.classList.add('avatar');
                                imgElement.classList.add('avatar-lg');
                            }
                        }
                    }
                })
                .catch(error => console.error('Error fetching student data:', error));
        });
    });
}

// Setup enhanced sidebar interactivity
function setupSidebar() {
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn');
    const sidebar = document.querySelector('.sidebar');
    const sidebarCollapse = document.querySelector('.sidebar-collapse');
    const mainContent = document.querySelector('.main-content');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Add active state to current page
    const currentPageUrl = window.location.pathname;
    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPageUrl.includes(href)) {
            link.classList.add('active');
        }
        
        // Keep active item highlighted
        link.addEventListener('click', function() {
            sidebarLinks.forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    // Toggle sidebar on mobile
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.toggle('expanded');
                if (sidebarOverlay) {
                    sidebarOverlay.style.display = sidebar.classList.contains('expanded') ? 'block' : 'none';
                    sidebarOverlay.style.opacity = sidebar.classList.contains('expanded') ? '1' : '0';
                }
            }
        });
    }
    
    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.remove('expanded');
                sidebarOverlay.style.display = 'none';
                sidebarOverlay.style.opacity = '0';
            }
        });
    }
    
    // Close sidebar on mobile when X is clicked
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.remove('expanded');
                if (sidebarOverlay) {
                    sidebarOverlay.style.display = 'none';
                    sidebarOverlay.style.opacity = '0';
                }
            }
        });
    }
    
    // Collapse/expand sidebar on desktop
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.toggle('collapsed');
                if (mainContent) {
                    mainContent.classList.toggle('expanded');
                }
                
                // Change chevron direction
                const chevron = this.querySelector('i');
                if (chevron) {
                    if (sidebar.classList.contains('collapsed')) {
                        chevron.classList.remove('bx-chevron-left');
                        chevron.classList.add('bx-chevron-right');
                    } else {
                        chevron.classList.remove('bx-chevron-right');
                        chevron.classList.add('bx-chevron-left');
                    }
                }
            }
        });
    }
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupThemeToggle(); // Add theme toggle functionality
    setupSmartPrint();
    setupStudentDetails();
    setupResponsiveCharts();
    setupSidebar();
});