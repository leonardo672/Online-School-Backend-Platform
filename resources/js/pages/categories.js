// Categories Management JavaScript

class CategoryManager {
    constructor() {
        this.initializeElements();
        this.initializeEventListeners();
        this.userEditedSlug = false;
        this.formChanged = false;
        this.currentSlug = document.getElementById('slug')?.getAttribute('data-current-slug') || '';
        
        // Get print data from global window object
        this.printData = window.categoryPrintData || null;
    }

    initializeElements() {
        // Create/Edit page elements
        this.nameInput = document.getElementById('name');
        this.slugInput = document.getElementById('slug');
        this.iconSelect = document.getElementById('icon');
        this.iconPreview = document.getElementById('iconPreview');
        this.colorInput = document.getElementById('color');
        this.colorText = document.getElementById('colorText');
        this.form = document.querySelector('form');
        this.submitBtn = document.querySelector('button[type="submit"]');
        this.formInputs = document.querySelectorAll('form input, form select, form textarea');
        
        // Index page elements
        this.deleteForms = document.querySelectorAll('form[action*="/categories/"]');
        this.tableRows = document.querySelectorAll('.table tbody tr');
        this.colorPreviews = document.querySelectorAll('.color-preview');
        this.searchForm = document.querySelector('form[action*="/categories"]');
        
        // Show page elements
        this.printBtn = document.getElementById('printCategoryBtn');
        this.statsCards = document.querySelectorAll('.stats-card');
    }

    initializeEventListeners() {
        // Create/Edit page listeners
        this.initCreateEditListeners();
        
        // Index page listeners
        this.initIndexListeners();
        
        // Show page listeners
        this.initShowListeners();
        
        // Set initial icon preview for create/edit page
        if (this.iconSelect && this.iconPreview && this.colorInput) {
            setTimeout(() => {
                this.updateIconPreview();
            }, 100);
        }
    }

    initCreateEditListeners() {
        // Slug auto-generation
        if (this.slugInput) {
            this.slugInput.setAttribute('data-current-slug', this.slugInput.value);
            this.slugInput.addEventListener('input', () => {
                this.userEditedSlug = true;
                this.markFormAsChanged();
            });
        }

        if (this.nameInput && this.slugInput) {
            this.nameInput.addEventListener('input', () => {
                this.generateSlug();
                this.markFormAsChanged();
            });
        }

        // Icon and color preview
        if (this.iconSelect && this.iconPreview) {
            this.iconSelect.addEventListener('change', () => {
                this.updateIconPreview();
                this.markFormAsChanged();
            });
        }

        if (this.colorInput && this.iconPreview) {
            this.colorInput.addEventListener('input', (e) => {
                this.updateColorPreview(e);
                this.updateColorText(e.target.value);
                this.markFormAsChanged();
            });
        }

        // Color text input sync
        if (this.colorText && this.colorInput) {
            this.colorText.addEventListener('input', (e) => {
                const colorValue = e.target.value;
                if (/^#[0-9A-F]{6}$/i.test(colorValue)) {
                    this.colorInput.value = colorValue;
                    this.updateIconPreview();
                    this.markFormAsChanged();
                }
            });
        }

        // Form submission
        if (this.form && this.submitBtn) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Track form changes
        if (this.formInputs.length > 0) {
            this.formInputs.forEach(input => {
                input.addEventListener('input', () => this.markFormAsChanged());
            });
        }

        // Remove invalid class on input
        if (this.formInputs.length > 0) {
            this.formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        }

        // Warn before leaving unsaved changes
        window.addEventListener('beforeunload', (e) => this.handleBeforeUnload(e));
    }

    initIndexListeners() {
        // Delete confirmation for all category forms
        if (this.deleteForms.length > 0) {
            this.deleteForms.forEach(form => {
                if (form.method.toLowerCase() === 'post' || form.querySelector('input[name="_method"]')) {
                    form.addEventListener('submit', (e) => this.confirmDelete(e));
                }
            });
        }

        // Add hover effects to table rows
        if (this.tableRows.length > 0) {
            this.tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.2s ease';
                });
            });
        }

        // Color preview enhancement
        if (this.colorPreviews.length > 0) {
            this.colorPreviews.forEach(preview => {
                preview.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.2)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                preview.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        }

        // Search form enhancement
        if (this.searchForm) {
            const searchInput = this.searchForm.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('keyup', this.debounce(() => {
                    if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
                        this.searchForm.submit();
                    }
                }, 500));
            }
        }
    }

    initShowListeners() {
        // Print button functionality
        if (this.printBtn) {
            this.printBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.printCategoryDetails();
            });
        }

        // Add animation to stats cards
        if (this.statsCards.length > 0) {
            this.statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.transition = 'all 0.3s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        }
    }

    generateSlug() {
        if (this.userEditedSlug) return;

        const name = this.nameInput.value;
        
        if (name.length > 0) {
            let slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            
            if (this.slugInput.value === this.currentSlug || !this.slugInput.value) {
                this.slugInput.value = slug;
            }
        }
    }

    updateIconPreview() {
        const iconClass = this.iconSelect?.value;
        const color = this.colorInput?.value || '#3498db';
        
        if (this.iconPreview) {
            if (iconClass) {
                this.iconPreview.innerHTML = `<i class="${iconClass} fa-2x" style="color: ${color};"></i>
                                             <p class="small mt-2">Icon Preview</p>`;
            } else {
                this.iconPreview.innerHTML = `<i class="fas fa-folder fa-2x text-muted"></i>
                                             <p class="small mt-2">Icon Preview</p>`;
            }
        }
    }

    updateColorPreview(e) {
        const icon = this.iconPreview?.querySelector('i');
        if (icon) {
            icon.style.color = e.target.value;
        }
    }

    updateColorText(color) {
        if (this.colorText) {
            this.colorText.value = color;
        }
    }

    markFormAsChanged() {
        this.formChanged = true;
    }

    handleBeforeUnload(e) {
        if (this.formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    }

    handleSubmit(e) {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        let isValid = true;
        
        if (nameInput && !nameInput.value.trim()) {
            nameInput.classList.add('is-invalid');
            isValid = false;
        }
        
        if (slugInput && !slugInput.value.trim()) {
            slugInput.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }

        if (this.submitBtn) {
            this.submitBtn.classList.add('loading');
            this.submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Processing...';
        }
    }

    confirmDelete(e) {
        const confirmed = confirm('Are you sure you want to delete this category? All courses in this category will become uncategorized.');
        if (!confirmed) {
            e.preventDefault();
        }
    }

    printCategoryDetails() {
        if (!this.printData) {
            alert('Print data not available');
            return;
        }

        const data = this.printData;
        
        // Build courses HTML
        let coursesHtml = '';
        if (data.courses && data.courses.length > 0) {
            data.courses.forEach(course => {
                coursesHtml += `
                    <tr>
                        <td>${course.title}</td>
                        <td>${course.instructor}</td>
                        <td>${course.price == 0 ? 'Free' : '$' + parseFloat(course.price).toFixed(2)}</td>
                        <td>${course.level ? course.level.charAt(0).toUpperCase() + course.level.slice(1) : 'N/A'}</td>
                        <td>${course.published ? 'Published' : 'Draft'}</td>
                    </tr>
                `;
            });
        }

        const printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Category Details - ${data.name}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 30px; color: #333; max-width: 1200px; margin: 0 auto; }
                    .print-header { text-align: center; border-bottom: 2px solid #2ecc71; padding-bottom: 20px; margin-bottom: 30px; }
                    .print-header h1 { color: #27ae60; margin: 0 0 10px 0; }
                    .print-header h2 { margin: 0 0 10px 0; color: #666; }
                    .print-header p { color: #999; margin: 0; }
                    .print-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
                    .print-section h3 { color: #2ecc71; margin-bottom: 15px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                    th { background-color: #f8f9fa; font-weight: 600; }
                    .color-box { width: 20px; height: 20px; display: inline-block; border: 1px solid #ddd; margin-right: 10px; vertical-align: middle; }
                    .stats-row { display: flex; justify-content: space-between; margin-bottom: 20px; }
                    .stat-box { flex: 1; padding: 15px; border-radius: 8px; color: white; text-align: center; margin-right: 10px; }
                    .stat-box:last-child { margin-right: 0; }
                    .stat-box h4 { margin: 0 0 5px 0; font-size: 14px; opacity: 0.9; }
                    .stat-box p { margin: 0; font-size: 24px; font-weight: bold; }
                    .print-footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee; }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h1>Category Details Report</h1>
                    <h2>${data.name}</h2>
                    <p>Generated on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                </div>
                
                <div class="stats-row">
                    <div class="stat-box" style="background: linear-gradient(135deg, #2ecc71, #27ae60);">
                        <h4>Total Courses</h4>
                        <p>${data.coursesCount}</p>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                        <h4>Published</h4>
                        <p>${data.publishedCount}</p>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                        <h4>Draft</h4>
                        <p>${data.draftCount}</p>
                    </div>
                </div>
                
                <div class="print-section">
                    <h3>Basic Information</h3>
                    <table>
                        <tr><td><strong>Category Name:</strong></td><td>${data.name}</td></tr>
                        <tr><td><strong>Slug:</strong></td><td>${data.slug}</td></tr>
                        <tr><td><strong>Icon:</strong></td><td>${data.icon || 'Default icon'}</td></tr>
                        <tr><td><strong>Color:</strong></td><td>${data.color ? `<div class="color-box" style="background-color: ${data.color};"></div> ${data.color}` : 'Default color'}</td></tr>
                    </table>
                </div>
                
                <div class="print-section">
                    <h3>Description</h3>
                    <p>${data.description}</p>
                </div>
                
                <div class="print-section">
                    <h3>Courses in This Category (${data.coursesCount})</h3>
                    ${data.coursesCount > 0 ? `
                    <table>
                        <thead><tr><th>Course Title</th><th>Instructor</th><th>Price</th><th>Level</th><th>Status</th></tr></thead>
                        <tbody>${coursesHtml}</tbody>
                    </table>
                    ` : '<p>No courses in this category.</p>'}
                </div>
                
                <div class="print-section">
                    <h3>Timeline</h3>
                    <table>
                        <tr><td><strong>Created On:</strong></td><td>${data.createdDate}</td></tr>
                        <tr><td><strong>Last Updated:</strong></td><td>${data.updatedDate}</td></tr>
                    </table>
                </div>
                
                <div class="print-footer">
                    <p>--- End of Category Details Report ---</p>
                </div>
            </body>
            </html>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.onload = function() { printWindow.print(); };
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new CategoryManager();
});