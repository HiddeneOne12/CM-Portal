/**
 * Stationary Items Management JavaScript
 * Handles DataTables, inline editing, and AJAX operations
 */

(function($) {
    'use strict';

    // Get configuration from window object
    const config = window.stationaryItemsConfig || {};
    const baseUrl = config.baseUrl || '';
    const stationaryItemsData = config.items || [];
    const stationarySuppliersData = config.suppliers || [];
    const unitsData = config.units || [];
    let selectedMonth = config.selectedMonth || '';
    let dataTable = null;

    // Initialize DataTable with search and pagination
    function initializeDataTable() {
        const tableElement = $('#stationary-items-table');
        
        if (tableElement.length === 0) {
            console.error('Table #stationary-items-table not found!');
            return null;
        }
        
        // Check if DataTables is loaded
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables library not loaded!');
            return null;
        }
        
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#stationary-items-table')) {
            $('#stationary-items-table').DataTable().destroy();
        }

        console.log('Initializing DataTable for #stationary-items-table');
        
        dataTable = $('#stationary-items-table').DataTable({
                order: [],
                pageLength: 100,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    emptyTable: "No stationary items found",
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [12] }, // Action column
                    { searchable: false, targets: [12] }  // Action column
                ],
                initComplete: function() {
                    const api = this.api();
                    console.log('DataTable initComplete - adding search fields');
                    
                    // Apply the search to tfoot inputs
                    api.columns().every(function(index) {
                        if (index === 12) return; // Skip action column
                        
                        const column = this;
                        const footer = $(column.footer());
                        
                        if (footer.length === 0) {
                            console.warn('Footer not found for column', index);
                            return;
                        }
                        
                        // Create search input in footer
                        const searchInput = $('<input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 100%; padding: 3px 5px; border: 1px solid #ddd;" />');
                        footer.html(searchInput);
                        
                        // Bind search functionality
                        searchInput.on('keyup change', function() {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                    });
                    
                    console.log('Search fields added to tfoot');
                    
                    // Initialize editable after DataTable is ready
                    setTimeout(function() {
                        initializeEditable();
                        initializeSelect2Editable();
                    }, 500);
                },
                drawCallback: function() {
                    // Re-initialize editable after each draw (pagination, search, etc.)
                    reinitializeEditableAfterDraw();
                }
            });
            
            console.log('DataTable initialized successfully');
            return dataTable;
        }
        console.error('Table element not found');
        return null;
    }

    // Initialize Select2 dropdowns
    function initializeSelect2() {
        $('.select2').select2();
    }

    // Initialize X-editable for inline editing
    function initializeEditable() {
        // Destroy existing editable instances
        $(".editable, .editable-click").each(function() {
            try {
                if ($(this).hasClass('editable') || $(this).hasClass('editable-click')) {
                    $(this).editable('destroy');
                }
            } catch(e) {
                // Ignore errors if not initialized
            }
        });
        
        // Standard editable fields (exclude Select2-based ones)
        $(".editable-click").each(function() {
            const $this = $(this);
            if (!$this.hasClass('stationary-item-source') && 
                !$this.hasClass('stationary-supplier-source') && 
                $this.data('name')) {
                
                $this.editable({
                    emptytext: "Empty",
                    showbuttons: true,
                    url: baseUrl + "/stationary-items/update",
                    highlight: "#FFFF80",
                    inputclass: "form-control-sm",
                    placement: "left",
                    step: '0.01',
                    success: function(response, newValue) {
                        const editableElement = this;
                        const currentTd = $(editableElement).closest("td");
                        const rowId = $(editableElement).data('pk');
                        
                        if (newValue !== "") {
                            currentTd.removeClass("empty-value");
                            $(editableElement).closest("td a").css("font-style", "normal");
                            
                            // Format numbers
                            if ($(editableElement).data('name') === 'quantity' || 
                                $(editableElement).data('name') === 'net_unit_price') {
                                newValue = formatNumber(newValue);
                                setTimeout(function() {
                                    $(editableElement).text(newValue);
                                    $(editableElement).attr('data-value', newValue);
                                }, 100);
                            }
                            
                            // Format dates
                            if ($(editableElement).data('name') === 'date_request' || 
                                $(editableElement).data('name') === 'date_received') {
                                newValue = formatDate(newValue);
                                setTimeout(function() {
                                    $(editableElement).text(newValue);
                                    $(editableElement).attr('data-value', newValue);
                                }, 100);
                            }
                            
                            // Auto-calculate amounts
                            if ($(editableElement).data('name') === 'net_unit_price' || 
                                $(editableElement).data('name') === 'quantity') {
                                if (response && response.net_amount !== undefined) {
                                    updateAllValues(rowId, response.net_amount, response.vat_amount, response.total_amount);
                                }
                            }
                        } else {
                            $(editableElement).closest("td a").css("font-style", "italic");
                            $(editableElement).closest("td").addClass("empty-value");
                            $(editableElement).closest("td a").css("color", "#007bff");
                        }
                    },
                    error: function(response) {
                        const errorMsg = response.responseJSON?.errors?.value?.[0] || response.responseJSON?.message || 'Error updating field';
                        alert(errorMsg);
                    }
                });
            }
        });
    }
    
    // Re-initialize editable after DataTable operations
    function reinitializeEditableAfterDraw() {
        setTimeout(function() {
            initializeEditable();
            initializeSelect2Editable();
        }, 200);
    }

    // Initialize Select2-based editable fields (Items & Suppliers)
    function initializeSelect2Editable() {
        // Remove existing event handlers
        $('.stationary-item-source').off('click.editable');
        $('.stationary-supplier-source').off('click.editable');
        
        // Stationary Items dropdown
        $('.stationary-item-source').on('click.editable', function(e) {
            e.preventDefault();
            const $this = $(this);
            
            try {
                $this.editable('destroy');
            } catch(e) {}
            
            $this.editable({
                source: stationaryItemsData,
                display: function(value) {
                    const select = $('<select class="select2-editable can-edit"></select>');
                    const emptyOption = $('<option value="">Empty</option>');
                    const pk = $(this).data('pk');
                    const name = $(this).data('name');
                    
                    select.append(emptyOption);
                    $.each(stationaryItemsData, function(index, item) {
                        const option = $('<option value="' + item.value + '">' + item.text + '</option>');
                        if (value == item.value) {
                            option.prop('selected', true);
                        }
                        select.append(option);
                    });
                    
                    const select2 = select.select2();
                    select2.on('change', function() {
                        const newValue = select2.val();
                        updateSelectedValues(newValue, pk, name, $(this));
                    });
                    
                    if (!value) {
                        $(this).css('font-style', 'italic');
                    } else {
                        $(this).css('font-style', 'normal');
                    }
                    
                    $(this).html(select2).find('.select2-editable').select2();
                    $(this).editable("destroy");
                }
            });
        });

        // Suppliers dropdown
        $('.stationary-supplier-source').on('click.editable', function(e) {
            e.preventDefault();
            const $this = $(this);
            
            try {
                $this.editable('destroy');
            } catch(e) {}
            
            $this.editable({
                source: stationarySuppliersData,
                display: function(value) {
                    const select = $('<select class="select2-editable can-edit"></select>');
                    const emptyOption = $('<option value="">Empty</option>');
                    const pk = $(this).data('pk');
                    const name = $(this).data('name');
                    
                    select.append(emptyOption);
                    $.each(stationarySuppliersData, function(index, item) {
                        const option = $('<option value="' + item.value + '">' + item.text + '</option>');
                        if (value == item.value) {
                            option.prop('selected', true);
                        }
                        select.append(option);
                    });
                    
                    const select2 = select.select2();
                    select2.on('change', function() {
                        const newValue = select2.val();
                        updateSelectedValues(newValue, pk, name, $(this));
                    });
                    
                    if (!value) {
                        $(this).css('font-style', 'italic');
                    } else {
                        $(this).css('font-style', 'normal');
                    }
                    
                    $(this).html(select2).find('.select2-editable').select2();
                    $(this).editable("destroy");
                }
            });
        });
    }

    // Update calculated values after price/quantity change
    function updateAllValues(rowId, netAmount, vatAmount, totalAmount) {
        const row = $('tr[data-id="' + rowId + '"]');
        if (row.length) {
            row.find('td#va_' + rowId + ' .number-space').text(formatNumber(vatAmount));
            row.find('td#ta_' + rowId + ' .number-space').text(formatNumber(totalAmount));
        }
    }

    // Update selected values (for Select2 dropdowns)
    function updateSelectedValues(newValue, pk, name, editableElement) {
        const params = {
            name: name,
            value: newValue,
            pk: pk,
            _token: $('meta[name="_token"]').attr('content')
        };
        
        $.ajax({
            url: baseUrl + '/stationary-items/update',
            method: 'POST',
            data: params,
            success: function(response) {
                const currentTd = $(editableElement).closest('td');
                if (newValue !== '') {
                    currentTd.removeClass('empty-value');
                    $(editableElement).closest('td a').css('font-style', 'normal');
                } else {
                    $(editableElement).closest('td a').css('font-style', 'italic');
                    $(editableElement).closest('td').addClass('empty-value');
                    $(editableElement).closest('td a').css('color', '#007bff');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.errors?.value?.[0] || 'Error updating field';
                alert(errorMsg);
            }
        });
    }

    // Format date string
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];
        const day = date.getDate();
        const monthIndex = date.getMonth();
        const year = date.getFullYear();
        const monthName = monthNames[monthIndex];
        const paddedDay = (day < 10) ? '0' + day : day;
        return paddedDay + '-' + monthName + '-' + year;
    }

    // Format number with commas and 2 decimal places
    function formatNumber(number) {
        if (!number && number !== 0) return '0.00';
        const roundedNumber = Math.round(parseFloat(number) * 100) / 100;
        let formattedNumber = roundedNumber.toFixed(2);
        formattedNumber = parseFloat(formattedNumber).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return formattedNumber;
    }

    // Handle year change - load months via AJAX
    window.accrualYearChange = function(selectedYearParam) {
        const selectedYear = selectedYearParam || $("#stationary_items_year").val();
        
        if (!selectedYear) {
            $('#stationary_items_months').empty().append('<option value="">Choose Period</option>');
            return;
        }
        
        // Show loading state
        const monthsSelect = $('#stationary_items_months');
        monthsSelect.prop('disabled', true);
        monthsSelect.empty().append('<option value="">Loading...</option>');
        
        $.ajax({
            method: 'GET',
            url: baseUrl + "/stationary-items-selected-year-periods",
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                selectedYear: selectedYear
            },
            success: function(data) {
                monthsSelect.prop('disabled', false);
                if (data && Array.isArray(data)) {
                    updateMonths(data, selectedMonth);
                } else {
                    console.error('Invalid response format:', data);
                    monthsSelect.empty().append('<option value="">Choose Period</option>');
                    alert('Error: Invalid response from server');
                }
            },
            error: function(xhr) {
                monthsSelect.prop('disabled', false);
                console.error('Error loading months:', xhr);
                monthsSelect.empty().append('<option value="">Choose Period</option>');
                alert('Error loading months. Please check if the route exists: /stationary-items-selected-year-periods');
            }
        });
    };

    // Update months dropdown
    function updateMonths(months, selectedMonthValue) {
        const monthsSelect = $('#stationary_items_months');
        monthsSelect.empty();
        monthsSelect.append('<option value="">Choose Period</option>');
        
        if (months && months.length > 0) {
            months.forEach(function(month) {
                const option = new Option(month, month);
                if (month === selectedMonthValue) {
                    $(option).prop('selected', true);
                }
                monthsSelect.append(option);
            });
        }
        
        monthsSelect.trigger('change');
    }

    // Roll forward functionality
    $(document).on('click', '.roll-forward', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to roll forward?')) {
            $.ajax({
                url: baseUrl + '/stationary-items-roll-forward',
                method: 'GET',
                success: function(response) {
                    if (response.responseCode == 1) {
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alert('Error occurred while rolling forward');
                }
            });
        }
    });

    // Delete functionality
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: baseUrl + '/stationary-items/delete/' + id,
                method: 'GET',
                success: function(response) {
                    const obj = typeof response === 'string' ? JSON.parse(response) : response;
                    if (obj.responseCode == 1) {
                        alert(obj.msg);
                        location.reload();
                    } else {
                        alert(obj.msg);
                    }
                },
                error: function() {
                    alert('Error occurred while deleting');
                }
            });
        }
    });

    // Initialize everything when document is ready
    $(document).ready(function() {
        // Initialize Select2 first
        initializeSelect2();
        
        // Initialize DataTable
        initializeDataTable();
        
        // Load months if year is already selected
        if (config.selectedYear) {
            setTimeout(function() {
                accrualYearChange(config.selectedYear);
            }, 500);
        }
    });

})(jQuery);
